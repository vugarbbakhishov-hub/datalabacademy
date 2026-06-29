<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/datalab-learning-data.php';

$course = dl_learning_online_course();
$sections = $course ? dl_learning_curriculum($course) : [];
$onlineDetails = $course ? dl_learning_online_course_details($course) : [];
$lessons = dl_learning_flat_lessons($sections);
$activeLesson = dl_learning_active_lesson($lessons);
$activeId = (string) ($activeLesson['id'] ?? '');
$activeIndex = 0;
foreach ($lessons as $index => $lesson) {
    if ((string) ($lesson['id'] ?? '') === $activeId) {
        $activeIndex = $index;
        break;
    }
}

$prevLesson = $lessons[$activeIndex - 1] ?? null;
$nextLesson = $lessons[$activeIndex + 1] ?? null;
$courseTitle = (string) ($course['title'] ?? 'Online təlim');
$lessonTitle = (string) ($activeLesson['title'] ?? 'Dərs');
$lessonType = (string) ($activeLesson['type'] ?? 'video');
$videoUrl = $activeLesson ? dl_learning_lesson_video($activeLesson) : '';
$backUrl = $course ? 'course-details-3.php?online=' . rawurlencode((string) ($course['slug'] ?? $course['id'] ?? '')) : 'course-filter-two-toggle.php';

