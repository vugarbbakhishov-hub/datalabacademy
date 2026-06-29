<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="az">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Ödəniş | DatalabAcademy</title>
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
        .dl-shop-wrap { margin-top: -70px; padding-bottom: 90px; }
        .dl-shop-grid { display: grid; grid-template-columns: 1fr 360px; gap: 24px; align-items: start; }
        .dl-shop-card { background: #fff; border-radius: 16px; box-shadow: 0 22px 60px rgba(31,31,44,.09); padding: 26px; }
        .dl-pay-tabs { display: flex; gap: 12px; margin-bottom: 22px; flex-wrap: wrap; }
        .dl-pay-tab { flex: 1; min-width: 180px; border: 2px solid #e4e9f3; border-radius: 14px; padding: 16px; cursor: pointer; display: flex; gap: 12px; align-items: center; transition: .15s; background: #fff; }
        .dl-pay-tab.active { border-color: #315df5; box-shadow: 0 0 0 3px rgba(49,93,245,.1); }
        .dl-pay-tab.disabled { opacity: .55; cursor: not-allowed; }
        .dl-pay-tab .ic { width: 42px; height: 42px; border-radius: 11px; display: grid; place-items: center; color: #fff; background: linear-gradient(135deg, #315df5, #9b51e0); font-size: 20px; flex: 0 0 auto; }
        .dl-pay-tab b { display: block; font-size: 15px; color: #192335; }
        .dl-pay-tab small { color: #8a93a6; }
        .dl-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: 18px; }
        .dl-bankcard { position: relative; border-radius: 18px; padding: 22px; color: #fff; min-height: 175px; cursor: pointer; overflow: hidden; box-shadow: 0 14px 34px rgba(15,23,42,.22); transition: transform .15s, box-shadow .15s; display: flex; flex-direction: column; justify-content: space-between; }
        .dl-bankcard:hover { transform: translateY(-3px); }
        .dl-bankcard.sel { outline: 3px solid #fff; box-shadow: 0 0 0 5px #315df5, 0 14px 34px rgba(15,23,42,.3); }
        .dl-bankcard::after { content: ""; position: absolute; right: -40px; top: -40px; width: 150px; height: 150px; border-radius: 50%; background: rgba(255,255,255,.12); }
        .dl-bankcard .top { display: flex; justify-content: space-between; align-items: center; position: relative; z-index: 1; }
        .dl-bankcard .bank { font-weight: 800; font-size: 16px; letter-spacing: .3px; }
        .dl-bankcard .chip { width: 40px; height: 30px; border-radius: 6px; background: linear-gradient(135deg, #f6d365, #fda085); opacity: .95; }
        .dl-bankcard .num { font-size: 21px; letter-spacing: 2px; font-family: "Courier New", monospace; font-weight: 700; position: relative; z-index: 1; }
        .dl-bankcard .bottom { display: flex; justify-content: space-between; align-items: flex-end; position: relative; z-index: 1; }
        .dl-bankcard .holder { font-size: 13px; text-transform: uppercase; letter-spacing: 1px; opacity: .92; }
        .dl-bankcard .note { font-size: 11px; opacity: .8; }
        .dl-bankcard .copy { position: absolute; top: 12px; right: 14px; z-index: 2; background: rgba(255,255,255,.2); border: 0; color: #fff; border-radius: 8px; padding: 5px 8px; cursor: pointer; font-size: 12px; }
        .dl-bankcard .selflag { position: absolute; bottom: 12px; right: 14px; z-index: 2; font-size: 12px; font-weight: 700; background: rgba(255,255,255,.25); padding: 3px 9px; border-radius: 20px; display: none; }
        .dl-bankcard.sel .selflag { display: inline-block; }
        .th-violet { background: linear-gradient(135deg, #7b2ff7, #f107a3); }
        .th-ocean { background: linear-gradient(135deg, #2193b0, #6dd5ed); }
        .th-emerald { background: linear-gradient(135deg, #0f9b8e, #38ef7d); }
        .th-sunset { background: linear-gradient(135deg, #ff512f, #f09819); }
        .th-dark { background: linear-gradient(135deg, #232526, #414345); }
        .th-gold { background: linear-gradient(135deg, #b8860b, #ffd700); color: #2a2200; }
        .dl-sum-row { display: flex; justify-content: space-between; margin-bottom: 9px; color: #5b6478; font-size: 14px; }
        .dl-sum-total { display: flex; justify-content: space-between; font-size: 21px; font-weight: 900; color: #192335; border-top: 1px solid #eef1f7; padding-top: 13px; margin-top: 6px; }
        .dl-pay-note textarea { width: 100%; margin-top: 16px; padding: 11px 13px; border: 1px solid #e4e9f3; border-radius: 10px; font: inherit; }
        .dl-pay-msg { margin-top: 12px; font-weight: 600; }
        .dl-instructions { background: #f1f5ff; border-radius: 12px; padding: 14px 16px; font-size: 13px; color: #3a4763; margin-bottom: 18px; }
        .dl-success { text-align: center; padding: 30px 16px; }
        .dl-success .ic { width: 72px; height: 72px; border-radius: 50%; background: #16a34a; color: #fff; display: grid; place-items: center; font-size: 34px; margin: 0 auto 16px; }
        .active-dark-mode .dl-shop-card { background: #16213a; color: #e7ecf6; box-shadow: 0 22px 60px rgba(0,0,0,.4); }
        .active-dark-mode .dl-pay-tab { background: #0f1828; border-color: #283449; }
        .active-dark-mode .dl-pay-tab b, .active-dark-mode .dl-sum-total { color: #f3f6fc; }
        .active-dark-mode .dl-instructions { background: #102038; color: #aeb9cd; }
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
                        <li class="rbt-breadcrumb-item"><a href="cart.php">Səbət</a></li>
                        <li><div class="icon-right"><i class="feather-chevron-right"></i></div></li>
                        <li class="rbt-breadcrumb-item active">Ödəniş</li>
                    </ul>
                    <h1 class="title display-one mt--20" style="color:#fff">Ödəniş</h1>
                </div>
            </div>
        </section>

        <section class="dl-shop-wrap">
            <div class="container">
                <div class="dl-shop-grid">
                    <div class="dl-shop-card" data-pay-root><div style="padding:30px;text-align:center;color:#94a3b8">Yüklənir…</div></div>
                    <div class="dl-shop-card" data-pay-summary></div>
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
            var root = document.querySelector("[data-pay-root]");
            var sumEl = document.querySelector("[data-pay-summary]");
            var cart = null, cards = [], method = "card", selectedCard = "";
            function esc(s){var d=document.createElement("div");d.textContent=s==null?"":String(s);return d.innerHTML;}
            function fmtNum(n){ n=String(n||"").replace(/\s+/g,""); return n.replace(/(.{4})/g,"$1 ").trim(); }

            function load() {
                Promise.all([
                    fetch(API + "?action=cart").then(function(r){return r.json();}),
                    fetch(API + "?action=payment-cards").then(function(r){return r.json();})
                ]).then(function (res) {
                    cart = res[0]; cards = (res[1] && res[1].cards) || [];
                    if (!cart.ok) { if (cart.auth === false) { location.href = "login.html?redirect=checkout.php"; return; } root.innerHTML = '<div style="padding:24px">'+esc(cart.message||"Xəta")+'</div>'; return; }
                    if (!cart.items.length) { root.innerHTML = '<div style="padding:30px;text-align:center"><i class="feather-shopping-cart" style="font-size:40px;color:#cbd5e1"></i><p style="margin-top:10px">Səbətiniz boşdur.</p><a class="rbt-btn btn-gradient btn-sm" href="course-filter-two-toggle.php">Təlimlərə bax</a></div>'; sumEl.innerHTML=""; return; }
                    renderSummary(); render();
                }).catch(function(){ root.innerHTML = '<div style="padding:24px">Şəbəkə xətası.</div>'; });
            }

            function renderSummary() {
                sumEl.innerHTML = '<h5 style="margin-top:0">Sifariş</h5>'
                    + cart.items.map(function(it){ return '<div class="dl-sum-row"><span>'+esc(it.title)+'</span><span>$'+Number(it.price).toFixed(2)+'</span></div>'; }).join("")
                    + '<div class="dl-sum-total"><span>Cəmi</span><span>$'+Number(cart.total).toFixed(2)+'</span></div>';
            }

            function cardHtml(c) {
                var theme = ["violet","ocean","emerald","sunset","dark","gold"].indexOf(c.theme) >= 0 ? c.theme : "violet";
                return '<div class="dl-bankcard th-'+theme+(selectedCard===c.id?" sel":"")+'" data-card="'+esc(c.id)+'">'
                    + '<button class="copy" data-copy="'+esc(c.number)+'" type="button"><i class="feather-copy"></i></button>'
                    + '<div class="top"><span class="bank">'+esc(c.bank||"Bank")+'</span><span class="chip"></span></div>'
                    + '<div class="num">'+esc(fmtNum(c.number))+'</div>'
                    + '<div class="bottom"><div><div class="holder">'+esc(c.holder||"")+'</div>'+(c.note?'<div class="note">'+esc(c.note)+'</div>':'')+'</div></div>'
                    + '<span class="selflag"><i class="feather-check"></i> Seçildi</span>'
                    + '</div>';
            }

            function render() {
                var cardsBlock = cards.length
                    ? '<div class="dl-instructions"><b>Necə ödəniş edilir?</b> Aşağıdakı kartlardan birini seçin, <b>$'+Number(cart.total).toFixed(2)+'</b> məbləğini həmin karta köçürün, sonra <b>“Ödənişi etdim”</b> düyməsinə basın. Admin ödənişi yoxlayıb təsdiqlədikdən sonra kurslara girişiniz açılacaq.</div>'
                        + '<div class="dl-cards">' + cards.map(cardHtml).join("") + '</div>'
                        + '<div class="dl-pay-note"><textarea rows="2" data-note placeholder="Qeyd (könüllü): ödəyən şəxsin adı, köçürmə vaxtı və s."></textarea></div>'
                        + '<button class="rbt-btn btn-gradient w-100 mt--20" data-pay-confirm>Ödənişi etdim</button>'
                        + '<p class="dl-pay-msg" data-msg></p>'
                    : '<div class="dl-instructions">Hazırda ödəniş kartı əlavə edilməyib. Zəhmət olmasa <a href="contact.php">bizimlə əlaqə</a> saxlayın.</div>';

                var onlineTab = '<div class="dl-pay-tab disabled" title="Tezliklə"><span class="ic"><i class="feather-credit-card"></i></span><div><b>Onlayn ödəniş</b><small>Bank kartı ilə — tezliklə</small></div></div>';
                var cardTab = '<div class="dl-pay-tab active"><span class="ic"><i class="feather-repeat"></i></span><div><b>Kart-kart köçürmə</b><small>Karta köçür, təsdiq gözlə</small></div></div>';

                root.innerHTML = '<div class="dl-pay-tabs">' + cardTab + onlineTab + '</div>' + cardsBlock;
                wire();
            }

            function wire() {
                root.querySelectorAll("[data-card]").forEach(function (el) {
                    el.addEventListener("click", function () {
                        selectedCard = el.getAttribute("data-card");
                        root.querySelectorAll("[data-card]").forEach(function(x){x.classList.remove("sel");});
                        el.classList.add("sel");
                    });
                });
                root.querySelectorAll("[data-copy]").forEach(function (b) {
                    b.addEventListener("click", function (e) {
                        e.stopPropagation();
                        var v = b.getAttribute("data-copy");
                        if (navigator.clipboard) navigator.clipboard.writeText(v).then(function(){ b.innerHTML='<i class="feather-check"></i>'; setTimeout(function(){b.innerHTML='<i class="feather-copy"></i>';},1200); });
                    });
                });
                var confirm = root.querySelector("[data-pay-confirm]");
                if (confirm) confirm.addEventListener("click", function () {
                    var msg = root.querySelector("[data-msg]");
                    if (!selectedCard) { msg.style.color="#dc2626"; msg.textContent="Əvvəlcə ödəniş etdiyiniz kartı seçin."; return; }
                    confirm.disabled = true; msg.style.color=""; msg.textContent = "Göndərilir…";
                    var note = (root.querySelector("[data-note]") || {}).value || "";
                    fetch(API + "?action=checkout", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify({ paymentMethod: "card", cardId: selectedCard, note: note }) })
                        .then(function(r){return r.json();}).then(function (p) {
                            if (p.ok) {
                                if (window.DLShopRefreshCart) DLShopRefreshCart();
                                root.innerHTML = '<div class="dl-success"><div class="ic"><i class="feather-check"></i></div><h4>Bildiriş göndərildi!</h4><p style="color:#64748b;max-width:420px;margin:0 auto">'+esc(p.message)+'</p><div style="margin-top:8px;color:#94a3b8">Sifariş №: '+esc(p.orderId)+'</div><a class="rbt-btn btn-gradient mt--20" href="student-dashboard.html">Panelimə keç</a></div>';
                                sumEl.innerHTML = "";
                            } else {
                                msg.style.color="#dc2626"; msg.textContent = p.message || "Xəta."; confirm.disabled = false;
                            }
                        }).catch(function(){ msg.style.color="#dc2626"; msg.textContent="Şəbəkə xətası."; confirm.disabled=false; });
                });
            }

            load();
        })();
    </script>
</body>

</html>
