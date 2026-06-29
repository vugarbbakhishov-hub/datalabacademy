/*
 * DatalabAcademy — paylaşılan katalog motoru (course-filter-two-toggle.html üçün).
 * - Dil dəstəyi (AZ/EN), data-i18n* atributları, localStorage("siteLang") index.php ilə ortaq.
 * - Valyuta (AZN/USD), data-usd atributları, localStorage("siteCurrency") index.php ilə ortaq.
 * - Kurslar admin paneldəki bazadan dinamik gəlir: api/admin.php?action=public
 *
 * index.php-dəki inline motorla eyni açar və qaydalardan istifadə edir ki,
 * səhifələr arası dil/valyuta seçimi qorunsun.
 */
(function () {
    "use strict";

    var USD_TO_AZN = 1.70; // index.php ilə eyni olmalıdır

    /* ----------------------------- Lüğət ----------------------------- */
    var dict = {
        az: {
            lang_az: "Azərbaycan",
            lang_en: "English",
            nav_home: "Ana səhifə",
            nav_courses: "Kurslar",
            nav_online_training: "Online təlimlər",
            nav_about: "Haqqımızda",
            nav_blog: "Bloq",
            nav_contact: "Əlaqə",
            join_now: "İndi qoşul",
            search_placeholder: "Kurs axtarın",

            page_title: "Onlayn Təlimlər",
            breadcrumb_home: "Ana səhifə",
            breadcrumb_current: "Onlayn Təlimlər",
            catalog_lead: "Data, SQL, Excel və AI üzrə praktik onlayn kurslar.",
            filter_sort: "Sırala",
            filter_all: "Hamısı",
            results_prefix: "Tapıldı:",
            results_suffix: "kurs",

            card_lessons: "dərs",
            card_students: "tələbə",
            card_learn_more: "Ətraflı",
            card_add_cart: "Səbətə əlavə et",
            card_free: "Pulsuz",
            badge_bestseller: "Bestseller",

            loading: "Kurslar yüklənir...",
            empty: "Hazırda aktiv kurs yoxdur.",
            load_error: "Kurslar yüklənmədi. Səhifəni yeniləyin.",

            footer_about: "DatalabAcademy praktiki Data Analitika, SQL, Excel və AI təlimləri ilə karyera bacarıqlarınızı inkişaf etdirir.",
            footer_quick_links: "Keçidlər",
            footer_courses: "Kurslar",
            footer_contact: "Əlaqə",
            footer_rights: "Bütün hüquqlar qorunur."
        },
        en: {
            lang_az: "Azərbaycan",
            lang_en: "English",
            nav_home: "Home",
            nav_courses: "Courses",
            nav_online_training: "Online training",
            nav_about: "About",
            nav_blog: "Blog",
            nav_contact: "Contact",
            join_now: "Join Now",
            search_placeholder: "Search course",

            page_title: "Online Training",
            breadcrumb_home: "Home",
            breadcrumb_current: "Online Training",
            catalog_lead: "Practical online courses in Data, SQL, Excel and AI.",
            filter_sort: "Sort",
            filter_all: "All",
            results_prefix: "Found:",
            results_suffix: "courses",

            card_lessons: "lessons",
            card_students: "students",
            card_learn_more: "Learn more",
            card_add_cart: "Add to cart",
            card_free: "Free",
            badge_bestseller: "Bestseller",

            loading: "Loading courses...",
            empty: "No active courses right now.",
            load_error: "Could not load courses. Please refresh.",

            footer_about: "DatalabAcademy builds your career skills through practical Data Analytics, SQL, Excel and AI training.",
            footer_quick_links: "Quick links",
            footer_courses: "Courses",
            footer_contact: "Contact",
            footer_rights: "All rights reserved."
        }
    };

    function t(lang, key) {
        var table = dict[lang] || dict.az;
        return (key in table) ? table[key] : (dict.az[key] !== undefined ? dict.az[key] : key);
    }

    // Yalnız bu motorun lüğətindəki açarlar — datalab-shared.js-in idarə etdiyi
    // header/footer mətnlərini səhvən əzməmək üçün.
    function ownsKey(key) {
        return (key in dict.az) || (key in dict.en);
    }

    /* ----------------------------- Dil ----------------------------- */
    function currentLang() {
        return localStorage.getItem("siteLang") || "az";
    }

    function setLang(lang) {
        var normalized = (lang === "en") ? "en" : "az";
        document.documentElement.setAttribute("lang", normalized);

        document.querySelectorAll("[data-i18n]").forEach(function (el) {
            var key = el.getAttribute("data-i18n");
            if (ownsKey(key)) el.textContent = t(normalized, key);
        });
        document.querySelectorAll("[data-i18n-html]").forEach(function (el) {
            var key = el.getAttribute("data-i18n-html");
            if (ownsKey(key)) el.innerHTML = t(normalized, key);
        });
        document.querySelectorAll("[data-i18n-placeholder]").forEach(function (el) {
            var key = el.getAttribute("data-i18n-placeholder");
            if (ownsKey(key)) el.setAttribute("placeholder", t(normalized, key));
        });
        document.querySelectorAll("[data-i18n-title]").forEach(function (el) {
            var key = el.getAttribute("data-i18n-title");
            if (ownsKey(key)) el.setAttribute("title", t(normalized, key));
        });

        document.querySelectorAll(".js-current-lang").forEach(function (label) {
            label.textContent = (normalized === "en") ? "English" : "Azərbaycan";
        });

        localStorage.setItem("siteLang", normalized);
    }

    /* ----------------------------- Valyuta ----------------------------- */
    function currentCurrency() {
        return localStorage.getItem("siteCurrency") || "AZN";
    }

    function formatPrice(usd, cur) {
        usd = parseFloat(usd) || 0;
        if (cur === "AZN") {
            return (usd * USD_TO_AZN).toFixed(0) + " ₼";
        }
        return "$" + usd;
    }

    function applyCurrency() {
        var cur = currentCurrency();
        document.querySelectorAll("[data-usd]").forEach(function (el) {
            el.textContent = formatPrice(el.getAttribute("data-usd"), cur);
        });
        document.querySelectorAll(".js-current-currency").forEach(function (label) {
            label.textContent = cur;
        });
    }

    /* ----------------------------- Kurslar ----------------------------- */
    function esc(value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;");
    }

    function courseCard(course, lang) {
        var price = parseFloat(course.price) || 0;
        var lessons = parseInt(course.lessons, 10) || 0;
        var students = parseInt(course.students, 10) || 0;
        var img = course.image || "assets/images/course/course-online-01.jpg";
        var detail = "course-details-4.php?course=" + encodeURIComponent(course.id);
        var priceHtml = price > 0
            ? '<span class="current-price" data-usd="' + price + '">' + formatPrice(price, currentCurrency()) + "</span>"
            : '<span class="current-price" data-i18n="card_free">' + t(lang, "card_free") + "</span>";

        return '' +
            '<div class="course-grid-3">' +
            '  <div class="rbt-card variation-01 rbt-hover">' +
            '    <div class="rbt-card-img">' +
            '      <a href="' + detail + '">' +
            '        <img src="' + esc(img) + '" alt="' + esc(course.title) + '">' +
            '      </a>' +
            '    </div>' +
            '    <div class="rbt-card-body">' +
            '      <div class="rbt-card-top">' +
            '        <div class="rbt-review">' +
            '          <div class="rating">' +
            '            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>' +
            '          </div>' +
            '          <span class="rating-count"> (' + students + ")</span>" +
            '        </div>' +
            '        <div class="rbt-bookmark-btn">' +
            '          <a class="rbt-round-btn" title="Bookmark" href="#"><i class="feather-bookmark"></i></a>' +
            '        </div>' +
            '      </div>' +
            '      <h4 class="rbt-card-title"><a href="' + detail + '">' + esc(course.title) + "</a></h4>" +
            '      <ul class="rbt-meta">' +
            '        <li><i class="feather-book"></i>' + lessons + ' <span data-i18n="card_lessons">' + t(lang, "card_lessons") + "</span></li>" +
            '        <li><i class="feather-users"></i>' + students + ' <span data-i18n="card_students">' + t(lang, "card_students") + "</span></li>" +
            '      </ul>' +
            '      <p class="rbt-card-text">' + esc(course.description || "") + "</p>" +
            '      <div class="rbt-card-bottom">' +
            '        <div class="rbt-price">' + priceHtml + "</div>" +
            '        <a class="rbt-btn-link" href="' + detail + '"><span data-i18n="card_learn_more">' + t(lang, "card_learn_more") + '</span><i class="feather-arrow-right"></i></a>' +
            '      </div>' +
            '    </div>' +
            '  </div>' +
            '</div>';
    }

    function renderCourses(courses) {
        var grid = document.querySelector("[data-catalog-grid]");
        if (!grid) return;
        var lang = currentLang();

        // Bu səhifə online_courses cədvəlindəki onlayn təlimləri göstərir.
        courses = courses || [];

        if (!courses || !courses.length) {
            grid.innerHTML = '<p class="rbt-card-text" style="padding:24px">' + t(lang, "empty") + "</p>";
            return;
        }

        grid.innerHTML = courses.map(function (c) { return courseCard(c, lang); }).join("");

        var counter = document.querySelector("[data-catalog-count]");
        if (counter) {
            counter.textContent = t(lang, "results_prefix") + " " + courses.length + " " + t(lang, "results_suffix");
        }

        // yeni qurulan elementlərə dil + valyutanı tətbiq et
        setLang(lang);
        applyCurrency();
        if (window.feather && typeof window.feather.replace === "function") {
            window.feather.replace();
        }
    }

    function loadCourses() {
        var grid = document.querySelector("[data-catalog-grid]");
        if (!grid) return;
        grid.innerHTML = '<p class="rbt-card-text" style="padding:24px">' + t(currentLang(), "loading") + "</p>";

        fetch("api/admin.php?action=public", { headers: { "Accept": "application/json" } })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                if (json && json.ok && json.data && Array.isArray(json.data.onlineCourses)) {
                    renderCourses(json.data.onlineCourses);
                } else {
                    grid.innerHTML = '<p class="rbt-card-text" style="padding:24px">' + t(currentLang(), "load_error") + "</p>";
                }
            })
            .catch(function () {
                grid.innerHTML = '<p class="rbt-card-text" style="padding:24px">' + t(currentLang(), "load_error") + "</p>";
            });
    }

    /* ----------------------------- Hadisələr ----------------------------- */
    function bindSwitchers() {
        document.querySelectorAll(".switcher-language [data-lang]").forEach(function (a) {
            a.addEventListener("click", function (e) {
                e.preventDefault();
                setLang(this.getAttribute("data-lang"));
                applyCurrency();
            });
        });
        document.querySelectorAll(".currency-menu [data-currency]").forEach(function (a) {
            a.addEventListener("click", function (e) {
                e.preventDefault();
                var cur = this.getAttribute("data-currency");
                localStorage.setItem("siteCurrency", cur === "USD" ? "USD" : "AZN");
                localStorage.setItem("siteCurrencyTouched", "1");
                applyCurrency();
            });
        });
    }

    function init() {
        setLang(currentLang());
        applyCurrency();
        bindSwitchers();
        loadCourses();
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }
})();
