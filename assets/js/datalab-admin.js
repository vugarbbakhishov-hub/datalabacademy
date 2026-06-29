(function () {
    "use strict";

    var STORAGE_KEY = "dlAdminData";
    var API_URL = "api/admin.php";
    var titles = {
        dashboard: "Dashboard",
        courses: "Kurslar",
        online: "Online Təlimlər",
        lessons: "Dərslər",
        "online-lessons": "Online Dərslər",
        "online-reviews": "Online Rəylər",
        orders: "Sifarişlər",
        leads: "Lead-lər",
        blog: "Blog Yazıları",
        students: "Tələbələr",
        home: "Ana səhifə",
        "ai-quiz": "AI Quiz",
        "ai-chat": "AI Chat Bazası",
        contact: "Əlaqə",
        content: "Kontent",
        seo: "SEO",
        settings: "Ayarlar"
    };

    var LESSON_TYPES = {
        video: { label: "Video", icon: "feather-play" },
        article: { label: "Mətn dərs", icon: "feather-file-text" },
        quiz: { label: "Quiz", icon: "feather-help-circle" },
        assignment: { label: "Tapşırıq", icon: "feather-clipboard" }
    };

    var SOURCE_LABELS = { auto: "Avtomatik", youtube: "YouTube", drive: "Google Drive", mp4: "MP4" };

    var seedData = {
        courses: [
            { id: "1", title: "Data Analitika", category: "Analitika", price: 180, lessons: 16, students: 40, rating: 5, reviewCount: 15, status: "active", image: "assets/images/course/datalab-data-analitika.svg", description: "KPI, data təhlili, vizuallaşdırma və dashboard düşüncəsi üzrə praktik kurs." },
            { id: "2", title: "SQL Developer", category: "Data", price: 220, lessons: 18, students: 35, rating: 5, reviewCount: 15, status: "active", image: "assets/images/course/datalab-sql-developer.svg", description: "JOIN, CTE, indekslər və real SQL ssenariləri ilə verilənlər bazası praktikası." },
            { id: "3", title: "Excel", category: "Ofis", price: 40, lessons: 14, students: 45, rating: 5, reviewCount: 5, status: "active", image: "assets/images/course/datalab-excel.svg", description: "Formullar, Pivot Table, Power Query və dashboard quruluşu." },
            { id: "4", title: "AI ilə Effektiv İş", category: "AI", price: 150, lessons: 12, students: 60, rating: 5, reviewCount: 12, status: "active", image: "assets/images/course/datalab-ai.svg", description: "AI alətlərindən gündəlik işdə peşəkar və etik istifadə." },
            { id: "5", title: "İnteraktiv AI Təcrübəsi", category: "AI", price: 240, lessons: 16, students: 0, rating: 5, reviewCount: 0, status: "active", image: "assets/images/course/datalab-ai.svg", description: "Generativ AI, agentlər və ağıllı iş axınları üzrə əyani praktiki proqram." }
        ],
        onlineCourses: [
            { id: "on-1", title: "Data Analitika Online", slug: "data-analitika-online", category: "Analitika", level: "Başlanğıc", price: 180, lessons: 24, students: 120, rating: 5, reviewCount: 18, status: "active", image: "assets/images/course/datalab-data-analitika.svg", description: "Video dərslər, praktiki tapşırıqlar və mentor dəstəyi ilə data analitika proqramı." },
            { id: "on-2", title: "SQL Praktiki Dərslər", slug: "sql-praktiki-dersler", category: "SQL", level: "Orta", price: 160, lessons: 18, students: 85, rating: 5, reviewCount: 12, status: "active", image: "assets/images/course/datalab-sql-developer.svg", description: "JOIN, CTE, indeks və real sorğularla SQL bacarıqlarınızı online inkişaf etdirin." },
            { id: "on-3", title: "Excel Dashboard Təlimi", slug: "excel-dashboard-telimi", category: "Excel", level: "Başlanğıc", price: 90, lessons: 14, students: 70, rating: 5, reviewCount: 9, status: "active", image: "assets/images/course/datalab-excel.svg", description: "Excel, Pivot Table, Power Query və dashboard quruluşu üzrə praktiki online təlim." }
        ],
        onlineCurriculum: {},
        onlineCourseDetails: {},
        courseDetails: {
            "1": { format: "Əyani / Offline", duration: "8 həftə", schedule: "Həftədə 2 dəfə, 19:00–21:00", location: "Bakı, DatalabAcademy təlim mərkəzi", startDate: "2026-07-06", level: "Başlanğıc", language: "Azərbaycan", seats: "15", instructor: "DatalabAcademy mentor komandası", splineScene: "https://prod.spline.design/kZDDjO5HuC9GJUM2/scene.splinecode", overview: "Məlumatlarla işləməyə sıfırdan başlayanlar üçün əyani proqram. İştirakçılar Excel, SQL, vizuallaşdırma və analitik düşüncəni real biznes tapşırıqları üzərində birləşdirirlər.", outcomes: "Məlumatı təmizləmək və analizə hazırlamaq\nSQL ilə biznes suallarına cavab verən sorğular yazmaq\nKPI və dashboard qurmaq\nAnalitik nəticəni aydın təqdim etmək", syllabus: "Analitik düşüncə və data ilə tanışlıq\nExcel ilə təmizləmə və hesabat\nSQL əsasları və JOIN-lar\nVizualizasiya və dashboard dizaynı\nReal biznes layihəsi və təqdimat" },
            "2": { format: "Əyani / Offline", duration: "10 həftə", schedule: "Həftədə 2 dəfə", location: "Bakı", startDate: "2026-07-13", level: "Orta", language: "Azərbaycan", seats: "14", instructor: "DatalabAcademy SQL mentorları", splineScene: "https://prod.spline.design/kZDDjO5HuC9GJUM2/scene.splinecode", overview: "SQL-i real verilənlər bazası ssenariləri ilə öyrədən əyani proqram.", outcomes: "Mürəkkəb SQL sorğuları yazmaq\nJOIN və CTE-lərdən düzgün istifadə etmək\nSorğu performansını analiz etmək", syllabus: "SQL təməli\nJOIN və alt sorğular\nCTE və window functions\nİndekslər və performans\nYekun praktiki layihə" },
            "3": { format: "Əyani / Offline", duration: "6 həftə", schedule: "Həftədə 2 dəfə", location: "Bakı", startDate: "2026-07-20", level: "Başlanğıc", language: "Azərbaycan", seats: "16", instructor: "DatalabAcademy Excel mentorları", splineScene: "https://prod.spline.design/kZDDjO5HuC9GJUM2/scene.splinecode", overview: "Excel-də gündəlik iş axınını sürətləndirmək və hesabatları peşəkarlaşdırmaq üçün əyani kurs.", outcomes: "Formul və funksiyalardan istifadə etmək\nPivot Table ilə hesabat qurmaq\nPower Query ilə məlumat hazırlamaq", syllabus: "Excel iş mühiti\nFormullar və funksiyalar\nPivot Table\nPower Query\nDashboard layihəsi" },
            "4": { format: "Əyani / Offline", duration: "4 həftə", schedule: "Həftədə 2 dəfə", location: "Bakı", startDate: "2026-07-27", level: "Bütün səviyyələr", language: "Azərbaycan", seats: "18", instructor: "DatalabAcademy AI mentorları", splineScene: "https://prod.spline.design/kZDDjO5HuC9GJUM2/scene.splinecode", overview: "AI alətlərini işdə təhlükəsiz, etik və məhsuldar tətbiq etmək üçün praktiki əyani proqram.", outcomes: "Effektiv prompt hazırlamaq\nAI ilə sənəd və hesabat işini sürətləndirmək\nMəlumat təhlükəsizliyi risklərini tanımaq", syllabus: "AI iş prinsipləri\nPrompt dizaynı\nOfis və analitika ssenariləri\nAvtomatlaşdırma\nEtik və təhlükəsiz istifadə" },
            "5": { format: "Əyani / Offline", duration: "8 həftə", schedule: "Həftədə 2 dəfə, 19:00–21:00", location: "Bakı, DatalabAcademy təlim mərkəzi", startDate: "2026-07-06", level: "Başlanğıc və orta", language: "Azərbaycan", seats: "15", instructor: "DatalabAcademy AI mentorları", splineScene: "https://prod.spline.design/kZDDjO5HuC9GJUM2/scene.splinecode", overview: "Generativ AI və ağıllı agentləri real iş prosesində qurub tətbiq etmək üçün hazırlanmış əyani proqram.", outcomes: "Effektiv prompt sistemləri qurmaq\nAI agentləri ilə iş axınları yaratmaq\nHesabat və data işini avtomatlaşdırmaq\nAI nəticələrini təhlükəsiz yoxlamaq", syllabus: "Generativ AI və LLM-lər\nPrompt və kontekst dizaynı\nAI agentləri\nData və hesabat avtomatlaşdırması\nTəhlükəsizlik və etika\nYekun interaktiv AI layihəsi" }
        },
        orders: [
            { id: "DL-1007", customer: "Aysel Məmmədova", courseId: "1", phone: "+994 50 111 22 33", status: "pending", amount: 180 },
            { id: "DL-1008", customer: "Murad Əliyev", courseId: "2", phone: "+994 55 444 55 66", status: "paid", amount: 220 },
            { id: "DL-1009", customer: "Nigar Həsənli", courseId: "4", phone: "+994 70 777 88 99", status: "new", amount: 150 }
        ],
        leads: [
            { id: "L-1", name: "Rauf", source: "Instagram", interest: "Excel", status: "new", date: "2026-05-24" },
            { id: "L-2", name: "Fidan", source: "Website", interest: "Data Analitika", status: "pending", date: "2026-05-24" }
        ],
        content: {
            heroTitle: "Karyeranıza təkan verən onlayn təhsil platforması",
            heroText: "Data Analitika, SQL, Excel və AI kursları ilə praktik bacarıqlarınızı artırın.",
            heroCta: "Kurslara bax",
            phone: "+994 50 654 97 37",
            email: "info@datalabacademy.az",
            footerText: "DatalabAcademy praktiki Data Analitika, SQL, Excel və AI təlimləri ilə karyera bacarıqlarınızı inkişaf etdirir."
        },
        seo: {
            siteTitle: "DatalabAcademy — Data Analitika, SQL, Excel və AI Kursları",
            domain: "https://datalabacademy.az/",
            description: "DatalabAcademy praktiki onlayn kurslar təqdim edir: Data Analitika, SQL Developer, Excel və AI ilə Effektiv İş.",
            keywords: "DatalabAcademy, data analitika kursu, SQL Developer, Excel kursu, AI kursu",
            ogImage: "https://datalabacademy.az/assets/images/course/datalab-data-analitika.svg",
            robots: "index, follow"
        },
        curriculum: {
            "1": [
                {
                    id: "s1", title: "Başlanğıc", locked: false,
                    lessons: [
                        { id: "l1", title: "Kursa giriş", type: "video", duration: "30 min", source: "auto", link: "", note: "", preview: true },
                        { id: "l2", title: "Giriş və hazırlıq", type: "article", duration: "10 min", source: "auto", link: "", note: "", preview: false }
                    ]
                },
                {
                    id: "s2", title: "Dərslər", locked: false,
                    lessons: [
                        { id: "l3", title: "İlk praktiki dərs", type: "video", duration: "37 min", source: "auto", link: "", note: "", preview: false },
                        { id: "l4", title: "Məlumatlar və dəyişənlər", type: "video", duration: "20 min", source: "auto", link: "", note: "", preview: false },
                        { id: "l5", title: "Əsas operatorlar", type: "video", duration: "15 min", source: "auto", link: "", note: "", preview: false }
                    ]
                },
                {
                    id: "s3", title: "Praktiki yoxlama", locked: true,
                    lessons: [
                        { id: "l6", title: "Praktiki yoxlama", type: "quiz", duration: "25 min", source: "auto", link: "", note: "", preview: false }
                    ]
                },
                {
                    id: "s4", title: "Tapşırıqlar", locked: true,
                    lessons: [
                        { id: "l7", title: "Yekun tapşırıq", type: "assignment", duration: "45 min", source: "auto", link: "", note: "", preview: false }
                    ]
                }
            ]
        }
    };

    var state = clone(seedData);
    var currentCourseId = "";
    var currentOnlineLessonId = "";
    var csrfToken = "";
    var orderQuery = "";
    var orderFilter = "all";
    var leadQuery = "";
    var leadFilter = "all";

    /* pagination + bulk selection (orders / leads) */
    var PAGE_SIZE = 10;
    var page = { orders: 1, leads: 1 };
    var sel = { orders: {}, leads: {} };
    var pageRowsCache = { orders: [], leads: [] };

    function selectedIds(kind) {
        return Object.keys(sel[kind]).filter(function (id) { return sel[kind][id]; });
    }
    function renderPager(kind, total) {
        var box = document.querySelector('[data-pager="' + kind + '"]');
        if (!box) return;
        var pages = Math.max(1, Math.ceil(total / PAGE_SIZE));
        if (page[kind] > pages) page[kind] = pages;
        if (page[kind] < 1) page[kind] = 1;
        if (total <= PAGE_SIZE) { box.innerHTML = ""; return; }
        var p = page[kind];
        var start = (p - 1) * PAGE_SIZE + 1;
        var end = Math.min(total, p * PAGE_SIZE);
        box.innerHTML =
            '<button type="button" data-page="' + kind + '" data-go="prev"' + (p <= 1 ? ' disabled' : '') + '><i class="feather-chevron-left"></i></button>' +
            '<span>' + start + '–' + end + ' / ' + total + '</span>' +
            '<button type="button" data-page="' + kind + '" data-go="next"' + (p >= pages ? ' disabled' : '') + '><i class="feather-chevron-right"></i></button>';
    }
    function renderBulkBar(kind) {
        var bar = document.querySelector('[data-bulk="' + kind + '"]');
        if (!bar) return;
        var n = selectedIds(kind).length;
        bar.hidden = n === 0;
        var c = bar.querySelector('[data-bulk-count="' + kind + '"]');
        if (c) c.textContent = n + " seçilib";
    }
    function syncSelectAll(kind) {
        var box = document.querySelector('[data-selall="' + kind + '"]');
        if (!box) return;
        var rows = pageRowsCache[kind];
        box.checked = rows.length > 0 && rows.every(function (r) { return sel[kind][r.id]; });
    }
    function selCell(kind, id) {
        return '<td class="dl-admin-check"><input type="checkbox" data-sel="' + kind + '" data-id="' + esc(id) + '"' + (sel[kind][id] ? ' checked' : '') + '></td>';
    }

    /* ---------------- helpers ---------------- */

    function clone(value) {
        return JSON.parse(JSON.stringify(value));
    }

    function esc(value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;").replace(/'/g, "&#39;");
    }

    function uid(prefix) {
        return prefix + Date.now().toString(36) + Math.random().toString(36).slice(2, 6);
    }

    function money(value) {
        return "$" + Number(value || 0).toLocaleString("en-US");
    }

    function parseMinutes(label) {
        var match = /(\d+)\s*saat/.exec(label || "");
        var minutes = match ? Number(match[1]) * 60 : 0;
        var minMatch = /(\d+)\s*min/.exec(label || "");
        if (minMatch) minutes += Number(minMatch[1]);
        if (!match && !minMatch) {
            var bare = parseInt(label, 10);
            if (!isNaN(bare)) minutes += bare;
        }
        return minutes;
    }

    function formatMinutes(total) {
        if (!total) return "0 min";
        var hours = Math.floor(total / 60);
        var mins = total % 60;
        if (hours && mins) return hours + " saat " + mins + " min";
        if (hours) return hours + " saat";
        return mins + " min";
    }

    function courseById(id) {
        return state.courses.find(function (course) { return course.id === String(id); });
    }

    function onlineById(id) {
        return (state.onlineCourses || []).find(function (oc) { return String(oc.id) === String(id); });
    }

    function slugify(value) {
        var map = { "ə": "e", "ı": "i", "ö": "o", "ü": "u", "ğ": "g", "ş": "s", "ç": "c" };
        return String(value || "")
            .toLowerCase()
            .replace(/[əıöüğşç]/g, function (ch) { return map[ch] || ch; })
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/^-+|-+$/g, "");
    }

    function courseSections(courseId) {
        if (!state.curriculum) state.curriculum = {};
        if (!Array.isArray(state.curriculum[courseId])) state.curriculum[courseId] = [];
        return state.curriculum[courseId];
    }

    function toast(message) {
        var el = document.querySelector("[data-admin-toast]");
        if (!el) return;
        el.textContent = message;
        el.classList.add("is-open");
        clearTimeout(toast.timer);
        toast.timer = setTimeout(function () { el.classList.remove("is-open"); }, 2400);
    }

    // Kreativ təsdiq modalı (datalab-shop-admin.js-dəki window.dlConfirm) — native confirm əvəzinə
    function adminConfirm(message, onYes, opts) {
        opts = opts || {};
        var danger = opts.danger !== false;
        if (typeof window.dlConfirm === "function") {
            window.dlConfirm({
                title: opts.title || (danger ? "Diqqət" : "Təsdiq"),
                message: message,
                confirmText: opts.confirmText || (danger ? "Bəli, sil" : "Bəli"),
                danger: danger
            }, onYes);
        } else if (window.confirm(message)) {
            onYes();
        }
    }

    function setText(selector, value) {
        var el = document.querySelector(selector);
        if (el) el.textContent = value;
    }

    function setDbState(online) {
        var el = document.querySelector("[data-admin-db-state]");
        if (!el) return;
        el.classList.toggle("is-online", !!online);
        el.innerHTML = "<i></i>" + (online ? "Database aktiv" : "Local rejim");
    }

    /* ---------------- persistence ---------------- */

    function normalizeState(data) {
        if (!data || typeof data !== "object") return clone(seedData);
        if (!Array.isArray(data.courses)) data.courses = [];
        if (!Array.isArray(data.onlineCourses)) data.onlineCourses = [];
        if (!Array.isArray(data.onlineReviews)) data.onlineReviews = [];
        if (!Array.isArray(data.orders)) data.orders = [];
        if (!Array.isArray(data.leads)) data.leads = [];
        if (!Array.isArray(data.blogs)) data.blogs = [];
        if (!data.content || typeof data.content !== "object") data.content = clone(seedData.content);
        if (!data.seo || typeof data.seo !== "object") data.seo = clone(seedData.seo);
        if (!data.curriculum || typeof data.curriculum !== "object" || Array.isArray(data.curriculum)) data.curriculum = {};
        if (!data.courseDetails || typeof data.courseDetails !== "object" || Array.isArray(data.courseDetails)) data.courseDetails = {};
        if (!data.onlineCurriculum || typeof data.onlineCurriculum !== "object" || Array.isArray(data.onlineCurriculum)) data.onlineCurriculum = {};
        if (!data.onlineCourseDetails || typeof data.onlineCourseDetails !== "object" || Array.isArray(data.onlineCourseDetails)) data.onlineCourseDetails = {};
        if (!Array.isArray(data.homeTestimonials)) data.homeTestimonials = [];
        if (!Array.isArray(data.homePortfolio)) data.homePortfolio = [];
        if (!Array.isArray(data.homeCertificates)) data.homeCertificates = [];
        if (!data.homeStats || typeof data.homeStats !== "object" || Array.isArray(data.homeStats)) data.homeStats = {};
        if (!data.contact || typeof data.contact !== "object" || Array.isArray(data.contact)) data.contact = {};
        if (!Array.isArray(data.aiQuizzes)) data.aiQuizzes = [];
        if (!Array.isArray(data.aiKnowledge)) data.aiKnowledge = [];
        return data;
    }

    function loadData() {
        try {
            var saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || "null");
            if (saved && saved.courses && saved.orders && saved.leads) return normalizeState(saved);
        } catch (err) {}
        return clone(seedData);
    }

    function saveData() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        return fetch(API_URL + "?action=save-state", {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-Token": csrfToken },
            body: JSON.stringify(state)
        })
            .then(function (response) {
                if (response.status === 401) {
                    showLogin("Sessiya bitdi. Yenidən daxil ol.");
                    throw new Error("__auth__");
                }
                return response.json().then(function (payload) {
                    return { status: response.status, payload: payload };
                });
            })
            .then(function (result) {
                var payload = result.payload;
                if (result.status === 409 && payload && payload.data) {
                    // başqa tab/admin dəyişiklik edib — server versiyasını qəbul et
                    state = normalizeState(payload.data);
                    currentCourseId = state.courses.length ? state.courses[0].id : "";
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
                    renderAll();
                    toast("Diqqət: data başqa sessiyada dəyişilib və yenilənib. Son dəyişikliyini təkrar et.");
                    throw new Error("__conflict__");
                }
                if (!payload.ok) throw new Error(payload.message || "Database save failed");
                if (payload.data) {
                    state = normalizeState(payload.data);
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
                }
                setDbState(true);
                return payload.data;
            })
            .catch(function (err) {
                if (err && (err.message === "__auth__" || err.message === "__conflict__")) return;
                console.error(err);
                setDbState(false);
                toast("Database saxlanmadı. Local fallback aktivdir.");
            });
    }

    function loadDataFromApi() {
        return fetch(API_URL + "?action=state")
            .then(function (response) { return response.json(); })
            .then(function (payload) {
                if (!payload.ok) throw new Error(payload.message || "Database load failed");
                return normalizeState(payload.data);
            });
    }

    /* ---------------- shared renderers ---------------- */

    function statusLabel(value) {
        var labels = {
            active: "Aktiv",
            draft: "Qaralama",
            archived: "Arxiv",
            pending: "Gözləyir",
            paid: "Ödənilib",
            cancelled: "Ləğv",
            new: "Yeni",
            approved: "Təsdiqli",
            hidden: "Gizli"
        };
        return labels[value] || value;
    }

    function status(value) {
        return '<span class="dl-admin-status is-' + esc(value) + '">' + esc(statusLabel(value)) + "</span>";
    }

    function renderStats() {
        var activeOrders = state.orders.filter(function (order) { return order.status !== "cancelled"; });
        var revenue = activeOrders.reduce(function (sum, order) { return sum + Number(order.amount || 0); }, 0);
        setText("[data-stat-courses]", state.courses.length);
        setText("[data-stat-orders]", activeOrders.length);
        setText("[data-stat-leads]", state.leads.length);
        setText("[data-stat-revenue]", money(revenue));
        setText("[data-nav-orders]", activeOrders.length);
        setText("[data-nav-leads]", state.leads.length);
    }

    function renderDashboard() {
        var tbody = document.querySelector("[data-dashboard-orders]");
        if (tbody) {
            tbody.innerHTML = state.orders.slice(0, 5).map(function (order) {
                var course = courseById(order.courseId);
                return "<tr><td>" + esc(order.customer) + "</td><td>" + esc(course ? course.title : "Silinmiş kurs") + "</td><td>" + status(order.status) + "</td><td>" + money(order.amount) + "</td></tr>";
            }).join("");
        }

        var meters = document.querySelector("[data-dashboard-meters]");
        if (meters) {
            var max = state.courses.reduce(function (m, c) { return Math.max(m, Number(c.students || 0)); }, 1);
            meters.innerHTML = state.courses.slice(0, 5).map(function (course) {
                var pct = Math.round((Number(course.students || 0) / max) * 100);
                return '<div class="dl-meter"><span>' + esc(course.title) + "<strong>" + esc(course.students) + ' tələbə</strong></span><div class="dl-meter-bar"><i style="width:' + pct + '%"></i></div></div>';
            }).join("");
        }

        // Mini stat kartlar — lead mənbəyi
        var leadSrc = document.querySelector("[data-dash-lead-sources]");
        if (leadSrc) {
            var srcCount = {};
            state.leads.forEach(function(l) { var s = l.source || "Digər"; srcCount[s] = (srcCount[s] || 0) + 1; });
            leadSrc.innerHTML = Object.keys(srcCount).map(function(src) {
                return '<span class="dl-tag">' + esc(src) + ' <strong>' + srcCount[src] + '</strong></span>';
            }).join("") || '<span class="dl-admin-note">Məlumat yoxdur.</span>';
        }

        // Real sparkline-lar (statik deyil — data-dan hesablanır)
        var nonCancelled = state.orders.filter(function (o) { return o.status !== "cancelled"; });
        setSpark("orders", weeklySeries(nonCancelled, 8));
        setSpark("revenue", weeklySeries(nonCancelled, 8, function (o) { return Number(o.amount || 0); }));
        setSpark("leads", weeklySeries(state.leads, 8));
        setSpark("courses", state.courses.map(function (c) { return Number(c.students || 0); }));

        // Konversiya funnel: Lead → Sifariş → Ödənilib
        var funnel = document.querySelector("[data-dashboard-funnel]");
        if (funnel) {
            var paid = state.orders.filter(function (o) { return o.status === "paid"; }).length;
            var steps = [
                { label: "Lead", value: state.leads.length, tone: "tone-amber" },
                { label: "Sifariş", value: state.orders.length, tone: "tone-indigo" },
                { label: "Ödənilib", value: paid, tone: "tone-emerald" }
            ];
            var fbase = Math.max.apply(null, steps.map(function (s) { return s.value; }).concat([1]));
            funnel.innerHTML = steps.map(function (s, i) {
                var pct = Math.round((s.value / fbase) * 100);
                var conv = i === 0 ? 100 : (steps[i - 1].value ? Math.round((s.value / steps[i - 1].value) * 100) : 0);
                return '<div class="dl-funnel-row"><span class="dl-funnel-label">' + s.label +
                    '<strong>' + s.value + (i > 0 ? ' · ' + conv + '%' : '') + '</strong></span>' +
                    '<div class="dl-funnel-bar ' + s.tone + '"><i style="width:' + pct + '%"></i></div></div>';
            }).join("");
        }

        // Aylıq gəlir qrafiki (canvas — real data)
        renderRevenueChart();
    }

    /* trend helper-ləri (dashboard sparkline-ları üçün) */
    function weeklySeries(items, weeks, valFn) {
        var now = new Date();
        var buckets = [];
        for (var i = 0; i < weeks; i++) buckets.push(0);
        items.forEach(function (it) {
            if (!it.date) return;
            var d = new Date(it.date);
            if (isNaN(d.getTime())) return;
            var wk = Math.floor((now - d) / 86400000 / 7);
            if (wk >= 0 && wk < weeks) buckets[weeks - 1 - wk] += valFn ? valFn(it) : 1;
        });
        return buckets;
    }
    function sparkPoints(values) {
        var n = values.length;
        if (!n) return "0,16 100,16";
        var max = Math.max.apply(null, values);
        var min = Math.min.apply(null, values);
        var range = (max - min) || 1;
        var top = 4, bot = 28;
        return values.map(function (v, i) {
            var x = n === 1 ? 50 : (i / (n - 1)) * 100;
            var y = bot - ((v - min) / range) * (bot - top);
            return (Math.round(x * 10) / 10) + "," + (Math.round(y * 10) / 10);
        }).join(" ");
    }
    function setSpark(key, values) {
        var el = document.querySelector('[data-spark="' + key + '"]');
        if (el) el.setAttribute("points", sparkPoints(values));
    }

    function renderRevenueChart() {
        var canvas = document.getElementById("dlRevenueChart");
        if (!canvas || !canvas.getContext) return;
        var ctx = canvas.getContext("2d");
        // Son 6 ay label-ları
        var months = [];
        var now = new Date();
        for (var i = 5; i >= 0; i--) {
            var d = new Date(now.getFullYear(), now.getMonth() - i, 1);
            months.push({ label: d.toLocaleString("az", { month: "short" }), key: d.getFullYear() + "-" + String(d.getMonth() + 1).padStart(2, "0") });
        }
        // Hər ay üçün gəlir
        var revenues = months.map(function(m) {
            return state.orders.filter(function(o) {
                return o.status !== "cancelled" && (o.date || "").startsWith(m.key);
            }).reduce(function(sum, o) { return sum + Number(o.amount || 0); }, 0);
        });
        var W = canvas.width, H = canvas.height;
        var pad = 36;
        var maxVal = Math.max.apply(null, revenues.concat([1]));
        ctx.clearRect(0, 0, W, H);
        // Grid
        ctx.strokeStyle = "rgba(255,255,255,0.07)";
        ctx.lineWidth = 1;
        [0.25, 0.5, 0.75, 1].forEach(function(f) {
            var y = pad + (H - pad * 2) * (1 - f);
            ctx.beginPath(); ctx.moveTo(pad, y); ctx.lineTo(W - pad, y); ctx.stroke();
        });
        // Bars
        var barW = Math.floor((W - pad * 2) / months.length * 0.55);
        var gap = Math.floor((W - pad * 2) / months.length);
        var grad = ctx.createLinearGradient(0, 0, 0, H);
        grad.addColorStop(0, "rgba(99,102,241,0.9)");
        grad.addColorStop(1, "rgba(99,102,241,0.2)");
        revenues.forEach(function(val, i) {
            var x = pad + i * gap + (gap - barW) / 2;
            var barH = val > 0 ? Math.max(4, (val / maxVal) * (H - pad * 2)) : 3;
            var y = H - pad - barH;
            ctx.fillStyle = grad;
            ctx.beginPath();
            ctx.roundRect ? ctx.roundRect(x, y, barW, barH, 4) : ctx.rect(x, y, barW, barH);
            ctx.fill();
            // Label
            ctx.fillStyle = "rgba(255,255,255,0.55)";
            ctx.font = "10px Inter, system-ui, sans-serif";
            ctx.textAlign = "center";
            ctx.fillText(months[i].label, x + barW / 2, H - pad + 13);
            if (val > 0) {
                ctx.fillStyle = "rgba(255,255,255,0.8)";
                ctx.font = "bold 9px Inter, system-ui, sans-serif";
                ctx.fillText("$" + val, x + barW / 2, y - 4);
            }
        });
    }

    function renderCourses() {
        var tbody = document.querySelector("[data-courses-table]");
        if (!tbody) return;
        tbody.innerHTML = state.courses.map(function (course) {
            return [
                "<tr>",
                '<td><div class="dl-admin-course-cell"><img src="' + esc(course.image) + '" alt=""><div><strong>' + esc(course.title) + "</strong><br><small>" + esc(course.category) + " · " + esc(course.lessons) + " dərs · " + esc(course.students) + " tələbə</small></div></div></td>",
                "<td>" + money(course.price) + "</td>",
                "<td>" + status(course.status) + "</td>",
                '<td><div class="dl-admin-row-actions"><button type="button" data-edit-course="' + esc(course.id) + '" title="Redaktə"><i class="feather-edit-2"></i></button><button type="button" data-delete-course="' + esc(course.id) + '" title="Sil"><i class="feather-trash-2"></i></button></div></td>',
                "</tr>"
            ].join("");
        }).join("");
    }

    function renderOnlineCourses() {
        var tbody = document.querySelector("[data-online-table]");
        if (!tbody) return;
        var list = state.onlineCourses || [];
        if (!list.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="dl-admin-empty-row">Hələ online təlim əlavə edilməyib.</td></tr>';
            return;
        }
        tbody.innerHTML = list.map(function (oc) {
            return [
                "<tr>",
                '<td><div class="dl-admin-course-cell"><img src="' + esc(oc.image) + '" alt=""><div><strong>' + esc(oc.title) + "</strong><br><small>" + esc(oc.category) + " · " + esc(oc.lessons) + " dərs · " + esc(oc.students) + " tələbə</small></div></div></td>",
                "<td>" + money(oc.price) + "</td>",
                "<td>" + status(oc.status) + "</td>",
                '<td><div class="dl-admin-row-actions"><button type="button" data-edit-online="' + esc(oc.id) + '" title="Redaktə"><i class="feather-edit-2"></i></button><button type="button" data-delete-online="' + esc(oc.id) + '" title="Sil"><i class="feather-trash-2"></i></button></div></td>',
                "</tr>"
            ].join("");
        }).join("");
    }

    /* ---------------- online lessons (curriculum + detail page) ---------------- */
    var OL_TYPES = { video: { label: "Video", icon: "feather-play" }, article: { label: "Mətn dərs", icon: "feather-file-text" }, quiz: { label: "Quiz", icon: "feather-help-circle" }, assignment: { label: "Tapşırıq", icon: "feather-clipboard" } };

    function olCourses() { return state.onlineCourses || []; }
    function olById(id) { return olCourses().find(function (c) { return String(c.id) === String(id); }); }
    function olEnsureCurrent() {
        var list = olCourses();
        if (!list.length) { currentOnlineLessonId = ""; return; }
        if (!olById(currentOnlineLessonId)) currentOnlineLessonId = String(list[0].id);
    }
    function olSections(courseId) {
        if (!state.onlineCurriculum) state.onlineCurriculum = {};
        if (!Array.isArray(state.onlineCurriculum[courseId])) state.onlineCurriculum[courseId] = [];
        return state.onlineCurriculum[courseId];
    }
    function olFindSection(id) { return olSections(currentOnlineLessonId).find(function (s) { return s.id === id; }); }
    function olFindLesson(id) {
        var found = null;
        olSections(currentOnlineLessonId).forEach(function (s) {
            (s.lessons || []).forEach(function (ls) { if (ls.id === id) { found = Object.assign({}, ls, { sectionId: s.id }); } });
        });
        return found;
    }

    function renderOlCourseSelect() {
        var select = document.querySelector("[data-ol-course]");
        if (!select) return;
        olEnsureCurrent();
        select.innerHTML = olCourses().map(function (c) {
            return '<option value="' + esc(c.id) + '"' + (String(c.id) === String(currentOnlineLessonId) ? " selected" : "") + ">" + esc(c.title) + "</option>";
        }).join("");
    }
    function renderOlSummary() {
        var box = document.querySelector("[data-ol-summary]");
        if (!box) return;
        var secs = olSections(currentOnlineLessonId);
        var lessonCount = secs.reduce(function (n, s) { return n + (s.lessons || []).length; }, 0);
        var totalMin = secs.reduce(function (n, s) { return n + (s.lessons || []).reduce(function (m, l) { return m + parseMinutes(l.duration); }, 0); }, 0);
        var previews = secs.reduce(function (n, s) { return n + (s.lessons || []).filter(function (l) { return l.preview; }).length; }, 0);
        box.innerHTML =
            '<span class="dl-cur-chip"><i class="feather-layers"></i>' + secs.length + " bölmə</span>" +
            '<span class="dl-cur-chip tone-emerald-chip"><i class="feather-play-circle"></i>' + lessonCount + " dərs</span>" +
            '<span class="dl-cur-chip tone-amber-chip"><i class="feather-clock"></i>' + esc(formatMinutes(totalMin)) + "</span>" +
            '<span class="dl-cur-chip"><i class="feather-eye"></i>' + previews + " preview</span>";
    }
    function renderOlCurriculum() {
        var list = document.querySelector("[data-ol-curriculum]");
        if (!list) return;
        olEnsureCurrent();
        renderOlSummary();
        if (!currentOnlineLessonId) { list.innerHTML = '<div class="dl-cur-empty"><i class="feather-monitor"></i>Əvvəlcə "Online Təlimlər" bölməsində kurs əlavə et.</div>'; return; }
        var sections = olSections(currentOnlineLessonId);
        if (!sections.length) { list.innerHTML = '<div class="dl-cur-empty"><i class="feather-layers"></i>Hələ bölmə yoxdur. "Yeni bölmə" düyməsi ilə başla.</div>'; return; }
        list.innerHTML = sections.map(function (section) {
            var lessons = section.lessons || [];
            var rows = lessons.length ? lessons.map(function (lesson) {
                var type = OL_TYPES[lesson.type] || OL_TYPES.video;
                var meta = [];
                if (lesson.duration) meta.push(esc(lesson.duration));
                return [
                    '<div class="dl-cur-lesson">',
                    '<span class="dl-cur-les-ico type-' + esc(lesson.type) + '"><i class="' + type.icon + '"></i></span>',
                    '<div class="dl-cur-lesson-info"><strong>' + esc(lesson.title) + "</strong>",
                    '<div class="dl-cur-lesson-meta">',
                    meta.length ? "<span>" + meta.join(" · ") + "</span>" : "",
                    '<span class="dl-cur-type-badge type-' + esc(lesson.type) + '">' + esc(type.label) + "</span>",
                    lesson.preview ? '<span class="dl-cur-preview-badge"><i class="feather-eye"></i>Preview</span>' : "",
                    lesson.locked ? '<span class="dl-cur-lock"><i class="feather-lock"></i></span>' : "",
                    "</div></div>",
                    '<div class="dl-cur-tools">',
                    '<button type="button" data-ol-edit-lesson="' + esc(lesson.id) + '" title="Redaktə"><i class="feather-edit-2"></i></button>',
                    '<button type="button" data-ol-del-lesson="' + esc(lesson.id) + '" title="Sil"><i class="feather-trash-2"></i></button>',
                    "</div></div>"
                ].join("");
            }).join("") : '<p class="dl-cur-no-lessons">Bu bölmədə dərs yoxdur. "+" düyməsi ilə əlavə et.</p>';
            return [
                '<article class="dl-cur-section">',
                '<header class="dl-cur-section-head">',
                '<span class="dl-cur-sec-ico"><i class="feather-layers"></i></span>',
                '<div class="dl-cur-section-title"><strong>' + esc(section.title) + "</strong>",
                "<small>" + esc(section.duration || "") + " · " + lessons.length + " dərs</small></div>",
                section.locked ? '<span class="dl-cur-lock"><i class="feather-lock"></i>Kilidli</span>' : "",
                '<div class="dl-cur-tools">',
                '<button type="button" data-ol-add-lesson="' + esc(section.id) + '" title="Dərs əlavə et"><i class="feather-plus"></i></button>',
                '<button type="button" data-ol-edit-section="' + esc(section.id) + '" title="Redaktə"><i class="feather-edit-2"></i></button>',
                '<button type="button" data-ol-del-section="' + esc(section.id) + '" title="Sil"><i class="feather-trash-2"></i></button>',
                "</div></header>",
                '<div class="dl-cur-lessons">' + rows + "</div></article>"
            ].join("");
        }).join("");
        if (window.feather && typeof window.feather.replace === "function") window.feather.replace();
    }
    function renderOlSectionOptions() {
        var select = document.querySelector("[data-ol-lesson-form] select[name=sectionId]");
        if (!select) return;
        select.innerHTML = olSections(currentOnlineLessonId).map(function (s) { return '<option value="' + esc(s.id) + '">' + esc(s.title) + "</option>"; }).join("");
    }
    function closeOlEditors() {
        var s = document.querySelector("[data-ol-section-editor]"); if (s) s.hidden = true;
        var l = document.querySelector("[data-ol-lesson-editor]"); if (l) l.hidden = true;
    }
    function fillOlSectionForm(section) {
        var panel = document.querySelector("[data-ol-section-editor]");
        var lpanel = document.querySelector("[data-ol-lesson-editor]");
        if (!panel) return;
        if (lpanel) lpanel.hidden = true;
        panel.hidden = false;
        var form = panel.querySelector("form");
        form.elements.id.value = section ? section.id : "";
        form.elements.title.value = section ? section.title : "";
        form.elements.duration.value = section ? (section.duration || "") : "";
        form.elements.locked.checked = !!(section && section.locked);
        var t = document.querySelector("[data-ol-section-form-title]"); if (t) t.textContent = section ? "Bölmə redaktəsi" : "Yeni bölmə";
        form.elements.title.focus();
    }
    function fillOlLessonForm(lesson, sectionId) {
        var panel = document.querySelector("[data-ol-lesson-editor]");
        var spanel = document.querySelector("[data-ol-section-editor]");
        if (!panel) return;
        if (spanel) spanel.hidden = true;
        panel.hidden = false;
        renderOlSectionOptions();
        var form = panel.querySelector("form");
        form.elements.id.value = lesson ? lesson.id : "";
        form.elements.sectionId.value = lesson ? (lesson.sectionId || sectionId || "") : (sectionId || "");
        form.elements.title.value = lesson ? lesson.title : "";
        form.elements.type.value = lesson ? (lesson.type || "video") : "video";
        form.elements.duration.value = lesson ? (lesson.duration || "") : "";
        form.elements.link.value = lesson ? (lesson.link || "") : "";
        form.elements.preview.checked = !!(lesson && lesson.preview);
        form.elements.locked.checked = !!(lesson && lesson.locked);
        var t = document.querySelector("[data-ol-lesson-form-title]"); if (t) t.textContent = lesson ? "Dərs redaktəsi" : "Yeni dərs";
        form.elements.title.focus();
    }
    function fillOlDetailForm() {
        var form = document.querySelector("[data-ol-detail-form]");
        if (!form) return;
        var d = (state.onlineCourseDetails && state.onlineCourseDetails[currentOnlineLessonId]) || {};
        ["subtitle", "instructor", "date", "previewVideo", "oldPrice", "discountText", "startDate", "registration", "enrolled", "language", "quizzes", "certificate", "passPercentage", "overview", "overviewMore"].forEach(function (k) {
            if (form.elements[k]) form.elements[k].value = d[k] != null ? d[k] : "";
        });
        ["outcomes", "requirements", "descriptionPoints"].forEach(function (k) {
            if (form.elements[k]) form.elements[k].value = Array.isArray(d[k]) ? d[k].join("\n") : (d[k] || "");
        });
        if (form.elements.videoIntro) form.elements.videoIntro.value = d.videoIntro ? "1" : "";
    }
    function renderOnlineLessons() {
        renderOlCourseSelect();
        renderOlCurriculum();
        fillOlDetailForm();
    }
    function persistOnlineLessons(msg) {
        saveData();
        renderOnlineLessons();
        if (msg) toast(msg);
    }

    /* ---------------- online reviews (moderation) ---------------- */
    function renderOnlineReviews() {
        var list = state.onlineReviews || [];
        var pending = list.filter(function (r) { return r.status === "pending"; }).length;
        setText("[data-nav-online-reviews]", pending);
        var summary = document.querySelector("[data-ol-reviews-summary]");
        if (summary) summary.textContent = list.length + " rəy · " + pending + " gözləyir";
        var tbody = document.querySelector("[data-ol-reviews-table]");
        if (!tbody) return;
        if (!list.length) { tbody.innerHTML = '<tr><td colspan="7" class="dl-admin-empty-row">Hələ rəy yoxdur.</td></tr>'; return; }
        tbody.innerHTML = list.map(function (r) {
            var course = onlineById(r.courseId);
            var stars = "";
            for (var i = 0; i < 5; i++) stars += '<i class="fas fa-star" style="color:' + (i < r.rating ? "#f59e0b" : "#d1d5db") + '"></i>';
            return [
                "<tr>",
                "<td>" + esc(r.name || "Tələbə") + "</td>",
                "<td>" + esc(course ? course.title : r.courseId) + "</td>",
                '<td style="white-space:nowrap">' + stars + "</td>",
                '<td style="max-width:320px;white-space:normal">' + esc(r.body || "") + "</td>",
                "<td>" + status(r.status) + "</td>",
                "<td>" + esc(String(r.date || "").slice(0, 10)) + "</td>",
                '<td><div class="dl-admin-row-actions">' +
                (r.status !== "approved" ? '<button type="button" data-ol-review-approve="' + esc(r.id) + '" title="Təsdiqlə"><i class="feather-check"></i></button>' : "") +
                (r.status !== "hidden" ? '<button type="button" data-ol-review-hide="' + esc(r.id) + '" title="Gizlət"><i class="feather-eye-off"></i></button>' : "") +
                '<button type="button" data-ol-review-delete="' + esc(r.id) + '" title="Sil"><i class="feather-trash-2"></i></button>' +
                "</div></td>",
                "</tr>"
            ].join("");
        }).join("");
        if (window.feather && typeof window.feather.replace === "function") window.feather.replace();
    }

    function olReviewAction(action, id) {
        fetch(API_URL + "?action=" + action, {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-Token": csrfToken },
            body: JSON.stringify({ id: id, status: arguments[2] || undefined })
        })
            .then(function (response) { return response.json(); })
            .then(function (payload) {
                if (!payload.ok) throw new Error(payload.message || "Əməliyyat alınmadı.");
                if (payload.data) { state = normalizeState(payload.data); localStorage.setItem(STORAGE_KEY, JSON.stringify(state)); }
                renderAll();
                toast("Rəy yeniləndi.");
            })
            .catch(function (err) { toast(err.message); });
    }

    /* ---------------- homepage CMS (testimonials, certs, portfolio, stats) ---------------- */
    var HOME_LISTS = {
        ht: { key: "homeTestimonials", form: "[data-ht-form]", table: "[data-ht-table]", titleSel: "[data-ht-form-title]", titleLabel: "Rəy", upload: "[data-ht-image-upload]", cols: function (it) { return [esc(it.name), esc(it.role), (it.rating || 5) + " ★"]; } },
        hc: { key: "homeCertificates", form: "[data-hc-form]", table: "[data-hc-table]", titleSel: "[data-hc-form-title]", titleLabel: "Sertifikat", upload: "[data-hc-image-upload]", cols: function (it) { return [esc(it.title), esc(it.tag)]; } },
        hp: { key: "homePortfolio", form: "[data-hp-form]", table: "[data-hp-table]", titleSel: "[data-hp-form-title]", titleLabel: "Portfolio", upload: "[data-hp-image-upload]", cols: function (it) { return [esc(it.title), esc(it.tag)]; } },
        ak: { key: "aiKnowledge", form: "[data-ak-form]", table: "[data-ak-table]", titleSel: "[data-ak-form-title]", titleLabel: "Bilik", upload: null, cols: function (it) { return [esc(it.title || "—"), esc(String(it.content || "").slice(0, 70))]; } }
    };
    function homeList(prefix) {
        var cfg = HOME_LISTS[prefix];
        if (!Array.isArray(state[cfg.key])) state[cfg.key] = [];
        return state[cfg.key];
    }
    function homeById(prefix, id) { return homeList(prefix).find(function (it) { return String(it.id) === String(id); }); }
    function renderHomeList(prefix) {
        var cfg = HOME_LISTS[prefix];
        var tbody = document.querySelector(cfg.table);
        if (!tbody) return;
        var list = homeList(prefix);
        if (!list.length) { tbody.innerHTML = '<tr><td colspan="4" class="dl-admin-empty-row">Boşdur.</td></tr>'; return; }
        tbody.innerHTML = list.map(function (it) {
            var cells = cfg.cols(it).map(function (c) { return "<td>" + c + "</td>"; }).join("");
            return "<tr>" + cells + '<td><div class="dl-admin-row-actions"><button type="button" data-edit-' + prefix + '="' + esc(it.id) + '" title="Redaktə"><i class="feather-edit-2"></i></button><button type="button" data-del-' + prefix + '="' + esc(it.id) + '" title="Sil"><i class="feather-trash-2"></i></button></div></td></tr>';
        }).join("");
        if (window.feather && typeof window.feather.replace === "function") window.feather.replace();
    }
    function fillHomeForm(prefix, item) {
        var cfg = HOME_LISTS[prefix];
        var form = document.querySelector(cfg.form);
        if (!form) return;
        Array.prototype.slice.call(form.elements).forEach(function (el) {
            if (!el.name) return;
            el.value = item && item[el.name] != null ? item[el.name] : (el.name === "rating" ? 5 : "");
        });
        var t = document.querySelector(cfg.titleSel);
        if (t) t.innerHTML = '<span class="dl-h-ico tone-rose"><i class="feather-edit-2"></i></span>' + cfg.titleLabel + (item ? " redaktəsi" : " — yeni");
    }
    function submitHomeForm(prefix, form) {
        var data = readForm(form);
        data.id = data.id || (prefix + "-" + Date.now());
        if (data.rating != null && data.rating !== "") data.rating = Math.max(1, Math.min(5, Number(data.rating) || 5));
        var list = homeList(prefix);
        var idx = list.findIndex(function (it) { return String(it.id) === String(data.id); });
        if (idx >= 0) list[idx] = data; else list.push(data);
        saveData();
        renderHomeList(prefix);
        fillHomeForm(prefix, data);
        toast("Yadda saxlandı.");
    }
    function fillHsForm() {
        var form = document.querySelector("[data-hs-form]");
        if (!form) return;
        var s = state.homeStats || {};
        ["statActive", "statActiveLabel", "statRating", "statRatingLabel", "newTag"].forEach(function (k) {
            if (form.elements[k]) form.elements[k].value = s[k] != null ? s[k] : "";
        });
        var badges = Array.isArray(s.badges) ? s.badges : [];
        for (var i = 0; i < 4; i++) {
            var b = badges[i] || {};
            if (form.elements["badge" + i + "icon"]) form.elements["badge" + i + "icon"].value = b.icon || "";
            if (form.elements["badge" + i + "color"]) form.elements["badge" + i + "color"].value = b.color || "";
            if (form.elements["badge" + i + "text"]) form.elements["badge" + i + "text"].value = b.text || "";
        }
    }
    function submitHsForm(form) {
        var d = readForm(form);
        var badges = [];
        for (var i = 0; i < 4; i++) {
            var text = d["badge" + i + "text"];
            if (text) badges.push({ icon: d["badge" + i + "icon"] || "feather-star", color: d["badge" + i + "color"] || "#2f57ef", text: text });
        }
        state.homeStats = { statActive: d.statActive || "", statActiveLabel: d.statActiveLabel || "", statRating: d.statRating || "", statRatingLabel: d.statRatingLabel || "", newTag: d.newTag || "", badges: badges };
        saveData();
        toast("Statistika yadda saxlandı.");
    }
    function renderHome() {
        renderHomeList("ht");
        renderHomeList("hc");
        renderHomeList("hp");
        fillHsForm();
    }

    /* ---------------- contact (Əlaqə) ---------------- */
    var CONTACT_FIELDS = ["phone", "email", "address", "facebook", "instagram", "linkedin", "twitter", "footerAbout", "newsletterTitle", "newsletterDesc", "pageSubtitle", "mapEmbed", "workingHours"];
    function fillContactForm() {
        var form = document.querySelector("[data-contact-form]");
        if (!form) return;
        var c = state.contact || {};
        CONTACT_FIELDS.forEach(function (k) { if (form.elements[k]) form.elements[k].value = c[k] != null ? c[k] : ""; });
    }
    function submitContactForm(form) {
        var d = readForm(form);
        var obj = {};
        CONTACT_FIELDS.forEach(function (k) { obj[k] = d[k] || ""; });
        state.contact = obj;
        saveData();
        toast("Əlaqə məlumatları yadda saxlandı.");
    }
    function renderAiChat() { renderHomeList("ak"); }
    function renderContact() { fillContactForm(); }

    /* ---------------- AI quiz generator ---------------- */
    var aiGenerated = null;
    function quizCardHtml(questions, withCorrect) {
        return questions.map(function (q, qi) {
            var opts = (q.options || []).map(function (o, oi) {
                var mark = withCorrect && oi === q.correct ? ' <span class="dl-admin-status is-active">düzgün</span>' : "";
                return '<li>' + String.fromCharCode(65 + oi) + ") " + esc(o) + mark + "</li>";
            }).join("");
            return '<div style="padding:12px 0;border-top:1px solid var(--dl-border,#e4e9f3)"><strong>' + (qi + 1) + ". " + esc(q.question) + '</strong><ul style="margin:8px 0 0;padding-left:18px;line-height:1.9">' + opts + "</ul></div>";
        }).join("");
    }
    function renderAiResult() {
        var box = document.querySelector("[data-aiq-result]");
        var saveBtn = document.querySelector("[data-aiq-save]");
        if (!box) return;
        if (!aiGenerated || !aiGenerated.length) { box.innerHTML = ""; if (saveBtn) saveBtn.hidden = true; return; }
        box.innerHTML = '<div class="dl-admin-section-label"><strong>Generasiya olunan suallar (' + aiGenerated.length + ")</strong></div>" + quizCardHtml(aiGenerated, true);
        if (saveBtn) saveBtn.hidden = false;
    }
    function renderAiSaved() {
        var box = document.querySelector("[data-aiq-saved]");
        if (!box) return;
        var list = state.aiQuizzes || [];
        if (!list.length) { box.innerHTML = '<p class="dl-admin-note">Hələ saxlanmış quiz yoxdur.</p>'; return; }
        box.innerHTML = list.map(function (qz) {
            return '<details style="margin-bottom:10px;border:1px solid var(--dl-border,#e4e9f3);border-radius:10px;padding:10px 14px">' +
                '<summary style="cursor:pointer;font-weight:700">' + esc(qz.title || "Quiz") + " · " + (qz.questions || []).length + " sual " +
                '<button type="button" data-aiq-del-quiz="' + esc(qz.id) + '" style="float:right;border:0;background:transparent;color:#d6455a;cursor:pointer"><i class="feather-trash-2"></i></button></summary>' +
                quizCardHtml(qz.questions || [], true) + "</details>";
        }).join("");
        if (window.feather && typeof window.feather.replace === "function") window.feather.replace();
    }
    function renderAiQuiz() { renderAiResult(); renderAiSaved(); }

    function matchesQuery(query, parts) {
        if (!query) return true;
        var q = query.toLowerCase();
        return parts.some(function (part) {
            return String(part || "").toLowerCase().indexOf(q) !== -1;
        });
    }

    function renderOrders() {
        var tbody = document.querySelector("[data-orders-table]");
        if (!tbody) return;
        var visible = state.orders.filter(function (order) {
            var course = courseById(order.courseId);
            if (orderFilter !== "all" && order.status !== orderFilter) return false;
            return matchesQuery(orderQuery, [order.id, order.customer, order.phone, course && course.title]);
        });
        renderPager("orders", visible.length);
        var pageRows = visible.slice((page.orders - 1) * PAGE_SIZE, page.orders * PAGE_SIZE);
        pageRowsCache.orders = pageRows;
        renderBulkBar("orders");
        if (!visible.length) {
            tbody.innerHTML = '<tr><td colspan="9" class="dl-admin-empty-cell">Nəticə tapılmadı.</td></tr>';
            syncSelectAll("orders");
            return;
        }
        tbody.innerHTML = pageRows.map(function (order) {
            var course = courseById(order.courseId);
            var wa = order.phone ? 'https://wa.me/' + order.phone.replace(/\D/g, '') : '';
            return [
                "<tr>",
                selCell("orders", order.id),
                "<td><strong>" + esc(order.id) + "</strong></td>",
                "<td>" + esc(order.customer) + "</td>",
                "<td>" + esc(course ? course.title : "Silinmiş kurs") + "</td>",
                "<td>" + esc(order.phone) + "</td>",
                "<td>" + esc(order.date || "—") + "</td>",
                "<td>" + status(order.status) + "</td>",
                "<td>" + money(order.amount) + "</td>",
                '<td><div class="dl-admin-row-actions">',
                wa ? '<a href="' + esc(wa) + '" target="_blank" rel="noopener" class="dl-admin-icon-btn dl-wa-btn" title="WhatsApp"><i class="feather-message-circle"></i></a>' : '',
                '<button type="button" data-view-order="' + esc(order.id) + '" title="Detallı bax"><i class="feather-eye"></i></button>',
                '<button type="button" data-cycle-order="' + esc(order.id) + '" title="Status dəyiş"><i class="feather-repeat"></i></button>',
                '<button type="button" data-delete-order="' + esc(order.id) + '" title="Sil"><i class="feather-trash-2"></i></button>',
                '</div></td>',
                "</tr>"
            ].join("");
        }).join("");
        syncSelectAll("orders");
    }

    function renderLeads() {
        var tbody = document.querySelector("[data-leads-table]");
        if (!tbody) return;
        var visible = state.leads.filter(function (lead) {
            if (leadFilter !== "all" && lead.status !== leadFilter) return false;
            return matchesQuery(leadQuery, [lead.name, lead.source, lead.interest]);
        });
        renderPager("leads", visible.length);
        var pageRows = visible.slice((page.leads - 1) * PAGE_SIZE, page.leads * PAGE_SIZE);
        pageRowsCache.leads = pageRows;
        renderBulkBar("leads");
        if (!visible.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="dl-admin-empty-cell">Nəticə tapılmadı.</td></tr>';
            syncSelectAll("leads");
            return;
        }
        tbody.innerHTML = pageRows.map(function (lead) {
            return [
                "<tr>",
                selCell("leads", lead.id),
                "<td><strong>" + esc(lead.name) + "</strong></td>",
                "<td>" + esc(lead.source) + "</td>",
                "<td>" + esc(lead.interest) + "</td>",
                "<td>" + status(lead.status) + "</td>",
                "<td>" + esc(lead.date) + "</td>",
                '<td><div class="dl-admin-row-actions">',
                '<button type="button" data-lead-to-order="' + esc(lead.id) + '" title="Sifarişə çevir"><i class="feather-shopping-bag"></i></button>',
                '<button type="button" data-cycle-lead="' + esc(lead.id) + '" title="Status dəyiş"><i class="feather-repeat"></i></button>',
                '<button type="button" data-delete-lead="' + esc(lead.id) + '" title="Sil"><i class="feather-trash-2"></i></button>',
                '</div></td>',
                "</tr>"
            ].join("");
        }).join("");
        syncSelectAll("leads");
        renderLeadKanban();
    }

    var LEAD_STAGES = [
        { k: "new", label: "Yeni" },
        { k: "pending", label: "Gözləyir" },
        { k: "paid", label: "Konvertasiya" },
        { k: "cancelled", label: "Ləğv" }
    ];
    function renderLeadKanban() {
        var board = document.querySelector("[data-lead-kanban]");
        if (!board) return;
        var leads = state.leads.filter(function (l) {
            return matchesQuery(leadQuery, [l.name, l.source, l.interest]);
        });
        board.innerHTML = LEAD_STAGES.map(function (c) {
            var items = leads.filter(function (l) { return (l.status || "new") === c.k; });
            var cards = items.map(function (l) {
                return '<div class="dl-kan-card" draggable="true" data-kanban-card="' + esc(l.id) + '">' +
                    '<strong>' + esc(l.name) + '</strong>' +
                    (l.interest ? '<span>' + esc(l.interest) + '</span>' : '') +
                    '<small>' + esc(l.source || "") + (l.date ? ' · ' + esc(l.date) : '') + '</small>' +
                    '</div>';
            }).join("") || '<div class="dl-kan-empty">Boşdur</div>';
            return '<div class="dl-kan-col" data-kanban-col="' + c.k + '">' +
                '<div class="dl-kan-col-head"><span>' + c.label + '</span><b>' + items.length + '</b></div>' +
                '<div class="dl-kan-col-body">' + cards + '</div>' +
                '</div>';
        }).join("");
    }

    /* ---------------- curriculum (Dərslər) ---------------- */

    function sectionMinutes(section) {
        return (section.lessons || []).reduce(function (sum, lesson) { return sum + parseMinutes(lesson.duration); }, 0);
    }

    function renderCourseSelect() {
        var select = document.querySelector("[data-cur-course]");
        if (!select) return;
        if (!currentCourseId && state.courses.length) currentCourseId = state.courses[0].id;
        select.innerHTML = state.courses.map(function (course) {
            return '<option value="' + esc(course.id) + '"' + (course.id === currentCourseId ? " selected" : "") + ">" + esc(course.title) + "</option>";
        }).join("");
    }

    function renderSectionOptions() {
        var select = document.querySelector('[data-lesson-form] select[name="sectionId"]');
        if (!select) return;
        var sections = courseSections(currentCourseId);
        select.innerHTML = sections.map(function (section) {
            return '<option value="' + esc(section.id) + '">' + esc(section.title) + "</option>";
        }).join("");
    }

    function renderCurriculumSummary() {
        var box = document.querySelector("[data-cur-summary]");
        if (!box) return;
        var sections = courseSections(currentCourseId);
        var lessonCount = sections.reduce(function (sum, s) { return sum + (s.lessons || []).length; }, 0);
        var totalMin = sections.reduce(function (sum, s) { return sum + sectionMinutes(s); }, 0);
        var previews = sections.reduce(function (sum, s) {
            return sum + (s.lessons || []).filter(function (l) { return l.preview; }).length;
        }, 0);
        box.innerHTML =
            '<span class="dl-cur-chip"><i class="feather-layers"></i>' + sections.length + " bölmə</span>" +
            '<span class="dl-cur-chip tone-emerald-chip"><i class="feather-play-circle"></i>' + lessonCount + " dərs</span>" +
            '<span class="dl-cur-chip tone-amber-chip"><i class="feather-clock"></i>' + esc(formatMinutes(totalMin)) + "</span>" +
            '<span class="dl-cur-chip"><i class="feather-eye"></i>' + previews + " preview</span>";
    }

    function lessonRow(section, lesson, lessonIndex, lessonTotal) {
        var type = LESSON_TYPES[lesson.type] || LESSON_TYPES.video;
        var meta = [];
        if (lesson.duration) meta.push(esc(lesson.duration));
        if (lesson.type === "video") meta.push(esc(SOURCE_LABELS[lesson.source] || "Avtomatik"));
        return [
            '<div class="dl-cur-lesson" draggable="true" data-drag-les="' + esc(lesson.id) + '" data-in-sec="' + esc(section.id) + '">',
            '<span class="dl-cur-drag" title="Sürüklə"><i class="feather-menu"></i></span>',
            '<span class="dl-cur-les-ico type-' + esc(lesson.type) + '"><i class="' + type.icon + '"></i></span>',
            '<div class="dl-cur-lesson-info"><strong>' + esc(lesson.title) + "</strong>",
            '<div class="dl-cur-lesson-meta">',
            meta.length ? "<span>" + meta.join(" · ") + "</span>" : "",
            '<span class="dl-cur-type-badge type-' + esc(lesson.type) + '">' + esc(type.label) + "</span>",
            lesson.preview ? '<span class="dl-cur-preview-badge"><i class="feather-eye"></i>Preview</span>' : "",
            "</div></div>",
            '<div class="dl-cur-tools">',
            '<button type="button" data-les-up data-sec="' + esc(section.id) + '" data-idx="' + lessonIndex + '" title="Yuxarı"' + (lessonIndex === 0 ? " disabled" : "") + '><i class="feather-arrow-up"></i></button>',
            '<button type="button" data-les-down data-sec="' + esc(section.id) + '" data-idx="' + lessonIndex + '" title="Aşağı"' + (lessonIndex === lessonTotal - 1 ? " disabled" : "") + '><i class="feather-arrow-down"></i></button>',
            '<button type="button" data-les-edit data-sec="' + esc(section.id) + '" data-les="' + esc(lesson.id) + '" title="Redaktə"><i class="feather-edit-2"></i></button>',
            '<button type="button" data-les-del data-sec="' + esc(section.id) + '" data-les="' + esc(lesson.id) + '" title="Sil"><i class="feather-trash-2"></i></button>',
            "</div></div>"
        ].join("");
    }

    function renderCurriculum() {
        var list = document.querySelector("[data-curriculum-list]");
        if (!list) return;

        renderCourseSelect();
        renderSectionOptions();
        renderCurriculumSummary();

        var sections = courseSections(currentCourseId);

        if (!sections.length) {
            list.innerHTML = '<div class="dl-cur-empty"><i class="feather-layers"></i>Hələ bölmə yoxdur. "Yeni bölmə" düyməsi ilə kurs proqramını qurmağa başla.</div>';
            return;
        }

        list.innerHTML = sections.map(function (section, index) {
            var lessons = section.lessons || [];
            var collapsed = section.collapsed ? " is-collapsed" : "";
            return [
                '<article class="dl-cur-section' + collapsed + '" data-section-card="' + esc(section.id) + '">',
                '<header class="dl-cur-section-head" data-sec-toggle="' + esc(section.id) + '">',
                '<span class="dl-cur-drag" draggable="true" data-drag-sec="' + esc(section.id) + '" title="Sürüklə"><i class="feather-menu"></i></span>',
                '<span class="dl-cur-sec-ico"><i class="feather-layers"></i></span>',
                '<div class="dl-cur-section-title"><strong>' + esc(section.title) + "</strong>",
                "<small>" + esc(formatMinutes(sectionMinutes(section))) + " · " + lessons.length + " dərs</small></div>",
                section.locked ? '<span class="dl-cur-lock"><i class="feather-lock"></i>Kilidli</span>' : "",
                '<div class="dl-cur-tools">',
                '<button type="button" data-sec-up data-idx="' + index + '" title="Yuxarı"' + (index === 0 ? " disabled" : "") + '><i class="feather-arrow-up"></i></button>',
                '<button type="button" data-sec-down data-idx="' + index + '" title="Aşağı"' + (index === sections.length - 1 ? " disabled" : "") + '><i class="feather-arrow-down"></i></button>',
                '<button type="button" data-sec-add-lesson="' + esc(section.id) + '" title="Dərs əlavə et"><i class="feather-plus"></i></button>',
                '<button type="button" data-sec-edit="' + esc(section.id) + '" title="Redaktə"><i class="feather-edit-2"></i></button>',
                '<button type="button" data-sec-del="' + esc(section.id) + '" title="Sil"><i class="feather-trash-2"></i></button>',
                '<button type="button" class="dl-cur-caret" data-sec-toggle="' + esc(section.id) + '" title="Aç/bağla"><i class="feather-chevron-down"></i></button>',
                "</div></header>",
                '<div class="dl-cur-lessons">',
                lessons.length
                    ? lessons.map(function (lesson, li) { return lessonRow(section, lesson, li, lessons.length); }).join("")
                    : '<p class="dl-cur-no-lessons">Bu bölmədə dərs yoxdur. "+" düyməsi ilə əlavə et.</p>',
                "</div></article>"
            ].join("");
        }).join("");
    }

    function syncCourseLessonCount() {
        var course = courseById(currentCourseId);
        if (!course) return;
        var total = courseSections(currentCourseId).reduce(function (sum, s) { return sum + (s.lessons || []).length; }, 0);
        if (total > 0) course.lessons = total;
    }

    function findSection(sectionId) {
        return courseSections(currentCourseId).find(function (s) { return s.id === sectionId; });
    }

    function openEditor(which) {
        var sectionEditor = document.querySelector("[data-section-editor]");
        var lessonEditor = document.querySelector("[data-lesson-editor]");
        if (sectionEditor) sectionEditor.hidden = which !== "section";
        if (lessonEditor) lessonEditor.hidden = which !== "lesson";
    }

    function closeEditors() {
        openEditor("none");
    }

    function fillSectionForm(section) {
        var form = document.querySelector("[data-section-form]");
        if (!form) return;
        form.elements.id.value = section ? section.id : "";
        form.elements.title.value = section ? section.title : "";
        form.elements.duration.value = section ? formatMinutes(sectionMinutes(section)) : "";
        form.elements.locked.checked = !!(section && section.locked);
        setText("[data-section-form-title]", section ? "Bölmə redaktəsi" : "Yeni bölmə");
        openEditor("section");
        form.elements.title.focus();
    }

    function toggleLessonTypeFields(form) {
        var isVideo = form.elements.type.value === "video";
        var sourceField = form.querySelector("[data-lesson-video-fields]");
        var linkField = form.querySelector("[data-lesson-link-field]");
        if (sourceField) sourceField.style.display = isVideo ? "" : "none";
        if (linkField) linkField.style.display = isVideo ? "" : "none";
    }

    function fillLessonForm(lesson, sectionId) {
        var form = document.querySelector("[data-lesson-form]");
        if (!form) return;
        renderSectionOptions();
        form.elements.id.value = lesson ? lesson.id : "";
        form.elements.sectionId.value = sectionId || (courseSections(currentCourseId)[0] || {}).id || "";
        form.elements.title.value = lesson ? lesson.title : "";
        form.elements.type.value = lesson ? lesson.type : "video";
        form.elements.duration.value = lesson ? lesson.duration : "";
        form.elements.source.value = lesson ? (lesson.source || "auto") : "auto";
        form.elements.link.value = lesson ? (lesson.link || "") : "";
        form.elements.note.value = lesson ? (lesson.note || "") : "";
        form.elements.preview.checked = !!(lesson && lesson.preview);
        setText("[data-lesson-form-title]", lesson ? "Dərs redaktəsi" : "Yeni dərs");
        toggleLessonTypeFields(form);
        openEditor("lesson");
        form.elements.title.focus();
    }

    function moveItem(list, from, to) {
        if (to < 0 || to >= list.length) return;
        var item = list.splice(from, 1)[0];
        list.splice(to, 0, item);
    }

    function persistCurriculum(message) {
        syncCourseLessonCount();
        saveData();
        renderAll();
        if (message) toast(message);
    }

    /* ---------------- forms ---------------- */

    function setCourseFormTitle(text) {
        var el = document.querySelector("[data-course-form-title]");
        if (el) el.innerHTML = '<span class="dl-h-ico tone-rose"><i class="feather-edit-2"></i></span>' + esc(text);
    }

    function fillCourseForm(course) {
        var form = document.querySelector("[data-course-form]");
        if (!form) return;
        var base = Object.assign({ id: "", title: "", category: "", price: 0, lessons: 1, students: 0, rating: 5, reviewCount: 0, status: "active", image: "assets/images/course/datalab-data-analitika.svg", description: "" }, course || {});
        var detail = course && state.courseDetails ? (state.courseDetails[course.id] || seedData.courseDetails[course.id] || {}) : { format: "Əyani / Offline", duration: "8 həftə", schedule: "Həftədə 2 dəfə", location: "Bakı", startDate: "", level: "Başlanğıc", language: "Azərbaycan", seats: "15", instructor: "DatalabAcademy mentor komandası", splineScene: "https://prod.spline.design/kZDDjO5HuC9GJUM2/scene.splinecode", overview: "", outcomes: "", syllabus: "" };
        var data = Object.assign({}, base, detail);
        Object.keys(data).forEach(function (key) {
            if (form.elements[key]) form.elements[key].value = data[key];
        });
        setCourseFormTitle(course ? "Kurs redaktəsi" : "Yeni kurs");
    }

    function setOnlineFormTitle(text) {
        var el = document.querySelector("[data-online-form-title]");
        if (el) el.innerHTML = '<span class="dl-h-ico tone-rose"><i class="feather-edit-2"></i></span>' + esc(text);
    }

    function fillOnlineForm(oc) {
        var form = document.querySelector("[data-online-form]");
        if (!form) return;
        var data = Object.assign({ id: "", title: "", slug: "", category: "", level: "Başlanğıc", price: 0, lessons: 1, students: 0, rating: 5, reviewCount: 0, status: "active", image: "assets/images/course/datalab-data-analitika.svg", description: "" }, oc || {});
        Object.keys(data).forEach(function (key) {
            if (form.elements[key]) form.elements[key].value = data[key];
        });
        setOnlineFormTitle(oc ? "Online təlim redaktəsi" : "Yeni online təlim");
    }

    function fillObjectForm(selector, data) {
        var form = document.querySelector(selector);
        if (!form) return;
        Object.keys(data || {}).forEach(function (key) {
            if (form.elements[key]) form.elements[key].value = data[key] || "";
        });
    }

    function readForm(form) {
        var data = {};
        Array.prototype.slice.call(form.elements).forEach(function (field) {
            if (!field.name) return;
            if (field.type === "checkbox") data[field.name] = field.checked;
            else data[field.name] = field.value;
        });
        return data;
    }

    /* ---------------- navigation ---------------- */

    function switchTab(tab) {
        document.querySelectorAll("[data-admin-tab]").forEach(function (button) {
            button.classList.toggle("is-active", button.getAttribute("data-admin-tab") === tab);
        });
        document.querySelectorAll("[data-admin-view]").forEach(function (view) {
            view.classList.toggle("is-active", view.getAttribute("data-admin-view") === tab);
        });
        setText("#adminPageTitle", titles[tab] || "Admin");
        document.querySelector(".dl-admin-sidebar").classList.remove("is-open");
        // Dinamik yüklənmə tələb edən tablar
        if (tab === "students") fetchStudents();
        if (tab === "online-lessons") renderOnlineLessons();
        if (tab === "home") renderHome();
        if (tab === "ai-quiz") renderAiQuiz();
        if (tab === "ai-chat") renderAiChat();
        if (tab === "contact") renderContact();
        if (tab === "dashboard") setTimeout(renderRevenueChart, 80);
    }

    function renderAll() {
        renderStats();
        renderDashboard();
        renderCourses();
        renderOnlineCourses();
        renderOnlineLessons();
        renderOnlineReviews();
        renderHome();
        renderAiSaved();
        renderAiChat();
        renderContact();
        renderOrders();
        renderLeads();
        renderCurriculum();
        renderBlogList();
        fillObjectForm("[data-content-form]", state.content);
        fillObjectForm("[data-seo-form]", state.seo);
        if (window.feather && typeof window.feather.replace === "function") window.feather.replace();
    }

    /* ---------------- events ---------------- */

    function bindEvents() {
        document.addEventListener("click", function (event) {
            var tabButton = event.target.closest("[data-admin-tab]");
            if (tabButton) switchTab(tabButton.getAttribute("data-admin-tab"));

            var jumpButton = event.target.closest("[data-admin-tab-jump]");
            if (jumpButton) switchTab(jumpButton.getAttribute("data-admin-tab-jump"));

            if (event.target.closest("[data-admin-menu]")) {
                document.querySelector(".dl-admin-sidebar").classList.toggle("is-open");
            }

            /* ---- courses ---- */

            if (event.target.closest("[data-new-course]") || event.target.closest("[data-course-reset]")) {
                fillCourseForm(null);
            }

            var editCourse = event.target.closest("[data-edit-course]");
            if (editCourse) fillCourseForm(courseById(editCourse.getAttribute("data-edit-course")));

            var deleteCourse = event.target.closest("[data-delete-course]");
            if (deleteCourse) adminConfirm("Kursu silmək istədiyinə əminsən? Kurikulumu da silinəcək.", function () {
                var removeId = deleteCourse.getAttribute("data-delete-course");
                state.courses = state.courses.filter(function (course) { return course.id !== removeId; });
                if (state.curriculum) delete state.curriculum[removeId];
                if (state.courseDetails) delete state.courseDetails[removeId];
                if (currentCourseId === removeId) currentCourseId = state.courses.length ? state.courses[0].id : "";
                saveData();
                renderAll();
                toast("Kurs silindi.");
            });

            /* ---- online courses ---- */

            if (event.target.closest("[data-new-online]") || event.target.closest("[data-online-reset]")) {
                fillOnlineForm(null);
            }

            var editOnline = event.target.closest("[data-edit-online]");
            if (editOnline) fillOnlineForm(onlineById(editOnline.getAttribute("data-edit-online")));

            var deleteOnline = event.target.closest("[data-delete-online]");
            if (deleteOnline) adminConfirm("Bu online təlimi silmək istədiyinə əminsən?", function () {
                var removeOnlineId = deleteOnline.getAttribute("data-delete-online");
                state.onlineCourses = (state.onlineCourses || []).filter(function (oc) { return String(oc.id) !== String(removeOnlineId); });
                saveData();
                renderAll();
                fillOnlineForm(null);
                toast("Online təlim silindi.");
            });

            /* ---- online lessons ---- */

            if (event.target.closest("[data-ol-new-section]")) {
                if (!currentOnlineLessonId) { toast("Əvvəlcə online təlim əlavə et."); }
                else fillOlSectionForm(null);
            }
            if (event.target.closest("[data-ol-new-lesson]")) {
                if (!currentOnlineLessonId) { toast("Əvvəlcə online təlim əlavə et."); }
                else if (!olSections(currentOnlineLessonId).length) { toast("Əvvəlcə bölmə yarat."); fillOlSectionForm(null); }
                else fillOlLessonForm(null, olSections(currentOnlineLessonId)[0].id);
            }
            var olAddLesson = event.target.closest("[data-ol-add-lesson]");
            if (olAddLesson) fillOlLessonForm(null, olAddLesson.getAttribute("data-ol-add-lesson"));

            var olEditSec = event.target.closest("[data-ol-edit-section]");
            if (olEditSec) fillOlSectionForm(olFindSection(olEditSec.getAttribute("data-ol-edit-section")));

            var olDelSec = event.target.closest("[data-ol-del-section]");
            if (olDelSec) adminConfirm("Bölmə və içindəki dərslər silinəcək. Davam edilsin?", function () {
                var secId = olDelSec.getAttribute("data-ol-del-section");
                state.onlineCurriculum[currentOnlineLessonId] = olSections(currentOnlineLessonId).filter(function (s) { return s.id !== secId; });
                closeOlEditors();
                persistOnlineLessons("Bölmə silindi.");
            });

            var olEditLes = event.target.closest("[data-ol-edit-lesson]");
            if (olEditLes) fillOlLessonForm(olFindLesson(olEditLes.getAttribute("data-ol-edit-lesson")));

            var olDelLes = event.target.closest("[data-ol-del-lesson]");
            if (olDelLes) adminConfirm("Dərs silinsin?", function () {
                var lesId = olDelLes.getAttribute("data-ol-del-lesson");
                olSections(currentOnlineLessonId).forEach(function (s) { s.lessons = (s.lessons || []).filter(function (ls) { return ls.id !== lesId; }); });
                closeOlEditors();
                persistOnlineLessons("Dərs silindi.");
            });

            if (event.target.closest("[data-ol-editor-close]")) closeOlEditors();

            /* ---- online reviews moderation ---- */
            var olRevApprove = event.target.closest("[data-ol-review-approve]");
            if (olRevApprove) olReviewAction("online-review-update", Number(olRevApprove.getAttribute("data-ol-review-approve")), "approved");
            var olRevHide = event.target.closest("[data-ol-review-hide]");
            if (olRevHide) olReviewAction("online-review-update", Number(olRevHide.getAttribute("data-ol-review-hide")), "hidden");
            var olRevDel = event.target.closest("[data-ol-review-delete]");
            if (olRevDel) adminConfirm("Rəy silinsin?", function () { olReviewAction("online-review-delete", Number(olRevDel.getAttribute("data-ol-review-delete"))); });

            /* ---- homepage CMS lists ---- */
            ["ht", "hc", "hp", "ak"].forEach(function (prefix) {
                if (event.target.closest("[data-new-" + prefix + "]") || event.target.closest("[data-" + prefix + "-reset]")) fillHomeForm(prefix, null);
                var hEdit = event.target.closest("[data-edit-" + prefix + "]");
                if (hEdit) fillHomeForm(prefix, homeById(prefix, hEdit.getAttribute("data-edit-" + prefix)));
                var hDel = event.target.closest("[data-del-" + prefix + "]");
                if (hDel) adminConfirm("Silinsin?", function () {
                    var hid = hDel.getAttribute("data-del-" + prefix);
                    state[HOME_LISTS[prefix].key] = homeList(prefix).filter(function (it) { return String(it.id) !== String(hid); });
                    saveData(); renderHomeList(prefix); fillHomeForm(prefix, null); toast("Silindi.");
                });
            });

            /* ---- AI quiz ---- */
            if (event.target.closest("[data-aiq-save]") && aiGenerated && aiGenerated.length) {
                var aiqTitleEl = document.querySelector("[data-aiq-form] [name=title]");
                var aiqTitle = (aiqTitleEl && aiqTitleEl.value.trim()) || ("Quiz " + ((state.aiQuizzes || []).length + 1));
                if (!Array.isArray(state.aiQuizzes)) state.aiQuizzes = [];
                state.aiQuizzes.unshift({ id: "q-" + Date.now(), title: aiqTitle, questions: aiGenerated });
                saveData();
                aiGenerated = null;
                renderAiQuiz();
                toast("Quiz saxlandı.");
            }
            var aiqDel = event.target.closest("[data-aiq-del-quiz]");
            if (aiqDel) adminConfirm("Quiz silinsin?", function () {
                var qid = aiqDel.getAttribute("data-aiq-del-quiz");
                state.aiQuizzes = (state.aiQuizzes || []).filter(function (q) { return String(q.id) !== String(qid); });
                saveData();
                renderAiSaved();
                toast("Silindi.");
            });

            /* ---- curriculum ---- */

            if (event.target.closest("[data-new-section]")) {
                fillSectionForm(null);
            }

            if (event.target.closest("[data-new-lesson]")) {
                if (!courseSections(currentCourseId).length) {
                    toast("Əvvəlcə bölmə yarat.");
                    fillSectionForm(null);
                } else {
                    fillLessonForm(null, null);
                }
            }

            var secAddLesson = event.target.closest("[data-sec-add-lesson]");
            if (secAddLesson) {
                fillLessonForm(null, secAddLesson.getAttribute("data-sec-add-lesson"));
            }

            var secEdit = event.target.closest("[data-sec-edit]");
            if (secEdit) fillSectionForm(findSection(secEdit.getAttribute("data-sec-edit")));

            var secDel = event.target.closest("[data-sec-del]");
            if (secDel) {
                var delId = secDel.getAttribute("data-sec-del");
                var target = findSection(delId);
                var hasLessons = target && (target.lessons || []).length;
                var doSecDel = function () {
                    state.curriculum[currentCourseId] = courseSections(currentCourseId).filter(function (s) { return s.id !== delId; });
                    closeEditors();
                    persistCurriculum("Bölmə silindi.");
                };
                if (!hasLessons) doSecDel();
                else adminConfirm("Bölmə ilə birlikdə içindəki dərslər də silinəcək. Davam edilsin?", doSecDel);
            }

            var secUp = event.target.closest("[data-sec-up]");
            if (secUp && !secUp.disabled) {
                var ui = Number(secUp.getAttribute("data-idx"));
                moveItem(courseSections(currentCourseId), ui, ui - 1);
                persistCurriculum();
            }

            var secDown = event.target.closest("[data-sec-down]");
            if (secDown && !secDown.disabled) {
                var di = Number(secDown.getAttribute("data-idx"));
                moveItem(courseSections(currentCourseId), di, di + 1);
                persistCurriculum();
            }

            var lesEdit = event.target.closest("[data-les-edit]");
            if (lesEdit) {
                var sec1 = findSection(lesEdit.getAttribute("data-sec"));
                var lesson = sec1 && (sec1.lessons || []).find(function (l) { return l.id === lesEdit.getAttribute("data-les"); });
                if (lesson) fillLessonForm(lesson, sec1.id);
            }

            var lesDel = event.target.closest("[data-les-del]");
            if (lesDel) {
                var sec2 = findSection(lesDel.getAttribute("data-sec"));
                if (sec2) {
                    sec2.lessons = (sec2.lessons || []).filter(function (l) { return l.id !== lesDel.getAttribute("data-les"); });
                    persistCurriculum("Dərs silindi.");
                }
            }

            var lesUp = event.target.closest("[data-les-up]");
            if (lesUp && !lesUp.disabled) {
                var sec3 = findSection(lesUp.getAttribute("data-sec"));
                if (sec3) {
                    var li1 = Number(lesUp.getAttribute("data-idx"));
                    moveItem(sec3.lessons || [], li1, li1 - 1);
                    persistCurriculum();
                }
            }

            var lesDown = event.target.closest("[data-les-down]");
            if (lesDown && !lesDown.disabled) {
                var sec4 = findSection(lesDown.getAttribute("data-sec"));
                if (sec4) {
                    var li2 = Number(lesDown.getAttribute("data-idx"));
                    moveItem(sec4.lessons || [], li2, li2 + 1);
                    persistCurriculum();
                }
            }

            var secToggle = event.target.closest("[data-sec-toggle]");
            if (secToggle && !event.target.closest(".dl-cur-tools button:not(.dl-cur-caret)")) {
                var sec5 = findSection(secToggle.getAttribute("data-sec-toggle"));
                if (sec5) {
                    sec5.collapsed = !sec5.collapsed;
                    renderCurriculum();
                }
            }

            if (event.target.closest("[data-editor-close]")) closeEditors();

            /* ---- pagination + bulk (orders / leads) ---- */

            var pageBtn = event.target.closest("[data-page]");
            if (pageBtn) {
                var pKind = pageBtn.getAttribute("data-page");
                if (pageBtn.getAttribute("data-go") === "prev") page[pKind] = Math.max(1, page[pKind] - 1);
                else page[pKind] = page[pKind] + 1;
                if (pKind === "orders") renderOrders(); else renderLeads();
            }

            var bulkDel = event.target.closest("[data-bulk-del]");
            if (bulkDel) {
                var bdKind = bulkDel.getAttribute("data-bulk-del");
                var bdIds = selectedIds(bdKind);
                if (bdIds.length) adminConfirm(bdIds.length + " qeydi silmək istədiyinə əminsən?", function () {
                    if (bdKind === "orders") state.orders = state.orders.filter(function (o) { return bdIds.indexOf(o.id) === -1; });
                    else state.leads = state.leads.filter(function (l) { return bdIds.indexOf(l.id) === -1; });
                    sel[bdKind] = {};
                    saveData();
                    renderAll();
                    toast(bdIds.length + " qeyd silindi.");
                });
            }

            var bulkCycle = event.target.closest("[data-bulk-cycle]");
            if (bulkCycle) {
                var bcKind = bulkCycle.getAttribute("data-bulk-cycle");
                var bcIds = selectedIds(bcKind);
                var bcStatuses = ["new", "pending", "paid", "cancelled"];
                var bcArr = bcKind === "orders" ? state.orders : state.leads;
                bcArr.forEach(function (it) {
                    if (bcIds.indexOf(it.id) !== -1) it.status = bcStatuses[(bcStatuses.indexOf(it.status) + 1) % bcStatuses.length];
                });
                if (bcIds.length) { saveData(); renderAll(); toast(bcIds.length + " qeydin statusu dəyişdi."); }
            }

            var leadModeBtn = event.target.closest("[data-lead-mode]");
            if (leadModeBtn) {
                var mode = leadModeBtn.getAttribute("data-lead-mode");
                document.querySelectorAll("[data-lead-mode]").forEach(function (b) { b.classList.toggle("is-active", b === leadModeBtn); });
                document.querySelectorAll("[data-lead-view]").forEach(function (v) { v.hidden = v.getAttribute("data-lead-view") !== mode; });
            }

            /* ---- orders / leads ---- */

            var cycleOrder = event.target.closest("[data-cycle-order]");
            if (cycleOrder) {
                var order = state.orders.find(function (item) { return item.id === cycleOrder.getAttribute("data-cycle-order"); });
                var statuses = ["new", "pending", "paid", "cancelled"];
                if (order) order.status = statuses[(statuses.indexOf(order.status) + 1) % statuses.length];
                saveData();
                renderAll();
            }

            var deleteOrder = event.target.closest("[data-delete-order]");
            if (deleteOrder) adminConfirm("Sifarişi silmək istədiyinə əminsən?", function () {
                state.orders = state.orders.filter(function (order) { return order.id !== deleteOrder.getAttribute("data-delete-order"); });
                saveData();
                renderAll();
                toast("Sifariş silindi.");
            });

            if (event.target.closest("[data-new-order]")) {
                var firstCourse = state.courses[0];
                var next = state.orders.length + 1010;
                state.orders.unshift({ id: "DL-" + next, customer: "Yeni müştəri", courseId: firstCourse ? firstCourse.id : "1", phone: "+994 50 000 00 00", status: "new", amount: firstCourse ? firstCourse.price : 0, date: new Date().toISOString().slice(0, 10) });
                saveData();
                renderAll();
            }

            var cycleLead = event.target.closest("[data-cycle-lead]");
            if (cycleLead) {
                var lead = state.leads.find(function (item) { return item.id === cycleLead.getAttribute("data-cycle-lead"); });
                var leadStatuses = ["new", "pending", "paid", "cancelled"];
                if (lead) lead.status = leadStatuses[(leadStatuses.indexOf(lead.status) + 1) % leadStatuses.length];
                saveData();
                renderAll();
            }

            var deleteLead = event.target.closest("[data-delete-lead]");
            if (deleteLead) adminConfirm("Lead-i silmək istədiyinə əminsən?", function () {
                state.leads = state.leads.filter(function (lead) { return lead.id !== deleteLead.getAttribute("data-delete-lead"); });
                saveData();
                renderAll();
                toast("Lead silindi.");
            });

            if (event.target.closest("[data-new-lead]")) {
                state.leads.unshift({ id: "L-" + Date.now(), name: "Yeni lead", source: "Website", interest: "Data Analitika", status: "new", date: new Date().toISOString().slice(0, 10) });
                saveData();
                renderAll();
            }

            /* ---- settings ---- */

            if (event.target.closest("[data-seed-reset]")) {
                state = clone(seedData);
                currentCourseId = state.courses[0].id;
                saveData();
                fillCourseForm(null);
                closeEditors();
                renderAll();
                toast("Demo data bərpa olundu.");
            }

            if (event.target.closest("[data-clear-data]")) {
                localStorage.removeItem(STORAGE_KEY);
                state = clone(seedData);
                currentCourseId = state.courses[0].id;
                fillCourseForm(null);
                closeEditors();
                renderAll();
                toast("Admin datası təmizləndi.");
            }

            if (event.target.closest("[data-admin-export]")) exportData();

            if (event.target.closest("[data-audit-refresh]")) loadAudit();

            if (event.target.closest("[data-admin-logout]")) logout();
            /* ---- blog ---- */

            if (event.target.closest("[data-new-blog]")) { fillBlogForm(null); }

            var editBlog = event.target.closest("[data-edit-blog]");
            if (editBlog) {
                var bId = editBlog.getAttribute("data-edit-blog");
                fillBlogForm((state.blogs || []).find(function(b) { return b.id === bId; }));
            }

            var deleteBlog = event.target.closest("[data-delete-blog]");
            if (deleteBlog) adminConfirm("Blog yazısını silmək istədiyinə əminsən?", function () {
                var dBId = deleteBlog.getAttribute("data-delete-blog");
                state.blogs = (state.blogs || []).filter(function(b) { return b.id !== dBId; });
                saveData(); renderBlogList(); toast("Blog yazısı silindi.");
            });

            if (event.target.closest("[data-blog-editor-close]")) { closeBlogEditor(); }

            /* ---- sifariş modal ---- */

            var viewOrder = event.target.closest("[data-view-order]");
            if (viewOrder) openOrderModal(viewOrder.getAttribute("data-view-order"));

            if (event.target.closest("[data-modal-close]") || (event.target.id === "dlOrderModal" && event.target === event.currentTarget)) {
                closeOrderModal();
            }

            /* ---- lead → sifariş ---- */

            var l2o = event.target.closest("[data-lead-to-order]");
            if (l2o) adminConfirm("Bu lead-i sifarişə çevirmək istəyirsən?", function () {
                leadToOrder(l2o.getAttribute("data-lead-to-order"));
            }, { danger: false, title: "Sifarişə çevir", confirmText: "Bəli, çevir" });

            /* ---- CSV export ---- */

            if (event.target.closest("[data-export-orders-csv]")) {
                exportCSV(state.orders.map(function(o) {
                    return { id: o.id, customer: o.customer, courseId: o.courseId, phone: o.phone, status: o.status, amount: o.amount, date: o.date || "" };
                }), ["id","customer","courseId","phone","status","amount","date"], "orders-" + new Date().toISOString().slice(0,10) + ".csv");
            }

            if (event.target.closest("[data-export-leads-csv]")) {
                exportCSV(state.leads, ["id","name","source","interest","status","date"], "leads-" + new Date().toISOString().slice(0,10) + ".csv");
            }

            if (event.target.closest("[data-export-students-csv]")) {
                exportCSV(students.map(function(s) {
                    return { id: s.id, name: s.name, email: s.email, phone: s.phone || "", provider: s.provider || "local", status: s.status || "active", last_login: s.last_login_at || "" };
                }), ["id","name","email","phone","provider","status","last_login"], "students-" + new Date().toISOString().slice(0,10) + ".csv");
            }

            /* ---- students ---- */

            if (event.target.closest("[data-students-refresh]")) fetchStudents();

            var toggleSt = event.target.closest("[data-toggle-student]");
            if (toggleSt) {
                var stId = toggleSt.getAttribute("data-toggle-student");
                var curStatus = toggleSt.getAttribute("data-student-status");
                var newStatus = curStatus === "blocked" ? "active" : "blocked";
                fetch(API_URL + "?action=update-student", {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-Token": csrfToken },
                    body: JSON.stringify({ id: stId, status: newStatus })
                }).then(function(r) { return r.json(); })
                  .then(function(p) {
                      if (!p.ok) throw new Error(p.message || "Yenilənmədi.");
                      toast(newStatus === "blocked" ? "Tələbə bloklandı." : "Tələbə aktivləşdirildi.");
                      fetchStudents();
                  }).catch(function(e) { toast(e.message); });
            }

            var deleteSt = event.target.closest("[data-delete-student]");
            if (deleteSt) adminConfirm("Tələbəni silmək istədiyinə əminsən? Bu əməliyyat geri qaytarıla bilməz.", function () {
                var dsId = deleteSt.getAttribute("data-delete-student");
                fetch(API_URL + "?action=delete-student", {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-Token": csrfToken },
                    body: JSON.stringify({ id: dsId })
                }).then(function(r) { return r.json(); })
                  .then(function(p) {
                      if (!p.ok) throw new Error(p.message || "Silinmədi.");
                      toast("Tələbə silindi.");
                      fetchStudents();
                  }).catch(function(e) { toast(e.message); });
            });

        });

        document.addEventListener("input", function (event) {
            if (event.target.matches("[data-order-search]")) {
                orderQuery = event.target.value;
                page.orders = 1;
                renderOrders();
            }
            if (event.target.matches("[data-lead-search]")) {
                leadQuery = event.target.value;
                page.leads = 1;
                renderLeads();
            }
            if (event.target.matches("[data-blog-search]")) {
                blogQuery = event.target.value;
                renderBlogList();
            }
            if (event.target.matches("[data-student-search]")) {
                studentQuery = event.target.value;
                renderStudentTable();
            }
        });


        /* ---- kurikulum drag & drop ---- */

        var dragInfo = null;

        document.addEventListener("dragstart", function (event) {
            if (!event.target.closest) return;
            var secHandle = event.target.closest("[data-drag-sec]");
            if (secHandle) {
                dragInfo = { type: "sec", id: secHandle.getAttribute("data-drag-sec") };
                var card = secHandle.closest(".dl-cur-section");
                if (card) card.classList.add("is-dragging");
                if (event.dataTransfer) event.dataTransfer.setData("text/plain", "sec");
                return;
            }
            var lessonRowEl = event.target.closest(".dl-cur-lesson[draggable]");
            if (lessonRowEl) {
                dragInfo = {
                    type: "les",
                    id: lessonRowEl.getAttribute("data-drag-les"),
                    sec: lessonRowEl.getAttribute("data-in-sec")
                };
                lessonRowEl.classList.add("is-dragging");
                if (event.dataTransfer) event.dataTransfer.setData("text/plain", "les");
            }
        });

        document.addEventListener("dragover", function (event) {
            if (!dragInfo || !event.target.closest) return;
            if (event.target.closest(".dl-cur-section")) event.preventDefault();
        });

        document.addEventListener("drop", function (event) {
            if (!dragInfo || !event.target.closest) return;
            event.preventDefault();
            var sections = courseSections(currentCourseId);

            if (dragInfo.type === "sec") {
                var targetCard = event.target.closest("[data-section-card]");
                if (targetCard && targetCard.getAttribute("data-section-card") !== dragInfo.id) {
                    var fromIdx = sections.findIndex(function (s) { return s.id === dragInfo.id; });
                    var toIdx = sections.findIndex(function (s) { return s.id === targetCard.getAttribute("data-section-card"); });
                    if (fromIdx >= 0 && toIdx >= 0) {
                        moveItem(sections, fromIdx, toIdx);
                        persistCurriculum();
                    }
                }
            } else if (dragInfo.type === "les") {
                var srcSec = findSection(dragInfo.sec);
                var dragged = srcSec && (srcSec.lessons || []).find(function (l) { return l.id === dragInfo.id; });
                if (dragged) {
                    var targetRow = event.target.closest(".dl-cur-lesson");
                    var targetSecCard = event.target.closest("[data-section-card]");
                    if (targetRow && targetRow.getAttribute("data-drag-les") !== dragInfo.id) {
                        var destSec = findSection(targetRow.getAttribute("data-in-sec"));
                        if (destSec) {
                            srcSec.lessons = srcSec.lessons.filter(function (l) { return l.id !== dragInfo.id; });
                            destSec.lessons = destSec.lessons || [];
                            var insertAt = destSec.lessons.findIndex(function (l) { return l.id === targetRow.getAttribute("data-drag-les"); });
                            destSec.lessons.splice(insertAt < 0 ? destSec.lessons.length : insertAt, 0, dragged);
                            persistCurriculum();
                        }
                    } else if (targetSecCard) {
                        var destSec2 = findSection(targetSecCard.getAttribute("data-section-card"));
                        if (destSec2 && destSec2.id !== dragInfo.sec) {
                            srcSec.lessons = srcSec.lessons.filter(function (l) { return l.id !== dragInfo.id; });
                            destSec2.lessons = destSec2.lessons || [];
                            destSec2.lessons.push(dragged);
                            persistCurriculum();
                        }
                    }
                }
            }
            dragInfo = null;
        });

        document.addEventListener("dragend", function () {
            dragInfo = null;
            document.querySelectorAll(".is-dragging").forEach(function (el) { el.classList.remove("is-dragging"); });
        });

        /* ---- Lead Kanban drag & drop ---- */
        var kanbanDrag = null;
        document.addEventListener("dragstart", function (event) {
            if (!event.target.closest) return;
            var card = event.target.closest("[data-kanban-card]");
            if (card) {
                kanbanDrag = card.getAttribute("data-kanban-card");
                card.classList.add("is-dragging");
            }
        });
        document.addEventListener("dragover", function (event) {
            if (!kanbanDrag || !event.target.closest) return;
            var col = event.target.closest("[data-kanban-col]");
            if (col) { event.preventDefault(); col.classList.add("is-over"); }
        });
        document.addEventListener("dragleave", function (event) {
            if (!event.target.closest) return;
            var col = event.target.closest("[data-kanban-col]");
            if (col) col.classList.remove("is-over");
        });
        document.addEventListener("drop", function (event) {
            if (!kanbanDrag || !event.target.closest) return;
            var col = event.target.closest("[data-kanban-col]");
            if (!col) return;
            event.preventDefault();
            var newStatus = col.getAttribute("data-kanban-col");
            var lead = state.leads.find(function (l) { return l.id === kanbanDrag; });
            col.classList.remove("is-over");
            kanbanDrag = null;
            if (lead && lead.status !== newStatus) {
                lead.status = newStatus;
                saveData();
                renderAll();
                toast("Lead köçürüldü: " + newStatus);
            }
        });
        document.addEventListener("dragend", function () {
            kanbanDrag = null;
            document.querySelectorAll(".dl-kan-col.is-over").forEach(function (el) { el.classList.remove("is-over"); });
        });

        document.addEventListener("change", function (event) {
            if (event.target.matches("[data-order-filter]")) {
                orderFilter = event.target.value;
                page.orders = 1;
                renderOrders();
            }
            if (event.target.matches("[data-lead-filter]")) {
                leadFilter = event.target.value;
                page.leads = 1;
                renderLeads();
            }
            if (event.target.matches("[data-sel]")) {
                var selKind = event.target.getAttribute("data-sel");
                sel[selKind][event.target.getAttribute("data-id")] = event.target.checked;
                if (selKind === "orders") renderOrders(); else renderLeads();
            }
            if (event.target.matches("[data-selall]")) {
                var allKind = event.target.getAttribute("data-selall");
                var checked = event.target.checked;
                pageRowsCache[allKind].forEach(function (r) { sel[allKind][r.id] = checked; });
                if (allKind === "orders") renderOrders(); else renderLeads();
            }
            if (event.target.matches("[data-blog-filter]")) {
                blogFilter = event.target.value;
                renderBlogList();
            }

            if (event.target.matches("[data-image-upload]")) {
                var imgFile = event.target.files && event.target.files[0];
                if (!imgFile) return;
                var fd = new FormData();
                fd.append("image", imgFile);
                fetch(API_URL + "?action=upload-image", {
                    method: "POST",
                    headers: { "X-CSRF-Token": csrfToken },
                    body: fd
                })
                    .then(function (response) { return response.json(); })
                    .then(function (payload) {
                        if (!payload.ok) throw new Error(payload.message || "Yükləmə alınmadı.");
                        var courseForm = document.querySelector("[data-course-form]");
                        if (courseForm) courseForm.elements.image.value = payload.path;
                        toast("Şəkil yükləndi: " + payload.path);
                    })
                    .catch(function (err) { toast(err.message); });
                event.target.value = "";
            }

            if (event.target.matches("[data-online-image-upload]")) {
                var onFile = event.target.files && event.target.files[0];
                if (!onFile) return;
                var ofd = new FormData();
                ofd.append("image", onFile);
                fetch(API_URL + "?action=upload-image", {
                    method: "POST",
                    headers: { "X-CSRF-Token": csrfToken },
                    body: ofd
                })
                    .then(function (response) { return response.json(); })
                    .then(function (payload) {
                        if (!payload.ok) throw new Error(payload.message || "Yükləmə alınmadı.");
                        var onlineForm = document.querySelector("[data-online-form]");
                        if (onlineForm) onlineForm.elements.image.value = payload.path;
                        toast("Şəkil yükləndi: " + payload.path);
                    })
                    .catch(function (err) { toast(err.message); });
                event.target.value = "";
            }

            if (event.target.matches("[data-cur-course]")) {
                currentCourseId = event.target.value;
                closeEditors();
                renderCurriculum();
                if (window.feather && typeof window.feather.replace === "function") window.feather.replace();
            }

            if (event.target.matches("[data-ol-course]")) {
                currentOnlineLessonId = event.target.value;
                closeOlEditors();
                renderOnlineLessons();
                if (window.feather && typeof window.feather.replace === "function") window.feather.replace();
            }

            ["ht", "hc", "hp"].forEach(function (prefix) {
                if (event.target.matches("[data-" + prefix + "-image-upload]")) {
                    var f = event.target.files && event.target.files[0];
                    if (!f) return;
                    var fd = new FormData(); fd.append("image", f);
                    fetch(API_URL + "?action=upload-image", { method: "POST", headers: { "X-CSRF-Token": csrfToken }, body: fd })
                        .then(function (r) { return r.json(); })
                        .then(function (p) {
                            if (!p.ok) throw new Error(p.message || "Yükləmə alınmadı.");
                            var form = document.querySelector(HOME_LISTS[prefix].form);
                            if (form && form.elements.image) form.elements.image.value = p.path;
                            toast("Şəkil yükləndi: " + p.path);
                        })
                        .catch(function (err) { toast(err.message); });
                    event.target.value = "";
                }
            });

            if (event.target.matches("[data-lesson-type]")) {
                toggleLessonTypeFields(event.target.closest("form"));
            }
        });

        document.addEventListener("submit", function (event) {
            if (event.target.matches("[data-course-form]")) {
                event.preventDefault();
                var data = readForm(event.target);
                data.id = data.id || String(Date.now());
                data.price = Number(data.price || 0);
                data.lessons = Number(data.lessons || 0);
                data.students = Number(data.students || 0);
                data.rating = Math.max(0, Math.min(5, Number(data.rating || 0)));
                data.reviewCount = Math.max(0, Number(data.reviewCount || 0));
                var detailFields = ["format", "duration", "schedule", "location", "startDate", "level", "language", "seats", "instructor", "splineScene", "overview", "outcomes", "syllabus"];
                var detailData = {};
                detailFields.forEach(function (field) { detailData[field] = data[field] || ""; delete data[field]; });
                if (!state.courseDetails) state.courseDetails = {};
                state.courseDetails[data.id] = detailData;
                var index = state.courses.findIndex(function (course) { return course.id === data.id; });
                if (index >= 0) state.courses[index] = data;
                else state.courses.push(data);
                saveData();
                renderAll();
                fillCourseForm(data);
                toast("Kurs yadda saxlandı.");
            }

            if (event.target.matches("[data-online-form]")) {
                event.preventDefault();
                var oData = readForm(event.target);
                oData.id = oData.id || ("on-" + Date.now());
                oData.slug = slugify(oData.slug || oData.title);
                oData.price = Number(oData.price || 0);
                oData.lessons = Number(oData.lessons || 0);
                oData.students = Number(oData.students || 0);
                oData.rating = Math.max(0, Math.min(5, Number(oData.rating || 0)));
                oData.reviewCount = Math.max(0, Number(oData.reviewCount || 0));
                if (!Array.isArray(state.onlineCourses)) state.onlineCourses = [];
                var oIndex = state.onlineCourses.findIndex(function (oc) { return String(oc.id) === String(oData.id); });
                if (oIndex >= 0) state.onlineCourses[oIndex] = oData;
                else state.onlineCourses.push(oData);
                saveData();
                renderAll();
                fillOnlineForm(oData);
                toast("Online təlim yadda saxlandı.");
            }

            if (event.target.matches("[data-ol-section-form]")) {
                event.preventDefault();
                if (!currentOnlineLessonId) { toast("Əvvəlcə online təlim əlavə et."); return; }
                var osData = readForm(event.target);
                var secs = olSections(currentOnlineLessonId);
                if (osData.id) {
                    var ex = secs.find(function (s) { return s.id === osData.id; });
                    if (ex) { ex.title = osData.title; ex.duration = osData.duration; ex.locked = !!osData.locked; }
                } else {
                    secs.push({ id: uid("ols"), title: osData.title, duration: osData.duration || "", locked: !!osData.locked, lessons: [] });
                }
                closeOlEditors();
                persistOnlineLessons("Bölmə yadda saxlandı.");
            }

            if (event.target.matches("[data-ol-lesson-form]")) {
                event.preventDefault();
                if (!currentOnlineLessonId) { toast("Əvvəlcə online təlim əlavə et."); return; }
                var olData = readForm(event.target);
                var targetSecId = olData.sectionId;
                var lessonObj = { id: olData.id || uid("oll"), title: olData.title, type: olData.type || "video", duration: olData.duration || "", link: olData.link || "", preview: !!olData.preview, locked: !!olData.locked };
                // mövcud dərsi bütün bölmələrdən çıxar, sonra hədəf bölməyə əlavə et
                olSections(currentOnlineLessonId).forEach(function (s) { s.lessons = (s.lessons || []).filter(function (ls) { return ls.id !== lessonObj.id; }); });
                var targetSec = olSections(currentOnlineLessonId).find(function (s) { return s.id === targetSecId; }) || olSections(currentOnlineLessonId)[0];
                if (targetSec) { targetSec.lessons = targetSec.lessons || []; targetSec.lessons.push(lessonObj); }
                closeOlEditors();
                persistOnlineLessons("Dərs yadda saxlandı.");
            }

            if (event.target.matches("[data-ol-detail-form]")) {
                event.preventDefault();
                if (!currentOnlineLessonId) { toast("Əvvəlcə online təlim əlavə et."); return; }
                var odData = readForm(event.target);
                function splitLines(v) { return String(v || "").split("\n").map(function (x) { return x.trim(); }).filter(Boolean); }
                if (!state.onlineCourseDetails) state.onlineCourseDetails = {};
                state.onlineCourseDetails[currentOnlineLessonId] = {
                    subtitle: odData.subtitle || "", instructor: odData.instructor || "", date: odData.date || "",
                    previewVideo: odData.previewVideo || "", oldPrice: odData.oldPrice || "", discountText: odData.discountText || "",
                    startDate: odData.startDate || "", registration: odData.registration || "", enrolled: odData.enrolled || "", language: odData.language || "",
                    quizzes: odData.quizzes || "", certificate: odData.certificate || "", passPercentage: odData.passPercentage || "",
                    overview: odData.overview || "", overviewMore: odData.overviewMore || "",
                    outcomes: splitLines(odData.outcomes), requirements: splitLines(odData.requirements), descriptionPoints: splitLines(odData.descriptionPoints),
                    videoIntro: odData.videoIntro === "1"
                };
                saveData();
                toast("Səhifə məlumatları yadda saxlandı.");
            }

            ["ht", "hc", "hp", "ak"].forEach(function (prefix) {
                if (event.target.matches(HOME_LISTS[prefix].form)) { event.preventDefault(); submitHomeForm(prefix, event.target); }
            });
            if (event.target.matches("[data-hs-form]")) { event.preventDefault(); submitHsForm(event.target); }
            if (event.target.matches("[data-contact-form]")) { event.preventDefault(); submitContactForm(event.target); }

            if (event.target.matches("[data-aiq-form]")) {
                event.preventDefault();
                var aiqForm = event.target;
                var aiqText = aiqForm.elements.text.value.trim();
                var aiqCount = Math.max(1, Math.min(10, Number(aiqForm.elements.count.value) || 5));
                var aiqMsg = document.querySelector("[data-aiq-msg]");
                var aiqGenBtn = aiqForm.querySelector("[data-aiq-generate]");
                if (aiqText.length < 30) { if (aiqMsg) { aiqMsg.style.color = "#dc2626"; aiqMsg.textContent = "Mətn çox qısadır (ən azı 30 simvol)."; } return; }
                if (aiqMsg) { aiqMsg.style.color = ""; aiqMsg.textContent = "AI suallar hazırlayır, bir az gözlə..."; }
                if (aiqGenBtn) aiqGenBtn.disabled = true;
                fetch(API_URL.replace("admin.php", "ai-quiz.php"), {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-Token": csrfToken },
                    body: JSON.stringify({ text: aiqText, count: aiqCount })
                })
                    .then(function (r) { return r.json(); })
                    .then(function (p) {
                        if (!p.ok) throw new Error(p.message || "Xəta.");
                        aiGenerated = p.questions;
                        renderAiResult();
                        if (aiqMsg) { aiqMsg.style.color = "#16a34a"; aiqMsg.textContent = p.questions.length + " sual hazırlandı. İstəsən 'Saxla' düyməsi ilə saxla."; }
                    })
                    .catch(function (err) { if (aiqMsg) { aiqMsg.style.color = "#dc2626"; aiqMsg.textContent = err.message; } })
                    .finally(function () { if (aiqGenBtn) aiqGenBtn.disabled = false; });
            }

            if (event.target.matches("[data-section-form]")) {
                event.preventDefault();
                var sData = readForm(event.target);
                if (sData.id) {
                    var existing = findSection(sData.id);
                    if (existing) {
                        existing.title = sData.title;
                        existing.locked = !!sData.locked;
                    }
                } else {
                    courseSections(currentCourseId).push({
                        id: uid("s"),
                        title: sData.title,
                        locked: !!sData.locked,
                        lessons: []
                    });
                }
                closeEditors();
                persistCurriculum("Bölmə yadda saxlandı.");
            }

            if (event.target.matches("[data-lesson-form]")) {
                event.preventDefault();
                var lData = readForm(event.target);
                var section = findSection(lData.sectionId);
                if (!section) {
                    toast("Bölmə tapılmadı.");
                    return;
                }
                var lessonObj = {
                    id: lData.id || uid("l"),
                    title: lData.title,
                    type: lData.type,
                    duration: lData.duration,
                    source: lData.source || "auto",
                    link: lData.link || "",
                    note: lData.note || "",
                    preview: !!lData.preview
                };
                if (lData.id) {
                    // remove old copy from whichever section holds it (supports moving between sections)
                    courseSections(currentCourseId).forEach(function (s) {
                        s.lessons = (s.lessons || []).filter(function (l) { return l.id !== lData.id; });
                    });
                }
                section.lessons = section.lessons || [];
                section.lessons.push(lessonObj);
                closeEditors();
                persistCurriculum("Dərs yadda saxlandı.");
            }

            if (event.target.matches("[data-content-form]")) {
                event.preventDefault();
                state.content = readForm(event.target);
                saveData();
                toast("Kontent yadda saxlandı.");
            }

            if (event.target.matches("[data-seo-form]")) {
                event.preventDefault();
                state.seo = readForm(event.target);
                saveData();
                toast("SEO ayarları yadda saxlandı.");
            }

            if (event.target.matches("[data-password-form]")) {
                event.preventDefault();
                var pForm = event.target;
                var pData = readForm(pForm);
                if (pData.newPassword !== pData.newPassword2) {
                    toast("Yeni şifrələr üst-üstə düşmür.");
                    return;
                }
                if (String(pData.newPassword).length < 8) {
                    toast("Yeni şifrə ən azı 8 simvol olmalıdır.");
                    return;
                }
                // Güclü şifrə tələbi: böyük hərf, kişik hərf, rəqəm
                if (!/[A-Z]/.test(pData.newPassword) || !/[a-z]/.test(pData.newPassword) || !/[0-9]/.test(pData.newPassword)) {
                    toast("Şifrə: ən azı bir böyük hərf, bir kişik hərf və bir rəqəm lazimdir.");
                    return;
                }
                fetch(API_URL + "?action=change-password", {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-Token": csrfToken },
                    body: JSON.stringify({ oldPassword: pData.oldPassword, newPassword: pData.newPassword, confirmPassword: pData.newPassword })
                })
                    .then(function (response) { return response.json(); })
                    .then(function (payload) {
                        if (!payload.ok) throw new Error(payload.message || "Şifrə yenilənmədi.");
                        pForm.reset();
                        // Məcburi dəyişim banner-ini gizlət
                        var banner = document.querySelector("[data-must-change-pw]");
                        if (banner) banner.hidden = true;
                        toast("Şifrə uğurla yeniləndi.");
                        loadAudit();
                    })
                    .catch(function (err) { toast(err.message); });
            }

            if (event.target.matches("[data-blog-form]")) {
                event.preventDefault();
                var bData = readForm(event.target);
                bData.id = bData.id || ("blog-" + Date.now());
                bData.slug = bData.slug || bData.id;
                bData.sortOrder = (state.blogs || []).length;
                if (!state.blogs) state.blogs = [];
                var bIdx = state.blogs.findIndex(function(b) { return b.id === bData.id; });
                if (bIdx >= 0) state.blogs[bIdx] = bData;
                else state.blogs.push(bData);
                saveData();
                renderBlogList();
                closeBlogEditor();
                toast("Blog yazısı yadda saxlandı.");
            }
        });

        var importInput = document.querySelector("[data-admin-import]");
        if (importInput) {
            importInput.addEventListener("change", function () {
                var file = importInput.files && importInput.files[0];
                if (!file) return;
                var reader = new FileReader();
                reader.onload = function () {
                    try {
                        var data = JSON.parse(reader.result);
                        if (!data.courses || !data.orders || !data.leads) throw new Error("Bad file");
                        state = normalizeState(data);
                        currentCourseId = state.courses.length ? state.courses[0].id : "";
                        saveData();
                        renderAll();
                        toast("Import tamamlandı.");
                    } catch (err) {
                        toast("JSON faylı düzgün deyil.");
                    }
                };
                reader.readAsText(file);
                importInput.value = "";
            });
        }
    }

    function exportData() {
        var blob = new Blob([JSON.stringify(state, null, 2)], { type: "application/json" });
        var url = URL.createObjectURL(blob);
        var a = document.createElement("a");
        a.href = url;
        a.download = "datalab-backup-" + new Date().toISOString().slice(0, 10) + ".json";
        a.click();
        URL.revokeObjectURL(url);
    }

    /* ================================================================
       CSV EXPORT
    ================================================================ */
    function exportCSV(rows, headers, filename) {
        var lines = [headers.join(",")];
        rows.forEach(function(row) {
            lines.push(headers.map(function(h) {
                var val = String(row[h] || "").replace(/"/g, '""');
                return '"' + val + '"';
            }).join(","));
        });
        var blob = new Blob(["\uFEFF" + lines.join("\r\n")], { type: "text/csv;charset=utf-8;" });
        var url = URL.createObjectURL(blob);
        var a = document.createElement("a");
        a.href = url; a.download = filename; a.click();
        URL.revokeObjectURL(url);
    }

    /* ================================================================
       SIFARIŞ MODAL
    ================================================================ */
    function openOrderModal(orderId) {
        var order = state.orders.find(function(o) { return o.id === orderId; });
        if (!order) return;
        var course = courseById(order.courseId);
        var modal = document.getElementById("dlOrderModal");
        if (!modal) return;
        var wa = order.phone ? "https://wa.me/" + order.phone.replace(/\D/g, "") : "";
        modal.querySelector("[data-modal-order-id]").textContent = order.id;
        modal.querySelector("[data-modal-customer]").textContent = order.customer || "—";
        modal.querySelector("[data-modal-course]").textContent = course ? course.title : "—";
        modal.querySelector("[data-modal-phone]").textContent = order.phone || "—";
        modal.querySelector("[data-modal-amount]").textContent = money(order.amount);
        modal.querySelector("[data-modal-date]").textContent = order.date || "—";
        modal.querySelector("[data-modal-status]").innerHTML = status(order.status);
        var waBtn = modal.querySelector("[data-modal-wa]");
        if (waBtn) { waBtn.href = wa || "#"; waBtn.style.display = wa ? "" : "none"; }
        modal.removeAttribute("hidden");
        modal.classList.add("is-open");
        document.body.style.overflow = "hidden";
    }

    function closeOrderModal() {
        var modal = document.getElementById("dlOrderModal");
        if (modal) { modal.hidden = true; modal.classList.remove("is-open"); }
        document.body.style.overflow = "";
    }

    /* ================================================================
       LEAD → SİFARİŞ ÇEVİRMƏ
    ================================================================ */
    function leadToOrder(leadId) {
        var lead = state.leads.find(function(l) { return l.id === leadId; });
        if (!lead) return;
        var firstCourse = state.courses.find(function(c) {
            return c.status === "active" && String(c.title).toLowerCase().indexOf(String(lead.interest || "").toLowerCase()) !== -1;
        }) || state.courses[0];
        var newOrder = {
            id: "DL-" + (1000 + state.orders.length + 1),
            customer: lead.name,
            courseId: firstCourse ? firstCourse.id : "",
            phone: lead.phone || "",
            status: "new",
            amount: firstCourse ? firstCourse.price : 0,
            date: new Date().toISOString().slice(0, 10)
        };
        state.orders.unshift(newOrder);
        saveData();
        renderAll();
        toast(lead.name + " sifarişlərə əlavə edildi!");
        var tabBtn = document.querySelector("[data-admin-tab='orders']");
        if (tabBtn) tabBtn.click();
    }

    /* ================================================================
       BLOG İDARƏETMƏ
    ================================================================ */
    var blogQuery = "";
    var blogFilter = "all";
    var currentBlog = null;

    function renderBlogList() {
        var tbody = document.querySelector("[data-blog-table]");
        if (!tbody) return;
        var blogs = state.blogs || [];
        var visible = blogs.filter(function(b) {
            if (blogFilter !== "all" && (b.status || "active") !== blogFilter) return false;
            return matchesQuery(blogQuery, [b.titleAz, b.titleEn, b.category, b.slug]);
        });
        if (!visible.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="dl-admin-empty-cell">Blog yazısı tapılmadı.</td></tr>';
            return;
        }
        tbody.innerHTML = visible.map(function(b) {
            var img = b.image ? '<img src="' + esc(b.image) + '" style="width:40px;height:40px;object-fit:cover;border-radius:6px;" alt="">' : '<span style="color:var(--dl-muted)">—</span>';
            return [
                "<tr>",
                "<td>" + img + "</td>",
                '<td><strong>' + esc(b.titleAz || b.titleEn || "—") + "</strong><br><small>" + esc(b.category || "") + "</small></td>",
                "<td>" + esc(b.publishedDate || "—") + "</td>",
                "<td>" + status(b.status || "active") + "</td>",
                '<td><div class="dl-admin-row-actions">',
                '<button type="button" data-edit-blog="' + esc(b.id) + '" title="Redaktə"><i class="feather-edit-2"></i></button>',
                '<button type="button" data-delete-blog="' + esc(b.id) + '" title="Sil"><i class="feather-trash-2"></i></button>',
                "</div></td>",
                "</tr>"
            ].join("");
        }).join("");
        if (window.feather && typeof window.feather.replace === "function") window.feather.replace();
    }

    function fillBlogForm(blog) {
        currentBlog = blog || null;
        var panel = document.querySelector("[data-blog-editor]");
        if (!panel) return;
        panel.hidden = false;
        var form = panel.querySelector("[data-blog-form]");
        if (!form) return;
        var defaults = { id:"", slug:"", titleAz:"", titleEn:"", excerptAz:"", excerptEn:"", contentAz:"", contentEn:"", category:"", image:"", author:"DatalabAcademy", readTimeAz:"", readTimeEn:"", publishedDate: new Date().toISOString().slice(0,10), status:"active", seoTitle:"", seoDescription:"", seoKeywords:"" };
        var data = Object.assign({}, defaults, blog || {});
        Object.keys(data).forEach(function(k) {
            if (form.elements[k]) form.elements[k].value = data[k] || "";
        });
        var title = panel.querySelector("[data-blog-form-title]");
        if (title) title.textContent = blog ? "Blog redaktəsi" : "Yeni blog yazısı";
        form.querySelector("[name=id]").value = blog ? blog.id : "";
        panel.scrollIntoView({ behavior: "smooth", block: "start" });
    }

    function closeBlogEditor() {
        var panel = document.querySelector("[data-blog-editor]");
        if (panel) panel.hidden = true;
        currentBlog = null;
    }

    /* ================================================================
       TƏLƏBƏ PANELİ
    ================================================================ */
    var studentQuery = "";
    var students = [];

    function fetchStudents() {
        var tbody = document.querySelector("[data-students-table]");
        if (!tbody) return;
        tbody.innerHTML = '<tr><td colspan="7" class="dl-admin-empty-cell">Yüklənir...</td></tr>';
        fetch(API_URL + "?action=students")
            .then(function(r) { return r.json(); })
            .then(function(payload) {
                if (!payload.ok) throw new Error(payload.message || "Yüklənmədi.");
                students = payload.data || [];
                renderStudentTable();
            })
            .catch(function(err) {
                if (tbody) tbody.innerHTML = '<tr><td colspan="7" class="dl-admin-empty-cell">Tələbə məlumatları yüklənmədi: ' + esc(err.message) + '</td></tr>';
            });
    }

    function renderStudentTable() {
        var tbody = document.querySelector("[data-students-table]");
        if (!tbody) return;
        var visible = students.filter(function(s) {
            return matchesQuery(studentQuery, [s.name, s.email, s.phone, s.provider]);
        });
        setText("[data-student-count]", students.length);
        if (!visible.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="dl-admin-empty-cell">Tələbə tapılmadı.</td></tr>';
            return;
        }
        tbody.innerHTML = visible.map(function(s) {
            var avatar = s.avatar_url
                ? '<img src="' + esc(s.avatar_url) + '" class="dl-student-avatar" alt="">'
                : '<span class="dl-student-avatar dl-student-initials">' + esc((s.name || "?")[0].toUpperCase()) + "</span>";
            var providerIcon = s.provider === "google" ? '<i class="feather-chrome" title="Google"></i>' : '<i class="feather-user" title="Local"></i>';
            var isBlocked = s.status === "blocked";
            return [
                "<tr>",
                "<td>" + avatar + "</td>",
                "<td><strong>" + esc(s.name) + "</strong><br><small>" + esc(s.email) + "</small></td>",
                "<td>" + esc(s.phone || "—") + "</td>",
                "<td>" + providerIcon + " " + esc(s.provider || "local") + "</td>",
                "<td>" + status(s.status || "active") + "</td>",
                "<td>" + esc(s.last_login_at ? s.last_login_at.slice(0,16) : "—") + "</td>",
                '<td><div class="dl-admin-row-actions">',
                '<button type="button" data-toggle-student="' + esc(String(s.id)) + '" data-student-status="' + esc(s.status || "active") + '" title="' + (isBlocked ? "Aktivləşdir" : "Blokla") + '"><i class="' + (isBlocked ? "feather-user-check" : "feather-user-x") + '"></i></button>',
                '<button type="button" data-delete-student="' + esc(String(s.id)) + '" title="Sil"><i class="feather-trash-2"></i></button>',
                "</div></td>",
                "</tr>"
            ].join("");
        }).join("");
        if (window.feather && typeof window.feather.replace === "function") window.feather.replace();
    }

    /* ================================================================
       AUTO-POLLING (hər 90 saniyə)
    ================================================================ */
    var pollTimer = null;

    function startPolling() {
        if (pollTimer) return;
        pollTimer = setInterval(function() {
            if (document.hidden) return;   // tab görünmürsə keç
            fetch(API_URL + "?action=state")
                .then(function(r) { return r.json(); })
                .then(function(payload) {
                    if (!payload.ok || !payload.data) return;
                    var newOrders = (payload.data.orders || []).length;
                    var newLeads  = (payload.data.leads  || []).length;
                    var oldOrders = state.orders.length;
                    var oldLeads  = state.leads.length;
                    // Yeni sifariş/lead gəldikdə state yenilə + badge göstər
                    if (newOrders !== oldOrders || newLeads !== oldLeads) {
                        state = normalizeState(payload.data);
                        renderStats();
                        renderDashboard();
                        renderOrders();
                        renderLeads();
                        if (newOrders > oldOrders) {
                            toast("🔔 Yeni sifariş daxil oldu!");
                            if (Notification && Notification.permission === "granted") {
                                new Notification("DatalabAcademy Admin", { body: "Yeni sifariş!", icon: "assets/images/favicon.svg" });
                            }
                        }
                        if (newLeads > oldLeads) toast("🔔 Yeni lead daxil oldu!");
                    }
                })
                .catch(function() {}); // sessiz fail
        }, 90000);
    }

    function requestNotifPermission() {
        if (typeof Notification !== "undefined" && Notification.permission === "default") {
            Notification.requestPermission().catch(function() {});
        }
    }

    /* ---------------- auth ---------------- */

    function showLogin(message) {
        var overlay = document.querySelector("[data-login-overlay]");
        if (overlay) overlay.hidden = false;
        var error = document.querySelector("[data-login-error]");
        if (error) {
            error.hidden = !message;
            error.textContent = message || "";
        }
    }

    function hideLogin() {
        var overlay = document.querySelector("[data-login-overlay]");
        if (overlay) overlay.hidden = true;
    }

    function setUserChip(email) {
        var chip = document.querySelector("[data-admin-user]");
        if (!chip) return;
        chip.title = email || "Admin";
        chip.textContent = email ? email.slice(0, 2).toUpperCase() : "DA";
    }

    function bindLogin() {
        var form = document.querySelector("[data-login-form]");
        if (form) {
            form.addEventListener("submit", function (event) {
                event.preventDefault();
                var button = form.querySelector(".dl-login-submit");
                if (button) button.disabled = true;
                fetch(API_URL + "?action=login", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        email: form.elements.email.value,
                        password: form.elements.password.value
                    })
                })
                    .then(function (response) { return response.json(); })
                    .then(function (payload) {
                        if (!payload.ok) throw new Error(payload.message || "Giriş alınmadı.");
                        csrfToken = payload.csrf || "";
                        setUserChip(payload.user && payload.user.email);
                        form.reset();
                        hideLogin();
                        startData();
                        if (payload.mustChangePassword) {
                            // Adminə məcburi şifrə dəyişim bildirişi
                            setTimeout(function () {
                                // Settings tabına keç
                                var settingsBtn = document.querySelector("[data-admin-tab='settings']");
                                if (settingsBtn) settingsBtn.click();
                                // Xəbərdarlıq göstər
                                var banner = document.querySelector("[data-must-change-pw]");
                                if (banner) banner.hidden = false;
                                toast("⚠️ Təhlükəsizlik: Default şifrəni dərhal dəyişdirin!");
                            }, 600);
                        } else {
                            toast("Xoş gəldin!");
                        }
                    })
                    .catch(function (err) {
                        showLogin(err.message === "Failed to fetch" ? "Server əlçatan deyil." : err.message);
                    })
                    .finally(function () {
                        if (button) button.disabled = false;
                    });
            });
        }

        var localBtn = document.querySelector("[data-login-local]");
        if (localBtn) {
            localBtn.addEventListener("click", function () {
                hideLogin();
                startLocal();
            });
        }
    }

    function logout() {
        fetch(API_URL + "?action=logout", { method: "POST" }).catch(function () {});
        csrfToken = "";
        setUserChip("");
        showLogin("");
    }

    var AUDIT_ICONS = {
        "login": "feather-log-in",
        "logout": "feather-log-out",
        "save-state": "feather-save",
        "change-password": "feather-key",
        "upload-image": "feather-image"
    };

    var AUDIT_LABELS = {
        "login": "Giriş",
        "logout": "Çıxış",
        "save-state": "Data yadda saxlandı",
        "change-password": "Şifrə dəyişdirildi",
        "upload-image": "Şəkil yükləndi"
    };

    function loadAudit() {
        var box = document.querySelector("[data-audit-list]");
        if (!box) return;
        fetch(API_URL + "?action=audit")
            .then(function (response) { return response.json(); })
            .then(function (payload) {
                if (!payload.ok || !Array.isArray(payload.data)) throw new Error("audit failed");
                if (!payload.data.length) {
                    box.innerHTML = '<p class="dl-admin-note">Hələ heç bir əməliyyat qeydə alınmayıb.</p>';
                    return;
                }
                box.innerHTML = payload.data.map(function (row) {
                    return [
                        '<div class="dl-audit-row">',
                        '<i class="' + (AUDIT_ICONS[row.action] || "feather-activity") + '"></i>',
                        '<div class="dl-audit-info"><strong>' + esc(AUDIT_LABELS[row.action] || row.action) + "</strong>",
                        "<small>" + esc(row.email) + (row.detail ? " · " + esc(row.detail) : "") + (row.ip ? " · " + esc(row.ip) : "") + "</small></div>",
                        "<time>" + esc(row.created_at) + "</time>",
                        "</div>"
                    ].join("");
                }).join("");
            })
            .catch(function () {
                box.innerHTML = '<p class="dl-admin-note">Əməliyyat tarixçəsi yüklənmədi (local rejimdə mövcud deyil).</p>';
            });
    }

    /* ---------------- boot ---------------- */

    function startLocal() {
        state = loadData();
        currentCourseId = state.courses.length ? state.courses[0].id : "";
        fillCourseForm(state.courses[0]);
        fillOnlineForm((state.onlineCourses || [])[0]);
        setDbState(false);
        renderAll();
        toast("Local rejim aktivdir. Data MySQL-ə yazılmır!");
    }

    function startData() {
        loadDataFromApi()
            .then(function (data) {
                if (!data.courses.length && !data.orders.length && !data.leads.length) {
                    // fresh/empty database: seed it once
                    state = clone(seedData);
                    saveData();
                } else {
                    state = data;
                }
                currentCourseId = state.courses.length ? state.courses[0].id : "";
                localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
                fillCourseForm(state.courses[0]);
                fillOnlineForm((state.onlineCourses || [])[0]);
                setDbState(true);
                renderAll();
                loadAudit();
                startPolling();
                requestNotifPermission();
                toast("Database qoşuldu.");
            })
            .catch(function (err) {
                console.error(err);
                startLocal();
            });
    }

    function boot() {
        bindEvents();
        bindLogin();

        fetch(API_URL + "?action=session")
            .then(function (response) {
                return response.json().then(function (payload) {
                    return { status: response.status, payload: payload };
                });
            })
            .then(function (result) {
                if (result.payload && result.payload.ok) {
                    csrfToken = result.payload.csrf || "";
                    setUserChip(result.payload.user && result.payload.user.email);
                    hideLogin();
                    startData();
                    if (result.payload.mustChangePassword) {
                        setTimeout(function () {
                            var settingsBtn = document.querySelector("[data-admin-tab='settings']");
                            if (settingsBtn) settingsBtn.click();
                            var banner = document.querySelector("[data-must-change-pw]");
                            if (banner) banner.hidden = false;
                            toast("⚠️ Təhlükəsizlik: Default şifrəni dərhal dəyişdirin!");
                        }, 600);
                    }
                } else {
                    showLogin("");
                }
            })
            .catch(function () {
                // API unreachable (no PHP server): allow local demo entry
                showLogin("Server əlçatan deyil. PHP və MySQL-in işlədiyini yoxla.");
                var localBtn = document.querySelector("[data-login-local]");
                if (localBtn) localBtn.hidden = false;
            });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", boot);
    } else {
        boot();
    }
}());
