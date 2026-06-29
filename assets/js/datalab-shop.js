/* DatalabAcademy mağaza/səbət/enrollment frontend.
   - Kurs detal səhifəsində (course-details-3.php) düymə vəziyyətləri.
   - Səbətə əlavə, pulsuz enroll, səbət sayğacı.
   API: api/shop.php  (sessiya student-auth ilə paylaşılır). */
(function () {
    "use strict";
    if (window.__dlShop) return;
    window.__dlShop = true;

    var API = "api/shop.php";

    function post(action, body) {
        return fetch(API + "?action=" + action, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(body || {})
        }).then(function (r) { return r.json().then(function (d) { return { status: r.status, data: d }; }); });
    }
    function get(action, qs) {
        return fetch(API + "?action=" + action + (qs ? "&" + qs : ""), { headers: { "Accept": "application/json" } })
            .then(function (r) { return r.json(); });
    }

    function loginUrl() {
        // yalnız fayl adı + query — login.html eyni qovluqda olduğu üçün nisbi yol qatlanmasın
        var file = location.pathname.split("/").pop() || "course-filter-two-toggle.php";
        return "login.html?redirect=" + encodeURIComponent(file + location.search);
    }

    /* ---------- səbət sayğacı (başlıqdakı nişan, varsa) ---------- */
    function refreshCartBadge() {
        var badges = document.querySelectorAll("[data-cart-count]");
        if (!badges.length) return;
        get("cart").then(function (p) {
            var n = p && p.ok ? (p.count || 0) : 0;
            badges.forEach(function (b) {
                b.textContent = n;
                b.style.display = n > 0 ? "inline-flex" : "none";
            });
        }).catch(function () {});
    }
    window.DLShopRefreshCart = refreshCartBadge;

    /* ---------- kurs detal səhifəsi düymələri ---------- */
    function renderActions(box) {
        var courseId = box.getAttribute("data-course-id");
        var firstLesson = box.getAttribute("data-first-lesson") || "";
        if (!courseId) return;

        box.innerHTML = '<div style="padding:14px 0;color:#94a3b8">Yüklənir…</div>';

        get("access", "course=" + encodeURIComponent(courseId)).then(function (s) {
            if (!s || !s.ok) { box.innerHTML = ""; return; }
            var html = "";
            var startBtn = firstLesson
                ? '<a class="rbt-btn btn-gradient hover-icon-reverse w-100 d-block text-center" href="' + firstLesson + '"><span class="btn-text">Dərsə davam et</span></a>'
                : "";

            if (!s.loggedIn) {
                html = '<a class="rbt-btn btn-gradient w-100 d-block text-center" href="' + loginUrl() + '">Qeydiyyat / Daxil ol</a>'
                    + '<p class="dl-shop-hint">Kursa qoşulmaq üçün hesabınıza daxil olun.</p>';
            } else if (s.enrolled) {
                html = startBtn + '<p class="dl-shop-hint" style="color:#16a34a"><i class="feather-check-circle"></i> Bu kursa girişiniz var.</p>';
            } else if (s.free) {
                html = '<button class="rbt-btn btn-gradient w-100" data-shop-enroll-free>Pulsuz başla</button>'
                    + '<p class="dl-shop-hint">Bu kurs pulsuzdur — dərhal başlaya bilərsiniz.</p>';
            } else if (s.pending) {
                html = '<button class="rbt-btn btn-border w-100" disabled>Ödəniş təsdiqi gözlənilir</button>'
                    + '<p class="dl-shop-hint">Kart-kart ödənişiniz admin tərəfindən yoxlanılır.</p>';
            } else if (s.inCart) {
                html = '<a class="rbt-btn btn-gradient w-100 d-block text-center" href="cart.php">Səbətə bax</a>'
                    + '<p class="dl-shop-hint">Bu kurs səbətinizdədir.</p>';
            } else {
                html = '<button class="rbt-btn btn-gradient w-100" data-shop-buy-now>İndi al ($' + (s.price || 0) + ')</button>'
                    + '<button class="rbt-btn btn-border w-100 mt--10" data-shop-add-cart>Səbətə əlavə et</button>';
            }
            box.innerHTML = html;
            wireActions(box, courseId, firstLesson);
        }).catch(function () { box.innerHTML = ""; });
    }

    function wireActions(box, courseId, firstLesson) {
        var free = box.querySelector("[data-shop-enroll-free]");
        if (free) free.addEventListener("click", function () {
            free.disabled = true; free.textContent = "Qoşulur…";
            post("enroll-free", { courseId: courseId }).then(function (res) {
                if (res.data.ok) { location.href = firstLesson || "student-dashboard.html"; }
                else { alert(res.data.message || "Xəta."); free.disabled = false; free.textContent = "Pulsuz başla"; }
            });
        });

        var add = box.querySelector("[data-shop-add-cart]");
        if (add) add.addEventListener("click", function () {
            add.disabled = true;
            post("cart-add", { courseId: courseId }).then(function (res) {
                if (res.data.ok) { refreshCartBadge(); renderActions(box); }
                else if (res.status === 401) { location.href = loginUrl(); }
                else { alert(res.data.message || "Xəta."); add.disabled = false; }
            });
        });

        var buy = box.querySelector("[data-shop-buy-now]");
        if (buy) buy.addEventListener("click", function () {
            buy.disabled = true;
            post("cart-add", { courseId: courseId }).then(function (res) {
                if (res.data.ok || res.status === 409) { location.href = "checkout.php"; }
                else if (res.status === 401) { location.href = loginUrl(); }
                else { alert(res.data.message || "Xəta."); buy.disabled = false; }
            });
        });
    }

    function init() {
        document.querySelectorAll("[data-shop-actions]").forEach(renderActions);
        refreshCartBadge();
        // shared header sonradan render oluna bilər — badge görünənə qədər bir neçə dəfə yoxla
        var tries = 0;
        var iv = setInterval(function () {
            tries++;
            if (document.querySelector("[data-cart-count]")) { refreshCartBadge(); clearInterval(iv); }
            if (tries > 12) clearInterval(iv);
        }, 250);
    }
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }
})();
