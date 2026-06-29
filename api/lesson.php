<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'path' => '/',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https',
]);
session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function respond(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function read_json(): array
{
    $raw = file_get_contents('php://input') ?: '';
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        respond(['ok' => false, 'message' => 'Invalid JSON body.'], 400);
    }
    return $data;
}

function clean_id(string $value, string $fallback = ''): string
{
    return preg_match('/^[A-Za-z0-9_-]{1,120}$/', $value) ? $value : $fallback;
}

function require_same_origin(): void
{
    // CSRF müdafiəsi: Origin və Referer hər ikisi yoxdursa rədd et.
    $source = (string) ($_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '');
    if ($source === '') {
        respond(['ok' => false, 'message' => 'Sorğunun mənbəyi yoxlanmadı.'], 403);
    }
    $sourceHost = strtolower((string) parse_url($source, PHP_URL_HOST));
    $requestHost = strtolower(explode(':', (string) ($_SERVER['HTTP_HOST'] ?? ''))[0]);
    if ($sourceHost === '' || !hash_equals($requestHost, $sourceHost)) {
        respond(['ok' => false, 'message' => 'Sorğunun mənbəyi etibarlı deyil.'], 403);
    }
}

/** Progress yazılarına sürət limiti (sessiya əsaslı) — spam-ın qarşısını alır. */
function lesson_rate_limit(string $bucket, int $max, int $windowSec): void
{
    $now = time();
    $key = 'lesson_rl_' . $bucket;
    $hits = array_values(array_filter((array) ($_SESSION[$key] ?? []), static fn($t) => (int) $t > $now - $windowSec));
    if (count($hits) >= $max) {
        respond(['ok' => false, 'message' => 'Çox sayda sorğu.'], 429);
    }
    $hits[] = $now;
    $_SESSION[$key] = $hits;
}

