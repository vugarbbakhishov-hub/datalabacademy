<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="az">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Səbət | DatalabAcademy</title>
    <meta name="robots" content="noindex, follow">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/logo/favicon.png">
    <link rel="stylesheet" href="assets/css/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/plugins/feather.css">
    <link rel="stylesheet" href="assets/css/plugins/fontawesome.min.css">
    <link rel="stylesheet" href="assets/css/plugins/euclid-circulara.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/datalab-shared.css?v=20260627-shop-1">
    <style>
        .dl-shop-hero { padding: 90px 0 120px; background: linear-gradient(135deg, #16264f 0%, #315df5 55%, #9b51e0 100%); }
        .dl-shop-hero .title { color: #fff; }
        .dl-shop-hero .desc { color: rgba(255,255,255,.85); }
        .dl-shop-wrap { margin-top: -70px; padding-bottom: 90px; }
        .dl-shop-grid { display: grid; grid-template-columns: 1fr 360px; gap: 24px; align-items: start; }
        .dl-shop-card { background: #fff; border-radius: 16px; box-shadow: 0 22px 60px rgba(31,31,44,.09); padding: 26px; }
        .dl-cart-item { display: flex; gap: 16px; align-items: center; padding: 14px 0; border-bottom: 1px solid #eef1f7; }
        .dl-cart-item:last-child { border-bottom: 0; }
        .dl-cart-item img { width: 84px; height: 60px; object-fit: cover; border-radius: 10px; background: #eef1f7; flex: 0 0 auto; }
        .dl-cart-item h6 { margin: 0 0 3px; font-size: 15px; }
        .dl-cart-item .cat { color: #8a93a6; font-size: 13px; }
        .dl-cart-item .price { font-weight: 800; color: #192335; margin-left: auto; white-space: nowrap; }
        .dl-cart-item .rm { background: none; border: 0; color: #e2516a; cursor: pointer; font-size: 18px; padding: 4px; }
        .dl-sum-row { display: flex; justify-content: space-between; margin-bottom: 10px; color: #5b6478; }
        .dl-sum-total { display: flex; justify-content: space-between; font-size: 22px; font-weight: 900; color: #192335; border-top: 1px solid #eef1f7; padding-top: 14px; margin-top: 6px; }
        .dl-empty { text-align: center; padding: 40px 10px; color: #64748b; }
        .active-dark-mode .dl-shop-card { background: #16213a; color: #e7ecf6; box-shadow: 0 22px 60px rgba(0,0,0,.4); }
        .active-dark-mode .dl-cart-item { border-color: #283449; }
        .active-dark-mode .dl-cart-item h6, .active-dark-mode .dl-cart-item .price, .active-dark-mode .dl-sum-total { color: #f3f6fc; }
        @media (max-width: 991px) { .dl-shop-grid { grid-template-columns: 1fr; } }
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
        <section class="dl-shop-hero">
            <div class="container">
                <div class="breadcrumb-inner text-center">
                    <ul class="page-list justify-content-center">
                        <li class="rbt-breadcrumb-item"><a href="index.php">Ana səhifə</a></li>
                        <li><div class="icon-right"><i class="feather-chevron-right"></i></div></li>
                        <li class="rbt-breadcrumb-item active">Səbət</li>
                    </ul>
                    <h1 class="title display-one mt--20" style="color:#fff">Səbətim</h1>
                    <p class="desc mt--10">Seçdiyiniz onlayn təlimləri yoxlayın və ödənişə keçin.</p>
                </div>
            </div>
        </section>

        <section class="dl-shop-wrap">
            <div class="container">
                <div class="dl-shop-grid">
                    <div class="dl-shop-card" data-cart-list><div class="dl-empty">Yüklənir…</div></div>
                    <div class="dl-shop-card" data-cart-summary></div>
                </div>
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
    <script src="assets/js/main.js"></script>
    <script src="assets/js/datalab-shared.js?v=20260627-hdr-2"></script>
    <script src="assets/js/datalab-shop.js?v=20260627-shop-3"></script>
    <script>
        (function () {
            var API = "api/shop.php";
            var listEl = document.querySelector("[data-cart-list]");
            var sumEl = document.querySelector("[data-cart-summary]");
            function esc(s){var d=document.createElement("div");d.textContent=s==null?"":String(s);return d.innerHTML;}

            function render(p) {
                if (!p.ok) {
                    if (p.auth === false) { location.href = "login.html?redirect=cart.php"; return; }
                    listEl.innerHTML = '<div class="dl-empty">' + esc(p.message || "Xəta") + '</div>'; return;
                }
                if (!p.items.length) {
                    listEl.innerHTML = '<div class="dl-empty"><i class="feather-shopping-cart" style="font-size:42px;color:#cbd5e1"></i><p style="margin-top:12px">Səbətiniz boşdur.</p><a class="rbt-btn btn-gradient btn-sm mt--10" href="course-filter-two-toggle.php">Onlayn təlimlərə bax</a></div>';
                    sumEl.innerHTML = ""; return;
                }
                listEl.innerHTML = p.items.map(function (it) {
                    return '<div class="dl-cart-item">'
                        + '<img src="' + esc(it.image || "assets/images/course/datalab-data-analitika.svg") + '" alt="">'
                        + '<div><h6>' + esc(it.title) + '</h6><div class="cat">' + esc(it.category || "Online") + ' • ' + (it.lessons || 0) + ' dərs</div></div>'
                        + '<span class="price">$' + Number(it.price).toFixed(2) + '</span>'
                        + '<button class="rm" title="Sil" data-rm="' + esc(it.courseId) + '"><i class="feather-trash-2"></i></button>'
                        + '</div>';
                }).join("");
                sumEl.innerHTML = '<h5 style="margin-top:0">Yekun</h5>'
                    + '<div class="dl-sum-row"><span>Kurs sayı</span><span>' + p.count + '</span></div>'
                    + '<div class="dl-sum-total"><span>Cəmi</span><span>$' + Number(p.total).toFixed(2) + '</span></div>'
                    + '<a class="rbt-btn btn-gradient w-100 mt--20 d-block text-center" href="checkout.php">Ödənişə keç</a>'
                    + '<a class="rbt-btn btn-border btn-sm w-100 mt--10 d-block text-center" href="course-filter-two-toggle.php">Alış-verişə davam et</a>';

                listEl.querySelectorAll("[data-rm]").forEach(function (b) {
                    b.addEventListener("click", function () {
                        b.disabled = true;
                        fetch(API + "?action=cart-remove", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify({ courseId: b.getAttribute("data-rm") }) })
                            .then(function (r) { return r.json(); }).then(function (res) { if (res.ok) { render(res); if (window.DLShopRefreshCart) DLShopRefreshCart(); } });
                    });
                });
            }

            fetch(API + "?action=cart", { headers: { "Accept": "application/json" } })
                .then(function (r) { return r.json(); }).then(render)
                .catch(function () { listEl.innerHTML = '<div class="dl-empty">Şəbəkə xətası.</div>'; });
        })();
    </script>
</body>

</html>
