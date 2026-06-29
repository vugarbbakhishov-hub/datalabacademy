<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/datalab-home-data.php';

$dlContact = dl_home_contact();

// Xəritə embed-ini təhlükəsizləşdir: yalnız Google Maps domenlərinə icazə (stored-XSS qarşısı).
$dlMapHtml = (static function (string $embed): string {
    $embed = trim($embed);
    if ($embed === '') {
        return '';
    }
    if (preg_match('/src\s*=\s*["\\\']([^"\\\']+)["\\\']/i', $embed, $m)) {
        $url = $m[1];
    } elseif (preg_match('#^https?://#i', $embed)) {
        $url = $embed;
    } else {
        return '';
    }
    $host = strtolower((string) parse_url($url, PHP_URL_HOST));
    $allowed = ['www.google.com', 'google.com', 'maps.google.com', 'www.google.az', 'google.az', 'maps.app.goo.gl'];
    if ($host === '' || !in_array($host, $allowed, true)) {
        return '';
    }
    return '<iframe src="' . htmlspecialchars($url, ENT_QUOTES) . '" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen style="width:100%;height:100%;min-height:320px;border:0;border-radius:14px"></iframe>';
})((string) ($dlContact['mapEmbed'] ?? ''));
?>
<!DOCTYPE html>
<html lang="az">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Əlaqə | DatalabAcademy</title>
    <meta name="robots" content="index, follow">
    <meta name="description" content="DatalabAcademy ilə əlaqə: telefon, e-poçt, ünvan və müraciət formu.">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="shortcut icon" type="image/x-icon" href="assets/images/logo/favicon.png">
    <link rel="stylesheet" href="assets/css/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/plugins/feather.css">
    <link rel="stylesheet" href="assets/css/plugins/fontawesome.min.css">
    <link rel="stylesheet" href="assets/css/plugins/euclid-circulara.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/datalab-shared.css?v=20260626-contact-1">
    <style>
        .dl-contact-hero { padding: 110px 0 150px; background: linear-gradient(135deg, #16264f 0%, #315df5 55%, #9b51e0 100%); }
        .dl-contact-hero .page-list a, .dl-contact-hero .page-list li { color: rgba(255, 255, 255, .85); }
        .dl-contact-hero .title { color: #fff; }
        .dl-contact-hero .description { color: rgba(255, 255, 255, .88); max-width: 620px; margin-inline: auto; }
        .dl-contact-area { margin-top: -90px; padding-bottom: 90px; }
        .dl-contact-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 40px; }
        .dl-contact-card { display: flex; gap: 16px; align-items: flex-start; padding: 24px; border-radius: 16px; background: #fff; box-shadow: 0 22px 60px rgba(31, 31, 44, .09); }
        .dl-contact-card .ico { width: 52px; height: 52px; flex: 0 0 auto; display: grid; place-items: center; border-radius: 14px; color: #fff; background: linear-gradient(135deg, #315df5, #9b51e0); font-size: 22px; }
        .dl-contact-card h5 { margin: 0 0 4px; font-size: 16px; }
        .dl-contact-card a, .dl-contact-card p { color: #5b6478; margin: 0; word-break: break-word; }
        .dl-contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: stretch; }
        .dl-contact-box { padding: 30px; border-radius: 16px; background: #fff; box-shadow: 0 22px 60px rgba(31, 31, 44, .09); }
        .dl-contact-box h3 { margin-top: 0; }
        .dl-contact-form label { display: block; margin-bottom: 14px; font-weight: 600; font-size: 14px; color: #35405a; }
        .dl-contact-form input, .dl-contact-form textarea { width: 100%; margin-top: 6px; padding: 12px 14px; border: 1px solid #e4e9f3; border-radius: 10px; font: inherit; outline: none; }
        .dl-contact-form input:focus, .dl-contact-form textarea:focus { border-color: #315df5; box-shadow: 0 0 0 3px rgba(49, 93, 245, .12); }
        .dl-contact-msg { margin: 10px 0 0; font-weight: 600; }
        .dl-contact-map iframe, .dl-contact-map img { width: 100%; height: 100%; min-height: 320px; border: 0; border-radius: 14px; display: block; }
        .dl-contact-socials { display: flex; gap: 12px; margin-top: 18px; }
        .dl-contact-socials a { width: 42px; height: 42px; display: grid; place-items: center; border-radius: 12px; color: #315df5; background: rgba(49, 93, 245, .1); }
        .active-dark-mode .dl-contact-card, .active-dark-mode .dl-contact-box { background: #16213a; color: #e7ecf6; box-shadow: 0 22px 60px rgba(0, 0, 0, .4); }
        .active-dark-mode .dl-contact-card a, .active-dark-mode .dl-contact-card p, .active-dark-mode .dl-contact-form label { color: #aeb9cd; }
        .active-dark-mode .dl-contact-form input, .active-dark-mode .dl-contact-form textarea { background: #0f1828; color: #f3f6fc; border-color: #283449; }
        @media (max-width: 991px) { .dl-contact-cards { grid-template-columns: 1fr; } .dl-contact-grid { grid-template-columns: 1fr; } }
    </style>
</head>

<body class="rbt-header-sticky">
    <div id="my_switcher" class="my_switcher">
        <ul>
            <li><a href="javascript: void(0);" data-theme="light" class="setColor light"><img src="assets/images/about/sun-01.svg" alt="Light"><span title="Light Mode"> Light</span></a></li>
            <li><a href="javascript: void(0);" data-theme="dark" class="setColor dark"><img src="assets/images/about/vector.svg" alt="Dark"><span title="Dark Mode"> Dark</span></a></li>
        </ul>
    </div>

    <div data-datalab-header></div>

    <main>
        <section class="dl-contact-hero">
            <div class="container">
                <div class="breadcrumb-inner text-center">
                    <ul class="page-list justify-content-center">
                        <li class="rbt-breadcrumb-item"><a href="index.php">Ana səhifə</a></li>
                        <li><div class="icon-right"><i class="feather-chevron-right"></i></div></li>
                        <li class="rbt-breadcrumb-item active">Əlaqə</li>
                    </ul>
                    <h1 class="title display-one mt--20">Bizimlə əlaqə</h1>
                    <p class="description mt--20"><?php echo dl_home_e($dlContact['pageSubtitle']); ?></p>
                </div>
            </div>
        </section>

        <section class="rbt-section-gap bg-color-extra2 dl-contact-area">
            <div class="container">
                <div class="dl-contact-cards">
                    <div class="dl-contact-card">
                        <span class="ico"><i class="feather-phone"></i></span>
                        <div><h5>Telefon</h5><a href="tel:<?php echo dl_home_e(preg_replace('/[^0-9+]/', '', $dlContact['phone'])); ?>"><?php echo dl_home_e($dlContact['phone']); ?></a></div>
                    </div>
                    <div class="dl-contact-card">
                        <span class="ico"><i class="feather-mail"></i></span>
                        <div><h5>E-poçt</h5><a href="mailto:<?php echo dl_home_e($dlContact['email']); ?>"><?php echo dl_home_e($dlContact['email']); ?></a></div>
                    </div>
                    <div class="dl-contact-card">
                        <span class="ico"><i class="feather-map-pin"></i></span>
                        <div><h5>Ünvan</h5><p><?php echo dl_home_e($dlContact['address']); ?></p>
                        <?php if (!empty($dlContact['workingHours'])): ?><p style="margin-top:4px"><i class="feather-clock"></i> <?php echo dl_home_e($dlContact['workingHours']); ?></p><?php endif; ?></div>
                    </div>
                </div>

                <div class="dl-contact-grid">
                    <div class="dl-contact-box">
                        <h3>Müraciət göndərin</h3>
                        <p class="text-muted">Adınızı və mesajınızı yazın, tezliklə sizinlə əlaqə saxlayacağıq.</p>
                        <form class="dl-contact-form" data-contact-submit>
                            <label>Ad və soyad <input type="text" name="name" required></label>
                            <label>E-poçt <input type="email" name="email"></label>
                            <label>Telefon <input type="tel" name="phone"></label>
                            <label>Mesaj <textarea name="message" rows="4" required></textarea></label>
                            <button class="rbt-btn btn-gradient" type="submit">Göndər</button>
                            <p class="dl-contact-msg" data-contact-result></p>
                        </form>
                        <div class="dl-contact-socials">
                            <?php if (!empty($dlContact['facebook']) && $dlContact['facebook'] !== '#'): ?><a href="<?php echo dl_home_e($dlContact['facebook']); ?>" target="_blank" rel="noopener"><i class="feather-facebook"></i></a><?php endif; ?>
                            <?php if (!empty($dlContact['instagram']) && $dlContact['instagram'] !== '#'): ?><a href="<?php echo dl_home_e($dlContact['instagram']); ?>" target="_blank" rel="noopener"><i class="feather-instagram"></i></a><?php endif; ?>
                            <?php if (!empty($dlContact['linkedin']) && $dlContact['linkedin'] !== '#'): ?><a href="<?php echo dl_home_e($dlContact['linkedin']); ?>" target="_blank" rel="noopener"><i class="feather-linkedin"></i></a><?php endif; ?>
                            <?php if (!empty($dlContact['twitter']) && $dlContact['twitter'] !== '#'): ?><a href="<?php echo dl_home_e($dlContact['twitter']); ?>" target="_blank" rel="noopener"><i class="feather-twitter"></i></a><?php endif; ?>
                        </div>
                    </div>
                    <div class="dl-contact-box dl-contact-map" style="padding:10px">
                        <?php if ($dlMapHtml !== ''): ?>
                            <?php echo $dlMapHtml; // yalnız təsdiqlənmiş Google Maps iframe ?>
                        <?php else: ?>
                            <iframe src="https://www.google.com/maps?q=Baku,Azerbaijan&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                        <?php endif; ?>
                    </div>
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
    <script>
        (function () {
            var form = document.querySelector("[data-contact-submit]");
            if (!form) return;
            form.addEventListener("submit", function (e) {
                e.preventDefault();
                var msg = form.querySelector("[data-contact-result]");
                var btn = form.querySelector("button[type=submit]");
                var data = {
                    name: form.elements.name.value.trim(),
                    email: form.elements.email.value.trim(),
                    phone: form.elements.phone.value.trim(),
                    message: form.elements.message.value.trim(),
                    interest: "Əlaqə formu",
                    source: "contact.php"
                };
                if (data.name.length < 2 || data.message.length < 2) { if (msg) { msg.style.color = "#dc2626"; msg.textContent = "Ad və mesaj boş ola bilməz."; } return; }
                if (btn) btn.disabled = true;
                if (msg) { msg.style.color = ""; msg.textContent = "Göndərilir..."; }
                fetch("api/admin.php?action=submit-lead", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(data) })
                    .then(function (r) { return r.json(); })
                    .then(function (p) {
                        if (msg) { msg.style.color = p.ok ? "#16a34a" : "#dc2626"; msg.textContent = p.message || (p.ok ? "Göndərildi!" : "Xəta."); }
                        if (p.ok) form.reset();
                    })
                    .catch(function () { if (msg) { msg.style.color = "#dc2626"; msg.textContent = "Şəbəkə xətası."; } })
                    .finally(function () { if (btn) btn.disabled = false; });
            });
        })();
    </script>
</body>

</html>
