(function () {
    "use strict";

    var scheduled = false;
    var featherScheduled = false;

    function currentCourseId() {
        var id = new URLSearchParams(window.location.search).get("id");
        return id ? String(id) : "";
    }

    function scheduleFeatherReplace() {
        if (featherScheduled || !window.feather || typeof window.feather.replace !== "function") return;
        featherScheduled = true;
        window.requestAnimationFrame(function () {
            featherScheduled = false;
            window.feather.replace();
        });
    }

    /* ---- SQL Developer hero ------------------------------------------- */
    function sqlMarkup() {
        var codeLines = [
            '<b class="kw">SELECT</b> c.name, <b class="fn">COUNT</b>(o.id) <b class="kw">AS</b> orders',
            '<b class="kw">FROM</b> customers c',
            '<b class="kw">JOIN</b> orders o <b class="kw">ON</b> o.cust_id = c.id',
            '<b class="kw">GROUP BY</b> c.name',
            '<b class="kw">ORDER BY</b> orders <b class="kw">DESC</b>;'
        ];
        var rows = [
            ["Aytac M.", "184"],
            ["Rashad N.", "162"],
            ["Leyla H.", "151"],
            ["Kamran V.", "138"]
        ];
        var code = codeLines.map(function (l, i) {
            return '<span class="dl-cv-ln" style="--i:' + i + '">' + l + "</span>";
        }).join("");
        var resultRows = rows.map(function (r, i) {
            return '<span class="dl-cv-row" style="--i:' + (i + 6) + '"><span>' + r[0] + "</span><b>" + r[1] + "</b></span>";
        }).join("");
        return "" +
            '<div class="dl-cv-card dl-cv-sql">' +
                '<div class="dl-cv-head"><span class="dl-cv-kicker">SQL DEVELOPER</span>' +
                    '<em><i class="feather-database"></i> Canlı sorğu</em></div>' +
                '<div class="dl-cv-console"><span class="dl-cv-tabs"><i></i><i></i><i></i></span>' +
                    '<pre class="dl-cv-code">' + code + '<span class="dl-cv-caret" style="--i:5"></span></pre></div>' +
                '<div class="dl-cv-result">' +
                    '<span class="dl-cv-row dl-cv-rhead" style="--i:5"><span>name</span><b>orders</b></span>' +
                    resultRows +
                "</div>" +
                '<div class="dl-cv-foot"><b>JOIN</b><b>GROUP BY</b><b>INDEX</b><b>CTE</b></div>' +
            "</div>";
    }

    /* ---- Excel hero --------------------------------------------------- */
    function excelMarkup() {
        var values = [120, 168, 96, 205, 152, 188];
        var sum = values.reduce(function (a, b) { return a + b; }, 0);
        var max = Math.max.apply(null, values);
        var cells = values.map(function (v, i) {
            return '<span class="dl-cv-cell" style="--i:' + (i + 1) + '"><i>B' + (i + 2) + '</i><b>' + v + "</b></span>";
        }).join("");
        var bars = values.map(function (v, i) {
            return '<span class="dl-cv-bar" style="--i:' + (i + 1) + ';--h:' + Math.round((v / max) * 100) + '%"></span>';
        }).join("");
        return "" +
            '<div class="dl-cv-card dl-cv-xl">' +
                '<div class="dl-cv-head"><span class="dl-cv-kicker">EXCEL</span>' +
                    '<em><i class="feather-grid"></i> Avtomatik hesabat</em></div>' +
                '<div class="dl-cv-formula"><span class="fx">fx</span><code>=SUM(B2:B7)</code>' +
                    '<span class="dl-cv-sum">' + sum + "</span></div>" +
                '<div class="dl-cv-sheet">' + cells + "</div>" +
                '<div class="dl-cv-chart">' + bars + "</div>" +
                '<div class="dl-cv-foot"><b>PIVOT</b><b>VLOOKUP</b><b>DASHBOARD</b></div>' +
            "</div>";
    }

    var TREATMENTS = {
        "2": { cls: "is-sql-course", build: sqlMarkup },
        "3": { cls: "is-excel-course", build: excelMarkup }
    };

    function render() {
        scheduled = false;
        var hero = document.querySelector(".dl-offline-hero");
        var scene = hero && hero.querySelector(".dl-offline-scene");
        if (!hero || !scene) return;

        var treatment = TREATMENTS[currentCourseId()];

        // Not a video-course page → make sure our card/classes are gone.
        if (!treatment) {
            hero.classList.remove("is-sql-course", "is-excel-course");
            var stale = scene.querySelector(".dl-cv-card");
            if (stale) stale.parentNode.removeChild(stale);
            return;
        }

        if (!hero.classList.contains(treatment.cls)) {
            hero.classList.remove("is-sql-course", "is-excel-course");
            hero.classList.add(treatment.cls);
        }

        // Hide the default image / spline scene.
        var spline = scene.querySelector("spline-viewer");
        if (spline && spline.style.display !== "none") spline.style.display = "none";
        var cover = scene.querySelector("[data-course-cover]");
        if (cover && cover.style.display !== "none") cover.style.display = "none";

        var existing = scene.querySelector(".dl-cv-card");
        var wanted = treatment.cls === "is-sql-course" ? "dl-cv-sql" : "dl-cv-xl";
        if (existing && !existing.classList.contains(wanted)) {
            existing.parentNode.removeChild(existing);
            existing = null;
        }
        if (!existing) {
            scene.insertAdjacentHTML("beforeend", treatment.build());
            scheduleFeatherReplace();
        }
    }

    function schedule() {
        if (scheduled) return;
        scheduled = true;
        window.requestAnimationFrame(render);
    }

    function boot() {
        schedule();
        window.addEventListener("dl:siteState", schedule);
        var observer = new MutationObserver(schedule);
        observer.observe(document.documentElement, { childList: true, subtree: true });
    }

    if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
    else boot();
}());
