(function () {
    "use strict";

    var scheduled = false;
    var featherScheduled = false;

    function scheduleFeatherReplace() {
        if (featherScheduled || !window.feather || typeof window.feather.replace !== "function") return;
        featherScheduled = true;
        window.requestAnimationFrame(function () {
            featherScheduled = false;
            window.feather.replace();
        });
    }

    /* ---- Compact animated covers for listing/carousel thumbnails ------- */
    function sqlCover() {
        var lines = [
            '<b class="kw">SELECT</b> name, <b class="fn">COUNT</b>(*)',
            '<b class="kw">FROM</b> orders <b class="kw">JOIN</b> …',
            '<b class="kw">GROUP BY</b> name;'
        ].map(function (l, i) {
            return '<span class="ln" style="--i:' + i + '">' + l + "</span>";
        }).join("");
        var rows = [["Aytac", "184"], ["Rashad", "162"], ["Leyla", "151"]].map(function (r, i) {
            return '<span class="row" style="--i:' + (i + 4) + '"><span>' + r[0] + "</span><b>" + r[1] + "</b></span>";
        }).join("");
        return '<span class="dl-cv-cover dl-cv-cover-sql">' +
            '<span class="dl-cv-cover-head"><b>SQL DEVELOPER</b><em><i class="feather-database"></i> Canlı sorğu</em></span>' +
            '<span class="dl-cv-cover-code">' + lines + '<span class="dl-cv-cover-caret" style="--i:3"></span></span>' +
            '<span class="dl-cv-cover-rows">' + rows + "</span>" +
            "</span>";
    }

    function excelCover() {
        var values = [120, 168, 96, 205, 152, 188];
        var max = Math.max.apply(null, values);
        var cells = values.map(function (v, i) {
            return '<span class="c" style="--i:' + (i + 1) + '"><i>B' + (i + 2) + '</i><b>' + v + "</b></span>";
        }).join("");
        var bars = values.map(function (v, i) {
            return '<span class="b" style="--i:' + (i + 1) + ';--h:' + Math.round((v / max) * 100) + '%"></span>';
        }).join("");
        return '<span class="dl-cv-cover dl-cv-cover-xl">' +
            '<span class="dl-cv-cover-head"><b>EXCEL</b><em><i class="feather-grid"></i> =SUM()</em></span>' +
            '<span class="dl-cv-cover-cells">' + cells + "</span>" +
            '<span class="dl-cv-cover-bars">' + bars + "</span>" +
            "</span>";
    }

    var COVERS = {
        "2": sqlCover,
        "3": excelCover
    };

    /* ---- Spline robot cover for İnteraktiv AI Təcrübəsi (id=5) --------- */
    var SPLINE_SCENE = "https://prod.spline.design/kZDDjO5HuC9GJUM2/scene.splinecode";
    var splineRequested = false;

    function ensureSplineViewer() {
        if (splineRequested || document.querySelector("script[data-dl-spline]")) return;
        splineRequested = true;
        var s = document.createElement("script");
        s.type = "module";
        s.src = "https://unpkg.com/@splinetool/viewer/build/spline-viewer.js";
        s.setAttribute("data-dl-spline", "1");
        document.head.appendChild(s);
    }

    function renderRobotCovers() {
        document.querySelectorAll('.rbt-card-img a[data-course-id="5"]').forEach(function (link) {
            // Skip carousels — a 3D scene per cloned slide is far too heavy.
            if (link.closest(".swiper")) return;
            if (link.classList.contains("has-dl-robot-cover")) return;
            link.classList.add("has-dl-robot-cover");

            var img = link.querySelector(":scope > img");
            if (img && !img.hidden) img.hidden = true;
            if (link.querySelector(".dl-robot-cover")) return;

            link.insertAdjacentHTML("beforeend",
                '<span class="dl-robot-cover"><span class="dl-robot-glow"></span>' +
                '<span class="dl-robot-tag">İnteraktiv AI</span></span>');
            var cover = link.querySelector(".dl-robot-cover");

            // Load the 3D scene only when the card approaches the viewport.
            var io = new IntersectionObserver(function (entries, obs) {
                entries.forEach(function (e) {
                    if (!e.isIntersecting) return;
                    ensureSplineViewer();
                    if (!cover.querySelector("spline-viewer")) {
                        var v = document.createElement("spline-viewer");
                        v.setAttribute("url", SPLINE_SCENE);
                        v.setAttribute("loading-anim-type", "spinner-small-dark");
                        cover.insertBefore(v, cover.firstChild);
                    }
                    obs.disconnect();
                });
            }, { rootMargin: "250px" });
            io.observe(link);
        });
    }

    function renderCovers() {
        var changed = false;
        Object.keys(COVERS).forEach(function (id) {
            document.querySelectorAll('.rbt-card-img a[data-course-id="' + id + '"]').forEach(function (link) {
                if (!link.classList.contains("has-dl-cv-cover")) {
                    link.classList.add("has-dl-cv-cover");
                    changed = true;
                }
                var img = link.querySelector(":scope > img");
                if (img && !img.hidden) { img.hidden = true; changed = true; }
                if (!link.querySelector(".dl-cv-cover")) {
                    link.insertAdjacentHTML("beforeend", COVERS[id]());
                    changed = true;
                }
            });
        });
        if (changed) scheduleFeatherReplace();
    }

    /* ---- Sync the course grid with the DB's active courses ------------- */
    function activeIdSet() {
        var state = window.DL_SITE_STATE;
        var list = state && Array.isArray(state.courses) ? state.courses : null;
        if (!list || !list.length) return null; // no DB data yet → keep static cards
        var set = {};
        list.forEach(function (c) { set[String(c.id)] = true; });
        return set;
    }

    function syncCatalog() {
        var ids = activeIdSet();
        if (!ids) return;

        // Remove grid cards for courses that no longer exist in the database.
        document.querySelectorAll(".dl-course-card[data-course-id]").forEach(function (card) {
            if (!ids[card.getAttribute("data-course-id")] && card.parentNode) {
                card.parentNode.removeChild(card);
            }
        });

        // Remove category-panel quick links for removed courses.
        document.querySelectorAll("a.js-course-open[data-course-id]").forEach(function (a) {
            if (ids[a.getAttribute("data-course-id")]) return;
            if (a.closest(".dl-course-card")) return; // handled above
            var li = a.closest("li");
            if (li && li.parentNode) li.parentNode.removeChild(li);
        });
    }

    function run() {
        scheduled = false;
        renderCovers();
        renderRobotCovers();
        syncCatalog();
    }

    function schedule() {
        if (scheduled) return;
        scheduled = true;
        window.requestAnimationFrame(run);
    }

    function boot() {
        schedule();
        window.addEventListener("dl:siteState", schedule);
        // Re-run after carousels/sliders clone or inject slides.
        var observer = new MutationObserver(schedule);
        observer.observe(document.documentElement, { childList: true, subtree: true });
        setTimeout(schedule, 800);
        setTimeout(schedule, 1800);
    }

    if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
    else boot();
}());
