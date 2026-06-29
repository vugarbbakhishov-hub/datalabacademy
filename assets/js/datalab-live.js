(function () {
    "use strict";

    /*
     * DatalabAcademy — sayt <-> admin database körpüsü.
     * datalab-shared.js public API-dən datanı çəkib window.DL_SITE_STATE-ə yazır
     * və "dl:siteState" eventini atır. Bu fayl həmin datanı səhifəyə tətbiq edir.
     */

    var DEFAULT_HERO_TITLE = "Karyeranıza təkan verən onlayn təhsil platforması";

    var TYPE_ICONS = {
        video: "feather-play-circle",
        article: "feather-file-text",
        quiz: "feather-help-circle",
        assignment: "feather-clipboard"
    };

    function esc(value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;").replace(/'/g, "&#39;");
    }

    function textNodesOnly(el, value) {
        // replace text without destroying child elements like <img> or <i>
        if (!el) return;
        var hasElementChild = false;
        Array.prototype.slice.call(el.childNodes).forEach(function (node) {
            if (node.nodeType === 1 && node.tagName !== "BR") hasElementChild = true;
        });
        if (!hasElementChild) el.textContent = value;
    }

    /* ---------------- content (hero, əlaqə, footer) ---------------- */

    function applyContent(content) {
        if (!content) return;

        if (content.heroTitle && content.heroTitle !== DEFAULT_HERO_TITLE) {
            var heroTitle = document.querySelector('[data-i18n-html="hero_title"]');
            if (heroTitle) heroTitle.textContent = content.heroTitle;
        }
        if (content.heroText) {
            var heroDesc = document.querySelector('[data-i18n-html="hero_desc"]');
            if (heroDesc) heroDesc.textContent = content.heroText;
        }
        if (content.heroCta) {
            document.querySelectorAll('[data-i18n="hero_cta"]').forEach(function (el) {
                el.textContent = content.heroCta;
            });
        }

        if (content.phone) {
            var telHref = "tel:" + content.phone.replace(/[^+\d]/g, "");
            document.querySelectorAll('a[href^="tel:"]').forEach(function (link) {
                link.href = telHref;
                textNodesOnly(link, content.phone);
            });
        }
        if (content.email) {
            document.querySelectorAll('a[href^="mailto:"]').forEach(function (link) {
                link.href = "mailto:" + content.email;
                textNodesOnly(link, content.email);
            });
        }
        if (content.footerText) {
            document.querySelectorAll('[data-i18n="footer_about_text"]').forEach(function (el) {
                el.textContent = content.footerText;
            });
        }
    }

    /* ---------------- SEO (yalnız ana səhifə) ---------------- */

    function isHomePage() {
        var path = window.location.pathname;
        return /(^\/$|index\.html$)/.test(path);
    }

    function applySeo(seo) {
        if (!seo || !isHomePage()) return;
        if (seo.siteTitle) document.title = seo.siteTitle;
        if (seo.description) {
            var meta = document.querySelector('meta[name="description"]');
            if (meta) meta.setAttribute("content", seo.description);
        }
        if (seo.keywords) {
            var kw = document.querySelector('meta[name="keywords"]');
            if (kw) kw.setAttribute("content", seo.keywords);
        }
    }

    /* ---------------- kurs kartları və qiymətlər ---------------- */

    function applyCourses(courseList) {
        if (!Array.isArray(courseList) || !courseList.length) return;

        courseList.forEach(function (course) {
            var id = String(course.id);

            document.querySelectorAll('[data-course-id="' + id + '"]').forEach(function (link) {
                // title links (text-only) və şəkillər
                textNodesOnly(link, course.title);
                var img = link.querySelector("img");
                if (img && course.image) {
                    img.src = course.image;
                    img.alt = course.title;
                }

                // kartın daxili sahələri
                var card = link.closest(".rbt-card, .minicart-item, .product-content");
                if (!card) return;

                var desc = card.querySelector(".rbt-card-text");
                if (desc && course.description) desc.textContent = course.description;

                var meta = card.querySelectorAll(".rbt-meta li");
                if (meta.length >= 2) {
                    meta[0].innerHTML = '<i class="feather-book"></i>' + esc(course.lessons) + " Dərs";
                    meta[1].innerHTML = '<i class="feather-users"></i>' + esc(course.students) + " Tələbə";
                }

                var reviewCount = card.querySelector(".rating-count");
                if (reviewCount) reviewCount.textContent = " (" + esc(course.reviewCount || 0) + " Rəy)";

                var rating = Math.max(0, Math.min(5, Number(course.rating || 0)));
                card.querySelectorAll(".rating i").forEach(function (star, index) {
                    star.style.opacity = index < Math.round(rating) ? "1" : "0.25";
                });

                card.querySelectorAll("[data-usd]").forEach(function (price) {
                    price.setAttribute("data-usd", String(course.price));
                    price.textContent = "$" + Number(course.price || 0).toLocaleString("en-US");
                });
            });

            // qiymət sahələri kartdan kənar id ilə işarələnibsə
            document.querySelectorAll('[data-course-price="' + id + '"]').forEach(function (el) {
                el.setAttribute("data-usd", String(course.price));
                el.textContent = "$" + Number(course.price || 0).toLocaleString("en-US");
            });
        });
    }

    /* ---------------- kurikulum (dərs proqramı) ---------------- */

    function parseMinutes(label) {
        var h = /(\d+)\s*saat/.exec(label || "");
        var m = /(\d+)\s*min/.exec(label || "");
        return (h ? Number(h[1]) * 60 : 0) + (m ? Number(m[1]) : 0);
    }

    function lessonLi(lesson) {
        var icon = TYPE_ICONS[lesson.type] || TYPE_ICONS.video;
        return [
            "<li><a href=\"lesson.html\">",
            '<div class="course-content-left">',
            '<i class="' + icon + '"></i> <span class="text">' + esc(lesson.title) + "</span>",
            lesson.preview ? ' <span class="rbt-badge-5 ml--5">Preview</span>' : "",
            "</div>",
            '<div class="course-content-right">',
            lesson.duration ? '<span class="min-lable">' + esc(lesson.duration) + "</span>" : "",
            '<span class="rbt-check unread"><i class="feather-circle"></i></span>',
            "</div></a></li>"
        ].join("");
    }

    function applyCurriculum(curriculum) {
        var container = document.querySelector("[data-dl-curriculum]") || document.getElementById("accordionExampleb2");
        if (!container || !curriculum) return;

        var params = new URLSearchParams(window.location.search);
        var courseId = params.get("course") || "1";
        var sections = curriculum[courseId];
        if (!Array.isArray(sections) || !sections.length) return;

        container.innerHTML = sections.map(function (section, index) {
            var lessons = section.lessons || [];
            var headId = "dlCurHead" + index;
            var bodyId = "dlCurBody" + index;
            return [
                '<div class="accordion-item card">',
                '<h2 class="accordion-header card-header" id="' + headId + '">',
                '<button class="accordion-button' + (index === 0 ? "" : " collapsed") + '" type="button" data-bs-toggle="collapse" aria-expanded="' + (index === 0 ? "true" : "false") + '" data-bs-target="#' + bodyId + '" aria-controls="' + bodyId + '">',
                "<span>" + esc(section.title) + "</span>",
                (section.locked ? ' <i class="feather-lock ml--5"></i>' : "") +
                ' <span class="rbt-badge-5 ml--10">' + lessons.length + " dərs</span>",
                "</button></h2>",
                '<div id="' + bodyId + '" class="accordion-collapse collapse' + (index === 0 ? " show" : "") + '" aria-labelledby="' + headId + '">',
                '<div class="accordion-body card-body">',
                '<ul class="rbt-course-main-content liststyle">',
                lessons.map(lessonLi).join(""),
                "</ul></div></div></div>"
            ].join("");
        }).join("");
    }

    /* ---------------- blog/news cards ---------------- */

    function blogImage(blog) {
        return blog && (blog.image || blog.img || blog.cover || blog.thumbnail || "");
    }

    function applyBlogToCard(blog) {
        var id = String(blog.id || "");
        if (!id) return;

        var image = blogImage(blog);
        var selectors = [
            '[data-blog-id="' + id + '"]',
            '[data-news-id="' + id + '"]',
            '[data-blog-image="' + id + '"]',
            '[data-news-image="' + id + '"]'
        ];

        document.querySelectorAll(selectors.join(",")).forEach(function (el) {
            var img = el.matches("img") ? el : el.querySelector("img");
            if (img && image) {
                img.src = image;
                img.alt = blog.title || blog.category || img.alt || "";
                img.removeAttribute("srcset");
            }

            var card = el.closest(".rbt-card, .rbt-blog-grid, .dl-feat-card, .dl-small-card, .recent-post-list li") || el;
            if (!card) return;

            var title = card.querySelector(".rbt-card-title a, .dl-feat-title, .dl-small-title, .title a");
            if (title && blog.title) title.textContent = blog.title;

            var text = card.querySelector(".rbt-card-text, .dl-small-desc");
            if (text && (blog.excerpt || blog.description)) text.textContent = blog.excerpt || blog.description;

            var link = blog.url || blog.link;
            if (link) {
                card.querySelectorAll("a").forEach(function (anchor) {
                    anchor.href = link;
                });
            }
        });
    }

    function applyBlogs(blogs) {
        if (!Array.isArray(blogs) || !blogs.length) return;
        blogs.forEach(applyBlogToCard);
    }

    /* ---------------- boot ---------------- */

    function applyAll(state) {
        if (!state) return;
        applyContent(state.content);
        applySeo(state.seo);
        applyCourses(state.courses);
        applyCurriculum(state.curriculum);
        applyBlogs(state.blogs);
    }

    function ensureState() {
        // shared.js olmayan səhifələrdə (məs. index.php) datanı özümüz çəkirik
        if (!window.DL_SITE_STATE) {
            try {
                var cached = JSON.parse(localStorage.getItem("dlSiteState") || "null");
                if (cached) window.DL_SITE_STATE = cached;
            } catch (err) {}
        }
        if (window.fetch && !window.DL_SITE_FETCHING) {
            window.DL_SITE_FETCHING = true;
            fetch("api/admin.php?action=public")
                .then(function (response) { return response.json(); })
                .then(function (payload) {
                    if (!payload.ok || !payload.data) return;
                    localStorage.setItem("dlSiteState", JSON.stringify(payload.data));
                    window.DL_SITE_STATE = payload.data;
                    document.dispatchEvent(new CustomEvent("dl:siteState", { detail: payload.data }));
                })
                .catch(function () {});
        }
    }

    function boot() {
        ensureState();
        if (window.DL_SITE_STATE) applyAll(window.DL_SITE_STATE);
        document.addEventListener("dl:siteState", function (event) {
            applyAll(event.detail || window.DL_SITE_STATE);
        });
    }

    if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
    else boot();
}());
