/* Populates course-details-3.html with the clicked online course (?online=slug). */
(function () {
    "use strict";

    var params = new URLSearchParams(window.location.search);
    var key = params.get("online");
    if (!key) return; // no course selected → keep static demo content

    function esc(v) {
        return String(v == null ? "" : v)
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;").replace(/'/g, "&#39;");
    }

    function setByI18n(i18nKey, text) {
        if (text == null || text === "") return;
        var el = document.querySelector('[data-i18n="' + i18nKey + '"]');
        if (!el) return;
        el.textContent = text;
        el.removeAttribute("data-i18n"); // prevent the i18n script from overwriting
    }

    function setSideValue(labelKey, text) {
        if (text == null || text === "") return;
        var lbl = document.querySelector('[data-i18n="' + labelKey + '"]');
        if (!lbl || !lbl.nextElementSibling) return;
        lbl.nextElementSibling.textContent = text;
        lbl.nextElementSibling.removeAttribute("data-i18n");
    }

    // Set text if provided, otherwise hide the element (so empty admin fields don't show demo text).
    function setOrHideByI18n(i18nKey, text) {
        var el = document.querySelector('[data-i18n="' + i18nKey + '"]');
        if (!el) return;
        el.removeAttribute("data-i18n");
        if (text) { el.textContent = text; el.style.display = ""; }
        else { el.style.display = "none"; }
    }

    // Hide a whole section (#id) and its one-page nav tab.
    function hideSection(id) {
        var el = document.getElementById(id);
        if (el) el.style.display = "none";
        var nav = document.querySelector('.onepagenav a[href="#' + id + '"]');
        if (nav && nav.parentElement) nav.parentElement.style.display = "none";
    }

    var TYPE_ICON = { video: "feather-play-circle", article: "feather-file-text", quiz: "feather-help-circle", assignment: "feather-clipboard" };

    function checkList(items) {
        return (items || []).map(function (t) {
            return '<li><i class="feather-check"></i><span>' + esc(t) + "</span></li>";
        }).join("");
    }

    function ytId(url) {
        var m = String(url).match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{6,})/) || String(url).match(/[?&]v=([A-Za-z0-9_-]{6,})/);
        return m ? m[1] : "";
    }
    function vimeoId(url) {
        var m = String(url).match(/vimeo\.com\/(\d+)/);
        return m ? m[1] : "";
    }
    function driveId(url) {
        var m = String(url).match(/drive\.google\.com\/file\/d\/([^/?#]+)/) || String(url).match(/[?&]id=([^&#]+)/);
        return m ? m[1] : "";
    }

    function renderPreviewPlayer(videoUrl) {
        var container = document.querySelector("[data-preview-container]");
        if (!container) return;

        var url = videoUrl || "https://youtu.be/DR9lxZ8kPYQ?si=6F0HYwZFTtStRFxG";
        var yt = ytId(url), vm = vimeoId(url), drv = driveId(url);
        var controls = ["play-large", "play", "progress", "current-time", "mute", "volume", "fullscreen"];

        // Bunny Stream önizləmə → Bunny öz pleyeri (iframe)
        var bunny = String(url).match(/mediadelivery\.net\/(?:embed|play)\/(\d+)\/([a-f0-9-]{16,})/i);
        if (bunny) {
            container.innerHTML = '<div style="position:relative;padding-bottom:56.25%;height:0;border-radius:10px;overflow:hidden">' +
                '<iframe src="https://iframe.mediadelivery.net/embed/' + bunny[1] + '/' + bunny[2] + '?autoplay=false&preload=true&responsive=true" loading="lazy" style="position:absolute;inset:0;width:100%;height:100%;border:0" allow="accelerometer;autoplay;encrypted-media;gyroscope;picture-in-picture;fullscreen" allowfullscreen></iframe></div>';
            return;
        }

        if (window.Plyr && (yt || vm)) {
            container.innerHTML = '<div data-plyr-provider="' + (yt ? "youtube" : "vimeo") + '" data-plyr-embed-id="' + esc(yt || vm) + '"></div>';
            try {
                new window.Plyr(container.firstElementChild, {
                    controls: controls
                });
                return;
            } catch (e) {}
        }

        if (window.Plyr && (/\.(mp4|webm|ogg)(\?.*)?$/i.test(url) || drv)) {
            var src = drv ? ("video-proxy.php?id=" + encodeURIComponent(drv)) : url;
            container.innerHTML = '<video playsinline controls preload="metadata"><source src="' + esc(src) + '"></video>';
            try {
                new window.Plyr(container.querySelector("video"), {
                    controls: controls
                });
                return;
            } catch (e) {}
        }

        // Fallback
        var embed = yt ? "https://www.youtube.com/embed/" + yt : (vm ? "https://player.vimeo.com/video/" + vm : url);
        container.innerHTML = '<iframe class="radius-10 overflow-hidden" src="' + esc(embed) + '" allowfullscreen allow="autoplay; encrypted-media"></iframe>';
    }

    function render(course, details, curriculum) {
        details = details || {};
        curriculum = Array.isArray(curriculum) ? curriculum : [];

        document.title = "DatalabAcademy | " + (course.title || "Online təlim");

        /* hero */
        setByI18n("detail_title", course.title);
        setByI18n("detail_subtitle", details.subtitle || course.description);
        setByI18n("detail_author", details.instructor || "DatalabAcademy");
        setByI18n("detail_date", details.date);
        setByI18n("detail_reviews", "(" + (course.reviewCount || 0) + " Rəy)");
        var heroNum = document.querySelector("h6.number");
        if (heroNum) heroNum.textContent = details.enrolled ? details.enrolled : Number(course.students || 0).toLocaleString("en-US");

        /* preview video */
        renderPreviewPlayer(details.previewVideo);

        /* overview — show only what the admin entered */
        setOrHideByI18n("detail_learn_body", details.overview);
        setOrHideByI18n("detail_learn_more", details.overviewMore);

        /* outcomes → two balanced columns (empty if admin left it blank) */
        var hasOutcomes = details.outcomes && details.outcomes.length;
        var outBox = document.querySelector('[data-dol="outcomes"]');
        if (outBox) {
            if (hasOutcomes) {
                var half = Math.ceil(details.outcomes.length / 2);
                outBox.innerHTML =
                    '<div class="col-lg-6"><ul class="rbt-list-style-1">' + checkList(details.outcomes.slice(0, half)) + "</ul></div>" +
                    '<div class="col-lg-6"><ul class="rbt-list-style-1">' + checkList(details.outcomes.slice(half)) + "</ul></div>";
            } else {
                outBox.innerHTML = "";
            }
        }
        // Bütün İcmal sahələri boşdursa, bölməni tam gizlət
        if (!details.overview && !details.overviewMore && !hasOutcomes) {
            hideSection("overview");
        } else {
            var smOverview = document.querySelector("#overview .rbt-show-more-btn");
            if (smOverview && !hasOutcomes) smOverview.style.display = "none";
        }

        /* requirements & description — hide the column if empty, hide the box if both empty */
        var hasReq = details.requirements && details.requirements.length;
        var hasDesc = details.descriptionPoints && details.descriptionPoints.length;
        var reqBox = document.querySelector('[data-dol="requirements"]');
        if (reqBox) {
            if (hasReq) reqBox.innerHTML = checkList(details.requirements);
            else { var rc = reqBox.closest(".col-lg-6"); if (rc) rc.style.display = "none"; }
        }
        var descBox = document.querySelector('[data-dol="description"]');
        if (descBox) {
            if (hasDesc) descBox.innerHTML = checkList(details.descriptionPoints);
            else { var dc = descBox.closest(".col-lg-6"); if (dc) dc.style.display = "none"; }
        }
        if (!hasReq && !hasDesc) hideSection("details");

        /* curriculum accordion */
        var acc = document.getElementById("accordionExampleb2");
        var lessonTotal = 0;
        if (!curriculum.length) hideSection("coursecontent");
        if (acc && curriculum.length) {
            acc.innerHTML = curriculum.map(function (section, si) {
                var lessons = (section.lessons || []);
                lessonTotal += lessons.length;
                var rows = lessons.map(function (ls) {
                    var icon = TYPE_ICON[ls.type] || TYPE_ICON.video;
                    var right = ls.preview
                        ? '<span class="rbt-badge variation-03 bg-primary-opacity"><i class="feather-eye"></i> Preview</span>'
                        : (ls.locked ? '<span class="course-lock"><i class="feather-lock"></i></span>' : "");
                    var href = "lesson.php?online=" + encodeURIComponent(key) + "&lesson=" + encodeURIComponent(ls.id);
                    return '<li><a href="' + href + '">' +
                        '<div class="course-content-left"><i class="' + icon + '"></i> <span class="text">' + esc(ls.title) + "</span></div>" +
                        '<div class="course-content-right">' + (ls.duration ? '<span class="min-lable">' + esc(ls.duration) + "</span>" : "") + right + "</div></a></li>";
                }).join("");
                var open = si === 0;
                return '<div class="accordion-item card">' +
                    '<h2 class="accordion-header card-header" id="dolHead' + si + '">' +
                    '<button class="accordion-button' + (open ? "" : " collapsed") + '" type="button" data-bs-toggle="collapse" data-bs-target="#dolCol' + si + '" aria-expanded="' + (open ? "true" : "false") + '" aria-controls="dolCol' + si + '">' +
                    esc(section.title) + (section.duration ? ' <span class="rbt-badge-5 ml--10">' + esc(section.duration) + "</span>" : "") +
                    "</button></h2>" +
                    '<div id="dolCol' + si + '" class="accordion-collapse collapse' + (open ? " show" : "") + '" aria-labelledby="dolHead' + si + '" data-bs-parent="#accordionExampleb2">' +
                    '<div class="accordion-body card-body pr--0"><ul class="rbt-course-main-content liststyle">' + rows + "</ul></div></div></div>";
            }).join("");
        }

        /* sidebar pricing */
        var cur = document.querySelector(".current-price");
        if (cur) { cur.textContent = "$" + Number(course.price || 0); cur.setAttribute("data-usd", course.price || 0); }
        var off = document.querySelector(".off-price");
        if (off) {
            if (details.oldPrice) { off.textContent = "$" + Number(details.oldPrice); off.setAttribute("data-usd", details.oldPrice); off.style.display = ""; }
            else { off.style.display = "none"; }
        }
        setByI18n("detail_discount_time", details.discountText);

        /* sidebar feature list */
        setByI18n("detail_start_date_value", details.startDate);
        setSideValue("detail_enrolled", details.registration || course.students);
        setSideValue("detail_lectures", lessonTotal || course.lessons);
        setByI18n("detail_skill_level_value", course.level || details.level);
        setByI18n("detail_language_value", details.language);
        setSideValue("detail_quizzes", details.quizzes);
        if (details.certificate) setSideValue("detail_certificate", details.certificate);
        setSideValue("detail_pass_percentage", details.passPercentage);

        /* Təlimçi bölməsi demo-dur — gizlət (Rəylər ayrıca idarə olunur) */
        hideSection("intructor");

        if (window.feather && typeof window.feather.replace === "function") window.feather.replace();
    }

    function starsHtml(rating) {
        var s = "";
        for (var i = 0; i < 5; i++) {
            s += '<i class="fa fa-star" style="color:' + (i < rating ? "#f59e0b" : "#cbd5e1") + ';margin-right:2px"></i>';
        }
        return s;
    }

    function renderReviews(course, reviews) {
        var box = document.getElementById("review");
        if (!box) return;
        reviews = Array.isArray(reviews) ? reviews : [];
        var avg = reviews.length ? (reviews.reduce(function (a, r) { return a + Number(r.rating || 0); }, 0) / reviews.length) : 0;

        var cards = reviews.length ? reviews.map(function (r) {
            return '<div style="padding:18px 0;border-top:1px solid rgba(148,163,184,.25)">' +
                '<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">' +
                '<strong style="font-size:16px">' + esc(r.name || "Tələbə") + "</strong>" +
                '<span style="white-space:nowrap">' + starsHtml(Number(r.rating || 0)) + "</span>" +
                '<span style="margin-left:auto;font-size:12px;opacity:.7">' + esc(String(r.date || "").slice(0, 10)) + "</span></div>" +
                '<p style="margin:8px 0 0">' + esc(r.body || "") + "</p></div>";
        }).join("") : '<p style="opacity:.7;padding:14px 0">Hələ təsdiqlənmiş rəy yoxdur. İlk rəyi sən yaz!</p>';

        box.innerHTML =
            '<div class="rbt-course-feature-box rbt-border-with-box">' +
            '<div class="section-title mb--20"><h4 class="rbt-title-style-3">Rəylər</h4></div>' +
            '<div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:10px">' +
            '<span style="font-size:34px;font-weight:800;line-height:1">' + avg.toFixed(1) + "</span>" +
            "<span>" + starsHtml(Math.round(avg)) + '<br><small style="opacity:.7">' + reviews.length + " rəy</small></span></div>" +
            cards +
            '<div data-review-form-slot style="margin-top:20px"></div>' +
            "</div>";

        renderReviewForm(course);
    }

    function renderReviewForm(course) {
        var slot = document.querySelector("[data-review-form-slot]");
        if (!slot) return;
        fetch("api/student-auth.php?action=online-review-state&course=" + encodeURIComponent(course.id) + "&_t=" + Date.now(), { credentials: "same-origin", cache: "no-store" })
            .then(function (r) { return r.json(); })
            .then(function (st) {
                if (!st || !st.ok) return;
                if (!st.loggedIn) {
                    slot.innerHTML = '<div style="padding:14px 16px;border:1px dashed rgba(148,163,184,.4);border-radius:12px">Rəy yazmaq üçün <a href="login.html" style="color:#3b82f6;font-weight:700">daxil olun</a>.</div>';
                    return;
                }
                if (!st.enrolled) {
                    slot.innerHTML = '<div style="padding:14px 16px;border:1px dashed rgba(148,163,184,.4);border-radius:12px">Rəy yazmaq üçün əvvəlcə bu kursa qoşulmalısınız.</div>';
                    return;
                }
                var mine = st.myReview;
                var pendNote = mine && mine.status === "pending" ? '<div style="margin-bottom:8px;color:#d97706;font-weight:600">Rəyiniz admin təsdiqini gözləyir.</div>' : "";
                var curRating = mine ? mine.rating : 5;
                var opts = "";
                for (var i = 5; i >= 1; i--) opts += '<option value="' + i + '"' + (i === curRating ? " selected" : "") + ">" + i + " ulduz</option>";
                slot.innerHTML =
                    '<form data-review-form style="padding:16px;border:1px solid rgba(148,163,184,.3);border-radius:12px">' +
                    pendNote +
                    '<h6 style="margin:0 0 10px">' + (mine ? "Rəyini yenilə" : "Rəy yaz") + "</h6>" +
                    '<div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:10px">' +
                    '<label style="font-weight:600">Reytinq</label><select name="rating" style="padding:8px 10px;border-radius:8px;border:1px solid rgba(148,163,184,.4);background:transparent;color:inherit">' + opts + "</select></div>" +
                    '<textarea name="body" rows="4" placeholder="Təcrübənizi yazın..." style="width:100%;padding:12px;border-radius:10px;border:1px solid rgba(148,163,184,.4);background:transparent;color:inherit">' + esc(mine ? mine.body : "") + "</textarea>" +
                    '<div data-review-msg style="margin:8px 0;font-weight:600"></div>' +
                    '<button type="submit" class="rbt-btn btn-gradient btn-sm">Göndər</button>' +
                    "</form>";

                var form = slot.querySelector("[data-review-form]");
                form.addEventListener("submit", function (e) {
                    e.preventDefault();
                    var msg = form.querySelector("[data-review-msg]");
                    var payload = { courseId: course.id, rating: Number(form.elements.rating.value), body: form.elements.body.value };
                    fetch("api/student-auth.php?action=submit-online-review", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        credentials: "same-origin",
                        body: JSON.stringify(payload)
                    })
                        .then(function (r) { return r.json(); })
                        .then(function (res) {
                            if (msg) { msg.style.color = res.ok ? "#16a34a" : "#dc2626"; msg.textContent = res.message || (res.ok ? "Göndərildi." : "Xəta."); }
                        })
                        .catch(function () { if (msg) { msg.style.color = "#dc2626"; msg.textContent = "Şəbəkə xətası."; } });
                });
            })
            .catch(function () { /* ignore */ });
    }

    fetch("api/admin.php?action=public&_t=" + Date.now(), { credentials: "same-origin", cache: "no-store" })
        .then(function (r) { return r.json(); })
        .then(function (payload) {
            if (!payload || !payload.ok || !payload.data) return;
            var data = payload.data;
            var list = Array.isArray(data.onlineCourses) ? data.onlineCourses : [];
            var course = list.filter(function (c) { return c.slug === key; })[0] ||
                list.filter(function (c) { return String(c.id) === String(key); })[0];
            if (!course) return; // unknown course → keep static content
            var details = (data.onlineCourseDetails || {})[course.id] || {};
            var curriculum = (data.onlineCurriculum || {})[course.id] || [];
            render(course, details, curriculum);
            renderReviews(course, (data.onlineReviews || {})[course.id] || []);
        })
        .catch(function () { /* network error → keep static content */ });
}());
