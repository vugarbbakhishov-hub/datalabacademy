<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require __DIR__ . '/config.php';

/*
 * Diaqnostika endpoint-i — DEFAULT OLARAQ BAĞLIDIR.
 * Aktiv etmək üçün api/config.local.php-də uzun, təsadüfi token təyin et:
 *   define('DL_DBCHECK_TOKEN', 'cox-uzun-tesadufi-deyer-bura');
 * Token təyin olunmayıbsa endpoint 404 qaytarır (yəni yoxdur kimi davranır).
 */
$expected = defined('DL_DBCHECK_TOKEN')
    ? (string) constant('DL_DBCHECK_TOKEN')
    : (string) (getenv('DL_DBCHECK_TOKEN') ?: '');
$token = (string) ($_GET['token'] ?? '');

if ($expected === '' || strlen($expected) < 16 || !hash_equals($expected, $token)) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'message' => 'Not found.']);
    exit;
}

try {
    $pdo = db();
    $checks = [];
    foreach (['courses', 'orders', 'leads', 'admin_settings', 'admin_users', 'student_users'] as $table) {
        try {
            $checks[$table] = (int) $pdo->query('SELECT COUNT(*) FROM `' . $table . '`')->fetchColumn();
        } catch (Throwable $error) {
            $checks[$table] = 'missing';
        }
    }
    // Yalnız minimal məlumat — host/user/şifrə/versiya sızdırılmır.
    echo json_encode([
        'ok' => true,
        'database' => DB_NAME,
        'tables' => $checks,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $error) {
    error_log('[db-check] ' . $error->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Database error.']);
}
