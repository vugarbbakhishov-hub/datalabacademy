(function () {
    "use strict";

    function esc(value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    }

    function lang() {
        return document.documentElement.lang || localStorage.getItem("siteLang") || "az";
    }

    function text(az, en) {
        return lang() === "en" ? en : az;
    }

    function stars(value) {
        var count = Math.max(1, Math.min(5, Number(value || 5)));
        return "★★★★★".slice(0, count) + "☆☆☆☆☆".slice(0, 5 - count);
    }

    function renderComments(list, rows) {
        if (!list) return;
        if (!Array.isArray(rows) || !rows.length) {
            list.innerHTML = '<p class="datalab-comments-empty">' + esc(text("Hələ təsdiqlənmiş rəy yoxdur.", "No approved comments yet.")) + "</p>";
            return;
        }
        list.innerHTML = rows.map(function (row) {
            return [
                '<article class="datalab-comment-card">',
                '<div class="datalab-comment-head">',
                '<strong>' + esc(row.authorName || text("Tələbə", "Student")) + "</strong>",
                '<span>' + esc(stars(row.rating)) + "</span>",
                "</div>",
                '<p>' + esc(row.commentText || "") + "</p>",
                row.lessonTitle ? '<small>' + esc(row.lessonTitle) + "</small>" : "",
                "</article>"
            ].join("");
        }).join("");
    }

    function boot() {
        var wrap = document.querySelector("[data-lesson-comments-wrap]");
        if (!wrap || !window.fetch) return;

        var params = new URLSearchParams(window.location.search);
        var courseId = wrap.getAttribute("data-course-id") || params.get("course") || "";
        var lessonId = wrap.getAttribute("data-lesson-id") || params.get("lesson") || "";
        var list = wrap.querySelector("[data-lesson-comments-list]");
        var form = wrap.querySelector("[data-lesson-comment-form]");
        var message = wrap.querySelector("[data-lesson-comment-message]");

        function showMessage(value, isError) {
            if (!message) return;
            message.hidden = false;
            message.textContent = value;
            message.classList.toggle("is-error", !!isError);
        }

        function load() {
            fetch("api/admin.php?action=lesson-comments&course=" + encodeURIComponent(courseId) + "&lesson=" + encodeURIComponent(lessonId))
                .then(function (response) { return response.json(); })
                .then(function (payload) {
                    renderComments(list, payload && payload.ok ? payload.data : []);
                })
                .catch(function () {
                    renderComments(list, []);
                });
        }

        if (form) {
            form.addEventListener("submit", function (event) {
                event.preventDefault();
                var data = {
                    courseId: courseId,
                    lessonId: lessonId,
                    authorName: form.elements.authorName ? form.elements.authorName.value.trim() : "",
                    authorEmail: form.elements.authorEmail ? form.elements.authorEmail.value.trim() : "",
                    rating: form.elements.rating ? form.elements.rating.value : 5,
                    commentText: form.elements.commentText ? form.elements.commentText.value.trim() : ""
                };

                fetch("api/admin.php?action=submit-comment", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(data)
                })
                    .then(function (response) {
                        return response.json().then(function (payload) {
                            return { status: response.status, payload: payload };
                        });
                    })
                    .then(function (result) {
                        var payload = result.payload;
                        if (result.status === 401) {
                            var target = window.location.pathname.split("/").pop() + window.location.search;
                            window.location.href = "login.html?mode=login&redirect=" + encodeURIComponent(target);
                            return;
                        }
                        if (!payload || !payload.ok) throw new Error((payload && payload.message) || text("Rəy göndərilmədi.", "Comment was not sent."));
                        form.reset();
                        showMessage(text("Rəyiniz göndərildi. Admin təsdiqindən sonra görünəcək.", "Your comment was sent and will appear after admin approval."), false);
                    })
                    .catch(function (err) {
                        showMessage((err && err.message) || text("Rəy göndərilmədi.", "Comment was not sent."), true);
                    });
            });
        }

        load();
    }

    if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
    else boot();
}());
