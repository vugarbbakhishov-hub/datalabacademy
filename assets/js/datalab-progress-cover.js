(function () {
    "use strict";

    var sequence = 0;
    var renderScheduled = false;
    var featherScheduled = false;

    /* ----- Listing thumbnail cover (small card on course grids) ---------- */
    var chartPoints = [
        { x: 25, y: 149, value: 112, date: "18 May 2026" },
        { x: 69, y: 166, value: 98, date: "25 May 2026" },
        { x: 108, y: 105, value: 146, date: "1 Jun 2026" },
        { x: 143, y: 57, value: 178, date: "8 Jun 2026" },
        { x: 175, y: 20, value: 205, date: "15 Jun 2026" },
        { x: 205, y: 72, value: 166, date: "22 Jun 2026" },
        { x: 264, y: 66, value: 172, date: "29 Jun 2026" },
        { x: 300, y: 126, value: 121, date: "6 Jul 2026" },
        { x: 337, y: 40, value: 191, date: "13 Jul 2026" }
    ];

    function numberFromText(value, fallback) {
        var source = value && value.textContent != null ? value.textContent : value;
        var match = String(source || "").match(/\d+/);
        return match ? Number(match[0]) : fallback;
    }

    function courseMetrics(link) {
        var card = link.closest(".dl-course-card, .rbt-card");
        var students = numberFromText(
            card && card.getAttribute("data-students"),
            numberFromText(card && card.querySelector(".rbt-meta li:nth-child(2)"), 40)
        );
        var reviews = numberFromText(card && card.querySelector(".rating-count"), 0);
        return { students: students || 40, reviews: reviews || 0 };
    }

    function scheduleFeatherReplace() {
        if (featherScheduled || !window.feather || typeof window.feather.replace !== "function") return;
        featherScheduled = true;
        window.requestAnimationFrame(function () {
            featherScheduled = false;
            window.feather.replace();
        });
    }

    function pointMarkup(point) {
        return "" +
            '<g class="dl-progress-point" transform="translate(' + point.x + " " + point.y + ')" tabindex="0" role="img" data-x="' + point.x + '" data-y="' + point.y + '" data-value="' + point.value + '" data-date="' + point.date + '" aria-label="' + point.date + ": " + point.value + ' qeydiyyat">' +
                '<circle class="dl-progress-point-dot" r="4"/>' +
                '<circle class="dl-progress-point-hit" r="14"/>' +
            "</g>";
    }

    function chartSvg(id) {
        return "" +
            '<svg class="dl-progress-chart" viewBox="0 0 360 190" aria-label="Data Analitika qeydiyyat qrafiki">' +
                "<defs>" +
                    '<linearGradient id="dl-progress-fill-' + id + '" x1="0" y1="0" x2="0" y2="1">' +
                        '<stop offset="0%" stop-color="#18c99a" stop-opacity=".34"/>' +
                        '<stop offset="100%" stop-color="#18c99a" stop-opacity="0"/>' +
                    "</linearGradient>" +
                    '<pattern id="dl-progress-grid-' + id + '" width="16" height="16" patternUnits="userSpaceOnUse">' +
                        '<circle cx="1.2" cy="1.2" r="1.2" fill="currentColor"/>' +
                    "</pattern>" +
                "</defs>" +
                '<rect class="dl-progress-dots" width="360" height="190" fill="url(#dl-progress-grid-' + id + ')"/>' +
                '<path class="dl-progress-area" d="M0 170 C30 140 52 142 69 166 C83 183 84 112 108 105 C126 100 124 54 143 57 C158 60 153 10 175 20 C193 31 184 64 205 72 C229 82 240 61 264 66 C290 70 281 128 300 126 C321 124 311 36 337 40 C350 42 350 91 360 82 L360 190 L0 190 Z" fill="url(#dl-progress-fill-' + id + ')"/>' +
                '<path class="dl-progress-line" pathLength="1" d="M0 170 C30 140 52 142 69 166 C83 183 84 112 108 105 C126 100 124 54 143 57 C158 60 153 10 175 20 C193 31 184 64 205 72 C229 82 240 61 264 66 C290 70 281 128 300 126 C321 124 311 36 337 40 C350 42 350 91 360 82"/>' +
                chartPoints.map(pointMarkup).join("") +
            "</svg>";
    }

    function chartBlock() {
        sequence += 1;
        return "" +
            '<span class="dl-progress-chart-wrap">' +
                chartSvg(sequence) +
                '<span class="dl-progress-floating-tip" aria-hidden="true"><strong></strong><small></small></span>' +
            "</span>";
    }

    function cardMarkup(metrics) {
        return "" +
            '<span class="dl-progress-cover">' +
                '<span class="dl-progress-head">' +
                    '<span class="dl-progress-label">Data Analitika</span>' +
                    '<span class="dl-progress-trend"><i class="feather-trending-up"></i> Canli</span>' +
                "</span>" +
                '<span class="dl-progress-total"><strong data-dl-progress-students>' + metrics.students + "</strong><small>telebe</small></span>" +
                chartBlock() +
                '<span class="dl-progress-footer"><b>Praktiki KPI</b><span>SQL - Excel - Dashboard</span></span>' +
            "</span>";
    }

    function nearestPoint(localX) {
        return chartPoints.reduce(function (best, point) {
            var distance = Math.abs(point.x - localX);
            return !best || distance < best.distance ? { point: point, distance: distance } : best;
        }, null).point;
    }

    function showTooltip(wrap, tooltip, point) {
        var valueEl = tooltip.querySelector("strong");
        var dateEl = tooltip.querySelector("small");
        if (!valueEl || !dateEl) return;

        valueEl.textContent = point.value + " qeydiyyat";
        dateEl.textContent = point.date;
        tooltip.style.left = ((point.x / 360) * 100) + "%";
        tooltip.style.top = ((point.y / 190) * 100) + "%";
        tooltip.classList.toggle("is-below", point.y < 55);
        tooltip.classList.add("is-visible");
        wrap.style.setProperty("--dl-progress-cursor-x", ((point.x / 360) * 100) + "%");
        wrap.style.setProperty("--dl-progress-cursor-y", ((point.y / 190) * 100) + "%");
        wrap.classList.add("is-active");
    }

    function hydrateChartTooltips(root) {
        (root || document).querySelectorAll(".dl-progress-chart-wrap:not([data-tooltip-ready])").forEach(function (wrap) {
            var tooltip = wrap.querySelector(".dl-progress-floating-tip");
            if (!tooltip) return;

            wrap.setAttribute("data-tooltip-ready", "1");

            function moveTooltip(event) {
                var rect = wrap.getBoundingClientRect();
                if (!rect.width || !rect.height) return;
                var localX = Math.max(0, Math.min(360, ((event.clientX - rect.left) / rect.width) * 360));
                showTooltip(wrap, tooltip, nearestPoint(localX));
            }

            function hideTooltip() {
                tooltip.classList.remove("is-visible", "is-below");
                wrap.classList.remove("is-active");
            }

            wrap.addEventListener("pointermove", moveTooltip);
            wrap.addEventListener("pointerenter", moveTooltip);
            wrap.addEventListener("pointerleave", hideTooltip);

            wrap.querySelectorAll(".dl-progress-point").forEach(function (pointNode) {
                pointNode.addEventListener("focus", function () {
                    showTooltip(wrap, tooltip, {
                        x: Number(pointNode.getAttribute("data-x")),
                        y: Number(pointNode.getAttribute("data-y")),
                        value: Number(pointNode.getAttribute("data-value")),
                        date: pointNode.getAttribute("data-date")
                    });
                });
                pointNode.addEventListener("blur", hideTooltip);
            });
        });
    }

    function dataAnalyticsLinks() {
        var links = Array.prototype.slice.call(document.querySelectorAll(
            ".rbt-card-img a[data-course-id='1'], " +
            ".dl-course-card[data-course-id='1'] .rbt-card-img a"
        ));

        document.querySelectorAll("img[src*='datalab-data-analitika']").forEach(function (image) {
            var link = image.closest(".rbt-card-img a");
            if (link && links.indexOf(link) === -1) links.push(link);
        });

        document.querySelectorAll(".rbt-card-title a, .rbt-card h4 a, .rbt-card h5 a").forEach(function (titleLink) {
            if (!/data\s*analitika/i.test(titleLink.textContent || "")) return;
            var card = titleLink.closest(".rbt-card");
            var cardLink = card && card.querySelector(".rbt-card-img a");
            if (cardLink && links.indexOf(cardLink) === -1) links.push(cardLink);
        });

        return links;
    }

    function renderCards() {
        var changed = false;

        dataAnalyticsLinks().forEach(function (link) {
            if (link.getAttribute("data-course-id") !== "1") {
                link.setAttribute("data-course-id", "1");
                changed = true;
            }
            if (!link.classList.contains("has-dl-progress-cover")) {
                link.classList.add("has-dl-progress-cover");
                changed = true;
            }
            var image = link.querySelector(":scope > img");
            if (image && !image.hidden) {
                image.hidden = true;
                changed = true;
            }
            if (!link.querySelector(".dl-progress-cover")) {
                link.insertAdjacentHTML("beforeend", cardMarkup(courseMetrics(link)));
                changed = true;
            }
            hydrateChartTooltips(link);
        });

        if (changed) scheduleFeatherReplace();
    }

    function updateMetrics(state) {
        var course = state && Array.isArray(state.courses)
            ? state.courses.find(function (item) { return String(item.id) === "1"; })
            : null;
        if (!course) return;

        document.querySelectorAll("[data-dl-progress-students]").forEach(function (el) {
            el.textContent = Number(course.students || 0);
        });
        document.querySelectorAll("[data-dl-progress-reviews]").forEach(function (el) {
            el.textContent = Number(course.reviewCount || 0);
        });
    }

    /* ====================================================================== */
    /* Hero Progress Metric Card — "total sifariş" (two views + dark/light)   */
    /* ====================================================================== */

    var ORDERS = [
        { value: 100, date: "18 Mar, 2025" },
        { value: 115, date: "21 Mar, 2025" },
        { value: 120, date: "24 Mar, 2025" },
        { value: 110, date: "27 Mar, 2025" },
        { value: 178, date: "30 Mar, 2025" },
        { value: 180, date: "02 Apr, 2025" },
        { value: 205, date: "05 Apr, 2025" },
        { value: 185, date: "08 Apr, 2025" },
        { value: 180, date: "11 Apr, 2025" },
        { value: 183, date: "14 Apr, 2025" },
        { value: 185, date: "17 Apr, 2025" },
        { value: 185, date: "20 Apr, 2025" },
        { value: 166, date: "23 Apr, 2025" },
        { value: 181, date: "26 Apr, 2025" },
        { value: 168, date: "29 Apr, 2025" },
        { value: 195, date: "02 May, 2025" },
        { value: 188, date: "05 May, 2025" },
        { value: 162, date: "08 May, 2025" },
        { value: 166, date: "11 May, 2025" }
    ];

    var DEFAULT_INDEX = 6; // 205 orders / 05 Apr, 2025
    var PM_VB = { w: 480, h: 210, padT: 24, padB: 14, padX: 8 };

    function pmCompact(n) {
        if (Math.abs(n) >= 1000) {
            return (n / 1000).toFixed(2).replace(/\.?0+$/, "") + "K";
        }
        return String(Math.round(n));
    }

    function pmStats(data) {
        var vals = data.map(function (d) { return d.value; });
        var sum = vals.reduce(function (a, b) { return a + b; }, 0);
        var first = vals[0];
        var last = vals[vals.length - 1];
        var prev = vals[vals.length - 2] != null ? vals[vals.length - 2] : first;
        var net = last - first;
        return {
            sum: sum,
            net: net,
            pct: first ? (net / first) * 100 : 0,
            step: last - prev,
            peak: Math.max.apply(null, vals),
            low: Math.min.apply(null, vals),
            avg: Math.round(sum / vals.length)
        };
    }

    // Maps each data point to chart coordinates inside the viewBox.
    function pmGeometry(data) {
        var vals = data.map(function (d) { return d.value; });
        var min = Math.min.apply(null, vals);
        var max = Math.max.apply(null, vals);
        var span = max - min || 1;
        var innerW = PM_VB.w - PM_VB.padX * 2;
        var innerH = PM_VB.h - PM_VB.padT - PM_VB.padB;
        return data.map(function (d, i) {
            var x = PM_VB.padX + (data.length === 1 ? innerW / 2 : (i / (data.length - 1)) * innerW);
            var y = PM_VB.padT + (1 - (d.value - min) / span) * innerH;
            return { x: x, y: y, value: d.value, date: d.date, index: i };
        });
    }

    // Smooth Catmull-Rom -> cubic Bézier path for the line view.
    function pmSmoothPath(pts) {
        if (pts.length < 2) return "";
        var d = "M" + pts[0].x.toFixed(1) + " " + pts[0].y.toFixed(1);
        for (var i = 0; i < pts.length - 1; i++) {
            var p0 = pts[i - 1] || pts[i];
            var p1 = pts[i];
            var p2 = pts[i + 1];
            var p3 = pts[i + 2] || p2;
            var c1x = p1.x + (p2.x - p0.x) / 6;
            var c1y = p1.y + (p2.y - p0.y) / 6;
            var c2x = p2.x - (p3.x - p1.x) / 6;
            var c2y = p2.y - (p3.y - p1.y) / 6;
            d += " C" + c1x.toFixed(1) + " " + c1y.toFixed(1) + " " +
                c2x.toFixed(1) + " " + c2y.toFixed(1) + " " +
                p2.x.toFixed(1) + " " + p2.y.toFixed(1);
        }
        return d;
    }

    function pmCurveSvg(id, pts) {
        var line = pmSmoothPath(pts);
        var area = line + " L" + pts[pts.length - 1].x.toFixed(1) + " " + PM_VB.h +
            " L" + pts[0].x.toFixed(1) + " " + PM_VB.h + " Z";
        var dots = pts.map(function (p) {
            return '<g class="dl-pm-point" transform="translate(' + p.x.toFixed(1) + " " + p.y.toFixed(1) + ')" data-index="' + p.index + '">' +
                '<circle class="dl-pm-point-dot" r="4"/>' +
                "</g>";
        }).join("");
        return '<svg class="dl-pm-svg dl-pm-svg-curve" viewBox="0 0 ' + PM_VB.w + " " + PM_VB.h + '" preserveAspectRatio="none" aria-hidden="true">' +
            "<defs>" +
                '<linearGradient id="dl-pm-fill-' + id + '" x1="0" y1="0" x2="0" y2="1">' +
                    '<stop offset="0%" stop-color="currentColor" stop-opacity=".26"/>' +
                    '<stop offset="100%" stop-color="currentColor" stop-opacity="0"/>' +
                "</linearGradient>" +
            "</defs>" +
            '<path class="dl-pm-area" d="' + area + '" fill="url(#dl-pm-fill-' + id + ')"/>' +
            '<path class="dl-pm-line" pathLength="1" d="' + line + '"/>' +
            dots +
            "</svg>";
    }

    function pmBarsSvg(pts) {
        var innerW = PM_VB.w - PM_VB.padX * 2;
        var slot = innerW / pts.length;
        var barW = Math.min(slot * 0.52, 13);
        var bars = pts.map(function (p) {
            var x = (p.x - barW / 2).toFixed(1);
            var h = (PM_VB.h - PM_VB.padB - p.y).toFixed(1);
            return '<rect class="dl-pm-bar" data-index="' + p.index + '" x="' + x + '" y="' + p.y.toFixed(1) +
                '" width="' + barW.toFixed(1) + '" height="' + h + '" rx="' + (barW / 2.6).toFixed(1) + '"/>';
        }).join("");
        return '<svg class="dl-pm-svg dl-pm-svg-bars" viewBox="0 0 ' + PM_VB.w + " " + PM_VB.h + '" preserveAspectRatio="none" aria-hidden="true">' +
            bars +
            "</svg>";
    }

    function pmDotsSvg(id) {
        return '<svg class="dl-pm-dots" viewBox="0 0 ' + PM_VB.w + " " + PM_VB.h + '" preserveAspectRatio="none" aria-hidden="true">' +
            "<defs>" +
                '<pattern id="dl-pm-grid-' + id + '" width="14" height="14" patternUnits="userSpaceOnUse">' +
                    '<circle cx="1" cy="1" r="1" fill="currentColor"/>' +
                "</pattern>" +
            "</defs>" +
            '<rect width="' + PM_VB.w + '" height="' + PM_VB.h + '" fill="url(#dl-pm-grid-' + id + ')"/>' +
            "</svg>";
    }

    function heroMarkup() {
        sequence += 1;
        var id = sequence;
        var stats = pmStats(ORDERS);
        var pts = pmGeometry(ORDERS);
        var trendUp = stats.net >= 0;
        return "" +
            '<div class="dl-pm-card" data-dl-pm data-pm-view="curve" data-pm-theme="light">' +
                '<button class="dl-pm-theme-btn" type="button" data-pm-theme-toggle aria-label="İşıqlı / qaranlıq rejim">' +
                    '<i class="feather-moon" data-pm-icon-dark></i>' +
                    '<i class="feather-sun" data-pm-icon-light></i>' +
                "</button>" +
                '<div class="dl-pm-top">' +
                    '<div class="dl-pm-title-row">' +
                        '<h3 class="dl-pm-title">total sifariş</h3>' +
                        '<div class="dl-pm-views" role="group" aria-label="Qrafik növü">' +
                            '<button type="button" class="is-active" data-pm-view-btn="curve" aria-label="Xətt qrafiki"><i class="feather-activity"></i></button>' +
                            '<button type="button" data-pm-view-btn="bars" aria-label="Sütun qrafiki"><i class="feather-bar-chart-2"></i></button>' +
                        "</div>" +
                    "</div>" +
                    '<div class="dl-pm-meta">' +
                        '<span class="dl-pm-trend"><i class="feather-arrow-' + (trendUp ? "up" : "down") + '"></i> ' + Math.abs(stats.pct).toFixed(1) + '%</span>' +
                        '<span class="dl-pm-period">Past 30 days <i class="feather-chevron-down"></i></span>' +
                    "</div>" +
                "</div>" +
                '<div class="dl-pm-headline" data-dl-progress-orders>' + pmCompact(stats.sum) + "</div>" +
                '<div class="dl-pm-chart-wrap" data-pm-chart>' +
                    pmDotsSvg(id) +
                    pmCurveSvg(id, pts) +
                    pmBarsSvg(pts) +
                    '<span class="dl-pm-tip" aria-hidden="true"><strong></strong><small></small></span>' +
                "</div>" +
                '<div class="dl-pm-footer">' +
                    '<span class="dl-pm-delta"><b>' + (stats.step >= 0 ? "+" : "−") + Math.abs(stats.step) + '</b> today</span>' +
                    '<span class="dl-pm-statline">' +
                        '<span><b>' + stats.peak + '</b> peak</span>' +
                        '<span class="dl-pm-dot">·</span>' +
                        '<span><b>' + stats.low + '</b> low</span>' +
                        '<span class="dl-pm-dot">·</span>' +
                        '<span><b>' + stats.avg + '</b> avg</span>' +
                    "</span>" +
                "</div>" +
            "</div>";
    }

    function hydrateMetricCard(card) {
        if (!card || card.getAttribute("data-pm-ready") === "1") return;
        card.setAttribute("data-pm-ready", "1");

        var pts = pmGeometry(ORDERS);

        // View toggle (curve <-> bars)
        card.querySelectorAll("[data-pm-view-btn]").forEach(function (btn) {
            btn.addEventListener("click", function () {
                var view = btn.getAttribute("data-pm-view-btn");
                card.setAttribute("data-pm-view", view);
                card.querySelectorAll("[data-pm-view-btn]").forEach(function (b) {
                    b.classList.toggle("is-active", b === btn);
                });
            });
        });

        // Dark / light toggle
        var themeBtn = card.querySelector("[data-pm-theme-toggle]");
        if (themeBtn) {
            themeBtn.addEventListener("click", function () {
                var next = card.getAttribute("data-pm-theme") === "dark" ? "light" : "dark";
                card.setAttribute("data-pm-theme", next);
            });
        }

        // Hover tooltip over the chart
        var wrap = card.querySelector("[data-pm-chart]");
        var tip = wrap && wrap.querySelector(".dl-pm-tip");
        if (!wrap || !tip) return;

        function place(point) {
            var strong = tip.querySelector("strong");
            var small = tip.querySelector("small");
            strong.textContent = point.value + " orders";
            small.textContent = point.date;
            var leftPct = (point.x / PM_VB.w) * 100;
            var topPct = (point.y / PM_VB.h) * 100;
            tip.style.left = leftPct + "%";
            tip.style.top = topPct + "%";
            tip.classList.toggle("is-below", topPct < 26);
            tip.classList.add("is-visible");
            wrap.style.setProperty("--pm-cx", leftPct + "%");
            wrap.style.setProperty("--pm-cy", topPct + "%");
            wrap.classList.add("is-active");
            card.querySelectorAll(".dl-pm-point, .dl-pm-bar").forEach(function (n) {
                n.classList.toggle("is-active", Number(n.getAttribute("data-index")) === point.index);
            });
        }

        function nearest(localX) {
            return pts.reduce(function (best, p) {
                var dist = Math.abs(p.x - localX);
                return !best || dist < best.dist ? { p: p, dist: dist } : best;
            }, null).p;
        }

        function move(event) {
            var rect = wrap.getBoundingClientRect();
            if (!rect.width) return;
            var localX = Math.max(0, Math.min(PM_VB.w, ((event.clientX - rect.left) / rect.width) * PM_VB.w));
            place(nearest(localX));
        }

        function hide() {
            tip.classList.remove("is-visible", "is-below");
            wrap.classList.remove("is-active");
            card.querySelectorAll(".dl-pm-point, .dl-pm-bar").forEach(function (n) {
                n.classList.remove("is-active");
            });
        }

        wrap.addEventListener("pointermove", move);
        wrap.addEventListener("pointerenter", move);
        wrap.addEventListener("pointerleave", hide);

        // Default highlighted point (matches the reference screenshot)
        place(pts[Math.min(DEFAULT_INDEX, pts.length - 1)]);
    }

    function renderHero() {
        var hero = document.querySelector(".dl-offline-hero.is-data-analytics-course");
        var scene = hero && hero.querySelector(".dl-offline-scene");
        if (!scene) return;

        var spline = scene.querySelector("spline-viewer");
        if (spline && spline.style.display !== "none") spline.style.display = "none";

        // Remove any legacy card from earlier versions.
        var legacy = scene.querySelector(".dl-progress-hero-card");
        if (legacy) legacy.parentNode.removeChild(legacy);

        if (!scene.querySelector(".dl-pm-card")) {
            scene.insertAdjacentHTML("beforeend", heroMarkup());
            scheduleFeatherReplace();
        }
        hydrateMetricCard(scene.querySelector(".dl-pm-card"));
    }

    function renderAll() {
        renderScheduled = false;
        renderCards();
        renderHero();
    }

    function scheduleRender() {
        if (renderScheduled) return;
        renderScheduled = true;
        window.requestAnimationFrame(renderAll);
    }

    function boot() {
        scheduleRender();
        if (window.DL_SITE_STATE) updateMetrics(window.DL_SITE_STATE);

        window.addEventListener("dl:siteState", function (event) {
            scheduleRender();
            updateMetrics((event && event.detail) || window.DL_SITE_STATE);
        });

        var observer = new MutationObserver(scheduleRender);
        observer.observe(document.documentElement, { childList: true, subtree: true });
    }

    if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
    else boot();
}());
