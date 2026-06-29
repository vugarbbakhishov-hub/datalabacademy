<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/datalab-learning-data.php';

$course = dl_learning_online_course();
$sections = $course ? dl_learning_curriculum($course) : [];
$onlineDetails = $course ? dl_learning_online_course_details($course) : [];
$flatLessons = dl_learning_flat_lessons($sections);
$previewLesson = null;

foreach ($flatLessons as $candidateLesson) {
    if (!empty($candidateLesson['preview']) && ($candidateLesson['type'] ?? 'video') === 'video') {
        $previewLesson = $candidateLesson;
        break;
    }
}
if (!$previewLesson) {
    foreach ($flatLessons as $candidateLesson) {
        if (($candidateLesson['type'] ?? 'video') === 'video') {
            $previewLesson = $candidateLesson;
            break;
        }
    }
}

$title = $course ? (string) ($course['title'] ?? 'Online təlim') : 'Online təlim tapılmadı';
$description = $course ? (string) ($course['description'] ?? 'Video dərslər və praktiki tapşırıqlarla öyrənməni davam etdirin.') : 'Bu online təlim tapılmadı.';
$image = trim((string) ($course['image'] ?? ''));
$image = $image !== '' ? $image : 'assets/images/course/datalab-data-analitika.svg';
$price = (float) ($course['price'] ?? 0);
$students = (int) ($course['students'] ?? 0);
$rating = (float) ($course['rating'] ?? 5);
$reviews = (int) ($course['review_count'] ?? $course['reviewCount'] ?? 0);
$category = (string) ($course['category'] ?? 'Online');
$level = (string) ($course['level'] ?? 'Başlanğıc');
$detailPreviewVideo = trim((string) ($onlineDetails['previewVideo'] ?? ''));
$previewVideo = $detailPreviewVideo !== '' ? $detailPreviewVideo : ($previewLesson ? dl_learning_lesson_video($previewLesson) : '');
?>
<!doctype html>
<html class="no-js" lang="az">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo dl_menu_h($title); ?> | DatalabAcademy</title>
    <meta name="description" content="<?php echo dl_menu_h($description); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.svg">
    <link rel="stylesheet" href="assets/css/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/plugins/feather.css">
    <link rel="stylesheet" href="assets/css/plugins/fontawesome.min.css">
    <link rel="stylesheet" href="assets/css/plugins/euclid-circulara.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/datalab-shared.css?v=20260627-intro-3">
    <style>
        .dl-online-detail-hero{padding:86px 0 92px;background:radial-gradient(circle at 78% 18%,rgba(180,91,232,.72),transparent 34%),linear-gradient(135deg,#101b31 0%,#223f96 56%,#8a48dd 100%);color:#fff}
        .dl-online-detail-hero .inner{display:grid;grid-template-columns:minmax(0,1.02fr) minmax(360px,.98fr);gap:54px;align-items:center}
        .dl-online-detail-hero .page-list,.dl-online-detail-hero .page-list a,.dl-online-detail-hero p{color:rgba(255,255,255,.86)}
        .dl-online-detail-kicker{display:inline-flex;gap:8px;align-items:center;padding:9px 16px;border-radius:999px;background:rgba(255,255,255,.14);color:#fff;font-weight:800;margin-bottom:20px}
        .dl-online-detail-title{font-size:clamp(42px,5vw,72px);line-height:1.04;margin-bottom:20px;color:#fff}
        .dl-online-detail-text{font-size:19px;line-height:1.65;max-width:760px}
        .dl-online-detail-meta{display:flex;flex-wrap:wrap;gap:12px;margin-top:30px}
        .dl-online-detail-meta span{display:inline-flex;align-items:center;gap:8px;padding:11px 15px;border-radius:10px;background:rgba(255,255,255,.12);font-weight:700;color:#fff}
        .dl-online-preview-card{padding:14px;border-radius:14px;background:rgba(255,255,255,.14);box-shadow:0 30px 80px rgba(0,0,0,.24)}
        .dl-online-preview-card .datalab-lesson-player{min-height:315px;border-radius:10px}
        .dl-online-detail-main{padding:78px 0;background:#f7f9ff}
        .dl-online-detail-grid{display:grid;grid-template-columns:minmax(0,1fr) 360px;gap:34px;align-items:start}
        .dl-online-panel{background:#fff;border:1px solid #e6e9f3;border-radius:10px;box-shadow:0 22px 60px rgba(22,34,78,.08)}
        .dl-online-panel-header{padding:28px 32px;border-bottom:1px solid #edf0f7}
        .dl-online-panel-body{padding:16px 32px 32px}
        .dl-online-section{border:1px solid #edf0f7;border-radius:10px;margin-top:16px;overflow:hidden}
        .dl-online-section-title{display:flex;align-items:center;justify-content:space-between;gap:18px;background:#f8faff;padding:18px 22px;font-weight:800}
        .dl-online-lesson{display:grid;grid-template-columns:1fr auto;gap:16px;padding:17px 22px;border-top:1px solid #edf0f7;color:#667085;text-decoration:none}
        .dl-online-lesson:hover{background:#fbfcff}
        .dl-online-lesson strong{color:#192335}
        .dl-online-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;background:#eef4ff;color:#2f57ef;font-size:12px;font-weight:800}
        .dl-online-sidebar{position:sticky;top:110px;padding:28px}
        .dl-online-price{font-size:38px;font-weight:900;color:#192335;margin-bottom:18px}
        .dl-online-sidebar .rbt-btn{width:100%;justify-content:center;margin-bottom:14px}
        .dl-online-side-row{display:flex;justify-content:space-between;gap:18px;padding:14px 0;border-bottom:1px solid #edf0f7;color:#667085}
        .dl-online-side-row strong{color:#192335}
        @media (max-width:991px){.dl-online-detail-hero .inner,.dl-online-detail-grid{grid-template-columns:1fr}.dl-online-sidebar{position:static}.dl-online-detail-hero{padding:64px 0}.dl-online-preview-card .datalab-lesson-player{min-height:260px}}
        @media (max-width:575px){.dl-online-preview-card{padding:8px}.dl-online-panel-header,.dl-online-panel-body,.dl-online-sidebar{padding:22px}.dl-online-lesson{grid-template-columns:1fr}.dl-online-detail-meta span{width:100%}}
    </style>
</head>
<body class="rbt-header-sticky">
    <div id="my_switcher" class="my_switcher">
        <ul>
            <li><a href="javascript: void(0);" data-theme="light" class="setColor light"><img src="assets/images/about/sun-01.svg" alt="Light"><span> Light</span></a></li>
            <li><a href="javascript: void(0);" data-theme="dark" class="setColor dark"><img src="assets/images/about/vector.svg" alt="Dark"><span> Dark</span></a></li>
        </ul>
    </div>

    <div data-datalab-header></div>

    <main>
        <section class="dl-online-detail-hero">
            <div class="container">
                <div class="inner">
                    <div>
                        <ul class="page-list mb--25">
                            <li class="rbt-breadcrumb-item"><a href="index.php">Ana səhifə</a></li>
                            <li><div class="icon-right"><i class="feather-chevron-right"></i></div></li>
                            <li class="rbt-breadcrumb-item"><a href="course-filter-two-toggle.php">Online təlimlər</a></li>
                        </ul>
                        <span class="dl-online-detail-kicker"><i class="feather-monitor"></i><?php echo dl_menu_h($category); ?></span>
                        <h1 class="dl-online-detail-title"><?php echo dl_menu_h($title); ?></h1>
                        <p class="dl-online-detail-text"><?php echo dl_menu_h($description); ?></p>
                        <div class="dl-online-detail-meta">
                            <span><i class="feather-book"></i><?php echo dl_menu_h((string) count($flatLessons)); ?> dərs</span>
                            <span><i class="feather-users"></i><?php echo dl_menu_h((string) $students); ?> tələbə</span>
                            <span><i class="feather-star"></i><?php echo dl_menu_h(number_format($rating, 1)); ?> (<?php echo dl_menu_h((string) $reviews); ?> rəy)</span>
                        </div>
                    </div>
                    <div class="dl-online-preview-card">
                        <?php if ($previewVideo !== ''): ?>
                            <div class="datalab-lesson-player"
                                data-video-url="<?php echo dl_menu_h($previewVideo); ?>"
                                data-video-title="<?php echo dl_menu_h((string) ($previewLesson['title'] ?? $title)); ?>"
                                <?php echo !empty($onlineDetails['videoIntro']) ? 'data-video-intro="1"' : ''; ?>
                                data-video-key="<?php echo dl_menu_h('preview:' . dl_learning_course_key($course ?? []) . ':' . (string) ($previewLesson['id'] ?? '')); ?>">
                                <div class="datalab-player-loading">Video yüklənir...</div>
                            </div>
                        <?php else: ?>
                            <img src="<?php echo dl_menu_h($image); ?>" alt="<?php echo dl_menu_h($title); ?>" style="width:100%;border-radius:10px;display:block">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="dl-online-detail-main" id="curriculum">
            <div class="container">
                <?php if (!$course): ?>
                    <div class="dl-online-panel">
                        <div class="dl-online-panel-header"><h3>Kurs tapılmadı</h3></div>
                        <div class="dl-online-panel-body"><p>Online təlim siyahısına qayıdıb aktiv proqram seçin.</p></div>
                    </div>
                <?php else: ?>
                    <div class="dl-online-detail-grid">
                        <div class="dl-online-panel">
                            <div class="dl-online-panel-header">
                                <h3>Kurs proqramı</h3>
                                <p class="mb--0">Dərsə klikləyəndə həmin kursun playlist-i ilə lesson player açılır.</p>
                            </div>
                            <div class="dl-online-panel-body">
                                <?php foreach ($sections as $sectionIndex => $section): ?>
                                    <div class="dl-online-section">
                                        <div class="dl-online-section-title">
                                            <span><?php echo dl_menu_h(($sectionIndex + 1) . '. ' . ($section['title'] ?? 'Bölmə')); ?></span>
                                            <small><?php echo dl_menu_h((string) count($section['lessons'] ?? [])); ?> dərs</small>
                                        </div>
                                        <?php foreach (($section['lessons'] ?? []) as $lesson): ?>
                                            <a class="dl-online-lesson" href="<?php echo dl_menu_h(dl_learning_lesson_url($course, $lesson)); ?>">
                                                <div>
                                                    <strong><i class="<?php echo dl_menu_h(dl_learning_type_icon((string) ($lesson['type'] ?? 'video'))); ?>"></i> <?php echo dl_menu_h($lesson['title'] ?? 'Dərs'); ?></strong>
                                                    <?php if (!empty($lesson['note'])): ?>
                                                        <div><?php echo dl_menu_h($lesson['note']); ?></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <?php if (!empty($lesson['preview'])): ?><span class="dl-online-badge"><i class="feather-eye"></i> Preview</span><?php endif; ?>
                                                    <span><?php echo dl_menu_h($lesson['duration'] ?? ''); ?></span>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <aside class="dl-online-panel dl-online-sidebar">
                            <div class="dl-online-price"><?php echo $price > 0 ? '$' . dl_menu_h(number_format($price, 2)) : '<span class="dl-free-badge">Pulsuz</span>'; ?></div>
                            <div data-shop-actions
                                 data-course-id="<?php echo dl_menu_h(dl_learning_online_course_id($course)); ?>"
                                 data-first-lesson="<?php echo $flatLessons ? dl_menu_h(dl_learning_lesson_url($course, $flatLessons[0])) : ''; ?>"></div>
                            <div class="dl-online-side-row"><span>Səviyyə</span><strong><?php echo dl_menu_h($level); ?></strong></div>
                            <div class="dl-online-side-row"><span>Dərslər</span><strong><?php echo dl_menu_h((string) count($flatLessons)); ?></strong></div>
                            <div class="dl-online-side-row"><span>Pulsuz preview</span><strong><?php echo dl_menu_h((string) count(array_filter($flatLessons, static fn(array $lesson): bool => !empty($lesson['preview'])))); ?></strong></div>
                            <div class="dl-online-side-row"><span>Dil</span><strong>Azərbaycan</strong></div>
                        </aside>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <div data-datalab-footer></div>

    <style>
        .dl-shop-hint{font-size:13px;color:#64748b;margin-top:10px;line-height:1.5}
        [data-shop-actions] .rbt-btn{margin-bottom:6px}
        .mt--10{margin-top:10px}
    </style>
    <script src="assets/js/vendor/jquery.js"></script>
    <script src="assets/js/vendor/js.cookie.js"></script>
    <script src="assets/js/vendor/jquery.style.switcher.js"></script>
    <script src="assets/js/vendor/bootstrap.min.js"></script>
    <script src="assets/js/datalab-shared.js?v=20260627-intro-2"></script>
    <script src="assets/js/datalab-shop.js?v=20260627-shop-3"></script>
</body>
</html>
