(function () {
    "use strict";

    var API_URL = "api/student-auth.php";
    var DASHBOARD_URL = "student-dashboard";

    function $(selector, root) {
        return (root || document).querySelector(selector);
    }

    function $all(selector, root) {
        return Array.prototype.slice.call((root || document).querySelectorAll(selector));
    }

    function getCookie(name) {
        var prefix = encodeURIComponent(name) + "=";
        var parts = document.cookie ? document.cookie.split(";") : [];
        for (var i = 0; i < parts.length; i += 1) {
            var part = parts[i].trim();
            if (part.indexOf(prefix) === 0) return decodeURIComponent(part.slice(prefix.length));
        }
        return "";
    }

    function applyAuthTheme(theme) {
        var isDark = theme === "dark";
        document.body.classList.toggle("active-dark-mode", isDark);
        document.body.classList.toggle("active-light-mode", !isDark);

        var button = $("[data-auth-theme-toggle]");
        var icon = $("[data-auth-theme-icon]");
        var label = isDark ? "Açıq rejimə keç" : "Qaranlıq rejimə keç";
        if (button) {
            button.setAttribute("aria-label", label);
            button.setAttribute("title", label);
        }
        if (icon) icon.className = isDark ? "feather-sun" : "feather-moon";
    }

    function initAuthTheme() {
        var savedTheme = getCookie("styleCookieName");
        var theme = savedTheme === "dark" || savedTheme === "light"
            ? savedTheme
            : (window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light");
        applyAuthTheme(theme);

        var button = $("[data-auth-theme-toggle]");
        if (!button) return;
        button.addEventListener("click", function () {
            var nextTheme = document.body.classList.contains("active-dark-mode") ? "light" : "dark";
            document.cookie = "styleCookieName=" + nextTheme + "; path=/; max-age=31536000; SameSite=Lax";
            applyAuthTheme(nextTheme);
        });
    }

    function esc(value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;").replace(/'/g, "&#39;");
    }

    function post(action, data) {
        return fetch(API_URL + "?action=" + encodeURIComponent(action), {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data || {})
        }).then(function (response) {
            return response.json().then(function (payload) {
                if (!response.ok || !payload.ok) {
                    var error = new Error(payload.message || "Əməliyyat alınmadı.");
                    error.payload = payload;
                    throw error;
                }
                return payload;
            });
        });
    }

    function getSession() {
        return fetch(API_URL + "?action=session", { credentials: "same-origin" })
            .then(function (response) {
                if (!response.ok) return null;
                return response.json();
            })
            .then(function (payload) {
                return payload && payload.ok ? payload.user : null;
            })
            .catch(function () { return null; });
    }

    function getConfig() {
        if (window.DL_STUDENT_AUTH_CONFIG) return Promise.resolve(window.DL_STUDENT_AUTH_CONFIG);
        return fetch(API_URL + "?action=config", { credentials: "same-origin" })
            .then(function (response) { return response.json(); })
            .then(function (payload) {
                window.DL_STUDENT_AUTH_CONFIG = payload && payload.ok ? payload : {};
                return window.DL_STUDENT_AUTH_CONFIG;
            })
            .catch(function () { return {}; });
    }

    function initials(name, email) {
        var source = String(name || email || "DL").trim();
        var parts = source.split(/\s+/).filter(Boolean);
        if (parts.length > 1) return (parts[0][0] + parts[1][0]).toUpperCase();
        return source.slice(0, 2).toUpperCase();
    }

    function authMenuHtml() {
        return [
            '<div class="datalab-auth-dropdown">',
            '<div class="datalab-auth-mini-icon"><i class="feather-user"></i></div>',
            '<h6>Tələbə hesabı</h6>',
            '<p>Kurslarını, sertifikatlarını və sifarişlərini bir paneldən idarə et.</p>',
            '<div class="datalab-auth-menu-actions">',
            '<a class="rbt-btn btn-gradient btn-sm" href="login.html?mode=login"><i class="feather-log-in"></i><span>Daxil ol</span></a>',
            '<a class="rbt-btn btn-border btn-sm" href="login.html?mode=register"><i class="feather-user-plus"></i><span>Yeni hesab</span></a>',
            '</div>',
            '</div>'
        ].join("");
    }

    function userMenuHtml(user) {
        var avatar = user.avatar
            ? '<img src="' + esc(user.avatar) + '" alt="' + esc(user.name || "User") + '">'
            : '<span class="datalab-avatar-initials">' + esc(initials(user.name, user.email)) + "</span>";
        return [
            '<div class="rbt-admin-profile datalab-student-profile-chip">',
            '<div class="admin-thumbnail">' + avatar + '</div>',
            '<div class="admin-info">',
            '<span class="name">' + esc(user.name || "Datalab tələbəsi") + '</span>',
            '<a class="rbt-btn-link color-primary" href="student-dashboard#profile">Profilə bax</a>',
            '</div></div>',
            '<ul class="user-list-wrapper">',
            '<li><a href="student-dashboard"><i class="feather-home"></i><span>İdarə panelim</span></a></li>',
            '<li><a href="student-dashboard#courses"><i class="feather-shopping-bag"></i><span>Qeydiyyatlı kurslar</span></a></li>',
            '<li><a href="student-wishlist.html"><i class="feather-heart"></i><span>İstək siyahısı</span></a></li>',
            '<li><a href="student-reviews.html"><i class="feather-star"></i><span>Rəylər</span></a></li>',
            '<li><a href="student-my-quiz-attempts.html"><i class="feather-list"></i><span>Quiz cəhdlərim</span></a></li>',
            '<li><a href="student-dashboard#orders"><i class="feather-clock"></i><span>Sifariş tarixçəsi</span></a></li>',
            '</ul>',
            '<hr class="mt--10 mb--10">',
            '<ul class="user-list-wrapper">',
            '<li><a href="student-dashboard#settings"><i class="feather-settings"></i><span>Parametrlər</span></a></li>',
            '<li><a href="#" data-student-logout><i class="feather-log-out"></i><span>Çıxış</span></a></li>',
            '</ul>'
        ].join("");
    }

    function renderMenus(user) {
        $all("[data-student-menu] .rbt-user-menu-list-wrapper .inner").forEach(function (inner) {
            inner.innerHTML = user ? userMenuHtml(user) : authMenuHtml();
        });
        if (window.feather && typeof window.feather.replace === "function") window.feather.replace();
    }

    function redirectTarget() {
        var params = new URLSearchParams(window.location.search);
        return params.get("redirect") || DASHBOARD_URL;
    }

    function showMessage(text, tone) {
        var box = $("[data-auth-message]");
        if (!box) return;
        box.hidden = !text;
        box.textContent = text || "";
        box.classList.toggle("is-success", tone === "success");
    }

    function setMode(mode) {
        var isRegister = mode === "register";
        document.body.classList.toggle("is-register-mode", isRegister);
        $all("[data-auth-mode]").forEach(function (button) {
            button.classList.toggle("is-active", button.getAttribute("data-auth-mode") === mode);
        });
        $all("[data-auth-panel]").forEach(function (panel) {
            panel.hidden = panel.getAttribute("data-auth-panel") !== mode;
        });
        showMessage("");
        var devLink = $("[data-auth-dev-link]");
        if (devLink) devLink.hidden = true;
    }

    function formData(form) {
        var data = {};
        Array.prototype.slice.call(form.elements).forEach(function (field) {
            if (field.name) data[field.name] = field.value;
        });
        return data;
    }

    function setBusy(form, busy) {
        var button = form && form.querySelector("button[type='submit']");
        if (button) {
            button.disabled = busy;
            button.classList.toggle("is-loading", busy);
        }
    }

    function initAuthPage() {
        var shell = $("[data-student-auth]");
        if (!shell) return;
        var params = new URLSearchParams(window.location.search);
        setMode(params.get("mode") === "register" ? "register" : "login");

        $all("[data-auth-form]").forEach(function (form) {
            form.reset();
            $all("input", form).forEach(function (input) {
                input.value = "";
            });
        });

        getSession().then(function (user) {
            if (user && !params.get("stay")) window.location.href = redirectTarget();
        });

        $all("[data-auth-mode]").forEach(function (button) {
            button.addEventListener("click", function () {
                setMode(button.getAttribute("data-auth-mode"));
            });
        });

        $all("[data-auth-form]").forEach(function (form) {
            form.addEventListener("submit", function (event) {
                event.preventDefault();
                setBusy(form, true);
                showMessage("");
                var action = form.getAttribute("data-auth-form");
                post(action, formData(form))
                    .then(function (payload) {
                        var devLink = $("[data-auth-dev-link]");
                        if (payload.devLink && devLink) {
                            devLink.href = payload.devLink;
                            devLink.hidden = false;
                        }
                        if (payload.requiresVerification || action === "request-reset") {
                            showMessage(payload.message, "success");
                            return;
                        }
                        showMessage("Xoş gəldin. Panelə yönləndirilirsən...", "success");
                        window.setTimeout(function () { window.location.href = redirectTarget(); }, 450);
                    })
                    .catch(function (err) {
                        showMessage(err.message);
                        var resend = $("[data-resend-verification]");
                        if (resend) resend.hidden = !(err.payload && err.payload.verificationRequired);
                    })
                    .finally(function () { setBusy(form, false); });
            });
        });

        var resendButton = $("[data-resend-verification]");
        if (resendButton) {
            resendButton.addEventListener("click", function () {
                var loginForm = $("[data-auth-form='login']");
                var email = loginForm && loginForm.elements.email ? loginForm.elements.email.value : "";
                post("resend-verification", { email: email })
                    .then(function (payload) {
                        showMessage(payload.message, "success");
                        var devLink = $("[data-auth-dev-link]");
                        if (payload.devLink && devLink) {
                            devLink.href = payload.devLink;
                            devLink.hidden = false;
                        }
                    })
                    .catch(function (error) { showMessage(error.message); });
            });
        }

        var googleButton = $("[data-google-auth]");
        if (googleButton) initGoogleButton(googleButton, 0);
    }

    function initGoogleButton(container, attempt) {
        getConfig().then(function (config) {
            var clientId = window.DL_GOOGLE_CLIENT_ID || config.googleClientId || "";
            var googleReady = window.google && window.google.accounts && window.google.accounts.id;
            if (!clientId) {
                showMessage("Google girişi üçün Google OAuth Client ID əlavə edilməlidir.");
                return;
            }
            if (!googleReady) {
                if (attempt < 30) {
                    window.setTimeout(function () { initGoogleButton(container, attempt + 1); }, 200);
                } else {
                    showMessage("Google giriş xidməti yüklənmədi. İnternet bağlantısını yoxlayın.");
                }
                return;
            }
            window.google.accounts.id.initialize({
                client_id: clientId,
                ux_mode: "popup",
                context: "signin",
                callback: function (response) {
                    if (!response || !response.credential) {
                        showMessage("Google hesabı seçilmədi.");
                        return;
                    }
                    post("google-login", { credential: response.credential })
                        .then(function () { window.location.href = redirectTarget(); })
                        .catch(function (err) { showMessage(err.message); });
                }
            });
            container.innerHTML = "";
            window.google.accounts.id.renderButton(container, {
                type: "standard",
                theme: document.body.classList.contains("active-dark-mode") ? "filled_black" : "outline",
                size: "large",
                text: "continue_with",
                shape: "rectangular",
                logo_alignment: "left",
                width: Math.min(400, Math.max(240, container.clientWidth || 400))
            });
        });
    }

    function protectStudentPage(user) {
        if (!document.body.hasAttribute("data-student-protected")) return;
        if (!user) {
            window.location.href = "login.html?redirect=" + encodeURIComponent(window.location.pathname.split("/").pop() || DASHBOARD_URL);
            return;
        }
        $all("[data-student-name]").forEach(function (el) { el.textContent = user.name || "Datalab tələbəsi"; });
        $all("[data-student-email]").forEach(function (el) { el.textContent = user.email || ""; });
        $all("[data-student-avatar]").forEach(function (el) {
            if (user.avatar && el.tagName === "IMG") el.src = user.avatar;
        });
    }

    function boot() {
        initAuthTheme();
        initAuthPage();
        getSession().then(function (user) {
            renderMenus(user);
            protectStudentPage(user);
        });
        document.addEventListener("click", function (event) {
            var logout = event.target.closest && event.target.closest("[data-student-logout]");
            if (!logout) return;
            event.preventDefault();
            post("logout", {}).finally(function () {
                window.location.href = "index.php";
            });
        });
    }

    if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
    else boot();
}());
