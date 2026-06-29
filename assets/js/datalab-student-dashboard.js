(function () {
    "use strict";

    var API = "api/student-auth.php";
    var state = { user: null, data: { enrollments: [], orders: [], stats: {} }, lang: "az" };
    var dict = {
        az: {
            nav_home: "Ana səhifə", nav_courses: "Kurslar", nav_blog: "Bloq", nav_contact: "Əlaqə",
            student_panel: "Tələbə paneli", active_courses: "aktiv kurs", certificates: "Sertifikatlar",
            choose_course: "Yeni kurs seç", welcome: "Xoş gəldin", overview: "İcmal", profile: "Profilim",
            my_courses: "Kurslarım", my_orders: "Sifarişlərim", wishlist: "İstək siyahısı", reviews: "Rəylər",
            quiz_attempts: "Quiz cəhdlərim", settings: "Parametrlər", logout: "Çıxış",
            learning_center: "Öyrənmə mərkəzi", pending_courses: "Gözləyən qeydiyyat", average_progress: "Orta irəliləyiş",
            course_library: "Kurs kitabxanası", view_all: "Hamısına bax", personal_area: "Şəxsi sahə",
            profile_settings: "Profil məlumatları", profile_cover_text: "Öyrənmə profilinizi fərdiləşdirin",
            full_name: "Ad və soyad", email: "E-poçt", phone: "Telefon", bio: "Haqqınızda",
            save_changes: "Dəyişiklikləri saxla", payment_tracking: "Ödəniş izləmə", order: "Sifariş",
            course: "Kurs", amount: "Məbləğ", payment_method: "Ödəniş üsulu", date: "Tarix",
            achievements: "Nailiyyətlər", security: "Təhlükəsizlik", password_settings: "Şifrə parametrləri",
            current_password: "Cari şifrə", new_password: "Yeni şifrə", confirm_password: "Yeni şifrəni təkrar et",
            update_password: "Şifrəni yenilə", footer_about: "Praktiki Data Analitika, SQL, Excel və AI təlimləri ilə karyera bacarıqlarınızı inkişaf etdirin.",
            useful_links: "Faydalı linklər", contact_info: "Əlaqə məlumatı", address: "Bakı, Azərbaycan",
            copyright: "© 2026 DatalabAcademy. Bütün hüquqlar qorunur.", privacy: "Məxfilik siyasəti",
            no_courses: "Hələ kurs qeydiyyatınız yoxdur. Kataloqdan ilk kursunuzu seçə bilərsiniz.",
            no_orders: "Hələ sifariş yoxdur.", no_certificates: "Kursu 100% tamamladıqda sertifikatınız burada görünəcək.",
            continue_course: "Davam et", awaiting: "Təsdiq gözlənilir", lesson: "dərs", completed: "Tamamlandı · sertifikat hazırdır",
            active: "Aktiv", pending: "Ödəniş gözlənilir", paid: "Ödənib", cancelled: "Ləğv edilib", card: "Kart", transfer: "Bank köçürməsi"
        },
        en: {
            nav_home: "Home", nav_courses: "Courses", nav_blog: "Blog", nav_contact: "Contact",
            student_panel: "Student panel", active_courses: "active courses", certificates: "Certificates",
            choose_course: "Choose a course", welcome: "Welcome", overview: "Overview", profile: "My profile",
            my_courses: "My courses", my_orders: "My orders", wishlist: "Wishlist", reviews: "Reviews",
            quiz_attempts: "Quiz attempts", settings: "Settings", logout: "Logout",
            learning_center: "Learning center", pending_courses: "Pending enrollments", average_progress: "Average progress",
            course_library: "Course library", view_all: "View all", personal_area: "Personal area",
            profile_settings: "Profile information", profile_cover_text: "Personalize your learning profile",
            full_name: "Full name", email: "Email", phone: "Phone", bio: "About you",
            save_changes: "Save changes", payment_tracking: "Payment tracking", order: "Order",
            course: "Course", amount: "Amount", payment_method: "Payment method", date: "Date",
            achievements: "Achievements", security: "Security", password_settings: "Password settings",
            current_password: "Current password", new_password: "New password", confirm_password: "Confirm new password",
            update_password: "Update password", footer_about: "Build career skills through practical Data Analytics, SQL, Excel and AI training.",
            useful_links: "Useful links", contact_info: "Contact information", address: "Baku, Azerbaijan",
            copyright: "© 2026 DatalabAcademy. All rights reserved.", privacy: "Privacy policy",
            no_courses: "You have no enrolled courses yet. Choose your first course from the catalog.",
            no_orders: "No orders yet.", no_certificates: "Your certificate will appear here after completing a course.",
            continue_course: "Continue", awaiting: "Awaiting approval", lesson: "lessons", completed: "Completed · certificate is ready",
            active: "Active", pending: "Payment pending", paid: "Paid", cancelled: "Cancelled", card: "Card", transfer: "Bank transfer"
        }
    };

    function one(selector, root) { return (root || document).querySelector(selector); }
    function all(selector, root) { return Array.prototype.slice.call((root || document).querySelectorAll(selector)); }
    function text(key) { return (dict[state.lang] && dict[state.lang][key]) || key; }
    function esc(value) {
        return String(value == null ? "" : value).replace(/&/g, "&amp;").replace(/</g, "&lt;")
            .replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#39;");
    }
    function initials(name) {
        return String(name || "DL").trim().split(/\s+/).slice(0, 2).map(function (part) { return part.charAt(0); }).join("").toUpperCase();
    }
    function setAll(selector, value) { all(selector).forEach(function (el) { el.textContent = value; }); }
    function money(amount) { return Number(amount || 0).toFixed(0) + " USD"; }
    function statusLabel(status) { return text(status === "new" ? "pending" : status); }
    function statusClass(status) { return status === "pending" || status === "new" ? " is-pending" : (status === "cancelled" ? " is-cancelled" : ""); }
    function empty(message) { return '<div class="student-empty">' + esc(message) + "</div>"; }

    function post(action, payload) {
        return fetch(API + "?action=" + encodeURIComponent(action), {
            method: "POST", credentials: "same-origin", headers: { "Content-Type": "application/json" }, body: JSON.stringify(payload || {})
        }).then(function (response) {
            return response.json().then(function (body) {
                if (!response.ok || !body.ok) throw new Error(body.message || "Əməliyyat alınmadı.");
                return body;
            });
        });
    }

    function toast(message, success) {
        var box = one("[data-dashboard-toast]");
        if (!box) return;
        box.textContent = message;
        box.hidden = false;
        box.classList.toggle("is-success", !!success);
        window.clearTimeout(toast.timer);
        toast.timer = window.setTimeout(function () { box.hidden = true; }, 3500);
    }

    function renderAvatar(user) {
        all("[data-dashboard-avatar]").forEach(function (avatar) {
            avatar.innerHTML = user.avatar
                ? '<img src="' + esc(user.avatar) + '" alt="' + esc(user.name || "Student") + '">'
                : esc(initials(user.name));
        });
    }

    function renderUser(user) {
        state.user = user;
        var first = String(user.name || "Tələbə").trim().split(/\s+/)[0];
        setAll("[data-dashboard-user-name]", user.name || "Datalab tələbəsi");
        setAll("[data-dashboard-first-name]", first);
        setAll("[data-dashboard-email]", user.email || "");
        renderAvatar(user);
        var form = one("[data-profile-form]");
        if (form) {
            form.elements.name.value = user.name || "";
            form.elements.email.value = user.email || "";
            form.elements.phone.value = user.phone || "";
            form.elements.bio.value = user.bio || "";
        }
    }

    function renderStats(stats) {
        setAll("[data-stat-active]", stats.activeCourses || 0);
        setAll("[data-stat-pending]", stats.pendingCourses || 0);
        setAll("[data-stat-progress]", (stats.averageProgress || 0) + "%");
        setAll("[data-stat-certificates]", stats.certificates || 0);
    }

    function courseHtml(course) {
        var active = course.status === "active";
        var image = course.image || "assets/images/course/datalab-data-analitika.svg";
        return [
            '<article class="student-course-card">',
            '<img src="' + esc(image) + '" alt="' + esc(course.title) + '">',
            '<div class="student-course-body">',
            '<div class="student-course-meta"><span>' + esc(course.category || "DatalabAcademy") + '</span><strong class="student-status' + statusClass(course.status) + '">' + esc(statusLabel(course.status)) + '</strong></div>',
            '<h3>' + esc(course.title) + '</h3>',
            '<div class="student-progress-label"><span>' + esc(course.completedLessons) + " / " + esc(course.totalLessons) + " " + esc(text("lesson")) + '</span><strong>' + esc(course.progress) + '%</strong></div>',
            '<div class="student-progress"><span style="width:' + Math.max(0, Math.min(100, Number(course.progress) || 0)) + '%"></span></div>',
            '<div class="student-course-actions"><a class="' + (active ? "" : "is-disabled") + '" href="' + (active ? ((course.type === "online" ? "lesson.php?online=" : "lesson.php?course=") + encodeURIComponent(course.courseId)) : "#") + '"><i class="feather-play"></i>' + esc(active ? text("continue_course") : text("awaiting")) + '</a></div>',
            "</div></article>"
        ].join("");
    }

    function renderCourses(items) {
        var full = one("[data-dashboard-courses]");
        var preview = one("[data-dashboard-courses-preview]");
        var html = items.length ? items.map(courseHtml).join("") : empty(text("no_courses"));
        if (full) full.innerHTML = html;
        if (preview) preview.innerHTML = items.length ? items.slice(0, 3).map(courseHtml).join("") : html;
    }

    function renderOrders(items) {
        var root = one("[data-dashboard-orders]");
        if (!root) return;
        if (!items.length) { root.innerHTML = '<tr><td colspan="6">' + esc(text("no_orders")) + "</td></tr>"; return; }
        root.innerHTML = items.map(function (order) {
            return "<tr><td><strong>" + esc(order.id) + "</strong></td><td>" + esc(order.courseTitle) +
                "</td><td>" + esc(money(order.amount)) + "</td><td>" + esc(text(order.paymentMethod === "transfer" ? "transfer" : "card")) +
                '</td><td><span class="student-status' + statusClass(order.status) + '">' + esc(statusLabel(order.status)) +
                "</span></td><td>" + esc(order.orderDate) + "</td></tr>";
        }).join("");
    }

    function renderCertificates(items) {
        var root = one("[data-dashboard-certificates]");
        if (!root) return;
        var certificates = items.filter(function (course) { return course.certificateReady; });
        if (!certificates.length) { root.innerHTML = empty(text("no_certificates")); return; }
        root.innerHTML = certificates.map(function (course) {
            return '<article class="student-certificate"><i class="feather-award"></i><div><strong>' + esc(course.title) +
                '</strong><span>' + esc(text("completed")) + "</span></div></article>";
        }).join("");
    }

    function renderDynamic() {
        renderStats(state.data.stats || {});
        renderCourses(state.data.enrollments || []);
        renderOrders(state.data.orders || []);
        renderCertificates(state.data.enrollments || []);
    }

    function setLanguage(lang) {
        state.lang = lang === "en" ? "en" : "az";
        document.documentElement.lang = state.lang;
        localStorage.setItem("siteLang", state.lang);
        all("[data-i18n]").forEach(function (el) { var value = dict[state.lang][el.getAttribute("data-i18n")]; if (value) el.textContent = value; });
        all("[data-student-lang]").forEach(function (button) { button.classList.toggle("is-active", button.getAttribute("data-student-lang") === state.lang); });
        renderDynamic();
    }

    function cookie(name) {
        var prefix = encodeURIComponent(name) + "=";
        var match = (document.cookie || "").split(";").map(function (part) { return part.trim(); }).find(function (part) { return part.indexOf(prefix) === 0; });
        return match ? decodeURIComponent(match.slice(prefix.length)) : "";
    }

    function setTheme(theme) {
        var dark = theme === "dark";
        document.body.classList.toggle("is-dark", dark);
        var icon = one("[data-student-theme-icon]");
        var button = one("[data-student-theme]");
        if (icon) icon.className = dark ? "feather-sun" : "feather-moon";
        if (button) button.setAttribute("aria-label", dark ? "Açıq rejim" : "Tünd rejim");
    }

    function showView(name) {
        var allowed = ["overview", "profile", "courses", "orders", "certificates", "settings"];
        if (allowed.indexOf(name) === -1) name = "overview";
        all("[data-student-panel]").forEach(function (panel) {
            var active = panel.getAttribute("data-student-panel") === name;
            panel.hidden = !active;
            panel.classList.toggle("is-active", active);
        });
        all("[data-dashboard-sidebar] [data-student-view]").forEach(function (button) {
            button.classList.toggle("is-active", button.getAttribute("data-student-view") === name);
        });
        document.body.classList.remove("is-menu-open");
        if (window.location.hash !== "#" + name) window.history.replaceState(null, "", "#" + name);
        window.scrollTo({ top: Math.max(0, one(".student-dashboard-layout").offsetTop - 100), behavior: "smooth" });
    }

    function bindActions() {
        document.addEventListener("click", function (event) {
            var view = event.target.closest("[data-student-view]");
            if (view) { event.preventDefault(); showView(view.getAttribute("data-student-view")); }
            if (event.target.closest("[data-student-menu]")) document.body.classList.toggle("is-menu-open");
            var lang = event.target.closest("[data-student-lang]");
            if (lang) setLanguage(lang.getAttribute("data-student-lang"));
            if (event.target.closest("[data-student-theme]")) {
                var next = document.body.classList.contains("is-dark") ? "light" : "dark";
                document.cookie = "styleCookieName=" + next + "; path=/; max-age=31536000; SameSite=Lax";
                setTheme(next);
            }
        });

        var avatarInput = one("[data-avatar-input]");
        if (avatarInput) avatarInput.addEventListener("change", function () {
            var file = avatarInput.files && avatarInput.files[0];
            if (!file) return;
            if (!/^image\/(jpeg|png|webp)$/.test(file.type) || file.size > 2 * 1024 * 1024) { toast("JPG, PNG və ya WebP şəkli seçin (maksimum 2 MB)."); avatarInput.value = ""; return; }
            var reader = new FileReader();
            reader.onload = function () {
                post("upload-avatar", { image: reader.result }).then(function (payload) { renderUser(payload.user); toast(payload.message, true); }).catch(function (error) { toast(error.message); });
            };
            reader.readAsDataURL(file);
        });

        var profileForm = one("[data-profile-form]");
        if (profileForm) profileForm.addEventListener("submit", function (event) {
            event.preventDefault();
            var button = profileForm.querySelector("button[type='submit']");
            button.disabled = true;
            post("update-profile", { name: profileForm.elements.name.value, phone: profileForm.elements.phone.value, bio: profileForm.elements.bio.value })
                .then(function (payload) { renderUser(payload.user); toast(payload.message, true); })
                .catch(function (error) { toast(error.message); })
                .finally(function () { button.disabled = false; });
        });

        var passwordForm = one("[data-password-form]");
        if (passwordForm) passwordForm.addEventListener("submit", function (event) {
            event.preventDefault();
            if (passwordForm.elements.newPassword.value !== passwordForm.elements.confirmPassword.value) { toast(state.lang === "en" ? "Passwords do not match." : "Yeni şifrələr eyni deyil."); return; }
            var button = passwordForm.querySelector("button[type='submit']");
            button.disabled = true;
            post("change-password", { currentPassword: passwordForm.elements.currentPassword.value, newPassword: passwordForm.elements.newPassword.value })
                .then(function (payload) { passwordForm.reset(); toast(payload.message, true); })
                .catch(function (error) { toast(error.message); })
                .finally(function () { button.disabled = false; });
        });
    }

    function load() {
        setTheme(cookie("styleCookieName") === "dark" ? "dark" : "light");
        setLanguage(localStorage.getItem("siteLang") || "az");
        fetch(API + "?action=dashboard", { credentials: "same-origin" })
            .then(function (response) {
                if (response.status === 401) { window.location.href = "login.html?redirect=student-dashboard"; throw new Error("auth"); }
                return response.json();
            })
            .then(function (payload) {
                if (!payload.ok) throw new Error(payload.message || "Panel yüklənmədi.");
                state.data = payload.data || state.data;
                renderUser(payload.user);
                renderDynamic();
                showView(window.location.hash.slice(1) || "overview");
            })
            .catch(function (error) { if (error.message !== "auth") toast(error.message); });
    }

    bindActions();
    if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", load); else load();
}());
