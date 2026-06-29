<?php
declare(strict_types=1);

require_once __DIR__ . '/datalab-menu-data.php';

const DL_DEFAULT_LESSON_VIDEO = 'assets/videos/wishlist-video-01.mp4';

function dl_learning_setting(string $name): array
{
    try {
        $stmt = db()->prepare('SELECT setting_value FROM admin_settings WHERE setting_name = ? LIMIT 1');
        $stmt->execute([$name]);
        $value = $stmt->fetchColumn();
        $decoded = is_string($value) ? json_decode($value, true) : null;
        return is_array($decoded) ? $decoded : [];
    } catch (Throwable) {
        return [];
    }
}

function dl_learning_online_course(?string $wanted = null): ?array
{
    $wanted = trim((string) ($wanted ?? ($_GET['online'] ?? $_GET['slug'] ?? $_GET['course'] ?? $_GET['id'] ?? '')));
    $courses = dl_menu_online_courses();
    if ($wanted === '') {
        return $courses[0] ?? null;
    }

    foreach ($courses as $course) {
        if ((string) ($course['slug'] ?? '') === $wanted || (string) ($course['id'] ?? '') === $wanted) {
            return $course;
        }
    }

    return null;
}

function dl_learning_course_key(array $course): string
{
    $slug = trim((string) ($course['slug'] ?? ''));
    return 'online:' . ($slug !== '' ? $slug : (string) ($course['id'] ?? ''));
}

function dl_learning_online_course_id(array $course): string
{
    return (string) ($course['id'] ?? '');
}

function dl_learning_default_curriculum(array $course): array
{
    $title = (string) ($course['title'] ?? 'Online təlim');
    return [
        [
            'id' => 'intro',
            'title' => 'Başlanğıc',
            'locked' => false,
            'lessons' => [
                [
                    'id' => 'intro-1',
                    'title' => $title . ' proqramına giriş',
                    'type' => 'video',
                    'duration' => '20 min',
                    'source' => 'drive',
                    'link' => DL_DEFAULT_LESSON_VIDEO,
                    'note' => 'Kursun məqsədi, platforma və öyrənmə qaydası.',
                    'preview' => true,
                ],
                [
                    'id' => 'intro-2',
                    'title' => 'İş mühiti və praktiki fayllar',
                    'type' => 'article',
                    'duration' => '15 min',
                    'source' => 'auto',
                    'link' => '',
                    'note' => 'Dərs materialları və çalışma faylları.',
                    'preview' => false,
                ],
            ],
        ],
        [
            'id' => 'practice',
            'title' => 'Praktiki dərslər',
            'locked' => false,
            'lessons' => [
                [
                    'id' => 'practice-1',
                    'title' => 'Real tapşırıq üzərində izah',
                    'type' => 'video',
                    'duration' => '45 min',
                    'source' => 'drive',
                    'link' => DL_DEFAULT_LESSON_VIDEO,
                    'note' => 'Mentor izahı ilə praktiki video dərs.',
                    'preview' => false,
                ],
                [
                    'id' => 'practice-2',
                    'title' => 'Ev tapşırığı və mentor qeydləri',
                    'type' => 'assignment',
                    'duration' => '30 min',
                    'source' => 'auto',
                    'link' => '',
                    'note' => 'Öyrəndiklərinizi yoxlamaq üçün tapşırıq.',
                    'preview' => false,
                ],
            ],
        ],
    ];
}

function dl_learning_curriculum(array $course): array
{
    $onlineSettings = dl_learning_setting('onlineCurriculum');
    $courseId = dl_learning_online_course_id($course);
    if ($courseId !== '' && isset($onlineSettings[$courseId]) && is_array($onlineSettings[$courseId])) {
        return $onlineSettings[$courseId];
    }

    $legacySettings = dl_learning_setting('curriculum');
    $legacyKey = dl_learning_course_key($course);
    if (isset($legacySettings[$legacyKey]) && is_array($legacySettings[$legacyKey])) {
        return $legacySettings[$legacyKey];
    }

    return [];
}

function dl_learning_online_course_details(array $course): array
{
    $settings = dl_learning_setting('onlineCourseDetails');
    $courseId = dl_learning_online_course_id($course);
    return $courseId !== '' && isset($settings[$courseId]) && is_array($settings[$courseId])
        ? $settings[$courseId]
        : [];
}

function dl_learning_flat_lessons(array $sections): array
{
    $flat = [];
    foreach ($sections as $sectionIndex => $section) {
        foreach (($section['lessons'] ?? []) as $lessonIndex => $lesson) {
            if (!is_array($lesson)) {
                continue;
            }
            $lesson['_sectionIndex'] = $sectionIndex;
            $lesson['_lessonIndex'] = $lessonIndex;
            $lesson['_sectionId'] = (string) ($section['id'] ?? ('section-' . $sectionIndex));
            $lesson['_sectionTitle'] = (string) ($section['title'] ?? 'Bölmə');
            $flat[] = $lesson;
        }
    }
    return $flat;
}

function dl_learning_lesson_url(array $course, array $lesson): string
{
    $slug = trim((string) ($course['slug'] ?? $course['id'] ?? ''));
    $lessonId = trim((string) ($lesson['id'] ?? ''));
    return 'lesson.php?online=' . rawurlencode($slug) . '&lesson=' . rawurlencode($lessonId);
}

function dl_learning_active_lesson(array $lessons): ?array
{
    $wanted = trim((string) ($_GET['lesson'] ?? ''));
    if ($wanted === '') {
        foreach ($lessons as $lesson) {
            if (($lesson['type'] ?? 'video') === 'video') {
                return $lesson;
            }
        }
        return $lessons[0] ?? null;
    }

    foreach ($lessons as $lesson) {
        if ((string) ($lesson['id'] ?? '') === $wanted) {
            return $lesson;
        }
    }

    return $lessons[0] ?? null;
}

function dl_learning_lesson_video(array $lesson): string
{
    $link = trim((string) ($lesson['link'] ?? ''));
    if ($link !== '') {
        return $link;
    }
    return ($lesson['type'] ?? 'video') === 'video' ? DL_DEFAULT_LESSON_VIDEO : '';
}

function dl_learning_type_icon(string $type): string
{
    return match ($type) {
        'article' => 'feather-file-text',
        'quiz' => 'feather-help-circle',
        'assignment' => 'feather-clipboard',
        default => 'feather-play-circle',
    };
}
