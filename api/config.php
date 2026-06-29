<?php
declare(strict_types=1);

/*
 * Database konfiqurasiyası.
 * Prioritet: environment dəyişənləri -> config.local.php -> aşağıdakı defaultlar.
 *
 * PRODUCTION ÜÇÜN VACİB: root istifadə etmə! phpMyAdmin-də bu SQL ilə
 * yalnız bu database-ə icazəsi olan ayrıca user yarat:
 *
 *   CREATE USER 'datalab_app'@'localhost' IDENTIFIED BY 'GUCLU_SIFRE_BURA';
 *   GRANT SELECT, INSERT, UPDATE, DELETE, CREATE ON datalab_academy.* TO 'datalab_app'@'localhost';
 *   FLUSH PRIVILEGES;
 *
 * Sonra ya aşağıdakı defaultları dəyiş, ya da bu qovluqda config.local.php yarat:
 *
 *   <?php
 *   define('DB_USER', 'datalab_app');
 *   define('DB_PASS', 'GUCLU_SIFRE_BURA');
 */

if (is_file(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

defined('DB_HOST') || define('DB_HOST', getenv('DL_DB_HOST') ?: 'localhost');
defined('DB_PORT') || define('DB_PORT', getenv('DL_DB_PORT') ?: '3306');
defined('DB_NAME') || define('DB_NAME', getenv('DL_DB_NAME') ?: '');
defined('DB_USER') || define('DB_USER', getenv('DL_DB_USER') ?: 'root');
defined('DB_PASS') || define('DB_PASS', getenv('DL_DB_PASS') !== false ? getenv('DL_DB_PASS') : '');

// Production təhlükəsizliyi: localhost xaricində root + boş şifrə ilə işləməyi bağla (fail-closed).
// Lokal XAMPP (localhost/127.0.0.1) üçün default root/boş şifrə işləməyə davam edir.
$dlHost = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
$dlIsLocal = ($dlHost === '') || (bool) preg_match('/^(localhost|127\.0\.0\.1|\[::1\]|::1)(:\d+)?$/', $dlHost);
if (!$dlIsLocal && DB_USER === 'root' && (string) DB_PASS === '') {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Konfiqurasiya xətası: production mühitində root/boş şifrə ilə baza bağlantısı bağlıdır. api/config.local.php-də DB_USER və DB_PASS təyin edin.');
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
