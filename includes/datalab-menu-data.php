<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/config.php';

function dl_menu_h(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function dl_menu_course_icon(string $category): string
{
    $category = mb_strtolower($category, 'UTF-8');

    if (str_contains($category, 'sql') || str_contains($category, 'data')) {
        return 'feather-database';
    }
    if (str_contains($category, 'analitika') || str_contains($category, 'analytics')) {
        return 'feather-bar-chart-2';
    }
    if (str_contains($category, 'ofis') || str_contains($category, 'office') || str_contains($category, 'excel')) {
        return 'feather-grid';
    }
    if (str_contains($category, 'ai') || str_contains($category, 'süni')) {
        return 'feather-cpu';
    }

    return 'feather-book-open';
}

function dl_menu_fallback_courses(): array
{
    return [
        ['id' => '1', 'title' => 'Data Analitika', 'category' => 'Analitika'],
        ['id' => '2', 'title' => 'SQL Developer', 'category' => 'Data'],
        ['id' => '3', 'title' => 'Excel', 'category' => 'Ofis'],
        ['id' => '5', 'title' => 'İnteraktiv AI Təcrübəsi', 'category' => 'AI'],
    ];
}

function dl_menu_fallback_online_courses(): array
{
    return [
        ['id' => '1', 'title' => 'Data Analitika Online', 'slug' => 'data-analitika-online', 'category' => 'Analitika'],
        ['id' => '2', 'title' => 'SQL Praktiki Dərslər', 'slug' => 'sql-praktiki-dersler', 'category' => 'SQL'],
        ['id' => '3', 'title' => 'Excel Dashboard Təlimi', 'slug' => 'excel-dashboard-telimi', 'category' => 'Excel'],
    ];
}

function dl_menu_courses(): array
{
    try {
        $rows = db()->query("SELECT id, title, category FROM courses WHERE status = 'active' ORDER BY sort_order, id")->fetchAll();
        return $rows ?: dl_menu_fallback_courses();
    } catch (Throwable) {
        return dl_menu_fallback_courses();
    }
}

function dl_menu_online_courses(): array
{
    try {
        $rows = db()->query("SELECT id, title, slug, category, price, lessons, students, rating, review_count, image, description, level FROM online_courses WHERE status = 'active' ORDER BY sort_order, id")->fetchAll();
        return $rows ?: dl_menu_fallback_online_courses();
    } catch (Throwable) {
        return dl_menu_fallback_online_courses();
    }
}

function dl_render_course_submenu(array $courses, array $onlineCourses): string
{
    $html = '';

    foreach ($courses as $course) {
        $id = rawurlencode((string) ($course['id'] ?? ''));
        $title = dl_menu_h($course['title'] ?? 'Kurs');
        $icon = dl_menu_h(dl_menu_course_icon((string) ($course['category'] ?? '')));
        $html .= '<li><a href="course-details-4.php?id=' . $id . '"><i class="' . $icon . '"></i> ' . $title . '</a></li>';
    }

    $html .= '<li class="has-dropdown has-menu-child-item dl-online-menu-item">';
    $html .= '<a href="course-filter-two-toggle.php"><i class="feather-monitor"></i> <span data-i18n="nav_online_training">Online təlimlər</span> <i class="feather-chevron-right dl-online-submenu-arrow"></i></a>';
    $html .= '<ul class="submenu dl-online-submenu">';

    foreach ($onlineCourses as $course) {
        $slug = trim((string) ($course['slug'] ?? ''));
        $target = $slug !== ''
            ? 'course-details-3.php?online=' . rawurlencode($slug)
            : 'course-details-3.php?online=' . rawurlencode((string) ($course['id'] ?? ''));
        $html .= '<li><a href="' . dl_menu_h($target) . '"><i class="feather-play-circle"></i> ' . dl_menu_h($course['title'] ?? 'Online təlim') . '</a></li>';
    }

    if (!$onlineCourses) {
        $html .= '<li><a href="course-filter-two-toggle.php"><i class="feather-clock"></i> Tezliklə</a></li>';
    }

    $html .= '</ul></li>';

    return $html;
}