function require_course_access(PDO $pdo, string $courseId): int
{
    $studentId = (int) ($_SESSION['student_user_id'] ?? 0);
    if ($studentId <= 0) {
        respond(['ok' => false, 'auth' => false, 'message' => 'Dərslərə baxmaq üçün hesabınıza daxil olun.'], 401);
    }
    $stmt = $pdo->prepare('
        SELECT id FROM student_enrollments
        WHERE student_id = ? AND course_id = ? AND status = "active"
        LIMIT 1
    ');
    $stmt->execute([$studentId, $courseId]);
    if (!$stmt->fetchColumn()) {
        respond(['ok' => false, 'access' => false, 'message' => 'Bu kurs üçün aktiv qeydiyyat tapılmadı.'], 403);
    }
    return $studentId;
}

function get_course_payload(PDO $pdo, string $courseId, string $clientId): array
{
    $courseStmt = $pdo->prepare('SELECT id, title, category, price, lessons, students, status, image, description FROM courses WHERE id = ? LIMIT 1');
    $courseStmt->execute([$courseId]);
    $course = $courseStmt->fetch();
    if (!$course) {
        respond(['ok' => false, 'message' => 'Course not found.'], 404);
    }

    $sectionsStmt = $pdo->prepare('SELECT id, course_id AS courseId, title, duration_label AS durationLabel, section_type AS type, sort_order AS sortOrder, is_locked AS isLocked FROM course_sections WHERE course_id = ? ORDER BY sort_order, id');
    $sectionsStmt->execute([$courseId]);
    $sections = $sectionsStmt->fetchAll();

    $lessonsStmt = $pdo->prepare('SELECT id, course_id AS courseId, section_id AS sectionId, title, lesson_type AS type, duration_label AS durationLabel, duration_seconds AS durationSeconds, video_source AS videoSource, video_url AS videoUrl, embed_html AS embedHtml, content_title AS contentTitle, content_body AS contentBody, is_preview AS isPreview, is_required AS isRequired, is_locked AS isLocked, sort_order AS sortOrder FROM course_lessons WHERE course_id = ? ORDER BY section_id, sort_order, id');
    $lessonsStmt->execute([$courseId]);
    $lessons = $lessonsStmt->fetchAll();

    $lessonIds = array_column($lessons, 'id');
    $materials = [];
    $questions = [];
    $progress = [];

    if ($lessonIds) {
        $in = implode(',', array_fill(0, count($lessonIds), '?'));

        $materialsStmt = $pdo->prepare("SELECT id, lesson_id AS lessonId, title, material_type AS type, url, sort_order AS sortOrder FROM lesson_materials WHERE lesson_id IN ($in) ORDER BY sort_order, id");
        $materialsStmt->execute($lessonIds);
        foreach ($materialsStmt->fetchAll() as $material) {
            $materials[$material['lessonId']][] = $material;
        }

        $questionsStmt = $pdo->prepare("SELECT id, lesson_id AS lessonId, question, explanation, sort_order AS sortOrder FROM lesson_quiz_questions WHERE lesson_id IN ($in) ORDER BY sort_order, id");
        $questionsStmt->execute($lessonIds);
        $questionRows = $questionsStmt->fetchAll();
        $questionIds = array_column($questionRows, 'id');

        $optionsByQuestion = [];
        if ($questionIds) {
            $qin = implode(',', array_fill(0, count($questionIds), '?'));
            $optionsStmt = $pdo->prepare("SELECT id, question_id AS questionId, option_text AS text, sort_order AS sortOrder FROM lesson_quiz_options WHERE question_id IN ($qin) ORDER BY sort_order, id");
            $optionsStmt->execute($questionIds);
            foreach ($optionsStmt->fetchAll() as $option) {
                $optionsByQuestion[$option['questionId']][] = $option;
            }
        }

        foreach ($questionRows as $question) {
            $question['options'] = $optionsByQuestion[$question['id']] ?? [];
            $questions[$question['lessonId']][] = $question;
        }

        if ($clientId !== '') {
            $progressStmt = $pdo->prepare("SELECT lesson_id AS lessonId, last_position AS lastPosition, duration, progress_percent AS progressPercent, completed, quiz_score AS quizScore, quiz_passed AS quizPassed FROM lesson_progress WHERE client_id = ? AND course_id = ?");
            $progressStmt->execute([$clientId, $courseId]);
            foreach ($progressStmt->fetchAll() as $row) {
                $progress[$row['lessonId']] = [
                    'lastPosition' => (float) $row['lastPosition'],
                    'duration' => (float) $row['duration'],
                    'progressPercent' => (int) $row['progressPercent'],
                    'completed' => (bool) $row['completed'],
                    'quizScore' => $row['quizScore'] === null ? null : (int) $row['quizScore'],
                    'quizPassed' => (bool) $row['quizPassed'],
                ];
            }
        }
    }

    foreach ($lessons as &$lesson) {
        $lesson['durationSeconds'] = (int) $lesson['durationSeconds'];
        $lesson['isPreview'] = (bool) $lesson['isPreview'];
        $lesson['isRequired'] = (bool) $lesson['isRequired'];
        $lesson['isLocked'] = (bool) $lesson['isLocked'];
        $lesson['sortOrder'] = (int) $lesson['sortOrder'];
        $lesson['materials'] = $materials[$lesson['id']] ?? [];
        $lesson['quiz'] = $questions[$lesson['id']] ?? [];
        $lesson['progress'] = $progress[$lesson['id']] ?? [
            'lastPosition' => 0,
            'duration' => 0,
            'progressPercent' => 0,
            'completed' => false,
            'quizScore' => null,
            'quizPassed' => false,
        ];
    }
    unset($lesson);

    foreach ($sections as &$section) {
        $section['sortOrder'] = (int) $section['sortOrder'];
        $section['isLocked'] = (bool) $section['isLocked'];
        $section['lessons'] = array_values(array_filter($lessons, static function (array $lesson) use ($section): bool {
            return $lesson['sectionId'] === $section['id'];
        }));
        $section['totalLessons'] = count($section['lessons']);
        $section['completedLessons'] = count(array_filter($section['lessons'], static function (array $lesson): bool {
            return !empty($lesson['progress']['completed']);
        }));
    }
    unset($section);

    $course['price'] = (float) $course['price'];
    $course['lessons'] = (int) $course['lessons'];
    $course['students'] = (int) $course['students'];

    return [
        'course' => $course,
        'sections' => $sections,
        'progressSummary' => progress_summary($lessons),
    ];
}

function progress_summary(array $lessons): array
{
    $required = array_values(array_filter($lessons, static fn(array $lesson): bool => !empty($lesson['isRequired'])));
    $completed = array_values(array_filter($required, static fn(array $lesson): bool => !empty($lesson['progress']['completed'])));
    $percent = count($required) ? (int) floor((count($completed) / count($required)) * 100) : 0;

    return [
        'required' => count($required),
        'completed' => count($completed),
        'percent' => $percent,
        'certificateReady' => $percent >= 100,
    ];
}

function save_progress(PDO $pdo, array $data): array
{
    $clientId = clean_id((string) ($data['clientId'] ?? ''));
    $courseId = clean_id((string) ($data['courseId'] ?? ''));
    $lessonId = clean_id((string) ($data['lessonId'] ?? ''));

    if ($clientId === '' || $courseId === '' || $lessonId === '') {
        respond(['ok' => false, 'message' => 'Missing progress identifiers.'], 400);
    }

    $lastPosition = max(0, (float) ($data['lastPosition'] ?? 0));
    $duration = max(0, (float) ($data['duration'] ?? 0));
    $progressPercent = max(0, min(100, (int) ($data['progressPercent'] ?? 0)));
    $completed = !empty($data['completed']) || $progressPercent >= 90;

    $stmt = $pdo->prepare('INSERT INTO lesson_progress (client_id, lesson_id, course_id, last_position, duration, progress_percent, completed) VALUES (:client_id, :lesson_id, :course_id, :last_position, :duration, :progress_percent, :completed) ON DUPLICATE KEY UPDATE last_position = GREATEST(last_position, VALUES(last_position)), duration = GREATEST(duration, VALUES(duration)), progress_percent = GREATEST(progress_percent, VALUES(progress_percent)), completed = IF(completed = 1 OR VALUES(completed) = 1, 1, 0), updated_at = CURRENT_TIMESTAMP');
    $stmt->execute([
        ':client_id' => $clientId,
        ':lesson_id' => $lessonId,
        ':course_id' => $courseId,
        ':last_position' => $lastPosition,
        ':duration' => $duration,
        ':progress_percent' => $progressPercent,
        ':completed' => $completed ? 1 : 0,
    ]);

    return ['saved' => true, 'completed' => $completed, 'progressPercent' => $progressPercent];
}

function submit_quiz(PDO $pdo, array $data): array
{
    $clientId = clean_id((string) ($data['clientId'] ?? ''));
    $courseId = clean_id((string) ($data['courseId'] ?? ''));
    $lessonId = clean_id((string) ($data['lessonId'] ?? ''));
    $answers = is_array($data['answers'] ?? null) ? $data['answers'] : [];

    if ($clientId === '' || $courseId === '' || $lessonId === '') {
        respond(['ok' => false, 'message' => 'Missing quiz identifiers.'], 400);
    }

    $stmt = $pdo->prepare('SELECT q.id AS question_id, o.id AS option_id FROM lesson_quiz_questions q JOIN lesson_quiz_options o ON o.question_id = q.id WHERE q.lesson_id = ? AND o.is_correct = 1');
    $stmt->execute([$lessonId]);
    $correct = [];
    foreach ($stmt->fetchAll() as $row) {
        $correct[$row['question_id']] = $row['option_id'];
    }

    $total = count($correct);
    $right = 0;
    foreach ($correct as $questionId => $optionId) {
        if (($answers[$questionId] ?? '') === $optionId) {
            $right++;
        }
    }

    $score = $total ? (int) round(($right / $total) * 100) : 0;
    $passed = $total > 0 && $score >= 70;

    $progressStmt = $pdo->prepare('INSERT INTO lesson_progress (client_id, lesson_id, course_id, progress_percent, completed, quiz_score, quiz_passed) VALUES (:client_id, :lesson_id, :course_id, :progress_percent, :completed, :quiz_score, :quiz_passed) ON DUPLICATE KEY UPDATE progress_percent = GREATEST(progress_percent, VALUES(progress_percent)), completed = IF(completed = 1 OR VALUES(completed) = 1, 1, 0), quiz_score = VALUES(quiz_score), quiz_passed = VALUES(quiz_passed), updated_at = CURRENT_TIMESTAMP');
    $progressStmt->execute([
        ':client_id' => $clientId,
        ':lesson_id' => $lessonId,
        ':course_id' => $courseId,
        ':progress_percent' => $passed ? 100 : 50,
        ':completed' => $passed ? 1 : 0,
        ':quiz_score' => $score,
        ':quiz_passed' => $passed ? 1 : 0,
    ]);

    return [
        'score' => $score,
        'passed' => $passed,
        'correct' => $correct,
        'right' => $right,
        'total' => $total,
    ];
}

try {
    $pdo = db();
    $action = $_GET['action'] ?? 'course';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_same_origin();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'course') {
        $courseId = clean_id((string) ($_GET['course'] ?? '1'), '1');
        $studentId = require_course_access($pdo, $courseId);
        $clientId = 'student-' . $studentId;
        respond(['ok' => true, 'data' => get_course_payload($pdo, $courseId, $clientId)]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'progress') {
        $data = read_json();
        $courseId = clean_id((string) ($data['courseId'] ?? ''));
        $studentId = require_course_access($pdo, $courseId);
        lesson_rate_limit('progress', 150, 600); // 10 dəqiqədə maksimum 150 progress yazısı
        $data['clientId'] = 'student-' . $studentId;
        respond(['ok' => true, 'data' => save_progress($pdo, $data)]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'quiz') {
        $data = read_json();
        $courseId = clean_id((string) ($data['courseId'] ?? ''));
        $studentId = require_course_access($pdo, $courseId);
        $data['clientId'] = 'student-' . $studentId;
        respond(['ok' => true, 'data' => submit_quiz($pdo, $data)]);
    }

    respond(['ok' => false, 'message' => 'Unknown action.'], 404);
} catch (Throwable $error) {
    respond(['ok' => false, 'message' => $error->getMessage()], 500);
}
