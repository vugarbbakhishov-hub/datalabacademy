<?php
declare(strict_types=1);

/*
 * Mağaza / səbət / enrollment API.
 * - Pulsuz onlayn kursa birbaşa enroll (enroll-free).
 * - Səbət (çox kurs): cart-add / cart-remove / cart.
 * - Kart-kart ödəniş kartları (admin idarə edir): payment-cards.
 * - Checkout (kart-kart) → 'pending' order + order_items, admin təsdiqi gözləyir.
 * - Onlayn (bank) ödəniş hələ aktiv deyil → 'tezliklə'.
 */

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

function shop_respond(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function shop_read_json(): array
{
    $raw = file_get_contents('php://input') ?: '';
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function shop_require_same_origin(): void
{
    // CSRF müdafiəsi: Origin və Referer-in hər ikisi yoxdursa mutating sorğunu rədd et.
    $source = (string) ($_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '');
    if ($source === '') {
        shop_respond(['ok' => false, 'message' => 'Sorğunun mənbəyi yoxlanmadı.'], 403);
    }
    $sourceHost = strtolower((string) parse_url($source, PHP_URL_HOST));
    $requestHost = strtolower(explode(':', (string) ($_SERVER['HTTP_HOST'] ?? ''))[0]);
    if ($sourceHost === '' || !hash_equals($requestHost, $sourceHost)) {
        shop_respond(['ok' => false, 'message' => 'Sorğunun mənbəyi etibarlı deyil.'], 403);
    }
}

/** Sadə sürət limiti (sessiya əsaslı) — sui-istifadəni (spam enroll/order) məhdudlaşdırır. */
function shop_rate_limit(string $bucket, int $max, int $windowSec): void
{
    $now = time();
    $key = 'shop_rl_' . $bucket;
    $hits = array_values(array_filter((array) ($_SESSION[$key] ?? []), static fn($t) => (int) $t > $now - $windowSec));
    if (count($hits) >= $max) {
        shop_respond(['ok' => false, 'message' => 'Çox sayda sorğu. Bir az sonra yenidən cəhd edin.'], 429);
    }
    $hits[] = $now;
    $_SESSION[$key] = $hits;
}

function shop_ensure_schema(PDO $pdo): void
{
    // Admin idarə etdiyi kart-kart ödəniş kartları (dizaynlı)
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS payment_cards (
            id VARCHAR(64) NOT NULL PRIMARY KEY,
            bank_name VARCHAR(120) NOT NULL DEFAULT "",
            card_number VARCHAR(40) NOT NULL DEFAULT "",
            cardholder VARCHAR(120) NOT NULL DEFAULT "",
            note VARCHAR(190) NOT NULL DEFAULT "",
            theme VARCHAR(24) NOT NULL DEFAULT "violet",
            sort_order INT NOT NULL DEFAULT 0,
            status VARCHAR(20) NOT NULL DEFAULT "active",
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');

    // Server tərəfli səbət (tələbəyə bağlı, davamlı)
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS student_cart (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            student_id INT UNSIGNED NOT NULL,
            course_id VARCHAR(64) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_cart_student_course (student_id, course_id),
            INDEX idx_cart_student (student_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');

    // Sifariş sətirləri (səbətdə çox kurs ola bilər)
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS order_items (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            order_id VARCHAR(64) NOT NULL,
            course_id VARCHAR(64) NOT NULL DEFAULT "",
            course_title VARCHAR(255) NOT NULL DEFAULT "",
            amount DECIMAL(10,2) NOT NULL DEFAULT 0,
            INDEX idx_order_items_order (order_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');

    // orders cədvəlinə əlavə sütunlar (mövcud quraşdırmalar üçün təhlükəsiz ALTER)
    foreach ([
        'student_id INT UNSIGNED NULL',
        'email VARCHAR(190) NOT NULL DEFAULT ""',
        'payment_method VARCHAR(32) NOT NULL DEFAULT "card"',
        'card_id VARCHAR(64) NOT NULL DEFAULT ""',
        'note VARCHAR(255) NOT NULL DEFAULT ""',
        'origin VARCHAR(16) NOT NULL DEFAULT "manual"',
        'declared_at DATETIME NULL',
    ] as $definition) {
        try {
            $pdo->exec('ALTER TABLE orders ADD COLUMN ' . $definition);
        } catch (Throwable $error) {
            // sütun artıq mövcuddur
        }
    }
}

function shop_current_student_id(): int
{
    if (empty($_SESSION['student_user_id'])) {
        return 0;
    }
    if (!empty($_SESSION['student_last_seen']) && time() - (int) $_SESSION['student_last_seen'] > 86400) {
        return 0;
    }
    return (int) $_SESSION['student_user_id'];
}

function shop_require_student(PDO $pdo): array
{
    $id = shop_current_student_id();
    if ($id <= 0) {
        shop_respond(['ok' => false, 'auth' => false, 'message' => 'Davam etmək üçün hesabınıza daxil olun.'], 401);
    }
    $stmt = $pdo->prepare('SELECT id, name, email FROM student_users WHERE id = ? AND status = "active" LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!is_array($row)) {
        shop_respond(['ok' => false, 'auth' => false, 'message' => 'Hesab tapılmadı.'], 401);
    }
    return $row;
}

/** Onlayn kursu id ilə gətirir (yalnız aktiv). */
function shop_online_course(PDO $pdo, string $courseId): ?array
{
    $stmt = $pdo->prepare('SELECT id, title, price, status FROM online_courses WHERE id = ? LIMIT 1');
    $stmt->execute([$courseId]);
    $row = $stmt->fetch();
    return is_array($row) ? $row : null;
}

function shop_is_enrolled(PDO $pdo, int $studentId, string $courseId): bool
{
    $stmt = $pdo->prepare('SELECT 1 FROM student_enrollments WHERE student_id = ? AND course_id = ? AND status = "active" LIMIT 1');
    $stmt->execute([$studentId, $courseId]);
    return (bool) $stmt->fetchColumn();
}

function shop_pending_order(PDO $pdo, int $studentId, string $courseId): bool
{
    $stmt = $pdo->prepare('
        SELECT 1 FROM orders o
        JOIN order_items oi ON oi.order_id = o.id
        WHERE o.student_id = ? AND oi.course_id = ? AND o.status = "pending"
        LIMIT 1
    ');
    $stmt->execute([$studentId, $courseId]);
    return (bool) $stmt->fetchColumn();
}

function shop_cart_items(PDO $pdo, int $studentId): array
{
    $stmt = $pdo->prepare('
        SELECT oc.id AS courseId, oc.title, oc.price, oc.image, oc.category, oc.lessons
        FROM student_cart sc
        JOIN online_courses oc ON oc.id = sc.course_id
        WHERE sc.student_id = ? AND oc.status = "active"
        ORDER BY sc.created_at, sc.id
    ');
    $stmt->execute([$studentId]);
    return array_map(static function (array $r): array {
        return [
            'courseId' => (string) $r['courseId'],
            'title' => (string) $r['title'],
            'price' => (float) $r['price'],
            'image' => (string) $r['image'],
            'category' => (string) $r['category'],
            'lessons' => (int) $r['lessons'],
        ];
    }, $stmt->fetchAll());
}

try {
    $pdo = db();
    shop_ensure_schema($pdo);
    $action = (string) ($_GET['action'] ?? '');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        shop_require_same_origin();
    }

    // --- Kursa giriş statusu (detal səhifəsi düymələri üçün) ---
    if ($action === 'access') {
        $courseId = mb_substr(trim((string) ($_GET['course'] ?? '')), 0, 64);
        $course = shop_online_course($pdo, $courseId);
        $studentId = shop_current_student_id();
        $loggedIn = $studentId > 0;
        $enrolled = $loggedIn && shop_is_enrolled($pdo, $studentId, $courseId);
        $pending = $loggedIn && !$enrolled && shop_pending_order($pdo, $studentId, $courseId);
        $inCart = false;
        if ($loggedIn) {
            $c = $pdo->prepare('SELECT 1 FROM student_cart WHERE student_id = ? AND course_id = ? LIMIT 1');
            $c->execute([$studentId, $courseId]);
            $inCart = (bool) $c->fetchColumn();
        }
        shop_respond([
            'ok' => true,
            'loggedIn' => $loggedIn,
            'enrolled' => $enrolled,
            'pending' => $pending,
            'inCart' => $inCart,
            'price' => $course ? (float) $course['price'] : 0,
            'free' => $course ? ((float) $course['price'] <= 0) : false,
            'exists' => (bool) $course,
        ]);
    }

    // --- Səbəti oxu ---
    if ($action === 'cart') {
        $student = shop_require_student($pdo);
        $items = shop_cart_items($pdo, (int) $student['id']);
        $total = array_sum(array_column($items, 'price'));
        shop_respond(['ok' => true, 'items' => $items, 'total' => round($total, 2), 'count' => count($items)]);
    }

    // --- Pulsuz kursa birbaşa enroll ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'enroll-free') {
        $student = shop_require_student($pdo);
        shop_rate_limit('enroll', 12, 600);
        $data = shop_read_json();
        $courseId = mb_substr(trim((string) ($data['courseId'] ?? '')), 0, 64);
        $course = shop_online_course($pdo, $courseId);
        if (!$course || ($course['status'] ?? '') !== 'active') {
            shop_respond(['ok' => false, 'message' => 'Kurs tapılmadı.'], 404);
        }
        if ((float) $course['price'] > 0) {
            shop_respond(['ok' => false, 'message' => 'Bu kurs ödənişlidir.'], 422);
        }
        if (!shop_is_enrolled($pdo, (int) $student['id'], $courseId)) {
            $pdo->prepare('INSERT INTO student_enrollments (student_id, course_id, course_title, enrollment_type, status) VALUES (?, ?, ?, "online", "active")')
                ->execute([(int) $student['id'], $courseId, (string) $course['title']]);
        }
        shop_respond(['ok' => true, 'enrolled' => true, 'message' => 'Kursa qoşuldunuz. Uğurlar!']);
    }

    // --- Səbətə əlavə et ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'cart-add') {
        $student = shop_require_student($pdo);
        shop_rate_limit('cart', 40, 300);
        $data = shop_read_json();
        $courseId = mb_substr(trim((string) ($data['courseId'] ?? '')), 0, 64);
        $course = shop_online_course($pdo, $courseId);
        if (!$course || ($course['status'] ?? '') !== 'active') {
            shop_respond(['ok' => false, 'message' => 'Kurs tapılmadı.'], 404);
        }
        if ((float) $course['price'] <= 0) {
            shop_respond(['ok' => false, 'message' => 'Bu kurs pulsuzdur, birbaşa qoşula bilərsiniz.'], 422);
        }
        if (shop_is_enrolled($pdo, (int) $student['id'], $courseId)) {
            shop_respond(['ok' => false, 'message' => 'Siz artıq bu kursa qoşulmusunuz.'], 409);
        }
        $pdo->prepare('INSERT IGNORE INTO student_cart (student_id, course_id) VALUES (?, ?)')
            ->execute([(int) $student['id'], $courseId]);
        $items = shop_cart_items($pdo, (int) $student['id']);
        shop_respond(['ok' => true, 'message' => 'Səbətə əlavə olundu.', 'count' => count($items)]);
    }

    // --- Səbətdən sil ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'cart-remove') {
        $student = shop_require_student($pdo);
        $data = shop_read_json();
        $courseId = mb_substr(trim((string) ($data['courseId'] ?? '')), 0, 64);
        $pdo->prepare('DELETE FROM student_cart WHERE student_id = ? AND course_id = ?')
            ->execute([(int) $student['id'], $courseId]);
        $items = shop_cart_items($pdo, (int) $student['id']);
        $total = array_sum(array_column($items, 'price'));
        shop_respond(['ok' => true, 'items' => $items, 'total' => round($total, 2), 'count' => count($items)]);
    }

    // --- Aktiv ödəniş kartları (kart-kart ekranı üçün) ---
    if ($action === 'payment-cards') {
        $rows = $pdo->query('SELECT id, bank_name, card_number, cardholder, note, theme FROM payment_cards WHERE status = "active" ORDER BY sort_order, id')->fetchAll();
        $cards = array_map(static function (array $r): array {
            return [
                'id' => (string) $r['id'],
                'bank' => (string) $r['bank_name'],
                'number' => (string) $r['card_number'],
                'holder' => (string) $r['cardholder'],
                'note' => (string) $r['note'],
                'theme' => (string) $r['theme'],
            ];
        }, $rows);
        shop_respond(['ok' => true, 'cards' => $cards]);
    }

    // --- Checkout: "Ödənişi etdim" (kart-kart) → pending order ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'checkout') {
        $student = shop_require_student($pdo);
        shop_rate_limit('checkout', 6, 600);
        $data = shop_read_json();
        $method = (string) ($data['paymentMethod'] ?? 'card');

        if ($method === 'online') {
            shop_respond(['ok' => false, 'message' => 'Onlayn (bank kartı) ödəniş tezliklə aktiv olacaq. Hələlik kart-kart üsulundan istifadə edin.'], 422);
        }

        $items = shop_cart_items($pdo, (int) $student['id']);
        if (!$items) {
            shop_respond(['ok' => false, 'message' => 'Səbətiniz boşdur.'], 422);
        }

        $cardId = mb_substr(trim((string) ($data['cardId'] ?? '')), 0, 64);
        $cardStmt = $pdo->prepare('SELECT id, bank_name FROM payment_cards WHERE id = ? AND status = "active" LIMIT 1');
        $cardStmt->execute([$cardId]);
        $card = $cardStmt->fetch();
        if (!$card) {
            shop_respond(['ok' => false, 'message' => 'Ödəniş kartı seçilməyib.'], 422);
        }

        $note = mb_substr(trim((string) ($data['note'] ?? '')), 0, 255);
        $total = round((float) array_sum(array_column($items, 'price')), 2);
        $orderId = 'SHOP-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        $pdo->beginTransaction();
        try {
            $pdo->prepare('
                INSERT INTO orders (id, customer, course_id, phone, status, amount, order_date, student_id, email, payment_method, card_id, note, origin, declared_at)
                VALUES (:id, :customer, :course_id, "", "pending", :amount, :order_date, :student_id, :email, "card", :card_id, :note, "shop", NOW())
            ')->execute([
                ':id' => $orderId,
                ':customer' => (string) $student['name'],
                ':course_id' => (string) $items[0]['courseId'],
                ':amount' => $total,
                ':order_date' => date('Y-m-d'),
                ':student_id' => (int) $student['id'],
                ':email' => (string) $student['email'],
                ':card_id' => (string) $card['id'],
                ':note' => $note,
            ]);
            $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, course_id, course_title, amount) VALUES (?, ?, ?, ?)');
            foreach ($items as $it) {
                $itemStmt->execute([$orderId, (string) $it['courseId'], (string) $it['title'], (float) $it['price']]);
            }
            $pdo->prepare('DELETE FROM student_cart WHERE student_id = ?')->execute([(int) $student['id']]);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        shop_respond([
            'ok' => true,
            'orderId' => $orderId,
            'message' => 'Ödəniş bildirişiniz qəbul olundu. Admin ödənişi yoxlayıb təsdiqlədikdən sonra kurslara giriş açılacaq.',
        ]);
    }

    shop_respond(['ok' => false, 'message' => 'Action tapılmadı.'], 404);
} catch (Throwable $error) {
    error_log('[shop] ' . $error->getMessage());
    shop_respond(['ok' => false, 'message' => 'Server xətası baş verdi.'], 500);
}
