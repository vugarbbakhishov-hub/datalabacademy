<?php
declare(strict_types=1);

/**
 * Dinamik XML Sitemap generatoru.
 * .htaccess: RewriteRule ^sitemap\.xml$ sitemap.php [L,QSA]
 *
 * Ehtiva edir:
 *  - Statik ictimai sehifeler
 *  - Aktiv kurslar (course-details-3?id=X)
 *  - Aktiv blog yazilari (blog-details?slug=X)
 */

header('Content-Type: application/xml; charset=utf-8');
header('X-Robots-Tag: noindex');

$domain = rtrim(defined('DL_DOMAIN') ? (string) constant('DL_DOMAIN') : 'https://datalabacademy.az', '/');
$today  = date('Y-m-d');

$staticPages = [
    ['loc' => '/',                       'changefreq' => 'weekly',  'priority' => '1.0'],
    ['loc' => '/course-filter-one-open', 'changefreq' => 'weekly',  'priority' => '0.9'],
    ['loc' => '/about',                  'changefreq' => 'monthly', 'priority' => '0.7'],
    ['loc' => '/blog-with-sidebar',      'changefreq' => 'weekly',  'priority' => '0.8'],
    ['loc' => '/contact',                'changefreq' => 'monthly', 'priority' => '0.6'],
];

$dynamicUrls = [];

try {
    require_once __DIR__ . '/api/config.php';
    $pdo = db();

    $courses = $pdo->query(
        "SELECT id, updated_at FROM courses WHERE status = 'active' ORDER BY sort_order, id"
    )->fetchAll();
    foreach ($courses as $course) {
        $lastmod = substr((string) ($course['updated_at'] ?? $today), 0, 10);
        $dynamicUrls[] = [
            'loc'        => '/course-details-3?id=' . rawurlencode((string) $course['id']),
            'lastmod'    => $lastmod,
            'changefreq' => 'monthly',
            'priority'   => '0.8',
        ];
    }

    $blogs = $pdo->query(
        "SELECT slug, id, updated_at FROM blog_posts WHERE status = 'active' ORDER BY sort_order, published_date DESC"
    )->fetchAll();
    foreach ($blogs as $blog) {
        $slugOrId = ($blog['slug'] ?? '') !== '' ? (string) $blog['slug'] : (string) $blog['id'];
        $lastmod  = substr((string) ($blog['updated_at'] ?? $today), 0, 10);
        $dynamicUrls[] = [
            'loc'        => '/blog-details?slug=' . rawurlencode($slugOrId),
            'lastmod'    => $lastmod,
            'changefreq' => 'monthly',
            'priority'   => '0.7',
        ];
    }
} catch (Throwable $error) {
    error_log('[sitemap] DB xetasi: ' . $error->getMessage());
}

function sitemap_url(string $domain, array $entry, string $defaultDate): string
{
    $loc        = htmlspecialchars($domain . $entry['loc'], ENT_XML1, 'UTF-8');
    $lastmod    = htmlspecialchars($entry['lastmod'] ?? $defaultDate, ENT_XML1, 'UTF-8');
    $changefreq = htmlspecialchars($entry['changefreq'] ?? 'monthly', ENT_XML1, 'UTF-8');
    $priority   = htmlspecialchars($entry['priority']   ?? '0.5', ENT_XML1, 'UTF-8');
    return "  <url>\n    <loc>$loc</loc>\n    <lastmod>$lastmod</lastmod>\n    <changefreq>$changefreq</changefreq>\n    <priority>$priority</priority>\n  </url>";
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($staticPages as $page) {
    $page['lastmod'] = $today;
    echo sitemap_url($domain, $page, $today) . "\n";
}

foreach ($dynamicUrls as $url) {
    echo sitemap_url($domain, $url, $today) . "\n";
}

echo '</urlset>' . "\n";