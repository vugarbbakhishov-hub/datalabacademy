<?php
declare(strict_types=1);

require_once __DIR__ . '/api/config.php';
require_once __DIR__ . '/includes/datalab-menu-data.php';

// Əyani (offline) kursu courses cədvəlindən id/course parametri ilə oxu
$wanted = trim((string) ($_GET['course'] ?? $_GET['id'] ?? ''));
$course = null;
if ($wanted !== '') {
    try {
        $stmt = db()->prepare("SELECT id, title, category, price, lessons, students, rating, review_count AS reviewCount, image, description
                               FROM courses WHERE id = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$wanted]);
        $course = $stmt->fetch() ?: null;
    } catch (Throwable $e) {
        $course = null;
    }
}

$title = $course ? (string) ($course['title'] ?? 'Kurs') : 'Kurs tapılmadı';
$category = $course ? (string) ($course['category'] ?? 'Əyani təlim') : '';
$description = $course ? (string) ($course['description'] ?? 'Praktiki, əyani təlimlə bacarıqlarını inkişaf etdir.') : 'Axtardığınız əyani kurs tapılmadı.';
$image = $course ? trim((string) ($course['image'] ?? '')) : '';
$image = $image !== '' ? $image : 'assets/images/course/datalab-data-analitika.svg';
$price = $course ? (float) ($course['price'] ?? 0) : 0;
$lessons = $course ? (int) ($course['lessons'] ?? 0) : 0;
$students = $course ? (int) ($course['students'] ?? 0) : 0;
$rating = $course ? (float) ($course['rating'] ?? 5) : 5;
$reviews = $course ? (int) ($course['reviewCount'] ?? 0) : 0;
$courseId = $course ? (string) ($course['id'] ?? '') : '';
?>
<!DOCTYPE html>
<html lang="az">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo dl_menu_h($title); ?> | DatalabAcademy</title>
    <meta name="robots" content="index, follow">
    <meta name="description" content="<?php echo dl_menu_h(mb_substr($description, 0, 160)); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/logo/favicon.png">
    <link rel="stylesheet" href="assets/css/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/plugins/feather.css">
    <link rel="stylesheet" href="assets/css/plugins/fontawesome.min.css">
    <link rel="stylesheet" href="assets/css/plugins/euclid-circulara.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/datalab-shared.css?v=20260627-intro-3">
    <style>
        .dl-od4-hero { padding: 110px 0 150px; background: linear-gradient(135deg, #16264f 0%, #315df5 55%, #9b51e0 100%); color: #fff; }
        .dl-od4-hero .page-list a, .dl-od4-hero .page-list li { color: rgba(255, 255, 255, .85); }
        .dl-od4-badge { display: inline-flex; align-items: center; gap: 7px; background: rgba(255, 255, 255, .15); padding: 7px 15px; border-radius: 30px; font-weight: 700; font-size: 13px; }
        .dl-od4-title { font-size: clamp(34px, 5vw, 56px); font-weight: 900; line-height: 1.05; margin: 18px 0 14px; }
        .dl-od4-desc { color: rgba(255, 255, 255, .9); max-width: 620px; }
        .dl-od4-stats { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 26px; }
        .dl-od4-stat { background: rgba(255, 255, 255, .12); border-radius: 12px; padding: 10px 16px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; }
        .dl-od4-area { margin-top: -90px; padding-bottom: 90px; }
        .dl-od4-grid { display: grid; grid-template-columns: 1fr 360px; gap: 24px; align-items: start; }
        .dl-od4-card { background: #fff; border-radius: 16px; box-shadow: 0 22px 60px rgba(31, 31, 44, .09); padding: 30px; }
        .dl-od4-card h3 { margin-top: 0; }
        .dl-od4-cover img { width: 100%; border-radius: 14px; display: block; margin-bottom: 22px; background: #eef1f7; }
        .dl-od4-price { font-size: 38px; font-weight: 900; color: #192335; margin-bottom: 16px; }
        .dl-od4-free { display: inline-block; background: #16a34a; color: #fff; font-weight: 800; padding: 6px 16px; border-radius: 10px; font-size: 22px; }
        .dl-od4-side-row { display: flex; justify-content: space-between; padding: 11px 0; border-top: 1px solid #eef1f7; color: #5b6478; }
        .dl-od4-form label { display: block; margin-bottom: 12px; font-weight: 600; font-size: 14px; color: #35405a; }
        .dl-od4-form input { width: 100%; margin-top: 6px; padding: 11px 13px; border: 1px solid #e4e9f3; border-radius: 10px; font: inherit; }
        .dl-od4-msg { margin-top: 10px; font-weight: 600; }
        .active-dark-mode .dl-od4-card { background: #16213a; color: #e7ecf6; box-shadow: 0 22px 60px rgba(0, 0, 0, .4); }
        .active-dark-mode .dl-od4-price, .active-dark-mode .dl-od4-card h3 { color: #f3f6fc; }
        .active-dark-mode .dl-od4-form input { background: #0f1828; color: #f3f6fc; border-color: #283449; }
        @media (max-width: 991px) { .dl-od4-grid { grid-template-columns: 1fr; } }
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
        <section class="dl-od4-hero">
            <div class="container">
                <ul class="page-list">
                    <li class="rbt-breadcrumb-item"><a href="index.php">Ana səhifə</a></li>
                    <li><span class="icon-right"><i class="feather-chevron-right"></i></span></li>
                    <li class="rbt-breadcrumb-item"><a href="course-filter-one-open.html">Kurslar</a></li>
                    <li><span class="icon-right"><i class="feather-chevron-right"></i></span></li>
                    <li class="rbt-breadcrumb-item active"><?php echo dl_menu_h($title); ?></li>
                </ul>
                <?php if ($course): ?>
                    <span class="dl-od4-badge mt--20"><i class="feather-map-pin"></i> ƏYANİ təlim<?php echo $category !== '' ? ' · ' . dl_menu_h($category) : ''; ?></span>
                    <h1 class="dl-od4-title"><?php echo dl_menu_h($title); ?></h1>
                    <p class="dl-od4-desc"><?php echo dl_menu_h($description); ?></p>
                    <div class="dl-od4-stats">
                        <span class="dl-od4-stat"><i class="feather-book"></i> <?php echo $lessons; ?> dərs</span>
                        <span class="dl-od4-stat"><i class="feather-users"></i> <?php echo $students; ?> tələbə</span>
                        <span class="dl-od4-stat"><i class="feather-star"></i> <?php echo rtrim(rtrim(number_format($rating, 1), '0'), '.'); ?> (<?php echo $reviews; ?> rəy)</span>
                    </div>
                <?php else: ?>
                    <h1 class="dl-od4-title mt--20">Kurs tapılmadı</h1>
                    <p class="dl-od4-desc">Axtardığınız əyani kurs mövcud deyil. <a href="course-filter-one-open.html" style="color:#fff;text-decoration:underline">Bütün kurslara baxın</a>.</p>
                <?php endif; ?>
            </div>
        </section>

        <?php if ($course): ?>
        <section class="dl-od4-area">
            <div class="container">
                <div class="dl-od4-grid">
                    <div class="dl-od4-card">
                        <div class="dl-od4-cover"><img src="<?php echo dl_menu_h($image); ?>" alt="<?php echo dl_menu_h($title); ?>"></div>
                        <h3>Bu kurs haqqında</h3>
                        <p style="color:#5b6478;line-height:1.7"><?php echo nl2br(dl_menu_h($description)); ?></p>
                        <p style="color:#5b6478;line-height:1.7">Bu, <b>əyani (offline)</b> təlimdir — təcrübəli mentorla canlı dərslər, praktiki tapşırıqlar və komanda işi. Yer məhduddur; aşağıdakı formu doldurun, sizinlə əlaqə saxlayaq.</p>
                    </div>

                    <aside class="dl-od4-card">
                        <div class="dl-od4-price">
                            <?php echo $price > 0 ? '$' . dl_menu_h(number_format($price, 2)) : '<span class="dl-od4-free">Pulsuz</span>'; ?>
                        </div>
                        <h4 style="margin:0 0 4px">Müraciət et</h4>
                        <p style="color:#8a93a6;font-size:13px;margin-bottom:14px">Ad və nömrənizi yazın — geri zəng edək.</p>
                        <form class="dl-od4-form" data-od4-form>
                            <label>Ad və soyad <input type="text" name="name" required></label>
                            <label>Telefon <input type="tel" name="phone" required></label>
                            <label>E-poçt <input type="email" name="email"></label>
                            <button class="rbt-btn btn-gradient w-100" type="submit">Müraciəti göndər</button>
                            <p class="dl-od4-msg" data-od4-msg></p>
                        </form>
                        <div class="dl-od4-side-row"><span>Format</span><strong>Əyani / Offline</strong></div>
                        <div class="dl-od4-side-row"><span>Dərslər</span><strong><?php echo $lessons; ?></strong></div>
                        <div class="dl-od4-side-row"><span>Dil</span><strong>Azərbaycan</strong></div>
                    </aside>
                </div>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <div data-datalab-cart></div>
    <div data-datalab-footer></div>

    <script src="assets/js/vendor/modernizr.min.js"></script>
    <script src="assets/js/vendor/jquery.js"></script>
    <script src="assets/js/vendor/js.cookie.js"></script>
    <script src="assets/js/vendor/jquery.style.switcher.js"></script>
    <script src="assets/js/vendor/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/datalab-shared.js?v=20260627-intro-3"></script>
    <script>
        (function () {
            var form = document.querySelector("[data-od4-form]");
            if (!form) return;
            var COURSE = <?php echo json_encode($title); ?>;
            form.addEventListener("submit", function (e) {
                e.preventDefault();
                var msg = form.querySelector("[data-od4-msg]");
                var btn = form.querySelector("button[type=submit]");
                var name = form.elements.name.value.trim();
                var phone = form.elements.phone.value.trim();
                if (name.length < 2 || phone.length < 4) { msg.style.color = "#dc2626"; msg.textContent = "Ad və telefon düzgün doldurulmalıdır."; return; }
                btn.disabled = true; msg.style.color = ""; msg.textContent = "Göndərilir...";
                fetch("api/admin.php?action=submit-lead", {
                    method: "POST", headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ name: name, phone: phone, email: form.elements.email.value.trim(), interest: COURSE, source: "course-details-4 (əyani)" })
                }).then(function (r) { return r.json(); }).then(function (p) {
                    msg.style.color = p.ok ? "#16a34a" : "#dc2626";
                    msg.textContent = p.message || (p.ok ? "Müraciətiniz qəbul olundu!" : "Xəta.");
                    if (p.ok) form.reset();
                }).catch(function () { msg.style.color = "#dc2626"; msg.textContent = "Şəbəkə xətası."; })
                  .finally(function () { btn.disabled = false; });
            });
        })();
    </script>
</body>

</html>
