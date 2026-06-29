<?php
declare(strict_types=1);

require __DIR__ . '/ai-client.php';

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'path' => '/',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https',
]);
session_start();

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function ai_quiz_respond(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ai_quiz_respond(['ok' => false, 'message' => 'POST tələb olunur.'], 405);
}

// Yalnız admin
if (empty($_SESSION['admin_user_id'])) {
    ai_quiz_respond(['ok' => false, 'message' => 'Giriş tələb olunur.'], 401);
}
$token = (string) ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
if ($token === '' || !hash_equals((string) ($_SESSION['csrf'] ?? ''), $token)) {
    ai_quiz_respond(['ok' => false, 'message' => 'CSRF token yanlışdır.'], 403);
}

if (!dl_ai_available()) {
    ai_quiz_respond(['ok' => false, 'message' => 'AI aktiv deyil. api/config.local.php-də DL_AI_KEY əlavə et.'], 503);
}

$raw = file_get_contents('php://input') ?: '';
$data = json_decode($raw, true);
if (!is_array($data)) {
    ai_quiz_respond(['ok' => false, 'message' => 'Yanlış sorğu.'], 400);
}

$text = trim((string) ($data['text'] ?? ''));
$count = max(1, min(10, (int) ($data['count'] ?? 5)));
if (mb_strlen($text) < 30) {
    ai_quiz_respond(['ok' => false, 'message' => 'Dərs mətni çox qısadır (ən azı 30 simvol).'], 422);
}
$text = mb_substr($text, 0, 12000);

$system = "Sən təcrübəli müəllimsən. Verilən dərs mətnindən {$count} ədəd çoxseçimli (multiple-choice) test sualı hazırla. "
    . "Hər sualın dəqiq 4 variantı olsun və yalnız BİR düzgün cavab olsun. Suallar Azərbaycan dilində, mətnə əsaslanan və müxtəlif çətinlikdə olsun. "
    . "NƏTİCƏNİ YALNIZ etibarlı JSON massiv kimi qaytar, heç bir izah və ya kod bloku yazma. Format: "
    . '[{"question":"sual mətni","options":["A","B","C","D"],"correct":0}] '
    . "burada \"correct\" düzgün variantın indeksidir (0-3).";

try {
    $reply = dl_ai_chat($system, [['role' => 'user', 'content' => $text]], 2500);

    // JSON-u təmizlə (kod bloku və ya artıq mətn olarsa)
    $clean = trim($reply);
    $clean = preg_replace('/^```(?:json)?\s*/i', '', $clean);
    $clean = preg_replace('/\s*```$/', '', $clean);
    $start = strpos($clean, '[');
    $end = strrpos($clean, ']');
    if ($start !== false && $end !== false && $end > $start) {
        $clean = substr($clean, $start, $end - $start + 1);
    }

    $questions = json_decode($clean, true);
    if (!is_array($questions) || !$questions) {
        ai_quiz_respond(['ok' => false, 'message' => 'AI cavabı oxunmadı. Yenidən cəhd et.'], 502);
    }

    $out = [];
    foreach ($questions as $q) {
        if (!is_array($q)) {
            continue;
        }
        $opts = array_values(array_filter(array_map(static fn($o) => trim((string) $o), (array) ($q['options'] ?? [])), static fn($o) => $o !== ''));
        $question = trim((string) ($q['question'] ?? ''));
        if ($question === '' || count($opts) < 2) {
            continue;
        }
        $opts = array_slice($opts, 0, 4);
        $correct = (int) ($q['correct'] ?? 0);
        if ($correct < 0 || $correct >= count($opts)) {
            $correct = 0;
        }
        $out[] = ['question' => $question, 'options' => $opts, 'correct' => $correct];
    }

    if (!$out) {
        ai_quiz_respond(['ok' => false, 'message' => 'Sual generasiya olunmadı. Yenidən cəhd et.'], 502);
    }
    ai_quiz_respond(['ok' => true, 'questions' => $out]);
} catch (Throwable $e) {
    error_log('[ai-quiz] ' . $e->getMessage());
    ai_quiz_respond(['ok' => false, 'message' => 'AI cavab verə bilmədi. Bir az sonra yenidən cəhd et.'], 502);
}
