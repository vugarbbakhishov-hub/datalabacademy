<?php
/*
 * BİR DƏFƏLİK TEST SKRİPTİ — Data Analitika təlimi üçün "Təlim səhifəsi" detallarını
 * BİRBAŞA bazaya (admin_settings.detailpages) yazır. Admin formasına və localStorage-a
 * toxunmur — məqsəd course-details-3-ün məlumatı BAZADAN oxuduğunu yoxlamaqdır.
 *
 * İstifadə: brauzerdə bir dəfə aç -> http://localhost/Project-CloudAI/seed-detailpage.php
 * Sonra bu faylı SİL (təhlükəsizlik üçün).
 */
declare(strict_types=1);
header('Content-Type: text/plain; charset=utf-8');

require __DIR__ . '/api/config.php';

try {
    $pdo = db();

    // "Data Analitika" adlı kursu tap — varsa online təlimə (training) üstünlük ver.
    $stmt = $pdo->query("SELECT id, title, course_type FROM courses WHERE title LIKE '%Data Analitika%' ORDER BY (course_type = 'training') DESC, id");
    $matches = $stmt->fetchAll();

    if (!$matches) {
        echo "XƏTA: 'Data Analitika' adlı kurs/təlim tapılmadı.\n";
        echo "Admin paneldə Online təlimlər bölməsində 'Data Analitika' yaratdığından əmin ol.\n";
        exit;
    }

    $target = $matches[0];
    $courseId = (string) $target['id'];

    echo "Tapılan kurs/təlim:\n";
    foreach ($matches as $m) {
        echo "  - id=" . $m['id'] . " | " . $m['title'] . " | tip=" . ($m['course_type'] ?? 'course') . "\n";
    }
    echo "\nSeçilən id (detalların yazılacağı): " . $courseId . "\n\n";

    // Data Analitika təliminə uyğun nümunə detallar
    $detail = [
        'heroSubtitle' => 'Data Analitika üzrə sıfırdan praktik online təlim — KPI, vizuallaşdırma və dashboard düşüncəsi.',
        'instructor' => 'DatalabAcademy',
        'rating' => 5,
        'reviews' => 84,
        'startDate' => 'Yeni qrup',
        'oldPrice' => 374,
        'level' => 'Başlanğıc',
        'language' => 'Azərbaycan',
        'enrollment' => 100,
        'overviewText' => 'Proqram başlanğıc səviyyədən başlayır və hər modulda öyrəndiyiniz bacarıqları real datasetlər üzərində praktiki tapşırıqlarla möhkəmləndirir. Məqsəd yalnız dərs izləmək deyil, biznes sualını analiz edib qərara çevirməkdir.',
        'learnPoints' => [
            'Məlumatları Excel və SQL ilə təmizləməyi, analiz etməyi və hesabatlaşdırmağı öyrənəcəksiniz.',
            'KPI və metrikaları düzgün seçib biznes sualına cavab verən dashboard quracaqsınız.',
            'Pivot Table, Power Query və vizuallaşdırma ilə təsirli hesabatlar hazırlayacaqsınız.',
            'SQL sorğuları (JOIN, qruplaşdırma, filtrləmə) ilə verilənləri çıxaracaqsınız.',
            'AI alətlərindən analizdə sürət üçün düzgün və etik istifadə edəcəksiniz.',
            'Kurs sonunda portfolionuza əlavə edilə bilən real layihə hazırlayacaqsınız.',
        ],
        'requirements' => [
            'Kompüterdə əsas iş bacarığı.',
            'Excel ilə ilkin tanışlıq (məcburi deyil).',
            'Həftədə bir neçə saat praktika üçün vaxt.',
        ],
        'descriptionPoints' => [
            'Real biznes datasetləri üzərində praktiki analiz.',
            'Excel/Power BI ilə dashboard qurma.',
            'SQL ilə verilənlər bazasından məlumat çıxarma.',
            'Nəticələri insight və qərara çevirmə (storytelling).',
        ],
    ];

    // Mövcud detailpages ayarını oxu, bu id üçün məlumatı əlavə/yenilə (digərlərini pozmadan).
    $stmt = $pdo->prepare("SELECT setting_value FROM admin_settings WHERE setting_name = 'detailpages' LIMIT 1");
    $stmt->execute();
    $existingRaw = $stmt->fetchColumn();
    $all = [];
    if (is_string($existingRaw) && $existingRaw !== '') {
        $decoded = json_decode($existingRaw, true);
        if (is_array($decoded)) {
            $all = $decoded;
        }
    }
    $all[$courseId] = $detail;

    $json = json_encode($all, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $pdo->prepare("
        INSERT INTO admin_settings (setting_name, setting_value)
        VALUES ('detailpages', ?)
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
    ")->execute([$json]);

    echo "✓ Bazaya YAZILDI (admin_settings.detailpages).\n\n";
    echo "İndi yoxla:\n";
    echo "  1) Sayt: http://localhost/Project-CloudAI/course-details-3?course=" . rawurlencode($courseId) . "\n";
    echo "     -> İcmal/Detallar/yan panel/hero yuxarıdakı məlumatları göstərməlidir (bazadan).\n";
    echo "  2) phpMyAdmin: admin_settings cədvəlində setting_name='detailpages' sətrinə bax.\n\n";
    echo "Yazılan JSON:\n" . json_encode($detail, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n\n";
    echo "QEYD: Yoxladıqdan sonra bu faylı (seed-detailpage.php) SİL.\n";
} catch (Throwable $e) {
    echo "XƏTA: " . $e->getMessage() . "\n";
}
