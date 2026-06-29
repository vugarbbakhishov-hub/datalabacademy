<?php
declare(strict_types=1);

require __DIR__ . '/config.php';
require_once __DIR__ . '/../includes/datalab-home-data.php';

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
        respond(['ok' => false, 'message' => 'Invalid JSON body.'], 400);
    }

    return $data;
}

function ensure_schema(PDO $pdo): void
{
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS courses (
            id VARCHAR(64) NOT NULL PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            category VARCHAR(120) NOT NULL DEFAULT "",
            price DECIMAL(10,2) NOT NULL DEFAULT 0,
            lessons INT NOT NULL DEFAULT 0,
            students INT NOT NULL DEFAULT 0,
            rating DECIMAL(2,1) NOT NULL DEFAULT 5.0,
            review_count INT NOT NULL DEFAULT 0,
            status VARCHAR(32) NOT NULL DEFAULT "draft",
            image VARCHAR(500) NOT NULL DEFAULT "",
            description TEXT NULL,
            sort_order INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS online_courses (
            id VARCHAR(64) NOT NULL PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            category VARCHAR(120) NOT NULL DEFAULT "",
            price DECIMAL(10,2) NOT NULL DEFAULT 0,
            lessons INT NOT NULL DEFAULT 0,
            students INT NOT NULL DEFAULT 0,
            rating DECIMAL(2,1) NOT NULL DEFAULT 5.0,
            review_count INT NOT NULL DEFAULT 0,
            status VARCHAR(32) NOT NULL DEFAULT "active",
            image VARCHAR(500) NOT NULL DEFAULT "",
            description TEXT NULL,
            sort_order INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');

    // İlk dəfə yaradıldıqda nümunə onlayn kurslarla doldur.
    $onlineCount = (int) $pdo->query('SELECT COUNT(*) FROM online_courses')->fetchColumn();
    if ($onlineCount === 0) {
        $pdo->exec("INSERT INTO online_courses
            (id, title, category, price, lessons, students, rating, review_count, status, image, description, sort_order) VALUES
            ('on-1','Python ilə Data Analitika (Onlayn)','Online',120,24,0,5,0,'active','assets/images/course/datalab-data-analitika.svg','Öz tempinlə video dərslərlə Python və data analitika.',1),
            ('on-2','SQL Onlayn Kurs','Online',90,20,0,5,0,'active','assets/images/course/datalab-sql-developer.svg','Onlayn SQL təlimi: sorğular, JOIN-lar və praktika.',2),
            ('on-3','Excel Onlayn','Online',45,16,0,5,0,'active','assets/images/course/datalab-excel.svg','Onlayn Excel kursu: formullar, Pivot və dashboard.',3)");
    }

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS orders (
            id VARCHAR(64) NOT NULL PRIMARY KEY,
            customer VARCHAR(255) NOT NULL,
            course_id VARCHAR(64) NOT NULL DEFAULT "",
            phone VARCHAR(64) NOT NULL DEFAULT "",
            status VARCHAR(32) NOT NULL DEFAULT "new",
            amount DECIMAL(10,2) NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS leads (
            id VARCHAR(64) NOT NULL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            source VARCHAR(120) NOT NULL DEFAULT "",
            interest VARCHAR(255) NOT NULL DEFAULT "",
            status VARCHAR(32) NOT NULL DEFAULT "new",
            lead_date VARCHAR(20) NOT NULL DEFAULT "",
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS admin_settings (
            setting_name VARCHAR(64) NOT NULL PRIMARY KEY,
            setting_value LONGTEXT NULL,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS admin_users (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(190) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            role VARCHAR(32) NOT NULL DEFAULT "admin",
            must_change_password TINYINT(1) NOT NULL DEFAULT 0,
            last_login_at DATETIME NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');
    // Köhnə quraşdırmalara must_change_password sütunu əlavə et
    ensure_column($pdo, 'admin_users', 'must_change_password TINYINT(1) NOT NULL DEFAULT 0');

    // Migration: köhnə default şifrəsi ("Azerbaijan5252$") olan adminlərə
    // must_change_password=1 qoy (yalnız bir dəfə işlənir — seed key ilə qorunur)
    $migKey = 'migration_must_change_pw_v1';
    $migStmt = $pdo->prepare('SELECT setting_value FROM admin_settings WHERE setting_name = ? LIMIT 1');
    $migStmt->execute([$migKey]);
    if ($migStmt->fetchColumn() === false) {
        // Bütün adminlərə must_change_password=1 — ilk girişdə şifrə dəyişdirilir
        $pdo->exec("UPDATE admin_users SET must_change_password = 1 WHERE must_change_password = 0");
        $pdo->prepare("INSERT INTO admin_settings (setting_name, setting_value) VALUES (?, '1')")->execute([$migKey]);
    }


    $pdo->exec('
        CREATE TABLE IF NOT EXISTS admin_login_attempts (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            ip VARCHAR(45) NOT NULL,
            email VARCHAR(190) NOT NULL DEFAULT "",
            success TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_ip_time (ip, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS admin_audit_log (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(190) NOT NULL DEFAULT "",
            action VARCHAR(64) NOT NULL,
            detail VARCHAR(500) NOT NULL DEFAULT "",
            ip VARCHAR(45) NOT NULL DEFAULT "",
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS site_submissions (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            ip VARCHAR(45) NOT NULL,
            kind VARCHAR(20) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_ip_time (ip, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');

    // köhnə quraşdırmalar üçün yeni sütun
    ensure_column($pdo, 'orders', 'order_date VARCHAR(20) NOT NULL DEFAULT ""');
    ensure_column($pdo, 'courses', 'rating DECIMAL(2,1) NOT NULL DEFAULT 5.0');
    ensure_column($pdo, 'courses', 'review_count INT NOT NULL DEFAULT 0');
    // online_courses: slug və level sütunları (public səhifə bunları oxuyur)
    ensure_column($pdo, 'online_courses', 'slug VARCHAR(160) NOT NULL DEFAULT ""');
    ensure_column($pdo, 'online_courses', 'level VARCHAR(120) NOT NULL DEFAULT ""');

    // Mağaza / ödəniş sistemi (shop.php ilə paylaşılan sxem)
    ensure_column($pdo, 'orders', 'student_id INT UNSIGNED NULL');
    ensure_column($pdo, 'orders', 'email VARCHAR(190) NOT NULL DEFAULT ""');
    ensure_column($pdo, 'orders', 'payment_method VARCHAR(32) NOT NULL DEFAULT "card"');
    ensure_column($pdo, 'orders', 'card_id VARCHAR(64) NOT NULL DEFAULT ""');
    ensure_column($pdo, 'orders', 'note VARCHAR(255) NOT NULL DEFAULT ""');
    ensure_column($pdo, 'orders', 'origin VARCHAR(16) NOT NULL DEFAULT "manual"');
    ensure_column($pdo, 'orders', 'declared_at DATETIME NULL');
    $pdo->exec('CREATE TABLE IF NOT EXISTS payment_cards (
        id VARCHAR(64) NOT NULL PRIMARY KEY,
        bank_name VARCHAR(120) NOT NULL DEFAULT "",
        card_number VARCHAR(40) NOT NULL DEFAULT "",
        cardholder VARCHAR(120) NOT NULL DEFAULT "",
        note VARCHAR(190) NOT NULL DEFAULT "",
        theme VARCHAR(24) NOT NULL DEFAULT "violet",
        sort_order INT NOT NULL DEFAULT 0,
        status VARCHAR(20) NOT NULL DEFAULT "active",
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
    $pdo->exec('CREATE TABLE IF NOT EXISTS order_items (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        order_id VARCHAR(64) NOT NULL,
        course_id VARCHAR(64) NOT NULL DEFAULT "",
        course_title VARCHAR(255) NOT NULL DEFAULT "",
        amount DECIMAL(10,2) NOT NULL DEFAULT 0,
        INDEX idx_order_items_order (order_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    // AI chatbot bilik bazası (admin idarə edir)
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS ai_knowledge (
            id VARCHAR(64) NOT NULL PRIMARY KEY,
            title VARCHAR(255) NOT NULL DEFAULT "",
            content TEXT NULL,
            sort_order INT NOT NULL DEFAULT 0,
            status VARCHAR(20) NOT NULL DEFAULT "active",
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');

    // Saytın əlaqə məlumatı (tək sətir, id=1)
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS site_contact (
            id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
            phone VARCHAR(64) NOT NULL DEFAULT "",
            email VARCHAR(190) NOT NULL DEFAULT "",
            address VARCHAR(255) NOT NULL DEFAULT "",
            facebook VARCHAR(255) NOT NULL DEFAULT "",
            instagram VARCHAR(255) NOT NULL DEFAULT "",
            linkedin VARCHAR(255) NOT NULL DEFAULT "",
            twitter VARCHAR(255) NOT NULL DEFAULT "",
            footer_about TEXT NULL,
            newsletter_title VARCHAR(190) NOT NULL DEFAULT "",
            newsletter_desc TEXT NULL,
            page_subtitle VARCHAR(500) NOT NULL DEFAULT "",
            map_embed TEXT NULL,
            working_hours VARCHAR(255) NOT NULL DEFAULT "",
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');
    // İlk dəfə əlaqə sətrini seed-lə (admin_settings-də köhnə contact varsa, ondan götür)
    if ((int) $pdo->query('SELECT COUNT(*) FROM site_contact')->fetchColumn() === 0) {
        $oldContact = setting_value($pdo, 'contact', []);
        $seed = array_merge([
            'phone' => '+994 50 654 97 37', 'email' => 'info@datalabacademy.az', 'address' => 'Bakı, Azərbaycan',
            'facebook' => '#', 'instagram' => '#', 'linkedin' => '#', 'twitter' => '#',
            'footerAbout' => 'DatalabAcademy praktiki Data Analitika, SQL, Excel və AI təlimləri ilə karyera bacarıqlarınızı inkişaf etdirir.',
            'newsletterTitle' => 'Xəbər bülleteni',
            'newsletterDesc' => 'Yeni qrup açılışları, faydalı materiallar və kampaniyalar üçün e-poçtunuzu qeyd edin.',
            'pageSubtitle' => 'Suallarınız üçün bizimlə əlaqə saxlayın — komandamız tezliklə cavab verəcək.',
            'mapEmbed' => '', 'workingHours' => 'B.e – Cümə: 10:00 – 19:00',
        ], is_array($oldContact) ? $oldContact : []);
        $pdo->prepare('INSERT INTO site_contact (id, phone, email, address, facebook, instagram, linkedin, twitter, footer_about, newsletter_title, newsletter_desc, page_subtitle, map_embed, working_hours)
            VALUES (1, :phone, :email, :address, :facebook, :instagram, :linkedin, :twitter, :footer_about, :newsletter_title, :newsletter_desc, :page_subtitle, :map_embed, :working_hours)')
            ->execute([
                ':phone' => $seed['phone'], ':email' => $seed['email'], ':address' => $seed['address'],
                ':facebook' => $seed['facebook'], ':instagram' => $seed['instagram'], ':linkedin' => $seed['linkedin'], ':twitter' => $seed['twitter'],
                ':footer_about' => $seed['footerAbout'], ':newsletter_title' => $seed['newsletterTitle'], ':newsletter_desc' => $seed['newsletterDesc'],
                ':page_subtitle' => $seed['pageSubtitle'], ':map_embed' => $seed['mapEmbed'], ':working_hours' => $seed['workingHours'],
            ]);
    }

    // Online təlim rəyləri (tələbələr yazır, admin təsdiqləyir)
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


    $seedKey = 'seed_interactive_ai_v1';
    $seedStmt = $pdo->prepare('SELECT setting_value FROM admin_settings WHERE setting_name = ? LIMIT 1');
    $seedStmt->execute([$seedKey]);
    if ($seedStmt->fetchColumn() === false) {
        $pdo->prepare('
            INSERT IGNORE INTO courses (id, title, category, price, lessons, students, status, image, description, sort_order)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ')->execute([
            '5',
            'İnteraktiv AI Təcrübəsi',
            'AI',
            240,
            16,
            0,
            'active',
            'assets/images/course/datalab-ai.svg',
            'Generativ AI, agentlər və ağıllı iş axınlarını əyani praktika ilə öyrənəcəyiniz yeni nəsil proqram.',
            4,
        ]);

        $detailStmt = $pdo->prepare('SELECT setting_value FROM admin_settings WHERE setting_name = ? LIMIT 1');
        $detailStmt->execute(['courseDetails']);
        $detailJson = $detailStmt->fetchColumn();
        $courseDetails = is_string($detailJson) ? json_decode($detailJson, true) : [];
        if (!is_array($courseDetails)) {
            $courseDetails = [];
        }
        $courseDetails['5'] = [
            'format' => 'Əyani / Offline',
            'duration' => '8 həftə',
            'schedule' => 'Həftədə 2 dəfə, 19:00–21:00',
            'location' => 'Bakı, DatalabAcademy təlim mərkəzi',
            'startDate' => '2026-07-06',
            'level' => 'Başlanğıc və orta',
            'language' => 'Azərbaycan',
            'seats' => '15',
            'instructor' => 'DatalabAcademy AI mentorları',
            'splineScene' => 'https://prod.spline.design/kZDDjO5HuC9GJUM2/scene.splinecode',
            'overview' => 'Generativ AI və ağıllı agentləri yalnız izləmək deyil, real iş prosesində qurub tətbiq etmək üçün hazırlanmış əyani proqramdır. Hər mövzu mentorla canlı praktika və komanda tapşırığı ilə möhkəmləndirilir.',
            'outcomes' => "Effektiv və təkrar istifadə olunan prompt sistemləri qurmaq\nAI agentləri ilə çoxaddımlı iş axınları yaratmaq\nMətn, data və hesabat işini avtomatlaşdırmaq\nAI nəticələrini təhlükəsizlik və keyfiyyət baxımından yoxlamaq",
            'syllabus' => "Generativ AI və böyük dil modelləri\nPrompt sistemləri və kontekst dizaynı\nAI agentləri və alət istifadəsi\nData analizi və hesabat avtomatlaşdırması\nTəhlükəsizlik, məxfilik və etika\nYekun interaktiv AI layihəsi",
        ];
        $upsertSetting = $pdo->prepare('
            INSERT INTO admin_settings (setting_name, setting_value) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = CURRENT_TIMESTAMP
        ');
        $upsertSetting->execute(['courseDetails', json_encode($courseDetails, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
        $upsertSetting->execute([$seedKey, '1']);
    }

    $userCount = (int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
    if ($userCount === 0) {
        // İlk admin yaradılır — must_change_password=1 ilə məcburi dəyişim tələb et.
        // Şifrə: rastgele 12 simvol, config.local.php-dən oxunur (DL_ADMIN_PASS) və ya default.
        $configured = defined('DL_ADMIN_PASS') || getenv('DL_ADMIN_PASS') !== false;
        $defaultPass = defined('DL_ADMIN_PASS') ? (string) constant('DL_ADMIN_PASS')
            : (getenv('DL_ADMIN_PASS') ?: 'ChangeMe@' . bin2hex(random_bytes(6)));
        $stmt = $pdo->prepare('INSERT INTO admin_users (email, password_hash, role, must_change_password) VALUES (?, ?, ?, 1)');
        $stmt->execute([
            defined('DL_ADMIN_EMAIL') ? (string) constant('DL_ADMIN_EMAIL') : (getenv('DL_ADMIN_EMAIL') ?: 'admin@datalabacademy.az'),
            password_hash($defaultPass, PASSWORD_DEFAULT),
            'admin',
        ]);
        // TƏHLÜKƏSİZLİK: şifrəni server loguna YAZMA. Yalnız təyin olunmayıbsa,
        // web-dən bağlı (.htaccess) backups qovluğundakı birdəfəlik fayla yaz — admin oxuyub silsin.
        if (!$configured) {
            $bdir = __DIR__ . '/backups';
            if (!is_dir($bdir)) {
                @mkdir($bdir, 0700, true);
            }
            @file_put_contents(
                $bdir . '/INITIAL_ADMIN_PASSWORD.txt',
                "İlk admin şifrəsi (dərhal dəyişin və bu faylı silin):\n" . $defaultPass . "\n",
                LOCK_EX
            );
            error_log('[datalab-admin] İlk admin yaradıldı. Şifrə api/backups/INITIAL_ADMIN_PASSWORD.txt faylındadır — oxuyub silin.');
        }
    }
}

function require_auth(): void
{
    if (empty($_SESSION['admin_user_id'])) {
        respond(['ok' => false, 'auth' => false, 'message' => 'Giriş tələb olunur.'], 401);
    }
}

function client_ip(): string
{
    return (string) ($_SERVER['REMOTE_ADDR'] ?? '');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function require_csrf(): void
{
    $token = (string) ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if ($token === '' || !hash_equals((string) ($_SESSION['csrf'] ?? ''), $token)) {
        respond(['ok' => false, 'message' => 'CSRF token yanlışdır. Səhifəni yenilə və yenidən daxil ol.'], 403);
    }
}

function log_audit(PDO $pdo, string $action, string $detail = ''): void
{
    try {
        $stmt = $pdo->prepare('INSERT INTO admin_audit_log (email, action, detail, ip) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            (string) ($_SESSION['admin_email'] ?? ''),
            $action,
            mb_substr($detail, 0, 500),
            client_ip(),
        ]);
    } catch (Throwable $error) {
        // audit heç vaxt əsas əməliyyatı poza bilməz
    }
}

function ensure_column(PDO $pdo, string $table, string $ddl): void
{
    try {
        $pdo->exec('ALTER TABLE ' . $table . ' ADD COLUMN ' . $ddl);
    } catch (Throwable $error) {
        // sütun artıq mövcuddur
    }
}

function sanitize_state(array $state): array
{
    $courseStatuses = ['active', 'draft', 'archived'];
    $flowStatuses = ['new', 'pending', 'paid', 'cancelled'];
    $blogStatuses = ['active', 'draft', 'archived'];

    foreach (($state['courses'] ?? []) as $i => $course) {
        $state['courses'][$i]['title'] = mb_substr(trim((string) ($course['title'] ?? '')), 0, 255);
        $state['courses'][$i]['category'] = mb_substr(trim((string) ($course['category'] ?? '')), 0, 120);
        $state['courses'][$i]['image'] = mb_substr(trim((string) ($course['image'] ?? '')), 0, 500);
        $state['courses'][$i]['description'] = mb_substr(trim((string) ($course['description'] ?? '')), 0, 2000);
        $state['courses'][$i]['price'] = max(0, (float) ($course['price'] ?? 0));
        $state['courses'][$i]['lessons'] = max(0, (int) ($course['lessons'] ?? 0));
        $state['courses'][$i]['students'] = max(0, (int) ($course['students'] ?? 0));
        $state['courses'][$i]['rating'] = max(0, min(5, (float) ($course['rating'] ?? 5)));
        $state['courses'][$i]['reviewCount'] = max(0, (int) ($course['reviewCount'] ?? 0));
        if (!in_array($course['status'] ?? '', $courseStatuses, true)) {
            $state['courses'][$i]['status'] = 'draft';
        }
    }

    if (isset($state['onlineCourses']) && is_array($state['onlineCourses'])) {
        foreach ($state['onlineCourses'] as $i => $oc) {
            if (!is_array($oc)) {
                unset($state['onlineCourses'][$i]);
                continue;
            }
            $state['onlineCourses'][$i]['title'] = mb_substr(trim((string) ($oc['title'] ?? '')), 0, 255);
            $state['onlineCourses'][$i]['slug'] = mb_substr(trim((string) ($oc['slug'] ?? '')), 0, 160);
            $state['onlineCourses'][$i]['category'] = mb_substr(trim((string) ($oc['category'] ?? '')), 0, 120);
            $state['onlineCourses'][$i]['level'] = mb_substr(trim((string) ($oc['level'] ?? '')), 0, 120);
            $state['onlineCourses'][$i]['image'] = mb_substr(trim((string) ($oc['image'] ?? '')), 0, 500);
            $state['onlineCourses'][$i]['description'] = mb_substr(trim((string) ($oc['description'] ?? '')), 0, 2000);
            $state['onlineCourses'][$i]['price'] = max(0, (float) ($oc['price'] ?? 0));
            $state['onlineCourses'][$i]['lessons'] = max(0, (int) ($oc['lessons'] ?? 0));
            $state['onlineCourses'][$i]['students'] = max(0, (int) ($oc['students'] ?? 0));
            $state['onlineCourses'][$i]['rating'] = max(0, min(5, (float) ($oc['rating'] ?? 5)));
            $state['onlineCourses'][$i]['reviewCount'] = max(0, (int) ($oc['reviewCount'] ?? 0));
            if (!in_array($oc['status'] ?? '', $courseStatuses, true)) {
                $state['onlineCourses'][$i]['status'] = 'active';
            }
        }
        $state['onlineCourses'] = array_values($state['onlineCourses']);
    }

    if (!isset($state['onlineCurriculum']) || !is_array($state['onlineCurriculum'])) {
        $state['onlineCurriculum'] = [];
    }
    if (!isset($state['onlineCourseDetails']) || !is_array($state['onlineCourseDetails'])) {
        $state['onlineCourseDetails'] = [];
    }
    foreach (['homeTestimonials', 'homePortfolio', 'homeCertificates', 'aiKnowledge'] as $homeKey) {
        if (isset($state[$homeKey]) && is_array($state[$homeKey])) {
            $state[$homeKey] = array_values(array_filter($state[$homeKey], 'is_array'));
        }
    }
    foreach (['homeStats', 'contact'] as $objKey) {
        if (isset($state[$objKey]) && !is_array($state[$objKey])) {
            unset($state[$objKey]);
        }
    }

    if (!isset($state['courseDetails']) || !is_array($state['courseDetails'])) {
        $state['courseDetails'] = [];
    }
    foreach ($state['courseDetails'] as $courseId => $detail) {
        if (!is_array($detail)) {
            unset($state['courseDetails'][$courseId]);
            continue;
        }
        $clean = [];
        foreach (['format', 'duration', 'schedule', 'location', 'startDate', 'level', 'language', 'seats', 'instructor'] as $field) {
            $clean[$field] = mb_substr(trim((string) ($detail[$field] ?? '')), 0, 255);
        }
        $clean['splineScene'] = mb_substr(trim((string) ($detail['splineScene'] ?? '')), 0, 500);
        foreach (['overview', 'outcomes', 'syllabus'] as $field) {
            $clean[$field] = mb_substr(trim((string) ($detail[$field] ?? '')), 0, 8000);
        }
        $state['courseDetails'][mb_substr((string) $courseId, 0, 64)] = $clean;
    }

    foreach (($state['orders'] ?? []) as $i => $order) {
        $state['orders'][$i]['customer'] = mb_substr(trim((string) ($order['customer'] ?? '')), 0, 255);
        $state['orders'][$i]['phone'] = mb_substr(trim((string) ($order['phone'] ?? '')), 0, 64);
        $state['orders'][$i]['amount'] = max(0, (float) ($order['amount'] ?? 0));
        if (!in_array($order['status'] ?? '', $flowStatuses, true)) {
            $state['orders'][$i]['status'] = 'new';
        }
    }

    foreach (($state['leads'] ?? []) as $i => $lead) {
        $state['leads'][$i]['name'] = mb_substr(trim((string) ($lead['name'] ?? '')), 0, 255);
        $state['leads'][$i]['source'] = mb_substr(trim((string) ($lead['source'] ?? '')), 0, 120);
        $state['leads'][$i]['interest'] = mb_substr(trim((string) ($lead['interest'] ?? '')), 0, 255);
        if (!in_array($lead['status'] ?? '', $flowStatuses, true)) {
            $state['leads'][$i]['status'] = 'new';
        }
    }

    if (isset($state['blogs']) && is_array($state['blogs'])) {
        foreach ($state['blogs'] as $i => $blog) {
            if (!is_array($blog)) {
                unset($state['blogs'][$i]);
                continue;
            }
            // id + slug
            $state['blogs'][$i]['id']   = mb_substr(trim((string) ($blog['id'] ?? 'blog-' . ($i + 1))), 0, 64);
            $state['blogs'][$i]['slug'] = mb_substr(trim((string) ($blog['slug'] ?? ($blog['id'] ?? 'blog-' . ($i + 1)))), 0, 160);
            // Azərbaycan sahələri (əsas)
            $state['blogs'][$i]['titleAz']   = mb_substr(trim((string) ($blog['titleAz']   ?? ($blog['title']   ?? ''))), 0, 255);
            $state['blogs'][$i]['titleEn']   = mb_substr(trim((string) ($blog['titleEn']   ?? '')), 0, 255);
            $state['blogs'][$i]['excerptAz'] = mb_substr(trim((string) ($blog['excerptAz'] ?? ($blog['excerpt'] ?? ($blog['description'] ?? '')))), 0, 1000);
            $state['blogs'][$i]['excerptEn'] = mb_substr(trim((string) ($blog['excerptEn'] ?? '')), 0, 1000);
            // Məzmun
            $state['blogs'][$i]['contentAz'] = mb_substr((string) ($blog['contentAz'] ?? ($blog['body'] ?? '')), 0, 80000);
            $state['blogs'][$i]['contentEn'] = mb_substr((string) ($blog['contentEn'] ?? ''), 0, 80000);
            // Metadata
            $state['blogs'][$i]['category']     = mb_substr(trim((string) ($blog['category']    ?? '')), 0, 120);
            $state['blogs'][$i]['image']        = mb_substr(trim((string) ($blog['image']        ?? ($blog['img'] ?? ($blog['cover'] ?? '')))), 0, 500);
            $state['blogs'][$i]['author']       = mb_substr(trim((string) ($blog['author']       ?? 'DatalabAcademy')), 0, 190);
            $state['blogs'][$i]['readTimeAz']   = mb_substr(trim((string) ($blog['readTimeAz']   ?? ($blog['readTime'] ?? ($blog['read_time'] ?? '')))), 0, 40);
            $state['blogs'][$i]['readTimeEn']   = mb_substr(trim((string) ($blog['readTimeEn']   ?? '')), 0, 40);
            $state['blogs'][$i]['publishedDate'] = mb_substr(trim((string) ($blog['publishedDate'] ?? ($blog['date'] ?? ''))), 0, 20);
            // SEO
            $state['blogs'][$i]['seoTitle']       = mb_substr(trim((string) ($blog['seoTitle']       ?? '')), 0, 255);
            $state['blogs'][$i]['seoDescription'] = mb_substr(trim((string) ($blog['seoDescription'] ?? '')), 0, 500);
            $state['blogs'][$i]['seoKeywords']    = mb_substr(trim((string) ($blog['seoKeywords']    ?? '')), 0, 500);
            $state['blogs'][$i]['sortOrder']      = max(0, (int) ($blog['sortOrder'] ?? $i));
            // Status
            if (!in_array($blog['status'] ?? 'active', $blogStatuses, true)) {
                $state['blogs'][$i]['status'] = 'active';
            }
        }
        $state['blogs'] = array_values($state['blogs']);
    }


    return $state;
}

function write_backup(array $state): void
{
    try {
        $dir = __DIR__ . '/backups';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            file_put_contents($dir . '/.htaccess', "Require all denied\n");
            file_put_contents($dir . '/index.php', '');
        }
        file_put_contents(
            $dir . '/backup-' . date('Y-m-d') . '.json',
            json_encode($state, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        );
        $files = glob($dir . '/backup-*.json') ?: [];
        sort($files);
        while (count($files) > 14) {
            @unlink(array_shift($files));
        }
    } catch (Throwable $error) {
        // backup xətası əsas əməliyyatı pozmur
    }
}

function bump_rev(PDO $pdo): void
{
    $pdo->exec("
        INSERT INTO admin_settings (setting_name, setting_value) VALUES ('rev', '1')
        ON DUPLICATE KEY UPDATE setting_value = CAST(setting_value AS UNSIGNED) + 1
    ");
}

function send_admin_mail(PDO $pdo, string $subject, string $body): void
{
    try {
        $to = (string) $pdo->query('SELECT email FROM admin_users ORDER BY id LIMIT 1')->fetchColumn();
        if ($to === '') {
            return;
        }
        $headers = "MIME-Version: 1.0\r\nContent-type: text/plain; charset=utf-8\r\nFrom: DatalabAcademy <no-reply@datalabacademy.az>\r\n";
        @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers);
    } catch (Throwable $error) {
        // email xətası əsas əməliyyatı pozmur
    }
}

function check_submission_rate(PDO $pdo, string $ip): void
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM site_submissions WHERE ip = ? AND created_at > (NOW() - INTERVAL 10 MINUTE)');
    $stmt->execute([$ip]);
    if ((int) $stmt->fetchColumn() >= 5) {
        respond(['ok' => false, 'message' => 'Çox sayda müraciət. Bir az sonra yenidən yoxla.'], 429);
    }
}

function record_submission(PDO $pdo, string $ip, string $kind): void
{
    $pdo->prepare('INSERT INTO site_submissions (ip, kind) VALUES (?, ?)')->execute([$ip, $kind]);
}

function notify_new_records(PDO $pdo, array $state, array $newOrderIds, array $newLeadIds): void
{
    if (!$newOrderIds && !$newLeadIds) {
        return;
    }
    try {
        $to = (string) $pdo->query('SELECT email FROM admin_users ORDER BY id LIMIT 1')->fetchColumn();
        if ($to === '') {
            return;
        }
        $lines = [];
        foreach (($state['orders'] ?? []) as $order) {
            if (in_array($order['id'] ?? '', $newOrderIds, true)) {
                $lines[] = 'Yeni sifariş: ' . ($order['id'] ?? '') . ' — ' . ($order['customer'] ?? '') . ' — $' . ($order['amount'] ?? 0);
            }
        }
        foreach (($state['leads'] ?? []) as $lead) {
            if (in_array($lead['id'] ?? '', $newLeadIds, true)) {
                $lines[] = 'Yeni lead: ' . ($lead['name'] ?? '') . ' — ' . ($lead['interest'] ?? '') . ' (' . ($lead['source'] ?? '') . ')';
            }
        }
        if (!$lines) {
            return;
        }
        $headers = "MIME-Version: 1.0\r\nContent-type: text/plain; charset=utf-8\r\nFrom: DatalabAcademy <no-reply@datalabacademy.az>\r\n";
        @mail($to, 'DatalabAcademy: yeni sifariş/lead', implode("\n", $lines), $headers);
    } catch (Throwable $error) {
        // email xətası əsas əməliyyatı pozmur
    }
}

function setting_value(PDO $pdo, string $name, array $fallback): array
{
    $stmt = $pdo->prepare('SELECT setting_value FROM admin_settings WHERE setting_name = ? LIMIT 1');
    $stmt->execute([$name]);
    $value = $stmt->fetchColumn();

    if (!is_string($value) || $value === '') {
        return $fallback;
    }

    $decoded = json_decode($value, true);
    return is_array($decoded) ? $decoded : $fallback;
}

function current_rev(PDO $pdo): int
{
    $stmt = $pdo->prepare('SELECT setting_value FROM admin_settings WHERE setting_name = ? LIMIT 1');
    $stmt->execute(['rev']);
    return (int) $stmt->fetchColumn();
}

function get_state(PDO $pdo): array
{
    $courses = $pdo->query('SELECT id, title, category, price, lessons, students, rating, review_count AS reviewCount, status, image, description FROM courses ORDER BY sort_order, id')->fetchAll();
    $orders  = $pdo->query('SELECT id, customer, course_id AS courseId, phone, status, amount, order_date AS date FROM orders WHERE origin = "manual" ORDER BY created_at DESC, id DESC')->fetchAll();
    $leads   = $pdo->query('SELECT id, name, source, interest, status, lead_date AS date FROM leads ORDER BY created_at DESC, id DESC')->fetchAll();

    foreach ($courses as &$course) {
        $course['price']       = (float) $course['price'];
        $course['lessons']     = (int)   $course['lessons'];
        $course['students']    = (int)   $course['students'];
        $course['rating']      = (float) $course['rating'];
        $course['reviewCount'] = (int)   $course['reviewCount'];
    }
    unset($course);

    // Onlayn təlimlər: ayrıca online_courses cədvəli
    try {
        $onlineCourses = $pdo->query('SELECT id, title, slug, category, price, lessons, students, rating, review_count AS reviewCount, status, image, description, level FROM online_courses ORDER BY sort_order, id')->fetchAll();
    } catch (Throwable $e) {
        $onlineCourses = [];
    }
    foreach ($onlineCourses as &$oc) {
        $oc['price']       = (float) $oc['price'];
        $oc['lessons']     = (int)   $oc['lessons'];
        $oc['students']    = (int)   $oc['students'];
        $oc['rating']      = (float) $oc['rating'];
        $oc['reviewCount'] = (int)   $oc['reviewCount'];
    }
    unset($oc);

    foreach ($orders as &$order) {
        $order['amount'] = (float) $order['amount'];
    }
    unset($order);

    // Blog-lar: mənbə blog_posts cədvəlidir (köhnə admin_settings JSON deyil)
    try {
        $blogRows = $pdo->query("
            SELECT id, slug, title_az AS titleAz, title_en AS titleEn,
                   excerpt_az AS excerptAz, excerpt_en AS excerptEn,
                   content_az AS contentAz, content_en AS contentEn,
                   category, image, author, read_time_az AS readTimeAz,
                   read_time_en AS readTimeEn, published_date AS publishedDate,
                   status, seo_title AS seoTitle, seo_description AS seoDescription,
                   seo_keywords AS seoKeywords, sort_order AS sortOrder
            FROM blog_posts
            ORDER BY sort_order, published_date DESC, id
        ")->fetchAll();
        $blogs = array_values($blogRows ?: []);
    } catch (Throwable $e) {
        $blogs = setting_value($pdo, 'blogs', []);  // fallback
    }

    try {
        $onlineReviews = $pdo->query('SELECT id, course_id AS courseId, student_name AS name, rating, body, status, created_at AS date FROM online_reviews ORDER BY created_at DESC, id DESC')->fetchAll();
    } catch (Throwable $e) {
        $onlineReviews = [];
    }
    foreach ($onlineReviews as &$rev) {
        $rev['id'] = (int) $rev['id'];
        $rev['rating'] = (int) $rev['rating'];
    }
    unset($rev);

    // Əlaqə (site_contact tək sətir)
    try {
        $cRow = $pdo->query('SELECT * FROM site_contact WHERE id = 1 LIMIT 1')->fetch();
    } catch (Throwable $e) {
        $cRow = null;
    }
    $contact = $cRow ? [
        'phone' => (string) $cRow['phone'], 'email' => (string) $cRow['email'], 'address' => (string) $cRow['address'],
        'facebook' => (string) $cRow['facebook'], 'instagram' => (string) $cRow['instagram'], 'linkedin' => (string) $cRow['linkedin'], 'twitter' => (string) $cRow['twitter'],
        'footerAbout' => (string) $cRow['footer_about'], 'newsletterTitle' => (string) $cRow['newsletter_title'], 'newsletterDesc' => (string) $cRow['newsletter_desc'],
        'pageSubtitle' => (string) $cRow['page_subtitle'], 'mapEmbed' => (string) $cRow['map_embed'], 'workingHours' => (string) $cRow['working_hours'],
    ] : dl_home_seed_contact();

    // AI bilik bazası (ai_knowledge)
    try {
        $kbRows = $pdo->query('SELECT id, title, content FROM ai_knowledge ORDER BY sort_order, id')->fetchAll();
    } catch (Throwable $e) {
        $kbRows = [];
    }
    $aiKnowledge = array_values($kbRows ?: []);

    // Ödəniş kartları (kart-kart)
    try {
        $cardRows = $pdo->query('SELECT id, bank_name AS bank, card_number AS number, cardholder AS holder, note, theme, status, sort_order AS sortOrder FROM payment_cards ORDER BY sort_order, id')->fetchAll();
    } catch (Throwable $e) {
        $cardRows = [];
    }
    $paymentCards = array_values($cardRows ?: []);

    // Mağaza sifarişləri (tələbə kart-kart ödəniş bildirişləri) + sətirlər
    try {
        $shopOrders = $pdo->query('
            SELECT o.id, o.customer, o.email, o.amount, o.status, o.card_id AS cardId, o.note,
                   o.declared_at AS declaredAt, o.student_id AS studentId,
                   COALESCE(pc.bank_name, "") AS cardBank,
                   COALESCE(NULLIF(o.order_date, ""), DATE(o.created_at)) AS date
            FROM orders o
            LEFT JOIN payment_cards pc ON pc.id = o.card_id
            WHERE o.origin = "shop"
            ORDER BY o.created_at DESC, o.id DESC
        ')->fetchAll();
        $itemStmt = $pdo->prepare('SELECT course_id AS courseId, course_title AS title, amount FROM order_items WHERE order_id = ?');
        foreach ($shopOrders as &$so) {
            $so['amount'] = (float) $so['amount'];
            $itemStmt->execute([$so['id']]);
            $so['items'] = $itemStmt->fetchAll();
        }
        unset($so);
    } catch (Throwable $e) {
        $shopOrders = [];
    }

    // Tələbələr + qeydiyyatlar + dərs izləmə (Coursera tipli idarəetmə)
    try {
        $studentRows = $pdo->query('
            SELECT id, name, email, provider, created_at AS createdAt, last_seen_at AS lastSeen
            FROM student_users WHERE status = "active" ORDER BY created_at DESC, id DESC
        ')->fetchAll();
        $enrStmt = $pdo->prepare('
            SELECT se.id, se.course_id AS courseId,
                   COALESCE(NULLIF(se.course_title, ""), oc.title, se.course_id) AS title,
                   se.status, se.enrolled_at AS enrolledAt,
                   COALESCE(oc.lessons, 0) AS totalLessons,
                   (SELECT COUNT(*) FROM lesson_progress lp WHERE lp.client_id = CONCAT("student-", se.student_id) AND lp.course_id = se.course_id) AS watchedLessons,
                   (SELECT COUNT(*) FROM lesson_progress lp WHERE lp.client_id = CONCAT("student-", se.student_id) AND lp.course_id = se.course_id AND lp.completed = 1) AS completedLessons
            FROM student_enrollments se
            LEFT JOIN online_courses oc ON oc.id = se.course_id
            WHERE se.student_id = ?
            ORDER BY se.enrolled_at DESC, se.id DESC
        ');
        $students = array_map(static function (array $s) use ($enrStmt): array {
            $enrStmt->execute([(int) $s['id']]);
            $enrs = array_map(static function (array $e): array {
                $e['id'] = (int) $e['id'];
                $e['totalLessons'] = (int) $e['totalLessons'];
                $e['watchedLessons'] = (int) $e['watchedLessons'];
                $e['completedLessons'] = (int) $e['completedLessons'];
                return $e;
            }, $enrStmt->fetchAll());
            return [
                'id' => (int) $s['id'],
                'name' => (string) $s['name'],
                'email' => (string) $s['email'],
                'provider' => (string) $s['provider'],
                'createdAt' => (string) $s['createdAt'],
                'lastSeen' => (string) ($s['lastSeen'] ?? ''),
                'enrollments' => $enrs,
            ];
        }, $studentRows ?: []);
    } catch (Throwable $e) {
        $students = [];
    }

    return [
        'courses'       => $courses,
        'onlineCourses' => $onlineCourses,
        'onlineReviews' => $onlineReviews,
        'orders'        => $orders,
        'leads'         => $leads,
        'content'       => setting_value($pdo, 'content', []),
        'seo'           => setting_value($pdo, 'seo', []),
        'curriculum'    => setting_value($pdo, 'curriculum', []),
        'courseDetails' => setting_value($pdo, 'courseDetails', []),
        'onlineCurriculum'    => setting_value($pdo, 'onlineCurriculum', []),
        'onlineCourseDetails' => setting_value($pdo, 'onlineCourseDetails', []),
        'homeTestimonials' => setting_value($pdo, 'homeTestimonials', dl_home_seed_testimonials()),
        'homePortfolio'    => setting_value($pdo, 'homePortfolio', dl_home_seed_portfolio()),
        'homeCertificates' => setting_value($pdo, 'homeCertificates', dl_home_seed_certificates()),
        'homeStats'        => setting_value($pdo, 'homeStats', dl_home_seed_stats()),
        'contact'          => $contact,
        'aiQuizzes'        => setting_value($pdo, 'aiQuizzes', []),
        'aiKnowledge'      => $aiKnowledge,
        'paymentCards'  => $paymentCards,
        'shopOrders'    => $shopOrders,
        'students'      => $students,
        'shopCsrf'      => csrf_token(),
        'blogs'         => $blogs,
        'rev'           => current_rev($pdo),
    ];
}

function save_state(PDO $pdo, array $state, int $newRev): void
{
    $pdo->beginTransaction();

    try {
        $pdo->exec('DELETE FROM orders WHERE origin = "manual"');
        $pdo->exec('DELETE FROM leads');
        $pdo->exec('DELETE FROM courses');

        $courseStmt = $pdo->prepare('
            INSERT INTO courses (id, title, category, price, lessons, students, rating, review_count, status, image, description, sort_order)
            VALUES (:id, :title, :category, :price, :lessons, :students, :rating, :review_count, :status, :image, :description, :sort_order)
        ');

        foreach (($state['courses'] ?? []) as $index => $course) {
            $courseStmt->execute([
                ':id' => (string) ($course['id'] ?? ''),
                ':title' => (string) ($course['title'] ?? ''),
                ':category' => (string) ($course['category'] ?? ''),
                ':price' => (float) ($course['price'] ?? 0),
                ':lessons' => (int) ($course['lessons'] ?? 0),
                ':students' => (int) ($course['students'] ?? 0),
                ':rating' => max(0, min(5, (float) ($course['rating'] ?? 5))),
                ':review_count' => max(0, (int) ($course['reviewCount'] ?? 0)),
                ':status' => (string) ($course['status'] ?? 'draft'),
                ':image' => (string) ($course['image'] ?? ''),
                ':description' => (string) ($course['description'] ?? ''),
                ':sort_order' => $index,
            ]);
        }

        // Onlayn təlimlər: yalnız state-də göndərildikdə yenidən yazılır
        if (isset($state['onlineCourses']) && is_array($state['onlineCourses'])) {
            $pdo->exec('DELETE FROM online_courses');
            $onlineStmt = $pdo->prepare('
                INSERT INTO online_courses (id, title, slug, category, price, lessons, students, rating, review_count, status, image, description, level, sort_order)
                VALUES (:id, :title, :slug, :category, :price, :lessons, :students, :rating, :review_count, :status, :image, :description, :level, :sort_order)
            ');
            foreach ($state['onlineCourses'] as $index => $oc) {
                $onlineStmt->execute([
                    ':id' => (string) ($oc['id'] ?? ''),
                    ':title' => (string) ($oc['title'] ?? ''),
                    ':slug' => (string) ($oc['slug'] ?? ''),
                    ':category' => (string) ($oc['category'] ?? ''),
                    ':price' => (float) ($oc['price'] ?? 0),
                    ':lessons' => (int) ($oc['lessons'] ?? 0),
                    ':students' => (int) ($oc['students'] ?? 0),
                    ':rating' => max(0, min(5, (float) ($oc['rating'] ?? 5))),
                    ':review_count' => max(0, (int) ($oc['reviewCount'] ?? 0)),
                    ':status' => (string) ($oc['status'] ?? 'active'),
                    ':image' => (string) ($oc['image'] ?? ''),
                    ':description' => (string) ($oc['description'] ?? ''),
                    ':level' => (string) ($oc['level'] ?? ''),
                    ':sort_order' => $index,
                ]);
            }
        }

        $orderStmt = $pdo->prepare('
            INSERT INTO orders (id, customer, course_id, phone, status, amount, order_date)
            VALUES (:id, :customer, :course_id, :phone, :status, :amount, :order_date)
        ');

        foreach (($state['orders'] ?? []) as $order) {
            $orderStmt->execute([
                ':id' => (string) ($order['id'] ?? ''),
                ':customer' => (string) ($order['customer'] ?? ''),
                ':course_id' => (string) ($order['courseId'] ?? ''),
                ':phone' => (string) ($order['phone'] ?? ''),
                ':status' => (string) ($order['status'] ?? 'new'),
                ':amount' => (float) ($order['amount'] ?? 0),
                ':order_date' => (string) ($order['date'] ?? ''),
            ]);
        }

        $leadStmt = $pdo->prepare('
            INSERT INTO leads (id, name, source, interest, status, lead_date)
            VALUES (:id, :name, :source, :interest, :status, :lead_date)
        ');

        foreach (($state['leads'] ?? []) as $lead) {
            $leadStmt->execute([
                ':id' => (string) ($lead['id'] ?? ''),
                ':name' => (string) ($lead['name'] ?? ''),
                ':source' => (string) ($lead['source'] ?? ''),
                ':interest' => (string) ($lead['interest'] ?? ''),
                ':status' => (string) ($lead['status'] ?? 'new'),
                ':lead_date' => (string) ($lead['date'] ?? date('Y-m-d')),
            ]);
        }

        $settingStmt = $pdo->prepare('
            INSERT INTO admin_settings (setting_name, setting_value)
            VALUES (:setting_name, :setting_value)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = CURRENT_TIMESTAMP
        ');

        // content, seo, curriculum, courseDetails, online*, home* — admin_settings-ə yazılır
        foreach (['content', 'seo', 'curriculum', 'courseDetails', 'onlineCurriculum', 'onlineCourseDetails', 'homeTestimonials', 'homePortfolio', 'homeCertificates', 'homeStats', 'aiQuizzes'] as $settingName) {
            $settingStmt->execute([
                ':setting_name' => $settingName,
                ':setting_value' => json_encode($state[$settingName] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]);
        }

        // Əlaqə → site_contact (tək sətir upsert)
        if (isset($state['contact']) && is_array($state['contact'])) {
            $c = $state['contact'];
            $pdo->prepare('
                INSERT INTO site_contact (id, phone, email, address, facebook, instagram, linkedin, twitter, footer_about, newsletter_title, newsletter_desc, page_subtitle, map_embed, working_hours)
                VALUES (1, :phone, :email, :address, :facebook, :instagram, :linkedin, :twitter, :footer_about, :newsletter_title, :newsletter_desc, :page_subtitle, :map_embed, :working_hours)
                ON DUPLICATE KEY UPDATE phone=VALUES(phone), email=VALUES(email), address=VALUES(address), facebook=VALUES(facebook), instagram=VALUES(instagram), linkedin=VALUES(linkedin), twitter=VALUES(twitter), footer_about=VALUES(footer_about), newsletter_title=VALUES(newsletter_title), newsletter_desc=VALUES(newsletter_desc), page_subtitle=VALUES(page_subtitle), map_embed=VALUES(map_embed), working_hours=VALUES(working_hours)
            ')->execute([
                ':phone' => (string) ($c['phone'] ?? ''), ':email' => (string) ($c['email'] ?? ''), ':address' => (string) ($c['address'] ?? ''),
                ':facebook' => (string) ($c['facebook'] ?? ''), ':instagram' => (string) ($c['instagram'] ?? ''), ':linkedin' => (string) ($c['linkedin'] ?? ''), ':twitter' => (string) ($c['twitter'] ?? ''),
                ':footer_about' => (string) ($c['footerAbout'] ?? ''), ':newsletter_title' => (string) ($c['newsletterTitle'] ?? ''), ':newsletter_desc' => (string) ($c['newsletterDesc'] ?? ''),
                ':page_subtitle' => (string) ($c['pageSubtitle'] ?? ''), ':map_embed' => (string) ($c['mapEmbed'] ?? ''), ':working_hours' => (string) ($c['workingHours'] ?? ''),
            ]);
        }

        // AI bilik bazası → ai_knowledge (delete + insert)
        if (isset($state['aiKnowledge']) && is_array($state['aiKnowledge'])) {
            $pdo->exec('DELETE FROM ai_knowledge');
            $kbStmt = $pdo->prepare('INSERT INTO ai_knowledge (id, title, content, sort_order) VALUES (:id, :title, :content, :sort_order)');
            foreach ($state['aiKnowledge'] as $idx => $entry) {
                if (!is_array($entry)) {
                    continue;
                }
                $kbStmt->execute([
                    ':id' => (string) ($entry['id'] ?? ('ak-' . $idx)),
                    ':title' => mb_substr((string) ($entry['title'] ?? ''), 0, 255),
                    ':content' => (string) ($entry['content'] ?? ''),
                    ':sort_order' => (int) $idx,
                ]);
            }
        }

        // QEYD: payment_cards yalnız xüsusi card-save / card-delete action-ları ilə idarə olunur
        // (bulk save-state burada toxunmur ki, əsas "Yadda saxla" düyməsi kartları silməsin).

        // blogs — blog_posts cədvəlinə UPSERT ilə yazılır (tək mənbə)
        if (!empty($state['blogs']) && is_array($state['blogs'])) {
            $blogUpsert = $pdo->prepare('
                INSERT INTO blog_posts
                    (id, slug, title_az, title_en, excerpt_az, excerpt_en,
                     content_az, content_en, category, image, author,
                     read_time_az, read_time_en, published_date, status,
                     seo_title, seo_description, seo_keywords, sort_order)
                VALUES
                    (:id, :slug, :title_az, :title_en, :excerpt_az, :excerpt_en,
                     :content_az, :content_en, :category, :image, :author,
                     :read_time_az, :read_time_en, :published_date, :status,
                     :seo_title, :seo_desc, :seo_kw, :sort_order)
                ON DUPLICATE KEY UPDATE
                    slug = VALUES(slug), title_az = VALUES(title_az), title_en = VALUES(title_en),
                    excerpt_az = VALUES(excerpt_az), excerpt_en = VALUES(excerpt_en),
                    content_az = VALUES(content_az), content_en = VALUES(content_en),
                    category = VALUES(category), image = VALUES(image), author = VALUES(author),
                    read_time_az = VALUES(read_time_az), read_time_en = VALUES(read_time_en),
                    published_date = VALUES(published_date), status = VALUES(status),
                    seo_title = VALUES(seo_title), seo_description = VALUES(seo_description),
                    seo_keywords = VALUES(seo_keywords), sort_order = VALUES(sort_order),
                    updated_at = CURRENT_TIMESTAMP
            ');
            foreach ($state['blogs'] as $idx => $blog) {
                $blogUpsert->execute([
                    ':id'           => (string) ($blog['id'] ?? ''),
                    ':slug'         => (string) ($blog['slug'] ?? ($blog['id'] ?? '')),
                    ':title_az'     => (string) ($blog['titleAz'] ?? ''),
                    ':title_en'     => (string) ($blog['titleEn'] ?? ''),
                    ':excerpt_az'   => (string) ($blog['excerptAz'] ?? ''),
                    ':excerpt_en'   => (string) ($blog['excerptEn'] ?? ''),
                    ':content_az'   => (string) ($blog['contentAz'] ?? ''),
                    ':content_en'   => (string) ($blog['contentEn'] ?? ''),
                    ':category'     => (string) ($blog['category'] ?? ''),
                    ':image'        => (string) ($blog['image'] ?? ''),
                    ':author'       => (string) ($blog['author'] ?? 'DatalabAcademy'),
                    ':read_time_az' => (string) ($blog['readTimeAz'] ?? ''),
                    ':read_time_en' => (string) ($blog['readTimeEn'] ?? ''),
                    ':published_date' => (string) ($blog['publishedDate'] ?? ''),
                    ':status'       => (string) ($blog['status'] ?? 'active'),
                    ':seo_title'    => (string) ($blog['seoTitle'] ?? ''),
                    ':seo_desc'     => (string) ($blog['seoDescription'] ?? ''),
                    ':seo_kw'       => (string) ($blog['seoKeywords'] ?? ''),
                    ':sort_order'   => (int) ($blog['sortOrder'] ?? $idx),
                ]);
            }
        }

        $settingStmt->execute([
            ':setting_name' => 'rev',
            ':setting_value' => (string) $newRev,
        ]);

        $pdo->commit();
    } catch (Throwable $error) {
        $pdo->rollBack();
        throw $error;
    }
}

try {
    $pdo = db();
    ensure_schema($pdo);
    $action = $_GET['action'] ?? 'state';

    // boş qalmış sessiyanı bağla (60 dəqiqə)
    if (!empty($_SESSION['admin_user_id'])) {
        if (!empty($_SESSION['last_seen']) && time() - (int) $_SESSION['last_seen'] > 3600) {
            $_SESSION = [];
        } else {
            $_SESSION['last_seen'] = time();
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
        $data = read_json();
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $password = (string) ($data['password'] ?? '');
        $ip = client_ip();

        // IP kilidi: son 10 dəqiqədə 5 uğursuz cəhd
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM admin_login_attempts WHERE ip = ? AND success = 0 AND created_at > (NOW() - INTERVAL 10 MINUTE)');
        $stmt->execute([$ip]);
        if ((int) $stmt->fetchColumn() >= 5) {
            respond(['ok' => false, 'message' => 'Çox sayda uğursuz cəhd. 10 dəqiqə sonra yenidən yoxla.'], 429);
        }

        // Email kilidi: paylanmış (distributed) parol hücumuna qarşı — eyni email üçün son 15 dəqiqədə 8 uğursuz cəhd
        if ($email !== '') {
            $emailStmt = $pdo->prepare('SELECT COUNT(*) FROM admin_login_attempts WHERE email = ? AND success = 0 AND created_at > (NOW() - INTERVAL 15 MINUTE)');
            $emailStmt->execute([$email]);
            if ((int) $emailStmt->fetchColumn() >= 8) {
                respond(['ok' => false, 'message' => 'Bu hesab müvəqqəti kilidləndi. 15 dəqiqə sonra yenidən yoxla.'], 429);
            }
        }

        $stmt = $pdo->prepare('SELECT id, email, password_hash, must_change_password FROM admin_users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        $attempt = $pdo->prepare('INSERT INTO admin_login_attempts (ip, email, success) VALUES (?, ?, ?)');

        if (!$user || !password_verify($password, (string) $user['password_hash'])) {
            $attempt->execute([$ip, $email, 0]);
            usleep(450000); // slow down brute force
            respond(['ok' => false, 'message' => 'E-poçt və ya şifrə yanlışdır.'], 401);
        }

        $attempt->execute([$ip, $email, 1]);
        session_regenerate_id(true);
        $_SESSION['admin_user_id'] = (int) $user['id'];
        $_SESSION['admin_email'] = (string) $user['email'];
        $_SESSION['last_seen'] = time();
        unset($_SESSION['csrf']);
        $pdo->prepare('UPDATE admin_users SET last_login_at = NOW() WHERE id = ?')->execute([$user['id']]);
        log_audit($pdo, 'login');

        $mustChange = (bool) ($user['must_change_password'] ?? false);
        respond([
            'ok' => true,
            'user' => ['email' => $user['email']],
            'csrf' => csrf_token(),
            'mustChangePassword' => $mustChange,
        ]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'logout') {
        if (!empty($_SESSION['admin_user_id'])) {
            log_audit($pdo, 'logout');
        }
        $_SESSION = [];
        session_destroy();
        respond(['ok' => true]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'session') {
        if (!empty($_SESSION['admin_user_id'])) {
            $sessStmt = $pdo->prepare('SELECT email, must_change_password FROM admin_users WHERE id = ? LIMIT 1');
            $sessStmt->execute([(int) $_SESSION['admin_user_id']]);
            $sessUser = $sessStmt->fetch();
            respond([
                'ok'                 => true,
                'user'               => ['email' => (string) ($_SESSION['admin_email'] ?? '')],
                'csrf'               => csrf_token(),
                'mustChangePassword' => (bool) ($sessUser['must_change_password'] ?? false),
            ]);
        }
        respond(['ok' => false, 'auth' => false], 401);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'submit-order') {
        $ip = client_ip();
        check_submission_rate($pdo, $ip);
        $data = read_json();

        $customer = mb_substr(trim((string) ($data['customer'] ?? '')), 0, 255);
        $phone = mb_substr(trim((string) ($data['phone'] ?? '')), 0, 64);
        $email = mb_substr(trim((string) ($data['email'] ?? '')), 0, 190);
        $note = mb_substr(trim((string) ($data['note'] ?? '')), 0, 500);
        $payment = ($data['payment'] ?? '') === 'transfer' ? 'Bank köçürməsi' : 'Kartla ödəniş';
        $courseIds = array_map('strval', (array) ($data['courseIds'] ?? []));

        if (mb_strlen($customer) < 2) {
            respond(['ok' => false, 'message' => 'Ad düzgün deyil.'], 422);
        }
        if (!preg_match('/^[0-9 \\-\\+\\(\\)]{7,20}$/', $phone)) {
            respond(['ok' => false, 'message' => 'Telefon nömrəsi düzgün deyil.'], 422);
        }
        if (!$courseIds) {
            respond(['ok' => false, 'message' => 'Səbət boşdur.'], 422);
        }

        $placeholders = implode(',', array_fill(0, count($courseIds), '?'));
        $stmt = $pdo->prepare("SELECT id, title, price FROM courses WHERE status = 'active' AND id IN ($placeholders)");
        $stmt->execute($courseIds);
        $validCourses = $stmt->fetchAll();
        if (!$validCourses) {
            respond(['ok' => false, 'message' => 'Seçilmiş kurslar tapılmadı.'], 422);
        }

        $insert = $pdo->prepare('
            INSERT INTO orders (id, customer, course_id, phone, status, amount, order_date)
            VALUES (?, ?, ?, ?, "new", ?, ?)
        ');
        $today = date('Y-m-d');
        $lines = [];
        foreach ($validCourses as $i => $course) {
            $orderId = 'DL-' . date('ymdHis') . '-' . ($i + 1);
            $insert->execute([$orderId, $customer, $course['id'], $phone, (float) $course['price'], $today]);
            $lines[] = $orderId . ': ' . $course['title'] . ' — $' . $course['price'];
        }
        bump_rev($pdo);
        record_submission($pdo, $ip, 'order');

        $body = "Yeni sifariş!\n\nMüştəri: $customer\nTelefon: $phone" .
            ($email !== '' ? "\nE-poçt: $email" : '') .
            "\nÖdəniş üsulu: $payment" .
            ($note !== '' ? "\nQeyd: $note" : '') .
            "\n\n" . implode("\n", $lines);
        send_admin_mail($pdo, 'DatalabAcademy: yeni sifariş — ' . $customer, $body);

        respond(['ok' => true, 'message' => 'Sifarişiniz qəbul olundu. Menecerimiz tezliklə sizinlə əlaqə saxlayacaq.']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'submit-lead') {
        $ip = client_ip();
        check_submission_rate($pdo, $ip);
        $data = read_json();

        $name = mb_substr(trim((string) ($data['name'] ?? '')), 0, 255);
        $email = mb_substr(trim((string) ($data['email'] ?? '')), 0, 190);
        $phone = mb_substr(trim((string) ($data['phone'] ?? '')), 0, 64);
        $interest = mb_substr(trim((string) ($data['interest'] ?? '')), 0, 255) ?: 'Ümumi';
        $source = mb_substr(trim((string) ($data['source'] ?? '')), 0, 120) ?: 'Website';
        $message = mb_substr(trim((string) ($data['message'] ?? '')), 0, 1000);

        if (mb_strlen($name) < 2) {
            respond(['ok' => false, 'message' => 'Ad düzgün deyil.'], 422);
        }
        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            respond(['ok' => false, 'message' => 'E-poçt düzgün deyil.'], 422);
        }

        $pdo->prepare('INSERT INTO leads (id, name, source, interest, status, lead_date) VALUES (?, ?, ?, ?, "new", ?)')
            ->execute(['L-' . date('ymdHis') . '-' . random_int(10, 99), $name, $source, $interest, date('Y-m-d')]);
        bump_rev($pdo);
        record_submission($pdo, $ip, 'lead');

        $body = "Yeni müraciət!\n\nAd: $name" .
            ($phone !== '' ? "\nTelefon: $phone" : '') .
            ($email !== '' ? "\nE-poçt: $email" : '') .
            "\nMaraq: $interest\nMənbə: $source" .
            ($message !== '' ? "\n\nMesaj:\n$message" : '');
        send_admin_mail($pdo, 'DatalabAcademy: yeni müraciət — ' . $name, $body);

        respond(['ok' => true, 'message' => 'Müraciətiniz qəbul olundu. Tezliklə sizinlə əlaqə saxlayacağıq.']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'public') {
        // read-only, no auth: data the public site needs
        $state = get_state($pdo);
        $activeCourses = array_values(array_filter($state['courses'], function ($course) {
            return ($course['status'] ?? '') === 'active';
        }));
        $activeOnline = array_values(array_filter($state['onlineCourses'] ?? [], function ($oc) {
            return ($oc['status'] ?? '') === 'active';
        }));
        respond(['ok' => true, 'data' => [
            'courses' => $activeCourses,
            'onlineCourses' => $activeOnline,
            'content' => $state['content'],
            'seo' => $state['seo'],
            'curriculum' => $state['curriculum'],
            'courseDetails' => $state['courseDetails'],
            'onlineCurriculum' => $state['onlineCurriculum'],
            'onlineCourseDetails' => $state['onlineCourseDetails'],
            'onlineReviews' => (function ($reviews) {
                $map = [];
                foreach ($reviews as $r) {
                    if (($r['status'] ?? '') !== 'approved') {
                        continue;
                    }
                    $cid = (string) ($r['courseId'] ?? '');
                    if (!isset($map[$cid])) {
                        $map[$cid] = [];
                    }
                    $map[$cid][] = ['name' => $r['name'], 'rating' => (int) $r['rating'], 'body' => $r['body'], 'date' => $r['date']];
                }
                return $map;
            })($state['onlineReviews'] ?? []),
            'blogs' => array_values(array_filter($state['blogs'], function ($blog) {
                return ($blog['status'] ?? 'active') === 'active';
            })),
        ]]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'health') {
        // Giriş etməmiş istifadəçiyə yalnız minimal "canlıdır" cavabı (DB adı/sayları sızdırılmır)
        if (empty($_SESSION['admin_user_id'])) {
            respond(['ok' => true, 'server_time' => date('Y-m-d H:i:s')]);
        }
        $counts = [];
        foreach (['courses', 'orders', 'leads', 'admin_settings'] as $table) {
            $counts[$table] = (int) $pdo->query('SELECT COUNT(*) FROM ' . $table)->fetchColumn();
        }
        respond([
            'ok' => true,
            'database' => DB_NAME,
            'tables' => $counts,
            'curriculum_saved' => $counts['admin_settings'] > 0,
            'server_time' => date('Y-m-d H:i:s'),
        ]);
    }

    // everything below requires a logged-in admin
    require_auth();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'change-password') {
        require_csrf();
        $data = read_json();
        $old = (string) ($data['oldPassword'] ?? '');
        $new = (string) ($data['newPassword'] ?? '');
        $confirm = (string) ($data['confirmPassword'] ?? $new);

        if (strlen($new) < 12) {
            respond(['ok' => false, 'message' => 'Admin şifrəsi ən azı 12 simvol olmalıdır.'], 422);
        }
        if ($new !== $confirm) {
            respond(['ok' => false, 'message' => 'Şifrə təkrarı uyğun deyil.'], 422);
        }
        // Güclü şifrə tələbi: böyük hərf, kiçik hərf, rəqəm
        if (!preg_match('/[A-Z]/', $new) || !preg_match('/[a-z]/', $new) || !preg_match('/[0-9]/', $new)) {
            respond(['ok' => false, 'message' => 'Şifrə ən azı bir böyük hərf, bir kiçik hərf və bir rəqəm ehtiva etməlidir.'], 422);
        }

        $stmt = $pdo->prepare('SELECT password_hash, must_change_password FROM admin_users WHERE id = ? LIMIT 1');
        $stmt->execute([(int) $_SESSION['admin_user_id']]);
        $row = $stmt->fetch();
        $hash = (string) ($row['password_hash'] ?? '');

        if (!password_verify($old, $hash)) {
            respond(['ok' => false, 'message' => 'Cari şifrə yanlışdır.'], 401);
        }

        $pdo->prepare('UPDATE admin_users SET password_hash = ?, must_change_password = 0 WHERE id = ?')
            ->execute([password_hash($new, PASSWORD_DEFAULT), (int) $_SESSION['admin_user_id']]);
        log_audit($pdo, 'change-password');
        respond(['ok' => true, 'message' => 'Şifrə uğurla yeniləndi.']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'upload-image') {
        require_csrf();

        if (empty($_FILES['image']) || !is_array($_FILES['image'])) {
            respond(['ok' => false, 'message' => 'Fayl göndərilmədi.'], 400);
        }
        $file = $_FILES['image'];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            respond(['ok' => false, 'message' => 'Yükləmə xətası.'], 400);
        }
        if ((int) $file['size'] > 2 * 1024 * 1024) {
            respond(['ok' => false, 'message' => 'Maksimum fayl ölçüsü 2MB-dır.'], 422);
        }

        $mime = (string) (new finfo(FILEINFO_MIME_TYPE))->file((string) $file['tmp_name']);
        $extensions = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/webp' => 'webp', 'image/svg+xml' => 'svg'];
        if (!isset($extensions[$mime])) {
            respond(['ok' => false, 'message' => 'Yalnız PNG, JPG, WEBP və SVG qəbul olunur.'], 422);
        }
        if ($extensions[$mime] === 'svg') {
            $svg = (string) file_get_contents((string) $file['tmp_name']);
            if (preg_match('/<script|on\w+\s*=|javascript:/i', $svg)) {
                respond(['ok' => false, 'message' => 'SVG faylı təhlükəsiz deyil.'], 422);
            }
        }

        $dir = dirname(__DIR__) . '/assets/images/uploads';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        if (!is_file($dir . '/.htaccess')) {
            // yüklənmiş fayllar qovluğunda PHP icrasını blokla
            file_put_contents($dir . '/.htaccess', "php_flag engine off\n<FilesMatch \"\\.(php|phtml|php5|phar)$\">\n    Require all denied\n</FilesMatch>\n");
        }
        $name = 'img-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $extensions[$mime];
        if (!move_uploaded_file((string) $file['tmp_name'], $dir . '/' . $name)) {
            respond(['ok' => false, 'message' => 'Fayl saxlanmadı.'], 500);
        }
        log_audit($pdo, 'upload-image', $name);
        respond(['ok' => true, 'path' => 'assets/images/uploads/' . $name]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'audit') {
        $rows = $pdo->query('SELECT email, action, detail, ip, created_at FROM admin_audit_log ORDER BY id DESC LIMIT 30')->fetchAll();
        respond(['ok' => true, 'data' => $rows]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'state') {
        respond(['ok' => true, 'data' => get_state($pdo)]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save-state') {
        require_csrf();
        $state = sanitize_state(read_json());
        $clientRev = (int) ($state['rev'] ?? 0);
        $serverRev = current_rev($pdo);

        if ($serverRev > 0 && $clientRev !== $serverRev) {
            respond([
                'ok' => false,
                'conflict' => true,
                'message' => 'Data başqa sessiyada dəyişdirilib.',
                'data' => get_state($pdo),
            ], 409);
        }

        // yeni sifariş/lead-ləri email bildirişi üçün aşkarla
        $existingOrderIds = $pdo->query('SELECT id FROM orders')->fetchAll(PDO::FETCH_COLUMN);
        $existingLeadIds = $pdo->query('SELECT id FROM leads')->fetchAll(PDO::FETCH_COLUMN);
        $newOrderIds = array_values(array_diff(
            array_map(function ($o) { return (string) ($o['id'] ?? ''); }, $state['orders'] ?? []),
            $existingOrderIds
        ));
        $newLeadIds = array_values(array_diff(
            array_map(function ($l) { return (string) ($l['id'] ?? ''); }, $state['leads'] ?? []),
            $existingLeadIds
        ));

        save_state($pdo, $state, $serverRev + 1);
        write_backup($state);
        notify_new_records($pdo, $state, $newOrderIds, $newLeadIds);
        log_audit($pdo, 'save-state', count($state['courses'] ?? []) . ' kurs, ' . count($state['orders'] ?? []) . ' sifariş, ' . count($state['leads'] ?? []) . ' lead');
        respond(['ok' => true, 'data' => get_state($pdo)]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'online-review-update') {
        require_csrf();
        $data = read_json();
        $id = (int) ($data['id'] ?? 0);
        $status = (string) ($data['status'] ?? '');
        if (!in_array($status, ['pending', 'approved', 'hidden'], true)) {
            respond(['ok' => false, 'message' => 'Status yanlışdır.'], 422);
        }
        $pdo->prepare('UPDATE online_reviews SET status = ? WHERE id = ?')->execute([$status, $id]);
        log_audit($pdo, 'online-review-update', 'id=' . $id . ' status=' . $status);
        respond(['ok' => true, 'data' => get_state($pdo)]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'online-review-delete') {
        require_csrf();
        $data = read_json();
        $id = (int) ($data['id'] ?? 0);
        $pdo->prepare('DELETE FROM online_reviews WHERE id = ?')->execute([$id]);
        log_audit($pdo, 'online-review-delete', 'id=' . $id);
        respond(['ok' => true, 'data' => get_state($pdo)]);
    }

    // Kart-kart ödənişini təsdiqlə → tələbəni kurslara enroll et
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'order-approve') {
        require_csrf();
        $data = read_json();
        $orderId = mb_substr(trim((string) ($data['id'] ?? '')), 0, 64);
        $stmt = $pdo->prepare('SELECT id, student_id, status FROM orders WHERE id = ? AND origin = "shop" LIMIT 1');
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        if (!$order) {
            respond(['ok' => false, 'message' => 'Sifariş tapılmadı.'], 404);
        }
        $studentId = (int) ($order['student_id'] ?? 0);
        $pdo->beginTransaction();
        try {
            $items = $pdo->prepare('SELECT course_id, course_title FROM order_items WHERE order_id = ?');
            $items->execute([$orderId]);
            $enrollStmt = $pdo->prepare('INSERT INTO student_enrollments (student_id, course_id, course_title, enrollment_type, status) VALUES (?, ?, ?, "online", "active")');
            $checkStmt = $pdo->prepare('SELECT id FROM student_enrollments WHERE student_id = ? AND course_id = ? LIMIT 1');
            foreach ($items->fetchAll() as $it) {
                $courseId = (string) $it['course_id'];
                if ($studentId <= 0 || $courseId === '') {
                    continue;
                }
                $checkStmt->execute([$studentId, $courseId]);
                $existing = $checkStmt->fetchColumn();
                if ($existing) {
                    $pdo->prepare('UPDATE student_enrollments SET status = "active" WHERE id = ?')->execute([(int) $existing]);
                } else {
                    $enrollStmt->execute([$studentId, $courseId, (string) $it['course_title']]);
                }
            }
            $pdo->prepare('UPDATE orders SET status = "paid" WHERE id = ?')->execute([$orderId]);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
        log_audit($pdo, 'order-approve', 'id=' . $orderId . ' student=' . $studentId);
        respond(['ok' => true, 'data' => get_state($pdo)]);
    }

    // Kart-kart ödənişini rədd et
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'order-reject') {
        require_csrf();
        $data = read_json();
        $orderId = mb_substr(trim((string) ($data['id'] ?? '')), 0, 64);
        $pdo->prepare('UPDATE orders SET status = "rejected" WHERE id = ? AND origin = "shop"')->execute([$orderId]);
        log_audit($pdo, 'order-reject', 'id=' . $orderId);
        respond(['ok' => true, 'data' => get_state($pdo)]);
    }

    // Ödəniş kartı əlavə/redaktə (kart-kart)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'card-save') {
        require_csrf();
        $data = read_json();
        $id = mb_substr(trim((string) ($data['id'] ?? '')), 0, 64);
        if ($id === '') {
            $id = 'pc-' . bin2hex(random_bytes(4));
        }
        $themes = ['violet', 'ocean', 'emerald', 'sunset', 'dark', 'gold'];
        $theme = in_array($data['theme'] ?? '', $themes, true) ? $data['theme'] : 'violet';
        $status = ($data['status'] ?? 'active') === 'hidden' ? 'hidden' : 'active';
        $pdo->prepare('
            INSERT INTO payment_cards (id, bank_name, card_number, cardholder, note, theme, sort_order, status)
            VALUES (:id, :bank, :number, :holder, :note, :theme, :sort_order, :status)
            ON DUPLICATE KEY UPDATE bank_name=VALUES(bank_name), card_number=VALUES(card_number), cardholder=VALUES(cardholder), note=VALUES(note), theme=VALUES(theme), status=VALUES(status)
        ')->execute([
            ':id' => $id,
            ':bank' => mb_substr(trim((string) ($data['bank'] ?? '')), 0, 120),
            ':number' => mb_substr(trim((string) ($data['number'] ?? '')), 0, 40),
            ':holder' => mb_substr(trim((string) ($data['holder'] ?? '')), 0, 120),
            ':note' => mb_substr(trim((string) ($data['note'] ?? '')), 0, 190),
            ':theme' => $theme,
            ':sort_order' => (int) ($data['sortOrder'] ?? 0),
            ':status' => $status,
        ]);
        log_audit($pdo, 'card-save', 'id=' . $id);
        respond(['ok' => true, 'data' => get_state($pdo)]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'card-delete') {
        require_csrf();
        $data = read_json();
        $id = mb_substr(trim((string) ($data['id'] ?? '')), 0, 64);
        $pdo->prepare('DELETE FROM payment_cards WHERE id = ?')->execute([$id]);
        log_audit($pdo, 'card-delete', 'id=' . $id);
        respond(['ok' => true, 'data' => get_state($pdo)]);
    }

    // Tələbənin kursa girişini ləğv et / bərpa et
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'enrollment-set-status') {
        require_csrf();
        $data = read_json();
        $id = (int) ($data['id'] ?? 0);
        $status = (string) ($data['status'] ?? '');
        if (!in_array($status, ['active', 'revoked'], true)) {
            respond(['ok' => false, 'message' => 'Status yanlışdır.'], 422);
        }
        $pdo->prepare('UPDATE student_enrollments SET status = ? WHERE id = ?')->execute([$status, $id]);
        log_audit($pdo, 'enrollment-set-status', 'id=' . $id . ' status=' . $status);
        respond(['ok' => true, 'data' => get_state($pdo)]);
    }

    // Qeydiyyatdan keçən tələbələrin siyahısı (admin "Tələbələr" tabı)
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'students') {
        try {
            $rows = $pdo->query('SELECT id, name, email, phone, provider, status, avatar_url, last_login_at FROM student_users ORDER BY created_at DESC, id DESC')->fetchAll();
        } catch (Throwable $e) {
            $rows = [];
        }
        respond(['ok' => true, 'data' => array_map(static function (array $s): array {
            return [
                'id' => (int) $s['id'],
                'name' => (string) $s['name'],
                'email' => (string) $s['email'],
                'phone' => (string) ($s['phone'] ?? ''),
                'provider' => (string) ($s['provider'] ?? 'local'),
                'status' => (string) ($s['status'] ?? 'active'),
                'avatar_url' => (string) ($s['avatar_url'] ?? ''),
                'last_login_at' => (string) ($s['last_login_at'] ?? ''),
            ];
        }, $rows ?: [])]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update-student') {
        require_csrf();
        $data = read_json();
        $id = (int) ($data['id'] ?? 0);
        $status = ($data['status'] ?? '') === 'blocked' ? 'blocked' : 'active';
        $pdo->prepare('UPDATE student_users SET status = ? WHERE id = ?')->execute([$status, $id]);
        log_audit($pdo, 'update-student', 'id=' . $id . ' status=' . $status);
        respond(['ok' => true]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete-student') {
        require_csrf();
        $data = read_json();
        $id = (int) ($data['id'] ?? 0);
        $pdo->prepare('DELETE FROM student_users WHERE id = ?')->execute([$id]);
        log_audit($pdo, 'delete-student', 'id=' . $id);
        respond(['ok' => true]);
    }

    // Bir tələbənin konkret kursdakı dərs-səviyyəli izləməsi (hansı dərsə baxıb/tamamlayıb)
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'student-lessons') {
        $studentId = (int) ($_GET['student'] ?? 0);
        $courseId = mb_substr(trim((string) ($_GET['course'] ?? '')), 0, 64);
        $curriculum = setting_value($pdo, 'onlineCurriculum', []);
        $sections = (isset($curriculum[$courseId]) && is_array($curriculum[$courseId])) ? $curriculum[$courseId] : [];

        $progress = [];
        try {
            $ps = $pdo->prepare('SELECT lesson_id, progress_percent, completed, last_position FROM lesson_progress WHERE client_id = ? AND course_id = ?');
            $ps->execute(['student-' . $studentId, $courseId]);
            foreach ($ps->fetchAll() as $r) {
                $progress[(string) $r['lesson_id']] = $r;
            }
        } catch (Throwable $e) {
            $progress = [];
        }

        $lessons = [];
        foreach ($sections as $sec) {
            if (!is_array($sec)) {
                continue;
            }
            $secTitle = (string) ($sec['title'] ?? 'Bölmə');
            foreach (($sec['lessons'] ?? []) as $ls) {
                if (!is_array($ls)) {
                    continue;
                }
                $lid = (string) ($ls['id'] ?? '');
                $p = $progress[$lid] ?? null;
                $lessons[] = [
                    'id' => $lid,
                    'title' => (string) ($ls['title'] ?? 'Dərs'),
                    'section' => $secTitle,
                    'type' => (string) ($ls['type'] ?? 'video'),
                    'watched' => $p !== null,
                    'completed' => $p !== null ? (bool) $p['completed'] : false,
                    'percent' => $p !== null ? (int) $p['progress_percent'] : 0,
                ];
            }
        }
        respond(['ok' => true, 'lessons' => $lessons]);
    }

    respond(['ok' => false, 'message' => 'Unknown action.'], 404);
} catch (Throwable $error) {
    // detallı xəta yalnız server loguna yazılır — istifadəçiyə sızdırılmır
    error_log('[datalab-admin] ' . $error->getMessage() . ' @ ' . $error->getFile() . ':' . $error->getLine());
    respond(['ok' => false, 'message' => 'Server xətası. Bir az sonra yenidən yoxla.'], 500);
}
