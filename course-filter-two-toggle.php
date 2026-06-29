<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/datalab-menu-data.php';

$onlineCourses = dl_menu_online_courses();
$categories = [];
foreach ($onlineCourses as $course) {
    $category = trim((string) ($course['category'] ?? ''));
    if ($category !== '' && !in_array($category, $categories, true)) {
        $categories[] = $category;
    }
}
?>
<!DOCTYPE html>
<html lang="az">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Online Təlimlər | DatalabAcademy</title>
    <meta name="robots" content="index, follow">
    <meta name="description" content="DatalabAcademy online təlimləri: Data Analitika, SQL, Excel və AI üzrə video dərslər, praktiki tapşırıqlar və mentor dəstəyi.">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="shortcut icon" type="image/x-icon" href="assets/images/logo/favicon.png">
    <link rel="stylesheet" href="assets/css/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/vendor/slick.css">
    <link rel="stylesheet" href="assets/css/vendor/slick-theme.css">
    <link rel="stylesheet" href="assets/css/plugins/sal.css">
    <link rel="stylesheet" href="assets/css/plugins/feather.css">
    <link rel="stylesheet" href="assets/css/plugins/fontawesome.min.css">
    <link rel="stylesheet" href="assets/css/plugins/euclid-circulara.css">
    <link rel="stylesheet" href="assets/css/plugins/swiper.css">
    <link rel="stylesheet" href="assets/css/plugins/odometer.css">
    <link rel="stylesheet" href="assets/css/plugins/animation.css">
    <link rel="stylesheet" href="assets/css/plugins/bootstrap-select.min.css">
    <link rel="stylesheet" href="assets/css/plugins/jquery-ui.css">
    <link rel="stylesheet" href="assets/css/plugins/magnigy-popup.min.css">
    <link rel="stylesheet" href="assets/css/plugins/plyr.css">
    <link rel="stylesheet" href="assets/css/plugins/jodit.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/datalab-shared.css?v=20260627-free-1">
    <style>
        .dl-online-hero {
            padding: 115px 0 155px;
            background: linear-gradient(135deg, #eef4ff 0%, #aabaff 45%, #b25be8 100%);
        }

        .dl-online-hero .page-list,
        .dl-online-hero .description {
            color: #374151;
        }

        .dl-online-hero .title {
            color: #192335;
        }

        .dl-online-area {
            margin-top: -88px;
        }

        .dl-online-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 34px;
        }

        .dl-online-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .dl-online-tabs button,
        .dl-online-search input {
            border: 0;
            border-radius: 999px;
            background: #fff;
            box-shadow: 0 14px 45px rgba(31, 31, 44, 0.08);
        }

        .dl-online-tabs button {
            padding: 11px 22px;
            font-weight: 700;
            color: #5b6478;
        }

        .dl-online-tabs button.active {
            color: #fff;
            background: linear-gradient(90deg, #315bff, #b55dea);
        }

        .dl-online-search {
            min-width: min(100%, 360px);
            position: relative;
        }

        .dl-online-search input {
            width: 100%;
            height: 54px;
            padding: 0 48px 0 22px;
            color: #192335;
            font-weight: 600;
        }

        .dl-online-search i {
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            color: #6b7280;
        }

        .dl-online-card {
            height: 100%;
            border: 0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 24px 70px rgba(31, 31, 44, 0.09);
        }

        .dl-online-card .rbt-card-img img {
            width: 100%;
            aspect-ratio: 16 / 10;
            object-fit: cover;
            background: #0f172a;
        }

        .dl-online-empty {
            display: none;
            padding: 34px;
            border-radius: 10px;
            background: #fff;
            text-align: center;
            font-weight: 700;
            color: #6b7280;
        }

        @media (max-width: 767px) {
            .dl-online-hero {
                padding: 82px 0 120px;
            }

            .dl-online-toolbar {
                justify-content: center;
            }
        }
    </style>
</head>

<body class="rbt-header-sticky">
    <div id="my_switcher" class="my_switcher">
        <ul>
            <li><a href="javascript: void(0);" data-theme="light" class="setColor light"><img src="assets/images/about/sun-01.svg" alt="Light mode"><span title="Light Mode"> Light</span></a></li>
            <li><a href="javascript: void(0);" data-theme="dark" class="setColor dark"><img src="assets/images/about/vector.svg" alt="Dark mode"><span title="Dark Mode"> Dark</span></a></li>
        </ul>
    </div>

    <div data-datalab-header></div>

    <main>
        <section class="dl-online-hero">
            <div class="container">
                <div class="breadcrumb-inner text-center">
                    <ul class="page-list justify-content-center">
                        <li class="rbt-breadcrumb-item"><a href="index.php">Ana səhifə</a></li>
                        <li><div class="icon-right"><i class="feather-chevron-right"></i></div></li>
                        <li class="rbt-breadcrumb-item active">Online Təlimlər</li>
                    </ul>
                    <h1 class="title display-one mt--20">Online Təlimlər</h1>
                    <p class="description mt--20">Video dərslər, praktiki tapşırıqlar və mentor dəstəyi ilə öyrənməni öz tempinizdə davam etdirin.</p>
                </div>
            </div>
        </section>

        <section class="rbt-course-area bg-color-extra2 rbt-section-gap dl-online-area">
            <div class="container">
                <div class="dl-online-toolbar">
                    <div class="dl-online-tabs" role="tablist" aria-label="Online təlim kateqoriyaları">
                        <button class="active" type="button" data-online-filter="all">Hamısı</button>
                        <?php foreach ($categories as $category): ?>
                            <button type="button" data-online-filter="<?php echo dl_menu_h($category); ?>"><?php echo dl_menu_h($category); ?></button>
                        <?php endforeach; ?>
                    </div>
                    <label class="dl-online-search">
                        <span class="visually-hidden">Online təlim axtarışı</span>
                        <input type="search" data-online-search placeholder="Online təlim axtarın...">
                        <i class="feather-search"></i>
                    </label>
                </div>

                <div class="row g-5" data-online-grid>
                    <?php foreach ($onlineCourses as $course): ?>
                        <?php
                        $slug = trim((string) ($course['slug'] ?? ''));
                        $target = $slug !== '' ? 'course-details-3.php?online=' . rawurlencode($slug) : 'course-details-3.php?online=' . rawurlencode((string) ($course['id'] ?? ''));
                        $image = trim((string) ($course['image'] ?? ''));
                        if ($image === '') {
                            $image = 'assets/images/course/datalab-data-analitika.svg';
                        }
                        $title = (string) ($course['title'] ?? 'Online təlim');
                        $category = (string) ($course['category'] ?? '');
                        ?>
                        <div class="col-lg-4 col-md-6 col-12" data-online-card data-category="<?php echo dl_menu_h($category); ?>" data-title="<?php echo dl_menu_h(strtolower($title)); ?>">
                            <div class="rbt-card variation-01 rbt-hover dl-online-card">
                                <div class="rbt-card-img">
                                    <a href="<?php echo dl_menu_h($target); ?>">
                                        <img src="<?php echo dl_menu_h($image); ?>" alt="<?php echo dl_menu_h($title); ?>">
                                    </a>
                                    <?php if ((float) ($course['price'] ?? 0) <= 0): ?><span class="dl-free-ribbon"><i class="feather-gift"></i> Pulsuz</span><?php endif; ?>
                                </div>
                                <div class="rbt-card-body">
                                    <div class="rbt-card-top">
                                        <div class="rbt-review">
                                            <div class="rating">
                                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                            </div>
                                            <span class="rating-count">(<?php echo dl_menu_h($course['reviewCount'] ?? $course['review_count'] ?? '0'); ?> Rəy)</span>
                                        </div>
                                    </div>
                                    <h4 class="rbt-card-title"><a href="<?php echo dl_menu_h($target); ?>"><?php echo dl_menu_h($title); ?></a></h4>
                                    <ul class="rbt-meta">
                                        <li><i class="feather-book"></i><?php echo dl_menu_h($course['lessons'] ?? '0'); ?> Dərs</li>
                                        <li><i class="feather-users"></i><?php echo dl_menu_h($course['students'] ?? '0'); ?> Tələbə</li>
                                    </ul>
                                    <p class="rbt-card-text"><?php echo dl_menu_h($course['description'] ?? 'Praktiki online dərslərlə bacarıqlarınızı inkişaf etdirin.'); ?></p>
                                    <div class="rbt-card-bottom">
                                        <div class="rbt-price">
                                            <?php if ((float) ($course['price'] ?? 0) <= 0): ?>
                                                <span class="dl-free-badge">Pulsuz</span>
                                            <?php else: ?>
                                                <span class="current-price">$<?php echo dl_menu_h($course['price'] ?? '0'); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <a class="rbt-btn-link" href="<?php echo dl_menu_h($target); ?>">Dərsə bax <i class="feather-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="dl-online-empty" data-online-empty>Bu filtr üzrə online təlim tapılmadı.</div>
            </div>
        </section>
    </main>

    <div data-datalab-cart></div>
    <div data-datalab-footer></div>

    <script src="assets/js/vendor/modernizr.min.js"></script>
    <script src="assets/js/vendor/jquery.js"></script>
    <script src="assets/js/vendor/js.cookie.js"></script>
    <script src="assets/js/vendor/jquery.style.switcher.js"></script>
    <script src="assets/js/vendor/bootstrap.min.js"></script>
    <script src="assets/js/vendor/sal.js"></script>
    <script src="assets/js/vendor/swiper.js"></script>
    <script src="assets/js/vendor/magnify-popup.min.js"></script>
    <script src="assets/js/vendor/odometer.js"></script>
    <script src="assets/js/vendor/appear.js"></script>
    <script src="assets/js/vendor/imageloaded.js"></script>
    <script src="assets/js/vendor/wow.js"></script>
    <script src="assets/js/vendor/waypoint.min.js"></script>
    <script src="assets/js/vendor/easypie.js"></script>
    <script src="assets/js/vendor/text-type.js"></script>
    <script src="assets/js/vendor/jquery-one-page-nav.js"></script>
    <script src="assets/js/vendor/bootstrap-select.min.js"></script>
    <script src="assets/js/vendor/jquery-ui.js"></script>
    <script src="assets/js/vendor/paralax-scroll.js"></script>
    <script src="assets/js/vendor/paralax.min.js"></script>
    <script src="assets/js/vendor/countdown.js"></script>
    <script src="assets/js/vendor/plyr.js"></script>
    <script src="assets/js/vendor/jodit.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/datalab-shared.js?v=20260627-hdr-2"></script>
    <script>
        (function () {
            var activeFilter = "all";
            var searchInput = document.querySelector("[data-online-search]");
            var empty = document.querySelector("[data-online-empty]");

            function normalize(value) {
                return String(value || "").toLocaleLowerCase("az-AZ").trim();
            }

            function applyFilters() {
                var query = normalize(searchInput ? searchInput.value : "");
                var visible = 0;
                document.querySelectorAll("[data-online-card]").forEach(function (card) {
                    var category = card.getAttribute("data-category") || "";
                    var title = normalize(card.getAttribute("data-title"));
                    var matchesCategory = activeFilter === "all" || category === activeFilter;
                    var matchesSearch = !query || title.indexOf(query) !== -1;
                    var show = matchesCategory && matchesSearch;
                    card.style.display = show ? "" : "none";
                    if (show) visible += 1;
                });
                if (empty) empty.style.display = visible ? "none" : "block";
            }

            document.querySelectorAll("[data-online-filter]").forEach(function (button) {
                button.addEventListener("click", function () {
                    activeFilter = button.getAttribute("data-online-filter") || "all";
                    document.querySelectorAll("[data-online-filter]").forEach(function (item) { item.classList.remove("active"); });
                    button.classList.add("active");
                    applyFilters();
                });
            });

            if (searchInput) searchInput.addEventListener("input", applyFilters);
        })();
    </script>
</body>

</html>