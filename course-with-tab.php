<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/datalab-menu-data.php';

$onlineCourses = dl_menu_online_courses();
?>
<!DOCTYPE html>
<html lang="az">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Online Təlimlər | DatalabAcademy</title>
    <meta name="robots" content="index, follow">
    <meta name="description" content="DatalabAcademy online dərsləri: Data Analitika, SQL, Excel və AI üzrə video təlimlər, praktiki tapşırıqlar və mentor dəstəyi.">
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
    <link rel="stylesheet" href="assets/css/datalab-shared.css?v=20260624-online-menu">
    <style>
        .dl-online-hero {
            padding: 110px 0 170px;
            background: linear-gradient(135deg, #eef4ff 0%, #b8c4ff 42%, #b35ce8 100%);
        }

        .dl-online-hero .breadcrumb-inner h1 {
            color: #192335;
        }

        .dl-online-course-area {
            margin-top: -95px;
        }

        .dl-online-course-card {
            height: 100%;
            border: 0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 24px 70px rgba(31, 31, 44, 0.09);
        }

        .dl-online-course-card .thumbnail img {
            width: 100%;
            aspect-ratio: 16 / 10;
            object-fit: cover;
            background: #0f172a;
        }

        .dl-online-tab {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 40px;
        }

        .dl-online-tab button {
            border: 0;
            border-radius: 999px;
            padding: 12px 24px;
            font-weight: 700;
            color: #5b6478;
            background: #fff;
            box-shadow: 0 10px 30px rgba(31, 31, 44, 0.08);
        }

        .dl-online-tab button.active {
            color: #fff;
            background: linear-gradient(90deg, #315bff, #b55dea);
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

        <section class="rbt-course-area bg-color-extra2 rbt-section-gap dl-online-course-area">
            <div class="container">
                <div class="dl-online-tab" role="tablist" aria-label="Online kurs kateqoriyaları">
                    <button class="active" type="button" data-online-filter="all">Hamısı</button>
                    <button type="button" data-online-filter="Analitika">Analitika</button>
                    <button type="button" data-online-filter="SQL">SQL</button>
                    <button type="button" data-online-filter="Excel">Excel</button>
                    <button type="button" data-online-filter="AI">AI</button>
                </div>

                <div class="row g-5">
                    <?php foreach ($onlineCourses as $course): ?>
                        <?php
                        $slug = trim((string) ($course['slug'] ?? ''));
                        $target = $slug !== '' ? 'course-details-3.php?online=' . rawurlencode($slug) : 'course-details-3.php?online=' . rawurlencode((string) ($course['id'] ?? ''));
                        $image = (string) ($course['image'] ?? '');
                        if ($image === '') {
                            $image = 'assets/images/course/datalab-data-analitika.svg';
                        }
                        ?>
                        <div class="col-lg-4 col-md-6 col-12" data-online-card data-category="<?php echo dl_menu_h($course['category'] ?? ''); ?>">
                            <div class="rbt-card variation-01 rbt-hover dl-online-course-card">
                                <div class="rbt-card-img thumbnail">
                                    <a href="<?php echo dl_menu_h($target); ?>">
                                        <img src="<?php echo dl_menu_h($image); ?>" alt="<?php echo dl_menu_h($course['title'] ?? 'Online təlim'); ?>">
                                    </a>
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
                                    <h4 class="rbt-card-title"><a href="<?php echo dl_menu_h($target); ?>"><?php echo dl_menu_h($course['title'] ?? 'Online təlim'); ?></a></h4>
                                    <ul class="rbt-meta">
                                        <li><i class="feather-book"></i><?php echo dl_menu_h($course['lessons'] ?? '0'); ?> Dərs</li>
                                        <li><i class="feather-users"></i><?php echo dl_menu_h($course['students'] ?? '0'); ?> Tələbə</li>
                                    </ul>
                                    <p class="rbt-card-text"><?php echo dl_menu_h($course['description'] ?? 'Praktiki online dərslərlə bacarıqlarınızı inkişaf etdirin.'); ?></p>
                                    <div class="rbt-card-bottom">
                                        <a class="rbt-btn-link" href="<?php echo dl_menu_h($target); ?>">Dərsə bax <i class="feather-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <div data-datalab-cart></div>
    <div data-datalab-footer></div>

    <script src="assets/js/vendor/modernizr.min.js"></script>
    <script src="assets/js/vendor/jquery.js"></script>
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
        document.querySelectorAll('[data-online-filter]').forEach(function (button) {
            button.addEventListener('click', function () {
                var filter = button.getAttribute('data-online-filter');
                document.querySelectorAll('[data-online-filter]').forEach(function (item) { item.classList.remove('active'); });
                button.classList.add('active');
                document.querySelectorAll('[data-online-card]').forEach(function (card) {
                    card.style.display = filter === 'all' || card.getAttribute('data-category') === filter ? '' : 'none';
                });
            });
        });
    </script>
</body>

</html>
