<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/config.php';

function dl_home_e(mixed $v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/* ---------------- seed defaults (current homepage content) ---------------- */

function dl_home_seed_testimonials(): array
{
    return [
        ['id' => 't1', 'name' => 'Saidə Jabbarlı', 'role' => 'SQL Developer kursunun tələbəsi', 'text' => 'SQL Developer kursu çox faydalı oldu. JOIN-lar, indekslər və real tapşırıqlar sayəsində sorğularım daha sürətli və daha düzgün işləməyə başladı. Dərslərin izahı aydın idi və praktika hissəsi xüsusilə xoşuma gəldi.', 'image' => 'assets/images/testimonial/client-01.png', 'rating' => 5],
    ];
}

function dl_home_seed_portfolio(): array
{
    return [
        ['id' => 'p1', 'title' => 'Amazon məhsul analitikası', 'desc' => 'Məhsul siyahısı, axtarış və vizual məhsul kartları ilə interaktiv dashboard nümunəsi.', 'image' => 'assets/images/portfolio/portfolio-amazon-products.png', 'tag' => 'Power BI', 'link' => 'assets/images/portfolio/portfolio-amazon-products.png'],
        ['id' => 'p2', 'title' => 'Nəqliyyat KPI hesabatı', 'desc' => 'Gəlir, xərc, mənfəət və booking metrikalarını bir ekranda göstərən analiz paneli.', 'image' => 'assets/images/portfolio/portfolio-vehicle-kpis.png', 'tag' => 'Dashboard', 'link' => 'assets/images/portfolio/portfolio-vehicle-kpis.png'],
        ['id' => 'p3', 'title' => 'Müştəri mənfəət analizi', 'desc' => 'Top və zəif müştəriləri gəlir, xərc və mənfəət üzrə müqayisə edən hesabat.', 'image' => 'assets/images/portfolio/portfolio-vehicle-customers.png', 'tag' => 'Report', 'link' => 'assets/images/portfolio/portfolio-vehicle-customers.png'],
        ['id' => 'p4', 'title' => 'Dövr üzrə satış analizi', 'desc' => 'Aylıq satış, mənfəət və xərc göstəricilərini izləmək üçün interaktiv dashboard.', 'image' => 'assets/images/portfolio/portfolio-sales-period.png', 'tag' => 'Report', 'link' => 'assets/images/portfolio/portfolio-sales-period.png'],
        ['id' => 'p5', 'title' => 'Seqment üzrə satış analizi', 'desc' => 'Seqmentlərin satış, mənfəət və marja performansını müqayisə edən layihə.', 'image' => 'assets/images/portfolio/portfolio-sales-segment.png', 'tag' => 'Power BI', 'link' => 'assets/images/portfolio/portfolio-sales-segment.png'],
        ['id' => 'p6', 'title' => 'Məhsul üzrə satış analizi', 'desc' => 'Məhsul performansı, COGS və profit margin nəticələrini göstərən portfolio işi.', 'image' => 'assets/images/portfolio/portfolio-sales-product.png', 'tag' => 'Dashboard', 'link' => 'assets/images/portfolio/portfolio-sales-product.png'],
    ];
}

function dl_home_seed_certificates(): array
{
    return [
        ['id' => 'c1', 'tag' => 'Beynəlxalq sertifikat', 'title' => 'Microsoft Certified: AI Business Professional', 'desc' => 'DatalabAcademy-yə qoşulan və proqramı uğurla tamamlayan iştirakçılar beynəlxalq səviyyədə tanınan bu sertifikat nəticəsini CV və LinkedIn profilində paylaşa biləcəklər.', 'image' => 'assets/certificates/microsoft-ai-business-professional-clean.png'],
        ['id' => 'c2', 'tag' => 'Microsoft sertifikatı', 'title' => 'Microsoft Office Specialist: Excel Expert (Microsoft 365 Apps)', 'desc' => 'Kursu tamamlayan iştirakçılar təlim nəticələrini təsdiqləyən bu sertifikatı əldə edib portfolio və karyera profillərində istifadə edə biləcəklər.', 'image' => 'assets/certificates/microsoft-office-specialist-excel-expert.png'],
    ];
}

function dl_home_seed_stats(): array
{
    return [
        'badges' => [
            ['icon' => 'feather-bar-chart-2', 'color' => '#2f57ef', 'text' => 'Data Analitika'],
            ['icon' => 'feather-award', 'color' => '#c586ee', 'text' => 'Sertifikat al'],
            ['icon' => 'feather-zap', 'color' => '#ff9f43', 'text' => 'Canlı Dərslər'],
            ['icon' => 'feather-check-circle', 'color' => '#2ed573', 'text' => 'Praktiki Tapşırıqlar'],
        ],
        'statActive' => '1000+',
        'statActiveLabel' => 'Aktiv Tələbə',
        'statRating' => '⭐ 4.9',
        'statRatingLabel' => 'Ortalama Reytinq',
        'newTag' => '🔥 Yeni Kurslar!',
    ];
}

/* ---------------- readers (DB value or seed) ---------------- */

function dl_home_setting(string $name, array $fallback): array
{
    try {
        $stmt = db()->prepare('SELECT setting_value FROM admin_settings WHERE setting_name = ? LIMIT 1');
        $stmt->execute([$name]);
        $value = $stmt->fetchColumn();
        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (is_array($decoded) && $decoded) {
                return $decoded;
            }
        }
    } catch (Throwable $e) {
        // fall through to seed
    }
    return $fallback;
}

