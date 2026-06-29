<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/blog-defaults.php';

function dl_blog_e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function dl_blog_image(array $post, string $fallback = 'assets/images/blog/blog-grid-01.jpg'): string
{
    $image = trim((string) ($post['image'] ?? ''));
    if ($image === '') {
        return $fallback;
    }

    $path = parse_url($image, PHP_URL_PATH);
    if (is_string($path) && preg_match('/\.(?:avif|gif|jpe?g|png|svg|webp)$/i', $path)) {
        return $image;
    }

    if (preg_match('/^https?:\/\/(?:images\.unsplash\.com|images\.pexels\.com|cdn\.medium\.com|miro\.medium\.com|media\.licdn\.com|encrypted-tbn\d*\.gstatic\.com|lh\d*\.googleusercontent\.com)\//i', $image)) {
        return $image;
    }

    if (preg_match('/^assets\/images\/uploads\/[^?#]+\.(?:avif|gif|jpe?g|png|svg|webp)$/i', $image)) {
        return $image;
    }

    return $fallback;
}

function dl_blog_fallback_posts(): array
{
    return dl_blog_default_posts();

    return [
        [
            'id' => 'blog-ai-trends-2026',
            'slug' => 'ai-trends-2026',
            'titleAz' => '2026-da AI trendləri: nələr dəyişir?',
            'titleEn' => 'AI trends in 2026: what is changing?',
            'excerptAz' => 'Generativ AI, agentlər və avtomatlaşdırma alətlərinin analitika iş axınını necə dəyişdirdiyinə praktik baxış.',
            'excerptEn' => 'A practical look at how generative AI, agents and automation tools reshape analytics workflows.',
            'contentAz' => "AI artıq yalnız mətn yazan köməkçi deyil. 2026-da əsas dəyişiklik odur ki, alətlər məlumat toplama, təmizləmə, sorğu yazma və hesabat hazırlama mərhələlərində daha çox iş görür.\n\nData analitika komandalarında AI alətləri Excel formulalarından SQL sorğularına, dashboard ideyalarından avtomatik izahlara qədər gündəlik işin içində istifadə olunur.\n\nDatalabAcademy dərslərində mövzular real tapşırıqlar üzərində qurulur: əvvəl problemi başa düşürük, sonra datanı hazırlayırıq, daha sonra AI-dan sürətləndirici alət kimi istifadə edirik.",
            'contentEn' => "AI is no longer only a text assistant. In 2026, the main shift is that tools help more across data collection, cleaning, querying and reporting.\n\nAnalytics teams use AI in everyday work, from Excel formulas and SQL queries to dashboard ideas and automated explanations.\n\nAt DatalabAcademy, lessons are built around real tasks: understand the problem, prepare the data and then use AI as an accelerator.",
            'category' => 'AI',
            'image' => 'https://images.unsplash.com/photo-1744640326166-433469d102f2?auto=format&fit=crop&w=1400&q=85',
            'author' => 'DatalabAcademy',
            'readTimeAz' => '5 dəq oxu',
            'readTimeEn' => '5 min read',
            'publishedDate' => '2026-05-16',
            'status' => 'active',
        ],
        [
            'id' => 'blog-sql-ai',
            'slug' => 'sql-ai-sorgular',
            'titleAz' => 'SQL + AI: sorğuları necə daha tez yazmaq olar?',
            'titleEn' => 'SQL + AI: how to write queries faster',
            'excerptAz' => 'AI köməkçiləri ilə JOIN, indeks və performans mövzularında daha sürətli nəticə almağın yolları.',
            'excerptEn' => 'Ways to get faster results with AI assistants across JOINs, indexes and performance topics.',
            'contentAz' => "AI SQL yazma prosesini sürətləndirir, amma düzgün nəticə üçün əsas məntiq yenə də analitikdə qalır.\n\nYaxşı prompt vermək üçün cədvəl strukturunu, gözlənilən nəticəni və performans məhdudiyyətlərini aydın yazmaq lazımdır.\n\nDatalabAcademy-də SQL mövzuları real suallar üzərində izah edilir ki, iştirakçılar həm sorğunu, həm də nəticəni yoxlamağı öyrənsinlər.",
            'contentEn' => "AI speeds up SQL writing, but the analyst still owns the logic and validation.\n\nA good prompt should describe table structure, expected output and performance limits clearly.\n\nDatalabAcademy explains SQL through real questions so students learn to write and validate queries.",
            'category' => 'SQL + AI',
            'image' => 'https://images.unsplash.com/photo-1753998943619-b9cd910887e5?auto=format&fit=crop&w=1400&q=85',
            'author' => 'DatalabAcademy',
            'readTimeAz' => '4 dəq oxu',
            'readTimeEn' => '4 min read',
            'publishedDate' => '2026-05-20',
            'status' => 'active',
        ],
        [
            'id' => 'blog-data-ai-tools',
            'slug' => 'data-analitika-ai-aletleri',
            'titleAz' => 'Data Analitika üçün AI alətləri',
            'titleEn' => 'AI tools for data analytics',
            'excerptAz' => 'Vizualizasiya, proqnoz və hesabat avtomatlaşdırması üçün praktik AI alətlərinin qısa siyahısı.',
            'excerptEn' => 'A short list of practical AI tools for visualization, forecasting and report automation.',
            'contentAz' => "Data analitika üçün AI alətləri əsasən üç işdə kömək edir: sürətli araşdırma, hesabat mətni və avtomatlaşdırma.\n\nExcel, Power BI, SQL və Python bilikləri ilə bu alətlər daha güclü nəticə verir.\n\nƏsas məqsəd aləti kor-koranə işlətmək deyil, nəticəni data məntiqi ilə yoxlamaqdır.",
            'contentEn' => "AI tools help analytics mainly in three areas: quick exploration, report writing and automation.\n\nThey become more useful when combined with Excel, Power BI, SQL and Python skills.\n\nThe goal is not blind usage, but validating output with data logic.",
            'category' => 'Data',
            'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1400&q=85',
            'author' => 'DatalabAcademy',
            'readTimeAz' => '6 dəq oxu',
            'readTimeEn' => '6 min read',
            'publishedDate' => '2026-05-24',
            'status' => 'active',
        ],
        [
            'id' => 'blog-excel-dashboard',
            'slug' => 'excel-dashboard-prinsipleri',
            'titleAz' => 'Excel dashboard hazırlayarkən 5 əsas prinsip',
            'titleEn' => '5 key principles for building Excel dashboards',
            'excerptAz' => 'Daha oxunaqlı, sürətli və idarəolunan hesabatlar qurmaq üçün sadə dizayn və model tövsiyələri.',
            'excerptEn' => 'Simple design and model tips for readable, fast and maintainable reports.',
            'contentAz' => "Yaxşı Excel dashboard yalnız gözəl görünən hesabat deyil, qərar verməyi asanlaşdıran iş alətidir.\n\nƏsas prinsiplər: az metrik, aydın filtr, oxunaqlı rəng, düzgün model və sürətli yenilənmə.\n\nKurslarda bu yanaşmanı real biznes sualları üzərində tətbiq edirik.",
            'contentEn' => "A good Excel dashboard is not only a nice report, but a tool that supports decisions.\n\nThe key principles are fewer metrics, clear filters, readable colors, proper model structure and fast refresh.\n\nIn the course, we apply this approach to real business questions.",
            'category' => 'Excel',
            'image' => 'assets/images/blog/blog-grid-04.jpg',
            'author' => 'DatalabAcademy',
            'readTimeAz' => '5 dəq oxu',
            'readTimeEn' => '5 min read',
            'publishedDate' => '2026-05-28',
            'status' => 'active',
        ],
    ];
}

