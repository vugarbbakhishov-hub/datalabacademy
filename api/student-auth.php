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
header('X-Content-Type-Options: nosniff');

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
        respond(['ok' => false, 'message' => 'Sorğu düzgün deyil.'], 400);
    }
    return $data;
}

function ensure_student_schema(PDO $pdo): void
{
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS student_users (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(190) NOT NULL,
            email VARCHAR(190) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NULL,
            avatar_url VARCHAR(500) NOT NULL DEFAULT "",
            phone VARCHAR(40) NOT NULL DEFAULT "",
            bio VARCHAR(600) NOT NULL DEFAULT "",
            provider VARCHAR(32) NOT NULL DEFAULT "local",
            google_sub VARCHAR(190) NULL,
            status VARCHAR(32) NOT NULL DEFAULT "active",
            email_verified_at DATETIME NULL,
            last_login_at DATETIME NULL,
            last_seen_at DATETIME NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_student_google_sub (google_sub)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');

    try {
        $pdo->exec('ALTER TABLE student_users ADD COLUMN last_seen_at DATETIME NULL AFTER last_login_at');
    } catch (Throwable $error) {
        // column already exists on upgraded installs
    }
    try {
        $pdo->exec('ALTER TABLE student_users ADD COLUMN email_verified_at DATETIME NULL AFTER status');
    } catch (Throwable $error) {
        // Existing installations already have the column.
    }
    foreach ([
        'phone VARCHAR(40) NOT NULL DEFAULT ""',
        'bio VARCHAR(600) NOT NULL DEFAULT ""',
    ] as $definition) {
        try {
            $pdo->exec('ALTER TABLE student_users ADD COLUMN ' . $definition);
        } catch (Throwable $error) {
            // Existing installations already have the column.
        }
    }
    $pdo->exec('UPDATE student_users SET email_verified_at = created_at WHERE status = "active" AND email_verified_at IS NULL');

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS student_auth_tokens (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            student_id INT UNSIGNED NOT NULL,
            token_hash CHAR(64) NOT NULL UNIQUE,
            token_type VARCHAR(32) NOT NULL,
            expires_at DATETIME NOT NULL,
            used_at DATETIME NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_student_token_lookup (token_hash, token_type, expires_at),
            CONSTRAINT fk_student_auth_tokens_user FOREIGN KEY (student_id) REFERENCES student_users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS student_auth_attempts (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            ip VARCHAR(45) NOT NULL,
            action_name VARCHAR(32) NOT NULL,
            success TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_student_auth_rate (ip, action_name, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS student_enrollments (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            student_id INT UNSIGNED NOT NULL,
            course_id VARCHAR(64) NOT NULL DEFAULT "",
            course_title VARCHAR(255) NOT NULL DEFAULT "",
            enrollment_type VARCHAR(32) NOT NULL DEFAULT "course",
            status VARCHAR(32) NOT NULL DEFAULT "active",
            enrolled_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_student_enrollments_student (student_id),
            INDEX idx_student_enrollments_course (course_id),
            CONSTRAINT fk_student_enrollments_user FOREIGN KEY (student_id) REFERENCES student_users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS online_reviews (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            course_id VARCHAR(64) NOT NULL DEFAULT "",
            student_id INT UNSIGNED NULL,
            student_name VARCHAR(190) NOT NULL DEFAULT "",
            rating TINYINT NOT NULL DEFAULT 5,
            body TEXT NULL,
            status VARCHAR(20) NOT NULL DEFAULT "pending",
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_online_reviews_course (course_id, status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS orders (
            id VARCHAR(64) NOT NULL PRIMARY KEY,
            customer VARCHAR(255) NOT NULL,
            course_id VARCHAR(64) NOT NULL DEFAULT "",
            phone VARCHAR(64) NOT NULL DEFAULT "",
            status VARCHAR(32) NOT NULL DEFAULT "new",
            amount DECIMAL(10,2) NOT NULL DEFAULT 0,
            order_date VARCHAR(20) NOT NULL DEFAULT "",
            student_id INT UNSIGNED NULL,
            email VARCHAR(190) NOT NULL DEFAULT "",
            payment_method VARCHAR(32) NOT NULL DEFAULT "card",
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_orders_student (student_id),
            INDEX idx_orders_status (status),
            CONSTRAINT fk_orders_student FOREIGN KEY (student_id) REFERENCES student_users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');

    foreach ([
        'student_id INT UNSIGNED NULL',
        'email VARCHAR(190) NOT NULL DEFAULT ""',
        'payment_method VARCHAR(32) NOT NULL DEFAULT "card"',
    ] as $definition) {
        try {
            $pdo->exec('ALTER TABLE orders ADD COLUMN ' . $definition);
        } catch (Throwable $error) {
            // Existing installations already have the column.
        }
    }
}

function clean_name(string $name): string
{
    $name = trim(preg_replace('/\s+/', ' ', $name) ?? '');
    return mb_substr($name, 0, 190);
}

function public_student(array $row): array
{
    return [
        'id' => (int) $row['id'],
        'name' => (string) $row['name'],
        'email' => (string) $row['email'],
        'avatar' => (string) ($row['avatar_url'] ?? ''),
        'phone' => (string) ($row['phone'] ?? ''),
        'bio' => (string) ($row['bio'] ?? ''),
        'provider' => (string) ($row['provider'] ?? 'local'),
        'emailVerified' => !empty($row['email_verified_at']) || ($row['provider'] ?? '') === 'google',
    ];
}

function set_student_session(PDO $pdo, array $row): void
{
    session_regenerate_id(true);
    $_SESSION['student_user_id'] = (int) $row['id'];
    $_SESSION['student_email'] = (string) $row['email'];
    $_SESSION['student_name'] = (string) $row['name'];
    $_SESSION['student_last_seen'] = time();
    $pdo->prepare('UPDATE student_users SET last_login_at = NOW(), last_seen_at = NOW() WHERE id = ?')->execute([(int) $row['id']]);
}

function current_student(PDO $pdo): ?array
{
    if (empty($_SESSION['student_user_id'])) {
        return null;
    }
    if (!empty($_SESSION['student_last_seen']) && time() - (int) $_SESSION['student_last_seen'] > 86400) {
        unset($_SESSION['student_user_id'], $_SESSION['student_email'], $_SESSION['student_name'], $_SESSION['student_last_seen']);
        return null;
    }
    $_SESSION['student_last_seen'] = time();
    $pdo->prepare('UPDATE student_users SET last_seen_at = NOW() WHERE id = ?')->execute([(int) $_SESSION['student_user_id']]);
    $stmt = $pdo->prepare('SELECT id, name, email, avatar_url, phone, bio, provider, email_verified_at FROM student_users WHERE id = ? AND status = "active" LIMIT 1');
    $stmt->execute([(int) $_SESSION['student_user_id']]);
    $row = $stmt->fetch();
    return is_array($row) ? $row : null;
}

function google_client_id(): string
{
    if (defined('DL_GOOGLE_CLIENT_ID')) {
        return (string) constant('DL_GOOGLE_CLIENT_ID');
    }
    return (string) (getenv('DL_GOOGLE_CLIENT_ID') ?: '');
}

function verify_google_credential(string $credential): array
{
    $clientId = google_client_id();
    if ($clientId === '') {
        respond(['ok' => false, 'message' => 'Google girişi üçün DL_GOOGLE_CLIENT_ID konfiqurasiya edilməyib.'], 501);
    }

    // curl istifadə et — allow_url_fopen tələb etmir, daha etibarlı və timeout dəstəkli
    $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . rawurlencode($credential);
    if (!function_exists('curl_init')) {
        respond(['ok' => false, 'message' => 'Server konfiqurasiyası Google girişini dəstəkləmir (cURL yoxdur).'], 501);
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,           // 10 saniyə timeout
        CURLOPT_CONNECTTIMEOUT => 5,            // bağlantı timeout
        CURLOPT_SSL_VERIFYPEER => true,         // SSL sertifikatı yoxla
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_HTTPGET        => true,
        CURLOPT_USERAGENT      => 'DatalabAcademy/1.0',
        CURLOPT_FOLLOWLOCATION => false,        // redirect-ə icazə vermə
    ]);
    $json   = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error  = curl_error($ch);
    curl_close($ch);

    if (!is_string($json) || $json === '' || $error !== '') {
        error_log('[student-auth] Google tokeninfo curl xətası: ' . $error);
        respond(['ok' => false, 'message' => 'Google hesabı yoxlanmadı. Bir az sonra yenidən yoxla.'], 502);
    }
    if ($status !== 200) {
        respond(['ok' => false, 'message' => 'Google tokeni qəbul edilmədi.'], 401);
    }

    $payload = json_decode($json, true);
    if (!is_array($payload) || ($payload['aud'] ?? '') !== $clientId || empty($payload['email'])) {
        respond(['ok' => false, 'message' => 'Google tokeni düzgün deyil.'], 401);
    }
    if (($payload['email_verified'] ?? 'false') !== 'true') {
        respond(['ok' => false, 'message' => 'Google e-poçtu təsdiqlənməyib.'], 401);
    }
    return $payload;
}

function client_ip(): string
{
    return mb_substr((string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
}

function require_same_origin(): void
{
    // Dəyişdirici (mutating) POST sorğuları üçün CSRF müdafiəsi:
    // Origin və Referer-in HƏR İKİSİ yoxdursa rədd et (əvvəl boş mənbə qəbul olunurdu).
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

function enforce_auth_rate(PDO $pdo, string $action, int $limit = 8): void
{
    $failedOnly = in_array($action, ['login', 'reset-submit'], true);
    $stmt = $pdo->prepare('
        SELECT COUNT(*) FROM student_auth_attempts
        WHERE ip = ? AND action_name = ? AND created_at >= (NOW() - INTERVAL 15 MINUTE)
        ' . ($failedOnly ? 'AND success = 0' : '') . '
    ');
    $stmt->execute([client_ip(), $action]);
    if ((int) $stmt->fetchColumn() >= $limit) {
        respond(['ok' => false, 'message' => 'Çox sayda cəhd edildi. 15 dəqiqə sonra yenidən yoxlayın.'], 429);
    }
}

function record_auth_attempt(PDO $pdo, string $action, bool $success): void
{
    $pdo->prepare('INSERT INTO student_auth_attempts (ip, action_name, success) VALUES (?, ?, ?)')
        ->execute([client_ip(), $action, $success ? 1 : 0]);
}

function app_base_url(): string
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    $scheme = $https ? 'https' : 'http';
    $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $scriptDir = str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/api/student-auth.php')));
    $root = preg_replace('#/api$#', '', rtrim($scriptDir, '/')) ?: '';
    return $scheme . '://' . $host . $root;
}

function create_auth_token(PDO $pdo, int $studentId, string $type, int $ttlSeconds): string
{
    $raw = bin2hex(random_bytes(32));
    $hash = hash('sha256', $raw);
    $pdo->prepare('DELETE FROM student_auth_tokens WHERE student_id = ? AND token_type = ? AND used_at IS NULL')
        ->execute([$studentId, $type]);
    $pdo->prepare('
        INSERT INTO student_auth_tokens (student_id, token_hash, token_type, expires_at)
        VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL ? SECOND))
    ')->execute([$studentId, $hash, $type, $ttlSeconds]);
    return $raw;
}

function send_student_email(string $to, string $subject, string $message): bool
{
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'From: DatalabAcademy <no-reply@datalabacademy.az>',
    ];
    return @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $message, implode("\r\n", $headers));
}

function is_local_request(): bool
{
    return in_array(strtolower((string) ($_SERVER['HTTP_HOST'] ?? '')), ['localhost', 'localhost:80', '127.0.0.1'], true);
}

function token_student(PDO $pdo, string $rawToken, string $type): ?array
{
    if (!preg_match('/^[a-f0-9]{64}$/', $rawToken)) {
        return null;
    }
    $stmt = $pdo->prepare('
        SELECT t.id AS token_id, u.id, u.name, u.email
        FROM student_auth_tokens t
        JOIN student_users u ON u.id = t.student_id
        WHERE t.token_hash = ? AND t.token_type = ? AND t.used_at IS NULL AND t.expires_at > NOW()
        LIMIT 1
    ');
    $stmt->execute([hash('sha256', $rawToken), $type]);
    $row = $stmt->fetch();
    return is_array($row) ? $row : null;
}

function student_dashboard(PDO $pdo, int $studentId): array
{
    $clientId = 'student-' . $studentId;
    $stmt = $pdo->prepare('
        SELECT
            se.id, se.course_id AS courseId,
            COALESCE(NULLIF(se.course_title, ""), c.title, se.course_id) AS title,
            se.enrollment_type AS type, se.status, se.enrolled_at AS enrolledAt,
            c.image, c.category, c.lessons,
            COUNT(DISTINCT cl.id) AS totalLessons,
            COUNT(DISTINCT CASE WHEN lp.completed = 1 THEN cl.id END) AS completedLessons
        FROM student_enrollments se
        LEFT JOIN courses c ON c.id = se.course_id
        LEFT JOIN course_lessons cl ON cl.course_id = se.course_id AND cl.is_required = 1
        LEFT JOIN lesson_progress lp ON lp.lesson_id = cl.id AND lp.client_id = ?
        WHERE se.student_id = ?
        GROUP BY se.id, se.course_id, se.course_title, se.enrollment_type, se.status,
                 se.enrolled_at, c.title, c.image, c.category, c.lessons
        ORDER BY se.enrolled_at DESC, se.id DESC
    ');
    $stmt->execute([$clientId, $studentId]);
    $enrollments = array_map(static function (array $row): array {
        $total = max((int) $row['totalLessons'], (int) ($row['lessons'] ?? 0));
        $completed = (int) $row['completedLessons'];
        $row['id'] = (int) $row['id'];
        $row['totalLessons'] = $total;
        $row['completedLessons'] = $completed;
        $row['progress'] = $total > 0 ? min(100, (int) round(($completed / $total) * 100)) : 0;
        $row['certificateReady'] = $row['status'] === 'active' && $total > 0 && $completed >= $total;
        return $row;
    }, $stmt->fetchAll());

    $orderStmt = $pdo->prepare('
        SELECT o.id, o.course_id AS courseId, COALESCE(c.title, o.course_id) AS courseTitle,
               o.amount, o.status, o.payment_method AS paymentMethod,
               COALESCE(NULLIF(o.order_date, ""), DATE(o.created_at)) AS orderDate
        FROM orders o
        LEFT JOIN courses c ON c.id = o.course_id
        WHERE o.student_id = ?
        ORDER BY o.created_at DESC, o.id DESC
    ');
    $orderStmt->execute([$studentId]);
    $orders = array_map(static function (array $row): array {
        $row['amount'] = (float) $row['amount'];
        return $row;
    }, $orderStmt->fetchAll());

    return [
        'enrollments' => $enrollments,
        'orders' => $orders,
        'stats' => [
            'activeCourses' => count(array_filter($enrollments, static fn(array $item): bool => $item['status'] === 'active')),
            'pendingCourses' => count(array_filter($enrollments, static fn(array $item): bool => $item['status'] === 'pending')),
            'certificates' => count(array_filter($enrollments, static fn(array $item): bool => !empty($item['certificateReady']))),
            'averageProgress' => $enrollments ? (int) round(array_sum(array_column($enrollments, 'progress')) / count($enrollments)) : 0,
        ],
    ];
}

try {
    $pdo = db();
    ensure_student_schema($pdo);
    $action = (string) ($_GET['action'] ?? 'session');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_same_origin();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'session') {
        $student = current_student($pdo);
        if (!$student) {
            respond(['ok' => false, 'auth' => false], 401);
        }
        respond(['ok' => true, 'user' => public_student($student)]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'config') {
        respond(['ok' => true, 'googleClientId' => google_client_id()]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'dashboard') {
        $student = current_student($pdo);
        if (!$student) {
            respond(['ok' => false, 'auth' => false, 'message' => 'Daxil olmaq tələb olunur.'], 401);
        }
        respond([
            'ok' => true,
            'user' => public_student($student),
            'data' => student_dashboard($pdo, (int) $student['id']),
        ]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'verify-email') {
        $token = (string) ($_GET['token'] ?? '');
        $student = token_student($pdo, $token, 'verify_email');
        if (!$student) {
            respond(['ok' => false, 'message' => 'Təsdiq linki yanlışdır və ya vaxtı bitib.'], 422);
        }
        $pdo->beginTransaction();
        $pdo->prepare('UPDATE student_users SET status = "active", email_verified_at = NOW() WHERE id = ?')
            ->execute([(int) $student['id']]);
        $pdo->prepare('UPDATE student_auth_tokens SET used_at = NOW() WHERE id = ?')
            ->execute([(int) $student['token_id']]);
        $pdo->commit();
        respond(['ok' => true, 'message' => 'E-poçt təsdiqləndi. İndi hesabınıza daxil ola bilərsiniz.']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'resend-verification') {
        enforce_auth_rate($pdo, 'resend', 5);
        $data = read_json();
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $stmt = $pdo->prepare('SELECT id, name, email, status, email_verified_at FROM student_users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $student = $stmt->fetch();
        $devLink = '';
        if ($student && empty($student['email_verified_at']) && ($student['status'] ?? '') !== 'active') {
            $token = create_auth_token($pdo, (int) $student['id'], 'verify_email', 86400);
            $link = app_base_url() . '/verify-email?token=' . rawurlencode($token);
            send_student_email($email, 'DatalabAcademy e-poçt təsdiqi', "Salam {$student['name']},\n\nHesabınızı təsdiqləmək üçün link:\n$link\n\nLink 24 saat aktivdir.");
            if (is_local_request()) $devLink = $link;
        }
        record_auth_attempt($pdo, 'resend', true);
        respond(['ok' => true, 'message' => 'Hesab mövcuddursa, təsdiq linki göndərildi.', 'devLink' => $devLink]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'request-reset') {
        enforce_auth_rate($pdo, 'reset', 5);
        $data = read_json();
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $stmt = $pdo->prepare('SELECT id, name, email FROM student_users WHERE email = ? AND status = "active" LIMIT 1');
        $stmt->execute([$email]);
        $student = $stmt->fetch();
        $devLink = '';
        if ($student) {
            $token = create_auth_token($pdo, (int) $student['id'], 'reset_password', 1800);
            $link = app_base_url() . '/reset-password.html?token=' . rawurlencode($token);
            send_student_email($email, 'DatalabAcademy şifrə bərpası', "Salam {$student['name']},\n\nYeni şifrə yaratmaq üçün link:\n$link\n\nLink 30 dəqiqə aktivdir.");
            if (is_local_request()) $devLink = $link;
        }
        record_auth_attempt($pdo, 'reset', true);
        respond(['ok' => true, 'message' => 'Hesab mövcuddursa, şifrə bərpası linki göndərildi.', 'devLink' => $devLink]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'reset-password') {
        enforce_auth_rate($pdo, 'reset-submit', 8);
        $data = read_json();
        $token = (string) ($data['token'] ?? '');
        $password = (string) ($data['password'] ?? '');
        if (strlen($password) < 8) {
            respond(['ok' => false, 'message' => 'Şifrə ən azı 8 simvol olmalıdır.'], 422);
        }
        $student = token_student($pdo, $token, 'reset_password');
        if (!$student) {
            record_auth_attempt($pdo, 'reset-submit', false);
            respond(['ok' => false, 'message' => 'Bərpa linki yanlışdır və ya vaxtı bitib.'], 422);
        }
        $pdo->beginTransaction();
        $pdo->prepare('UPDATE student_users SET password_hash = ? WHERE id = ?')
            ->execute([password_hash($password, PASSWORD_DEFAULT), (int) $student['id']]);
        $pdo->prepare('UPDATE student_auth_tokens SET used_at = NOW() WHERE id = ?')
            ->execute([(int) $student['token_id']]);
        $pdo->commit();
        record_auth_attempt($pdo, 'reset-submit', true);
        respond(['ok' => true, 'message' => 'Şifrə yeniləndi. İndi daxil ola bilərsiniz.']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'register') {
        $data = read_json();
        $name = clean_name((string) ($data['name'] ?? ''));
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $password = (string) ($data['password'] ?? '');

        if ($name === '') {
            respond(['ok' => false, 'message' => 'Ad və soyad daxil et.'], 422);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            respond(['ok' => false, 'message' => 'E-poçt düzgün deyil.'], 422);
        }
        if (strlen($password) < 8) {
            respond(['ok' => false, 'message' => 'Şifrə ən azı 8 simvol olmalıdır.'], 422);
        }

        $stmt = $pdo->prepare('SELECT id FROM student_users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            respond(['ok' => false, 'message' => 'Bu e-poçt ilə hesab artıq var. Daxil ol bölməsindən istifadə et.'], 409);
        }

        $stmt = $pdo->prepare('INSERT INTO student_users (name, email, password_hash, provider, status) VALUES (?, ?, ?, "local", "pending")');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
        $id = (int) $pdo->lastInsertId();
        $token = create_auth_token($pdo, $id, 'verify_email', 86400);
        $link = app_base_url() . '/verify-email?token=' . rawurlencode($token);
        send_student_email($email, 'DatalabAcademy e-poçt təsdiqi', "Salam $name,\n\nHesabınızı təsdiqləmək üçün link:\n$link\n\nLink 24 saat aktivdir.");
        respond([
            'ok' => true,
            'requiresVerification' => true,
            'message' => 'Hesab yaradıldı. E-poçtunuza göndərilən linklə hesabı təsdiqləyin.',
            'devLink' => is_local_request() ? $link : '',
        ]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
        enforce_auth_rate($pdo, 'login', 8);
        $data = read_json();
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $password = (string) ($data['password'] ?? '');

        $stmt = $pdo->prepare('SELECT id, name, email, password_hash, avatar_url, provider, status, email_verified_at FROM student_users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        if (!$row || empty($row['password_hash']) || !password_verify($password, (string) $row['password_hash'])) {
            record_auth_attempt($pdo, 'login', false);
            usleep(250000);
            respond(['ok' => false, 'message' => 'E-poçt və ya şifrə yanlışdır.'], 401);
        }
        if (($row['status'] ?? '') !== 'active' || empty($row['email_verified_at'])) {
            record_auth_attempt($pdo, 'login', false);
            respond(['ok' => false, 'verificationRequired' => true, 'message' => 'Əvvəlcə e-poçt ünvanınızı təsdiqləyin.'], 403);
        }
        record_auth_attempt($pdo, 'login', true);
        set_student_session($pdo, $row);
        respond(['ok' => true, 'user' => public_student($row)]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'google-login') {
        $data = read_json();
        $payload = verify_google_credential((string) ($data['credential'] ?? ''));
        $email = strtolower(trim((string) $payload['email']));
        $name = clean_name((string) ($payload['name'] ?? $email));
        $avatar = mb_substr((string) ($payload['picture'] ?? ''), 0, 500);
        $sub = mb_substr((string) ($payload['sub'] ?? ''), 0, 190);

        $stmt = $pdo->prepare('SELECT id, name, email, avatar_url, provider FROM student_users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        if ($row) {
            $pdo->prepare('UPDATE student_users SET name = ?, avatar_url = ?, provider = "google", google_sub = ?, status = "active", email_verified_at = NOW() WHERE id = ?')
                ->execute([$name ?: $row['name'], $avatar, $sub, (int) $row['id']]);
            $row['name'] = $name ?: $row['name'];
            $row['avatar_url'] = $avatar;
            $row['provider'] = 'google';
        } else {
            $stmt = $pdo->prepare('INSERT INTO student_users (name, email, avatar_url, provider, google_sub, status, email_verified_at) VALUES (?, ?, ?, "google", ?, "active", NOW())');
            $stmt->execute([$name, $email, $avatar, $sub]);
            $row = ['id' => (int) $pdo->lastInsertId(), 'name' => $name, 'email' => $email, 'avatar_url' => $avatar, 'provider' => 'google'];
        }
        set_student_session($pdo, $row);
        respond(['ok' => true, 'user' => public_student($row)]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update-profile') {
        $student = current_student($pdo);
        if (!$student) {
            respond(['ok' => false, 'auth' => false, 'message' => 'Daxil olmaq tələb olunur.'], 401);
        }
        $data = read_json();
        $name = clean_name((string) ($data['name'] ?? ''));
        $phone = mb_substr(trim((string) ($data['phone'] ?? '')), 0, 40);
        $bio = mb_substr(trim((string) ($data['bio'] ?? '')), 0, 600);
        if ($name === '') {
            respond(['ok' => false, 'message' => 'Ad və soyad boş ola bilməz.'], 422);
        }
        $pdo->prepare('UPDATE student_users SET name = ?, phone = ?, bio = ? WHERE id = ?')
            ->execute([$name, $phone, $bio, (int) $student['id']]);
        $_SESSION['student_name'] = $name;
        $fresh = current_student($pdo);
        respond(['ok' => true, 'message' => 'Profil məlumatları yeniləndi.', 'user' => public_student($fresh ?: $student)]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'upload-avatar') {
        $student = current_student($pdo);
        if (!$student) {
            respond(['ok' => false, 'auth' => false, 'message' => 'Daxil olmaq tələb olunur.'], 401);
        }
        $data = read_json();
        $source = (string) ($data['image'] ?? '');
        if (!preg_match('#^data:image/(jpeg|png|webp);base64,([A-Za-z0-9+/=]+)$#', $source, $matches)) {
            respond(['ok' => false, 'message' => 'Yalnız JPG, PNG və ya WebP şəkli seçin.'], 422);
        }
        $binary = base64_decode($matches[2], true);
        if (!is_string($binary) || strlen($binary) > 2 * 1024 * 1024 || @getimagesizefromstring($binary) === false) {
            respond(['ok' => false, 'message' => 'Şəkil düzgün deyil və ya 2 MB limitini keçir.'], 422);
        }
        $extensions = ['jpeg' => 'jpg', 'png' => 'png', 'webp' => 'webp'];
        $directory = dirname(__DIR__) . '/uploads/student-avatars';
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            respond(['ok' => false, 'message' => 'Şəkil qovluğu yaradıla bilmədi.'], 500);
        }
        $filename = 'student-' . (int) $student['id'] . '-' . bin2hex(random_bytes(8)) . '.' . $extensions[$matches[1]];
        $target = $directory . '/' . $filename;
        if (file_put_contents($target, $binary, LOCK_EX) === false) {
            respond(['ok' => false, 'message' => 'Şəkil saxlanmadı.'], 500);
        }
        $oldAvatar = (string) ($student['avatar_url'] ?? '');
        if (str_starts_with($oldAvatar, 'uploads/student-avatars/')) {
            $oldPath = dirname(__DIR__) . '/' . $oldAvatar;
            if (is_file($oldPath)) @unlink($oldPath);
        }
        $avatar = 'uploads/student-avatars/' . $filename;
        $pdo->prepare('UPDATE student_users SET avatar_url = ? WHERE id = ?')->execute([$avatar, (int) $student['id']]);
        $student['avatar_url'] = $avatar;
        respond(['ok' => true, 'message' => 'Profil şəkli yeniləndi.', 'user' => public_student($student)]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'change-password') {
        $student = current_student($pdo);
        if (!$student) {
            respond(['ok' => false, 'auth' => false, 'message' => 'Daxil olmaq tələb olunur.'], 401);
        }
        $data = read_json();
        $current = (string) ($data['currentPassword'] ?? '');
        $next = (string) ($data['newPassword'] ?? '');
        if (strlen($next) < 8) {
            respond(['ok' => false, 'message' => 'Yeni şifrə ən azı 8 simvol olmalıdır.'], 422);
        }
        $stmt = $pdo->prepare('SELECT password_hash FROM student_users WHERE id = ? LIMIT 1');
        $stmt->execute([(int) $student['id']]);
        $currentHash = (string) ($stmt->fetchColumn() ?: '');
        if ($currentHash !== '' && !password_verify($current, $currentHash)) {
            respond(['ok' => false, 'message' => 'Cari şifrə yanlışdır.'], 422);
        }
        $pdo->prepare('UPDATE student_users SET password_hash = ? WHERE id = ?')
            ->execute([password_hash($next, PASSWORD_DEFAULT), (int) $student['id']]);
        respond(['ok' => true, 'message' => 'Şifrə uğurla dəyişdirildi.']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'online-review-state') {
        $student = current_student($pdo);
        $courseId = mb_substr(trim((string) ($_GET['course'] ?? '')), 0, 64);
        if (!$student) {
            respond(['ok' => true, 'loggedIn' => false, 'enrolled' => false, 'myReview' => null]);
        }
        $enr = $pdo->prepare('SELECT 1 FROM student_enrollments WHERE student_id = ? AND course_id = ? LIMIT 1');
        $enr->execute([(int) $student['id'], $courseId]);
        $enrolled = (bool) $enr->fetchColumn();
        $mine = null;
        $mr = $pdo->prepare('SELECT rating, body, status FROM online_reviews WHERE student_id = ? AND course_id = ? LIMIT 1');
        $mr->execute([(int) $student['id'], $courseId]);
        $row = $mr->fetch();
        if (is_array($row)) {
            $mine = ['rating' => (int) $row['rating'], 'body' => (string) $row['body'], 'status' => (string) $row['status']];
        }
        respond(['ok' => true, 'loggedIn' => true, 'enrolled' => $enrolled, 'myReview' => $mine, 'name' => (string) $student['name']]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'submit-online-review') {
        $student = current_student($pdo);
        if (!$student) {
            respond(['ok' => false, 'auth' => false, 'message' => 'Rəy yazmaq üçün daxil olun.'], 401);
        }
        $data = read_json();
        $courseId = mb_substr(trim((string) ($data['courseId'] ?? '')), 0, 64);
        $rating = (int) ($data['rating'] ?? 0);
        $body = mb_substr(trim((string) ($data['body'] ?? '')), 0, 2000);
        if ($courseId === '') {
            respond(['ok' => false, 'message' => 'Kurs tapılmadı.'], 422);
        }
        if ($rating < 1 || $rating > 5) {
            respond(['ok' => false, 'message' => 'Reytinq 1-5 arasında olmalıdır.'], 422);
        }
        if (mb_strlen($body) < 3) {
            respond(['ok' => false, 'message' => 'Rəy mətni çox qısadır.'], 422);
        }
        $enr = $pdo->prepare('SELECT 1 FROM student_enrollments WHERE student_id = ? AND course_id = ? LIMIT 1');
        $enr->execute([(int) $student['id'], $courseId]);
        if (!$enr->fetchColumn()) {
            respond(['ok' => false, 'message' => 'Rəy yazmaq üçün bu kursa qoşulmalısınız.'], 403);
        }
        $ex = $pdo->prepare('SELECT id FROM online_reviews WHERE student_id = ? AND course_id = ? LIMIT 1');
        $ex->execute([(int) $student['id'], $courseId]);
        $existingId = $ex->fetchColumn();
        if ($existingId) {
            $pdo->prepare('UPDATE online_reviews SET rating = ?, body = ?, student_name = ?, status = "pending", created_at = NOW() WHERE id = ?')
                ->execute([$rating, $body, (string) $student['name'], (int) $existingId]);
        } else {
            $pdo->prepare('INSERT INTO online_reviews (course_id, student_id, student_name, rating, body, status) VALUES (?, ?, ?, ?, ?, "pending")')
                ->execute([$courseId, (int) $student['id'], (string) $student['name'], $rating, $body]);
        }
        respond(['ok' => true, 'message' => 'Rəyiniz qəbul olundu. Admin təsdiqindən sonra səhifədə görünəcək.']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'logout') {
        unset($_SESSION['student_user_id'], $_SESSION['student_email'], $_SESSION['student_name'], $_SESSION['student_last_seen']);
        session_regenerate_id(true);
        respond(['ok' => true]);
    }

    respond(['ok' => false, 'message' => 'Action tapılmadı.'], 404);
} catch (Throwable $error) {
    error_log('[student-auth] ' . $error->getMessage());
    respond(['ok' => false, 'message' => 'Server xətası baş verdi.'], 500);
}
