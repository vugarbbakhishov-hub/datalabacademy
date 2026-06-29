/* Online lesson page: playlist sidebar + site player (Plyr) with 30s skip,
   quality/speed menu, and resume-from-last-position. */
(function () {
    "use strict";

    var data = window.DL_ONLINE_LESSON;
    if (!data) return;

    function esc(v) {
        return String(v == null ? "" : v)
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;").replace(/'/g, "&#39;");
    }

    var TYPE_ICON = { video: "feather-play-circle", article: "feather-file-text", quiz: "feather-help-circle", assignment: "feather-clipboard" };

    /* ---------- playlist sidebar ---------- */
    function renderPlaylist() {
        var box = document.querySelector("[data-dl-curriculum]") || document.getElementById("accordionExampleb2");
        if (!box) return;
        var sections = Array.isArray(data.sections) ? data.sections : [];
        if (!sections.length) { return; }

        // current lesson-in section index (so we open the right group)
        var currentSec = 0;
        sections.forEach(function (sec, si) {
            (sec.lessons || []).forEach(function (ls) { if (String(ls.id) === String(data.currentLesson)) currentSec = si; });
        });

        box.innerHTML = sections.map(function (sec, si) {
            var open = si === currentSec;
            var lessons = (sec.lessons || []).map(function (ls) {
                var icon = TYPE_ICON[ls.type] || TYPE_ICON.video;
                var isCurrent = String(ls.id) === String(data.currentLesson);
                var href = "lesson.php?online=" + encodeURIComponent(data.slug) + "&lesson=" + encodeURIComponent(ls.id);
                var right = ls.preview
                    ? '<span class="rbt-badge variation-03 bg-primary-opacity"><i class="feather-eye"></i> Preview</span>'
                    : (ls.locked ? '<span class="course-lock"><i class="feather-lock"></i></span>' : "");
                return '<li' + (isCurrent ? ' class="active"' : "") + '><a href="' + href + '"' + (isCurrent ? ' style="font-weight:700"' : "") + '>' +
                    '<div class="course-content-left"><i class="' + icon + '"></i> <span class="text">' + esc(ls.title) + "</span></div>" +
                    '<div class="course-content-right">' + (ls.duration ? '<span class="min-lable">' + esc(ls.duration) + "</span>" : "") + right + "</div></a></li>";
            }).join("");
            return '<div class="accordion-item card">' +
                '<h2 class="accordion-header card-header" id="olHead' + si + '">' +
                '<button class="accordion-button' + (open ? "" : " collapsed") + '" type="button" data-bs-toggle="collapse" data-bs-target="#olCol' + si + '" aria-expanded="' + (open ? "true" : "false") + '" aria-controls="olCol' + si + '">' +
                esc(sec.title) + ' <span class="rbt-badge-5 ml--10">' + (sec.lessons || []).length + "</span>" +
                "</button></h2>" +
                '<div id="olCol' + si + '" class="accordion-collapse collapse' + (open ? " show" : "") + '" aria-labelledby="olHead' + si + '" data-bs-parent="#accordionExampleb2">' +
                '<div class="accordion-body card-body"><ul class="rbt-course-main-content liststyle">' + lessons + "</ul></div></div></div>";
        }).join("");
        if (window.feather && typeof window.feather.replace === "function") window.feather.replace();
    }

    /* ---------- player ---------- */
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

    function setupResume(player, key) {
        var saved = parseFloat(localStorage.getItem(key) || "0") || 0;
        var sought = false;
        function trySeek() {
            if (sought) return;
            var dur = player.duration || 0;
            if (saved > 5 && (!dur || saved < dur - 8)) {
                try { player.currentTime = saved; } catch (e) {}
            }
            sought = true;
        }
        player.on("playing", trySeek);
        player.on("ready", function () { window.setTimeout(trySeek, 400); });
        var last = 0;
        player.on("timeupdate", function () {
            var t = player.currentTime || 0;
            if (Math.abs(t - last) >= 2) { last = t; localStorage.setItem(key, String(Math.floor(t))); }
        });
        player.on("ended", function () { localStorage.removeItem(key); });
    }

    function buildPlayer() {
        var container = document.querySelector("[data-online-player]");
        if (!container) return;
        var url = container.getAttribute("data-online-video") || data.videoUrl || "";
        var key = "dlOnlineLessonPos:" + (container.getAttribute("data-lesson-key") || data.currentLesson || "");
        if (!url) { container.innerHTML = '<div class="datalab-video-error" style="padding:24px;text-align:center;opacity:.8">Bu dərsə hələ video əlavə edilməyib.</div>'; return; }

        var yt = ytId(url), vm = vimeoId(url), drv = driveId(url);
        var controls = ["play-large", "rewind", "play", "fast-forward", "progress", "current-time", "duration", "mute", "volume", "settings", "pip", "fullscreen"];

        if (window.Plyr && (yt || vm)) {
            container.innerHTML = '<div data-online-plyr data-plyr-provider="' + (yt ? "youtube" : "vimeo") + '" data-plyr-embed-id="' + esc(yt || vm) + '"></div>';
            try {
                var p = new window.Plyr(container.querySelector("[data-online-plyr]"), {
                    seekTime: 30,
                    controls: controls,
                    settings: ["quality", "speed", "loop"],
                    speed: { selected: 1, options: [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2] }
                });
                setupResume(p, key);
                return;
            } catch (e) { /* fallback below */ }
        }

        if (window.Plyr && (/\.(mp4|webm|ogg)(\?.*)?$/i.test(url) || drv)) {
            var src = drv ? ("video-proxy.php?id=" + encodeURIComponent(drv)) : url;
            container.innerHTML = '<video data-online-plyr playsinline controls preload="metadata"><source src="' + esc(src) + '"></video>';
            try {
                var p2 = new window.Plyr(container.querySelector("[data-online-plyr]"), {
                    seekTime: 30,
                    controls: controls,
                    settings: ["quality", "speed", "loop"],
                    speed: { selected: 1, options: [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2] }
                });
                setupResume(p2, key);
                return;
            } catch (e2) { /* fallback below */ }
        }

        // Fallback: plain responsive iframe (no Plyr)
        var embed = yt ? "https://www.youtube.com/embed/" + yt : (vm ? "https://player.vimeo.com/video/" + vm : url);
        container.innerHTML = '<div style="position:relative;padding-bottom:56.25%;height:0;border-radius:10px;overflow:hidden">' +
            '<iframe src="' + esc(embed) + '" style="position:absolute;inset:0;width:100%;height:100%;border:0" allowfullscreen allow="autoplay; encrypted-media; picture-in-picture"></iframe></div>';
    }

    function boot() {
        renderPlaylist();
        buildPlayer();
    }

    if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
    else boot();
}());