function dl_blog_ensure_storage(): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $pdo = db();
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS blog_posts (
            id VARCHAR(64) NOT NULL PRIMARY KEY,
            slug VARCHAR(160) NOT NULL UNIQUE,
            title_az VARCHAR(255) NOT NULL,
            title_en VARCHAR(255) NOT NULL DEFAULT "",
            excerpt_az TEXT NULL,
            excerpt_en TEXT NULL,
            content_az LONGTEXT NULL,
            content_en LONGTEXT NULL,
            category VARCHAR(120) NOT NULL DEFAULT "",
            image VARCHAR(500) NOT NULL DEFAULT "",
            author VARCHAR(190) NOT NULL DEFAULT "DatalabAcademy",
            read_time_az VARCHAR(40) NOT NULL DEFAULT "",
            read_time_en VARCHAR(40) NOT NULL DEFAULT "",
            published_date VARCHAR(20) NOT NULL DEFAULT "",
            status VARCHAR(32) NOT NULL DEFAULT "draft",
            seo_title VARCHAR(255) NOT NULL DEFAULT "",
            seo_description VARCHAR(500) NOT NULL DEFAULT "",
            seo_keywords VARCHAR(500) NOT NULL DEFAULT "",
            sort_order INT UNSIGNED NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_blog_status_sort (status, sort_order),
            INDEX idx_blog_slug (slug)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');
    foreach ([
        'seo_title VARCHAR(255) NOT NULL DEFAULT ""',
        'seo_description VARCHAR(500) NOT NULL DEFAULT ""',
        'seo_keywords VARCHAR(500) NOT NULL DEFAULT ""',
    ] as $definition) {
        try {
            $pdo->exec('ALTER TABLE blog_posts ADD COLUMN ' . $definition);
        } catch (Throwable $error) {
            // Existing installations already have the column.
        }
    }
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS admin_settings (
            setting_name VARCHAR(64) NOT NULL PRIMARY KEY,
            setting_value LONGTEXT NULL,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');

    $blogCount = (int) $pdo->query('SELECT COUNT(*) FROM blog_posts')->fetchColumn();
    $seedStmt = $pdo->prepare('SELECT setting_value FROM admin_settings WHERE setting_name = ? LIMIT 1');
    $seedStmt->execute(['blog_seed_version']);
    $seedRaw = $seedStmt->fetchColumn();
    $seedVersion = '';
    if (is_string($seedRaw) && $seedRaw !== '') {
        $decodedSeed = json_decode($seedRaw, true);
        $seedVersion = is_string($decodedSeed) ? $decodedSeed : $seedRaw;
    }

    if ($blogCount > 0 && $seedVersion === '2026-06-defaults') {
        return;
    }

    $stmt = $pdo->prepare('
        INSERT IGNORE INTO blog_posts
            (id, slug, title_az, title_en, excerpt_az, excerpt_en, content_az, content_en, category, image, author, read_time_az, read_time_en, published_date, status, sort_order)
        VALUES
            (:id, :slug, :title_az, :title_en, :excerpt_az, :excerpt_en, :content_az, :content_en, :category, :image, :author, :read_time_az, :read_time_en, :published_date, :status, :sort_order)
    ');
    foreach (dl_blog_fallback_posts() as $index => $post) {
        $stmt->execute([
            ':id' => (string) ($post['id'] ?? ''),
            ':slug' => (string) ($post['slug'] ?? ($post['id'] ?? '')),
            ':title_az' => (string) ($post['titleAz'] ?? ''),
            ':title_en' => (string) ($post['titleEn'] ?? ''),
            ':excerpt_az' => (string) ($post['excerptAz'] ?? ''),
            ':excerpt_en' => (string) ($post['excerptEn'] ?? ''),
            ':content_az' => (string) ($post['contentAz'] ?? ''),
            ':content_en' => (string) ($post['contentEn'] ?? ''),
            ':category' => (string) ($post['category'] ?? ''),
            ':image' => (string) ($post['image'] ?? ''),
            ':author' => (string) ($post['author'] ?? 'DatalabAcademy'),
            ':read_time_az' => (string) ($post['readTimeAz'] ?? ''),
            ':read_time_en' => (string) ($post['readTimeEn'] ?? ''),
            ':published_date' => (string) ($post['publishedDate'] ?? ''),
            ':status' => (string) ($post['status'] ?? 'active'),
            ':sort_order' => $index,
        ]);
    }
    $markStmt = $pdo->prepare('
        INSERT INTO admin_settings (setting_name, setting_value)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = CURRENT_TIMESTAMP
    ');
    $markStmt->execute(['blog_seed_version', json_encode('2026-06-defaults', JSON_UNESCAPED_UNICODE)]);
}

function dl_blog_posts(bool $activeOnly = true): array
{
    try {
        dl_blog_ensure_storage();
        $where = $activeOnly ? "WHERE status = 'active'" : '';
        $rows = db()->query("
            SELECT
                id, slug, title_az AS titleAz, title_en AS titleEn,
                excerpt_az AS excerptAz, excerpt_en AS excerptEn,
                content_az AS contentAz, content_en AS contentEn,
                category, image, author, read_time_az AS readTimeAz,
                read_time_en AS readTimeEn, published_date AS publishedDate,
                status, seo_title AS seoTitle, seo_description AS seoDescription,
                seo_keywords AS seoKeywords
            FROM blog_posts
            $where
            ORDER BY sort_order, published_date DESC, id
        ")->fetchAll();
        return $rows ?: dl_blog_fallback_posts();
    } catch (Throwable $error) {
        return dl_blog_fallback_posts();
    }
}

function dl_blog_post_by_slug(string $slug): array
{
    foreach (dl_blog_posts(true) as $post) {
        if (($post['slug'] ?? '') === $slug || ($post['id'] ?? '') === $slug) {
            return $post;
        }
    }
    $posts = dl_blog_posts(true);
    return $posts[0] ?? dl_blog_fallback_posts()[0];
}

function dl_blog_date(string $date): string
{
    if ($date === '') {
        return '';
    }
    $months = ['01' => 'Yanvar', '02' => 'Fevral', '03' => 'Mart', '04' => 'Aprel', '05' => 'May', '06' => 'İyun', '07' => 'İyul', '08' => 'Avqust', '09' => 'Sentyabr', '10' => 'Oktyabr', '11' => 'Noyabr', '12' => 'Dekabr'];
    $parts = explode('-', $date);
    if (count($parts) !== 3) {
        return $date;
    }
    return ltrim($parts[2], '0') . ' ' . ($months[$parts[1]] ?? $parts[1]) . ' ' . $parts[0];
}

function dl_blog_render_rich_blocks(string $text, string $fallback = ''): string
{
    $blocks = preg_split('/\R{2,}/', trim($text)) ?: [];
    $html = '';

    foreach ($blocks as $block) {
        $block = trim($block);
        if ($block === '') {
            continue;
        }

        if (preg_match('/^#{2,3}\s+(.+)$/u', $block, $matches)) {
            $html .= '<h3>' . dl_blog_e(trim($matches[1])) . '</h3>';
            continue;
        }

        if (str_starts_with($block, '>')) {
            $quote = trim(preg_replace('/^>\s?/m', '', $block) ?? $block);
            $html .= '<blockquote>' . nl2br(dl_blog_e($quote)) . '</blockquote>';
            continue;
        }

        $lines = preg_split('/\R/', $block) ?: [];
        $listItems = [];
        foreach ($lines as $line) {
            if (preg_match('/^\s*[-*]\s+(.+)$/u', $line, $matches)) {
                $listItems[] = trim($matches[1]);
            }
        }
        if ($listItems && count($listItems) === count(array_filter($lines, 'strlen'))) {
            $html .= '<ul>';
            foreach ($listItems as $item) {
                $html .= '<li><i class="feather-check-circle"></i><span>' . dl_blog_e($item) . '</span></li>';
            }
            $html .= '</ul>';
            continue;
        }

        $html .= '<p>' . nl2br(dl_blog_e($block)) . '</p>';
    }

    if ($html === '' && $fallback !== '') {
        return dl_blog_render_rich_blocks($fallback);
    }

    return $html;
}

function dl_blog_render_paragraphs(string $text): string
{
    $parts = preg_split('/\R{2,}/', trim($text)) ?: [];
    $html = '';
    foreach ($parts as $part) {
        $part = trim($part);
        if ($part !== '') {
            $html .= '<p data-dl-lang-block data-az="' . dl_blog_e($part) . '">' . nl2br(dl_blog_e($part)) . '</p>';
        }
    }
    return $html;
}
