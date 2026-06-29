<?php
declare(strict_types=1);

require __DIR__ . '/ai-client.php';

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'path' => '/']);
session_start();

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function ai_chat_respond(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ai_chat_respond(['ok' => false, 'message' => 'POST tələb olunur.'], 405);
}

if (!dl_ai_available()) {
    ai_chat_respond(['ok' => false, 'message' => 'AI hələ aktiv deyil. Admin api/config.local.php-də DL_AI_KEY əlavə etməlidir.'], 503);
}

$raw = file_get_contents('php://input') ?: '';
$data = json_decode($raw, true);
if (!is_array($data)) {
    ai_chat_respond(['ok' => false, 'message' => 'Yanlış sorğu.'], 400);
}

$message = trim((string) ($data['message'] ?? ''));
if ($message === '' || mb_strlen($message) > 2000) {
    ai_chat_respond(['ok' => false, 'message' => 'Mesaj boş və ya çox uzundur.'], 422);
}

// Sadə sürət limiti — 10 dəqiqədə 30 sorğu (sessiya əsaslı)
$now = time();
$hits = array_values(array_filter((array) ($_SESSION['ai_hits'] ?? []), static fn($t) => (int) $t > $now - 600));
if (count($hits) >= 30) {
    ai_chat_respond(['ok' => false, 'message' => 'Çox sayda sorğu. Bir az sonra yenidən cəhd edin.'], 429);
}
$hits[] = $now;
$_SESSION['ai_hits'] = $hits;

$history = [];
foreach ((array) ($data['history'] ?? []) as $h) {
    if (!is_array($h)) {
        continue;
    }
    $role = ($h['role'] ?? '') === 'assistant' ? 'assistant' : 'user';
    $content = trim((string) ($h['content'] ?? ''));
    if ($content !== '') {
        $history[] = ['role' => $role, 'content' => mb_substr($content, 0, 2000)];
    }
}
$history = array_slice($history, -8);
$history[] = ['role' => 'user', 'content' => $message];

/**
 * Saytın real məlumatlarını (kurslar, qiymətlər, əlaqə) bazadan çəkir ki,
 * AI uydurma deyil, real datadan cavab versin.
 */
function dl_ai_site_context(): string
{
    try {
        $pdo = db();
        $lines = [];

        $rows = $pdo->query("SELECT title, category, price, lessons FROM courses WHERE status = 'active' ORDER BY sort_order, id")->fetchAll();
        if ($rows) {
            $lines[] = 'ƏYANİ (OFFLINE) KURSLAR:';
            foreach ($rows as $r) {
                $lines[] = '- ' . $r['title'] . ' (' . $r['category'] . '), qiymət: $' . (float) $r['price'] . ', ' . (int) $r['lessons'] . ' dərs';
            }
        }

        try {
            $orows = $pdo->query("SELECT title, category, price, lessons, level FROM online_courses WHERE status = 'active' ORDER BY sort_order, id")->fetchAll();
        } catch (Throwable $e) {
            $orows = [];
        }
        if ($orows) {
            $lines[] = 'ONLINE TƏLİMLƏR:';
            foreach ($orows as $r) {
                $lines[] = '- ' . $r['title'] . ' (' . $r['category'] . '), qiymət: $' . (float) $r['price'] . ', ' . (int) $r['lessons'] . ' dərs'
                    . (!empty($r['level']) ? ', səviyyə: ' . $r['level'] : '');
            }
        }

        try {
            $contact = $pdo->query('SELECT phone, email, address FROM site_contact WHERE id = 1 LIMIT 1')->fetch();
        } catch (Throwable $e) {
            $contact = null;
        }
        $phone = is_array($contact) ? ($contact['phone'] ?: '+994 50 654 97 37') : '+994 50 654 97 37';
        $email = is_array($contact) ? ($contact['email'] ?: 'info@datalabacademy.az') : 'info@datalabacademy.az';
        $address = is_array($contact) ? ($contact['address'] ?: 'Bakı, Azərbaycan') : 'Bakı, Azərbaycan';
        $lines[] = 'ƏLAQƏ: Telefon ' . $phone . ', E-poçt ' . $email . ', Ünvan: ' . $address . '. Kurslar səhifəsi: course-filter-one-open.html. Əlaqə səhifəsi: contact.php';

        // Admin tərəfindən əlavə edilmiş bilik bazası (ai_knowledge cədvəli)
        try {
            $kb = $pdo->query("SELECT title, content FROM ai_knowledge WHERE status = 'active' ORDER BY sort_order, id")->fetchAll();
        } catch (Throwable $e) {
            $kb = [];
        }
        if ($kb) {
            $lines[] = '';
            $lines[] = 'ƏLAVƏ MƏLUMAT (admin tərəfindən):';
            foreach ($kb as $entry) {
                $title = trim((string) ($entry['title'] ?? ''));
                $body = trim((string) ($entry['content'] ?? ''));
                if ($body === '') {
                    continue;
                }
                $lines[] = ($title !== '' ? $title . ': ' : '') . $body;
            }
        }

        return implode("\n", $lines);
    } catch (Throwable $e) {
        return '';
    }
}

$siteContext = dl_ai_site_context();

$system = "Sən DatalabAcademy-nin AI mentor köməkçisisən. DatalabAcademy onlayn təhsil platformasıdır: Data Analitika, SQL, Excel və AI kursları. "
    . "Tələbələrə bu mövzularda (data analitika, SQL sorğuları, Excel, Power BI, Python, generativ AI) aydın, qısa və praktik kömək et. "
    . "Həmişə Azərbaycan dilində cavab ver. Mövzudan kənar, zərərli və ya uyğunsuz sorğuları nəzakətlə rədd et. Cavabları qısa (4-5 cümlə) saxla, lazım olduqda kiçik nümunə kod ver.\n\n"
    . "AŞAĞIDAKI REAL MƏLUMATLAR DatalabAcademy-nin bazasından gəlir. Kurslar, qiymətlər, dərs sayı və əlaqə barədə suallara YALNIZ bu siyahıya əsasən cavab ver. "
    . "Burada OLMAYAN kurs, qiymət və ya fakt UYDURMA. Əmin olmadıqda və ya məlumat siyahıda yoxdursa, " . ($siteContext !== '' ? "telefon nömrəsinə yönləndir" : "saytdakı 'Kurslar' bölməsinə və +994 50 654 97 37 nömrəsinə yönləndir") . ".\n\n"
    . "=== REAL MƏLUMATLAR ===\n" . ($siteContext !== '' ? $siteContext : '(məlumat yüklənmədi)');

try {
    $reply = dl_ai_chat($system, $history, 800);
    ai_chat_respond(['ok' => true, 'reply' => $reply]);
} catch (Throwable $e) {
    error_log('[ai-chat] ' . $e->getMessage());
    ai_chat_respond(['ok' => false, 'message' => 'AI cavab verə bilmədi. Bir az sonra yenidən cəhd edin.'], 502);
}