function dl_home_testimonials(): array { return dl_home_setting('homeTestimonials', dl_home_seed_testimonials()); }
function dl_home_portfolio(): array { return dl_home_setting('homePortfolio', dl_home_seed_portfolio()); }
function dl_home_certificates(): array { return dl_home_setting('homeCertificates', dl_home_seed_certificates()); }

function dl_home_stats(): array
{
    $stats = dl_home_setting('homeStats', dl_home_seed_stats());
    // ensure required keys exist (merge over seed)
    return array_merge(dl_home_seed_stats(), is_array($stats) ? $stats : []);
}

/* ---------------- contact / əlaqə ---------------- */

function dl_home_seed_contact(): array
{
    return [
        'phone' => '+994 50 654 97 37',
        'email' => 'info@datalabacademy.az',
        'address' => 'Bakı, Azərbaycan',
        'facebook' => '#',
        'instagram' => '#',
        'linkedin' => '#',
        'twitter' => '#',
        'footerAbout' => 'DatalabAcademy praktiki Data Analitika, SQL, Excel və AI təlimləri ilə karyera bacarıqlarınızı inkişaf etdirir.',
        'newsletterTitle' => 'Xəbər bülleteni',
        'newsletterDesc' => 'Yeni qrup açılışları, faydalı materiallar və kampaniyalar üçün e-poçtunuzu qeyd edin.',
    ];
}

function dl_home_contact(): array
{
    try {
        $row = db()->query('SELECT * FROM site_contact WHERE id = 1 LIMIT 1')->fetch();
        if (is_array($row)) {
            return array_merge(dl_home_seed_contact(), [
                'phone' => (string) $row['phone'], 'email' => (string) $row['email'], 'address' => (string) $row['address'],
                'facebook' => (string) $row['facebook'], 'instagram' => (string) $row['instagram'], 'linkedin' => (string) $row['linkedin'], 'twitter' => (string) $row['twitter'],
                'footerAbout' => (string) $row['footer_about'], 'newsletterTitle' => (string) $row['newsletter_title'], 'newsletterDesc' => (string) $row['newsletter_desc'],
                'pageSubtitle' => (string) $row['page_subtitle'], 'mapEmbed' => (string) $row['map_embed'], 'workingHours' => (string) $row['working_hours'],
            ]);
        }
    } catch (Throwable $e) {
        // fall through
    }
    return dl_home_seed_contact();
}