// --- Giriş nəzarəti: ödənişli online kursun preview olmayan dərsləri yalnız aktiv enroll olanlara ---
$lessonIsPreview = !empty($activeLesson['preview']);
$coursePrice = (float) ($course['price'] ?? 0);
if ($course && $coursePrice > 0 && !$lessonIsPreview) {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'path' => '/']);
        session_start();
    }
    $gateStudentId = (int) ($_SESSION['student_user_id'] ?? 0);
    $gateCourseKey = (string) ($course['id'] ?? '');
    $gateEnrolled = false;
    if ($gateStudentId > 0 && $gateCourseKey !== '') {
        try {
            $gateStmt = db()->prepare('SELECT 1 FROM student_enrollments WHERE student_id = ? AND course_id = ? AND status = "active" LIMIT 1');
            $gateStmt->execute([$gateStudentId, $gateCourseKey]);
            $gateEnrolled = (bool) $gateStmt->fetchColumn();
        } catch (Throwable $e) {
            $gateEnrolled = false;
        }
    }
    if (!$gateEnrolled) {
        $gateSelf = 'lesson.php?online=' . rawurlencode((string) ($course['slug'] ?? $course['id'] ?? '')) . '&lesson=' . rawurlencode($activeId);
        if ($gateStudentId <= 0) {
            header('Location: login.html?redirect=' . rawurlencode($gateSelf));
        } else {
            header('Location: ' . $backUrl);
        }
        exit;
    }
}
?>
<!doctype html>
<html lang="az">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo dl_menu_h($lessonTitle); ?> | DatalabAcademy</title>
    <meta name="robots" content="noindex, follow">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.svg">
    <link rel="stylesheet" href="assets/css/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/plugins/feather.css">
    <link rel="stylesheet" href="assets/css/plugins/fontawesome.min.css">
    <link rel="stylesheet" href="assets/css/plugins/euclid-circulara.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/datalab-shared.css?v=20260627-intro-3">
    <style>
        body{background:#111827}
        .dl-lesson-shell{min-height:100vh;display:grid;grid-template-columns:390px minmax(0,1fr);background:#0f172a;color:#e5e7eb}
        .dl-lesson-sidebar{background:#111827;border-right:1px solid rgba(255,255,255,.08);height:100vh;overflow:auto}
        .dl-lesson-side-head{position:sticky;top:0;z-index:2;padding:24px 24px 18px;background:#111827;border-bottom:1px solid rgba(255,255,255,.08)}
        .dl-lesson-side-head h4{color:#fff;margin-bottom:8px;font-size:20px}
        .dl-lesson-side-head p{color:#94a3b8;margin:0;line-height:1.5}
        .dl-lesson-search{position:relative;margin-top:18px}
        .dl-lesson-search input{width:100%;height:46px;border:1px solid rgba(255,255,255,.1);border-radius:8px;background:#0b1220;color:#fff;padding:0 42px 0 14px}
        .dl-lesson-search i{position:absolute;right:15px;top:50%;transform:translateY(-50%);color:#94a3b8}
        .dl-lesson-section{border-bottom:1px solid rgba(255,255,255,.07)}
        .dl-lesson-section-title{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:18px 24px;color:#cbd5e1;font-weight:900;background:rgba(255,255,255,.025)}
        .dl-lesson-list{list-style:none;margin:0;padding:0}
        .dl-lesson-item{display:grid;grid-template-columns:28px minmax(0,1fr) auto;gap:12px;align-items:center;padding:15px 24px;color:#cbd5e1;text-decoration:none;border-top:1px solid rgba(255,255,255,.05)}
        .dl-lesson-item:hover,.dl-lesson-item.is-active{background:rgba(47,87,239,.18);color:#fff}
        .dl-lesson-item strong{display:block;color:inherit;font-size:14px;line-height:1.35}
        .dl-lesson-item small{display:block;color:#94a3b8;margin-top:4px}
        .dl-lesson-pill{display:inline-flex;align-items:center;gap:4px;border-radius:999px;background:rgba(47,87,239,.22);color:#dbeafe;font-size:11px;font-weight:900;padding:4px 8px}
        .dl-lesson-main{display:flex;flex-direction:column;min-width:0;min-height:100vh}
        .dl-lesson-top{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:18px 28px;background:#172033;border-bottom:1px solid rgba(255,255,255,.08)}
        .dl-lesson-top h1{font-size:20px;line-height:1.35;color:#fff;margin:0}
        .dl-lesson-top p{margin:4px 0 0;color:#94a3b8}
        .dl-lesson-close{width:42px;height:42px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;background:rgba(255,255,255,.08);color:#fff}
        .dl-lesson-content{padding:28px;flex:1}
        .dl-lesson-player-wrap{max-width:1180px;margin:0 auto}
        .dl-lesson-player-wrap .datalab-lesson-player{min-height:620px;border-radius:12px;box-shadow:0 30px 90px rgba(0,0,0,.38)}
        .dl-lesson-note{max-width:1180px;margin:24px auto 0;background:#fff;color:#334155;border-radius:10px;padding:24px}
        .dl-lesson-note h3{margin-bottom:8px;color:#192335}
        .dl-lesson-actions{display:flex;align-items:center;justify-content:center;gap:14px;padding:18px 28px;background:#172033;border-top:1px solid rgba(255,255,255,.08)}
        .dl-lesson-actions .rbt-btn{min-width:150px;justify-content:center}
        .dl-lesson-empty{min-height:420px;display:grid;place-items:center;background:#111827;border-radius:12px;color:#cbd5e1;text-align:center;padding:40px}
        @media (max-width:1199px){.dl-lesson-shell{grid-template-columns:330px minmax(0,1fr)}.dl-lesson-player-wrap .datalab-lesson-player{min-height:480px}}
        @media (max-width:900px){.dl-lesson-shell{grid-template-columns:1fr}.dl-lesson-sidebar{height:auto;max-height:48vh}.dl-lesson-player-wrap .datalab-lesson-player{min-height:300px}.dl-lesson-content{padding:18px}.dl-lesson-top{padding:16px 18px}}
    </style>
</head>
<body>
    <div class="dl-lesson-shell">
        <aside class="dl-lesson-sidebar">
            <div class="dl-lesson-side-head">
                <h4><?php echo dl_menu_h($courseTitle); ?></h4>
                <p><?php echo dl_menu_h((string) count($lessons)); ?> dərslik playlist</p>
                <label class="dl-lesson-search">
                    <input type="search" placeholder="Dərs axtarın" data-lesson-search>
                    <i class="feather-search"></i>
                </label>
            </div>
            <?php foreach ($sections as $section): ?>
                <div class="dl-lesson-section">
                    <div class="dl-lesson-section-title">
                        <span><?php echo dl_menu_h($section['title'] ?? 'Bölmə'); ?></span>
                        <small><?php echo dl_menu_h((string) count($section['lessons'] ?? [])); ?></small>
                    </div>
                    <ul class="dl-lesson-list">
                        <?php foreach (($section['lessons'] ?? []) as $lesson): ?>
                            <?php $lessonId = (string) ($lesson['id'] ?? ''); ?>
                            <li data-lesson-row data-title="<?php echo dl_menu_h(mb_strtolower((string) ($lesson['title'] ?? ''), 'UTF-8')); ?>">
                                <a class="dl-lesson-item<?php echo $lessonId === $activeId ? ' is-active' : ''; ?>" href="<?php echo $course ? dl_menu_h(dl_learning_lesson_url($course, $lesson)) : '#'; ?>">
                                    <i class="<?php echo dl_menu_h(dl_learning_type_icon((string) ($lesson['type'] ?? 'video'))); ?>"></i>
                                    <span>
                                        <strong><?php echo dl_menu_h($lesson['title'] ?? 'Dərs'); ?></strong>
                                        <small><?php echo dl_menu_h($lesson['duration'] ?? ''); ?></small>
                                    </span>
                                    <?php if (!empty($lesson['preview'])): ?><span class="dl-lesson-pill"><i class="feather-eye"></i>Preview</span><?php endif; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </aside>

        <main class="dl-lesson-main">
            <div class="dl-lesson-top">
                <div>
                    <h1><?php echo dl_menu_h($lessonTitle); ?></h1>
                    <p><?php echo dl_menu_h((string) ($activeLesson['_sectionTitle'] ?? $courseTitle)); ?></p>
                </div>
                <a class="dl-lesson-close" href="<?php echo dl_menu_h($backUrl); ?>" title="Kursa qayıt"><i class="feather-x"></i></a>
            </div>

            <div class="dl-lesson-content">
                <div class="dl-lesson-player-wrap">
                    <?php if ($videoUrl !== '' && $lessonType === 'video'): ?>
                        <div class="datalab-lesson-player"
                            data-video-url="<?php echo dl_menu_h($videoUrl); ?>"
                            data-video-title="<?php echo dl_menu_h($lessonTitle); ?>"
                            <?php echo !empty($onlineDetails['videoIntro']) ? 'data-video-intro="1"' : ''; ?>
                            data-video-key="<?php echo dl_menu_h('lesson:' . ($course ? dl_learning_course_key($course) : 'unknown') . ':' . $activeId); ?>">
                            <div class="datalab-player-loading">Video yüklənir...</div>
                        </div>
                    <?php else: ?>
                        <div class="dl-lesson-empty">
                            <div>
                                <i class="<?php echo dl_menu_h(dl_learning_type_icon($lessonType)); ?>" style="font-size:42px"></i>
                                <h3 class="mt--15" style="color:#fff"><?php echo dl_menu_h($lessonTitle); ?></h3>
                                <p>Bu dərs video deyil və ya video linki əlavə edilməyib.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="dl-lesson-note">
                    <h3>Dərs haqqında</h3>
                    <p><?php echo dl_menu_h((string) ($activeLesson['note'] ?? 'Bu dərsdə mövzu addım-addım izah olunur və praktiki tətbiq üçün əsas məqamlar göstərilir.')); ?></p>
                </div>
            </div>

            <div class="dl-lesson-actions">
                <?php if ($prevLesson && $course): ?>
                    <a class="rbt-btn icon-hover icon-hover-left btn-md bg-primary-opacity" href="<?php echo dl_menu_h(dl_learning_lesson_url($course, $prevLesson)); ?>">
                        <span class="btn-icon"><i class="feather-arrow-left"></i></span>
                        <span class="btn-text">Əvvəlki</span>
                    </a>
                <?php endif; ?>
                <?php if ($nextLesson && $course): ?>
                    <a class="rbt-btn icon-hover btn-md" data-lesson-next href="<?php echo dl_menu_h(dl_learning_lesson_url($course, $nextLesson)); ?>">
                        <span class="btn-text">Növbəti</span>
                        <span class="btn-icon"><i class="feather-arrow-right"></i></span>
                    </a>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="assets/js/vendor/jquery.js"></script>
    <script src="assets/js/vendor/bootstrap.min.js"></script>
    <script src="assets/js/datalab-shared.js?v=20260627-intro-2"></script>
    <script>
        (function () {
            var input = document.querySelector("[data-lesson-search]");
            if (!input) return;
            input.addEventListener("input", function () {
                var query = input.value.trim().toLocaleLowerCase("az");
                document.querySelectorAll("[data-lesson-row]").forEach(function (row) {
                    row.hidden = query && row.getAttribute("data-title").indexOf(query) === -1;
                });
            });
        }());
    </script>
    <?php if ($course && $activeId !== ''): ?>
    <script>
        // Tələbə dərs progresini bazaya yazır (admin "Dərs izləmə" panelində görünür)
        (function () {
            var COURSE = <?php echo json_encode(dl_learning_online_course_id($course)); ?>;
            var LESSON = <?php echo json_encode($activeId); ?>;
            if (!COURSE || !LESSON) return;
            var top = 25;
            function save(percent, completed, beacon) {
                var body = JSON.stringify({ courseId: COURSE, lessonId: LESSON, progressPercent: percent, completed: completed ? 1 : 0 });
                if (beacon && navigator.sendBeacon) {
                    try { navigator.sendBeacon("api/lesson.php?action=progress", new Blob([body], { type: "application/json" })); return; } catch (e) {}
                }
                fetch("api/lesson.php?action=progress", { method: "POST", headers: { "Content-Type": "application/json" }, body: body }).catch(function () {});
            }
            // dərsi açan kimi "baxılan" kimi qeyd et
            setTimeout(function () { save(top, false); }, 3000);
            // səhifədə qaldıqca irəliləyişi artır (25 → 50 → 75)
            var t = setInterval(function () { top = Math.min(75, top + 25); save(top, false); if (top >= 75) clearInterval(t); }, 45000);
            // "Növbəti" dərsə keçəndə bu dərs tamamlanmış sayılır
            var nextBtn = document.querySelector("[data-lesson-next]");
            if (nextBtn) nextBtn.addEventListener("click", function () { save(100, true, true); });
            // səhifədən çıxanda cari progresi saxla
            window.addEventListener("beforeunload", function () { save(top, false, true); });
        }());
    </script>
    <?php endif; ?>
</body>
</html>
