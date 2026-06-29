(function () {
    "use strict";

    var defaultCourses = [
        { id: "1", titleAz: "Data Analitika", titleEn: "Data Analytics", img: "assets/images/course/datalab-data-analitika.svg", price: 180, icon: "feather-bar-chart-2" },
        { id: "2", titleAz: "SQL Developer", titleEn: "SQL Developer", img: "assets/images/course/datalab-sql-developer.svg", price: 220, icon: "feather-database" },
        { id: "3", titleAz: "Excel", titleEn: "Excel", img: "assets/images/course/datalab-excel.svg", price: 40, icon: "feather-grid" },
        { id: "4", titleAz: "AI ilə Effektiv İş", titleEn: "Working Effectively with AI", img: "assets/images/course/datalab-ai.svg", price: 150, icon: "feather-cpu" },
        { id: "5", titleAz: "İnteraktiv AI Təcrübəsi", titleEn: "Interactive AI Experience", img: "assets/images/course/datalab-ai.svg", price: 240, icon: "feather-zap" }
    ];
    var defaultOnlineCourses = [
        { id: "1", titleAz: "Data Analitika Online", slug: "data-analitika-online", icon: "feather-bar-chart-2" },
        { id: "2", titleAz: "SQL Praktiki Dərslər", slug: "sql-praktiki-dersler", icon: "feather-database" },
        { id: "3", titleAz: "Excel Dashboard Təlimi", slug: "excel-dashboard-telimi", icon: "feather-grid" },
        { id: "4", titleAz: "AI Alətləri ilə Produktivlik", slug: "ai-aletleri-produktivlik", icon: "feather-cpu" }
    ];
    var courses = defaultCourses;
    var onlineCourses = defaultOnlineCourses;
    var CART_KEY = "dlCart";
    var SITE_STATE_KEY = "dlSiteState";

    function mapAdminOnlineCourses(list) {
        if (!Array.isArray(list) || !list.length) return defaultOnlineCourses;
        return list.map(function (item) {
            return {
                id: String(item.id || item.slug || ""),
                titleAz: item.title || "Online təlim",
                slug: item.slug || String(item.id || ""),
                icon: "feather-play-circle"
            };
        });
    }

    function renderCourseMenuItems() {
        var courseItems = courses.map(function (course) {
            return '<li><a href="course-details-4.php?id=' + encodeURIComponent(course.id) + '"><i class="' + course.icon + '"></i> ' + course.titleAz + '</a></li>';
        }).join("");
        var onlineItems = onlineCourses.map(function (course) {
            var target = course.slug ? course.slug : course.id;
            return '<li><a href="course-details-3.php?online=' + encodeURIComponent(target) + '"><i class="' + (course.icon || 'feather-play-circle') + '"></i> ' + course.titleAz + '</a></li>';
        }).join("");
        return courseItems + '<li class="has-dropdown has-menu-child-item dl-online-menu-item"><a href="course-filter-two-toggle.php"><i class="feather-monitor"></i> <span data-i18n="nav_online_training">Online təlimlər</span> <i class="feather-chevron-right dl-online-submenu-arrow"></i></a><ul class="submenu dl-online-submenu">' + onlineItems + '</ul></li>';
    }

    function mapAdminCourses(list) {
        return list.map(function (item) {
            var def = defaultCourses.filter(function (d) { return d.id === String(item.id); })[0];
            return {
                id: String(item.id),
                titleAz: item.title || (def && def.titleAz) || "",
                titleEn: (def && def.titleEn) || item.title || "",
                img: item.image || (def && def.img) || "",
                price: Number(item.price) || 0,
                icon: (def && def.icon) || "feather-book-open"
            };
        });
    }

    // 1) apply cached admin data instantly (sync, before any rendering)
    try {
        var cachedSiteState = JSON.parse(localStorage.getItem(SITE_STATE_KEY) || "null");
        if (cachedSiteState && Array.isArray(cachedSiteState.courses) && cachedSiteState.courses.length) {
            courses = mapAdminCourses(cachedSiteState.courses);
        }
        if (cachedSiteState && Array.isArray(cachedSiteState.onlineCourses) && cachedSiteState.onlineCourses.length) {
            onlineCourses = mapAdminOnlineCourses(cachedSiteState.onlineCourses);
        }
        if (cachedSiteState) window.DL_SITE_STATE = cachedSiteState;
    } catch (err) {}

    // 2) refresh from database in the background
    if (window.fetch && !window.DL_SITE_FETCHING) {
        window.DL_SITE_FETCHING = true;
        fetch("api/admin.php?action=public")
            .then(function (response) { return response.json(); })
            .then(function (payload) {
                if (!payload.ok || !payload.data) return;
                if (Array.isArray(payload.data.onlineCourses)) onlineCourses = mapAdminOnlineCourses(payload.data.onlineCourses);
                localStorage.setItem(SITE_STATE_KEY, JSON.stringify(payload.data));
                window.DL_SITE_STATE = payload.data;
                if (Array.isArray(payload.data.courses) && payload.data.courses.length) {
                    courses = mapAdminCourses(payload.data.courses);
                    if (document.querySelector("[data-datalab-header]")) {
                        includeSharedBlocks();
                        applyLang(localStorage.getItem(LANG_KEY) || "az");
                        if (window.feather && typeof window.feather.replace === "function") window.feather.replace();
                    }
                }
                document.dispatchEvent(new CustomEvent("dl:siteState", { detail: payload.data }));
            })
            .catch(function () {});
    }

    var dict = {
        az: {
            lang_az: "Azərbaycan",
            lang_en: "English",
            search_placeholder: "Kurs axtarın",
            cart: "Səbət",
            nav_home: "Ana səhifə",
            nav_courses: "Kurslar",
            nav_online_training: "Online təlimlər",
            nav_about: "Haqqımızda",
            nav_blog: "Bloq",
            nav_contact: "Əlaqə",
            join_now: "Qoşul",
            cart_title: "Səbət",
            cart_breadcrumb: "Səbət",
            cart_table_image: "Şəkil",
            cart_table_course: "Kurs",
            cart_table_price: "Qiymət",
            cart_table_quantity: "Say",
            cart_table_total: "Yekun",
            cart_table_remove: "Sil",
            cart_summary: "Səbət xülasəsi",
            subtotal: "Ara yekun",
            grand_total: "Ümumi yekun",
            continue_courses: "Kurslara qayıt",
            checkout: "Ödənişə keç",
            view_cart: "Səbətə bax",
            mini_cart_title: "Səbətiniz",
            cart_empty_title: "Səbət boşdur",
            cart_empty_text: "Karyeranıza uyğun kurs seçmək üçün kurs kataloquna baxın.",
            checkout_details: "Qeydiyyat məlumatları",
            field_first_name: "Ad*",
            field_last_name: "Soyad*",
            field_email: "E-poçt*",
            field_phone: "Telefon*",
            field_note: "Qeyd",
            field_note_placeholder: "Hansı kursla bağlı əlavə sualınız var?",
            payment_method: "Ödəniş üsulu",
            payment_card: "Kartla ödəniş",
            payment_card_desc: "Menecerimiz ödəniş linkini sizə göndərəcək.",
            payment_transfer: "Bank köçürməsi",
            payment_transfer_desc: "Rekvizitlər təsdiqdən sonra paylaşılacaq.",
            accept_terms: "Şərtləri qəbul edirəm",
            place_order: "Sifarişi tamamla",
            order_success: "Sifariş qəbul edildi! Tezliklə əlaqə saxlayacağıq.",
            footer_about_text: "DatalabAcademy praktiki Data Analitika, SQL, Excel və AI təlimləri ilə karyera bacarıqlarınızı inkişaf etdirir.",
            footer_contact_cta: "Bizimlə əlaqə",
            footer_useful_links: "Faydalı linklər",
            footer_our_company: "Kurslarımız",
            footer_link_home: "Ana səhifə",
            footer_link_courses: "Kurslar",
            footer_link_about: "Haqqımızda",
            footer_link_blog: "Bloq",
            footer_link_contact: "Əlaqə",
            get_contact: "Əlaqə məlumatı",
            phone: "Telefon:",
            email: "E-poçt:",
            footer_address_label: "Ünvan:",
            footer_address: "Bakı, Azərbaycan",
            newsletter: "Yeniliklər",
            newsletter_desc: "Yeni kurslar və faydalı materiallar üçün e-poçtunuzu qeyd edin.",
            enter_email: "E-poçtunuzu daxil edin",
            submit_now: "Göndər",
            terms_service: "İstifadə şərtləri",
            privacy_policy: "Məxfilik siyasəti",
            login_register: "Giriş və qeydiyyat",
            copyright_text: "Copyright © 2026 DatalabAcademy. Bütün hüquqlar qorunur.",
            toast: "Təşəkkürlər! Tezliklə əlaqə saxlayacağıq.",
            detail_bc_home: "Ana səhifə",
            detail_bc_category: "Online təlimlər",
            detail_students_enrolled: "Tələbə qeydiyyatdan keçib",
            detail_title: "Online təlimlər: Data, SQL, Excel və AI",
            detail_subtitle: "DatalabAcademy ilə praktiki tapşırıqlar, mentor dəstəyi və real karyera yönümlü proqram.",
            detail_author_prefix: "Təlimçi",
            detail_author: "DatalabAcademy",
            detail_date: "31 May 2026",
            detail_reviews: "(84 Rəy)",
            detail_tab_overview: "İcmal",
            detail_tab_content: "Kurs proqramı",
            detail_tab_details: "Detallar",
            detail_tab_instructor: "Təlimçi",
            detail_tab_review: "Rəylər",
            detail_learn_title: "Nə öyrənəcəksiniz",
            detail_learn_body: "Bu online proqram data analitika, SQL, Excel və AI alətlərini real iş ssenariləri üzərində öyrənmək istəyənlər üçündür. Dərslər praktiki tapşırıqlar, mentor izahları və portfolionuza əlavə edə biləcəyiniz layihələrlə qurulub.",
            detail_point_1: "Məlumatları Excel və SQL ilə təmizləməyi, analiz etməyi və hesabatlaşdırmağı öyrənəcəksiniz.",
            detail_point_2: "Praktiki tapşırıqlarla real biznes suallarına cavab verən dashboard və sorğular hazırlayacaqsınız.",
            detail_point_3: "AI alətlərindən iş prosesini sürətləndirmək üçün düzgün prompt və yoxlama üsulları ilə istifadə edəcəksiniz.",
            detail_point_4: "Kurs sonunda portfolionuza əlavə edilə bilən layihələr üzərində işləyəcəksiniz.",
            detail_point_5: "Canlı dərslər və qeydlər vasitəsilə mövzuları öz tempinizdə təkrar edə biləcəksiniz.",
            detail_point_6: "Mentor dəstəyi ilə çətin mövzuları sual-cavab formatında möhkəmləndirəcəksiniz.",
            detail_point_7: "Karyera yönümlü tapşırıqlarla müsahibə və iş prosesinə hazırlaşacaqsınız.",
            detail_point_8: "DatalabAcademy sertifikatı ilə nəticənizi təsdiqləyə biləcəksiniz.",
            detail_learn_more: "Proqram başlanğıc səviyyədən irəliləyir və hər modulda öyrəndiyiniz bacarıqları kiçik praktik işlərlə möhkəmləndirir. Məqsəd yalnız dərs izləmək deyil, real nəticə çıxarmaqdır.",
            detail_show_more: "Daha çox",
            detail_discount_time: "3 gün qaldı!",
            detail_add_cart: "Səbətə əlavə et",
            detail_buy_now: "İndi al",
            detail_guarantee: "30 günlük geri qaytarma zəmanəti",
            detail_start_date: "Başlama tarixi",
            detail_start_date_value: "Yeni qrup",
            detail_enrolled: "Qeydiyyat",
            detail_lectures: "Dərslər",
            detail_skill_level: "Səviyyə",
            detail_skill_level_value: "Başlanğıc",
            detail_language: "Dil",
            detail_language_value: "Azərbaycan",
            detail_quizzes: "Tapşırıqlar",
            detail_certificate: "Sertifikat",
            detail_yes: "Bəli",
            detail_pass_percentage: "Praktiki tamamlanma",
            detail_contact_text: "Kurs haqqında məlumat üçün",
            detail_call_us: "Zəng edin:",
            lesson_course_content: "Kurs proqramı",
            lesson_search_placeholder: "Dərs axtarın",
            lesson_module_intro: "Başlanğıc",
            lesson_course_intro: "Kursa giriş",
            lesson_intro_text: "Giriş",
            lesson_module_lessons: "Dərslər",
            lesson_hello_world: "İlk praktiki dərs",
            lesson_values_variables: "Məlumatlar və dəyişənlər",
            lesson_basic_operators: "Əsas operatorlar",
            lesson_module_practice: "Praktiki yoxlama",
            lesson_task_1: "Tapşırıq növləri",
            lesson_task_2: "Ardıcıl suallar",
            lesson_task_3: "Mərhələli test formatı",
            lesson_task_4: "Tək sual",
            lesson_task_5: "Ballı praktiki test",
            lesson_task_6: "Vaxt limitli test",
            lesson_task_7: "Yoxlamaya başla",
            lesson_task_8: "Yoxlama nəticəsi",
            lesson_module_assignments: "Tapşırıqlar",
            lesson_assignment: "Praktiki tapşırıqlar",
            lesson_assignment_submit: "Tapşırığı göndər",
            lesson_top_title: "Online təlim: Data, SQL, Excel və AI",
            lesson_back_title: "Kursa qayıt",
            lesson_about_title: "Dərs haqqında",
            lesson_about_text: "Bu dərsdə mövzunu real iş nümunəsi üzərində addım-addım izah edirik və praktik tətbiq üçün əsas məqamları göstəririk.",
            lesson_previous: "Əvvəlki",
            lesson_next: "Növbəti",
            lesson_player_loading: "Video yüklənir..."
        },
        en: {
            lang_az: "Azerbaijani",
            lang_en: "English",
            search_placeholder: "Search courses",
            cart: "Cart",
            nav_home: "Home",
            nav_courses: "Courses",
            nav_online_training: "Online training",
            nav_about: "About",
            nav_blog: "Blog",
            nav_contact: "Contact",
            join_now: "Join Now",
            cart_title: "Cart",
            cart_breadcrumb: "Cart",
            cart_table_image: "Image",
            cart_table_course: "Course",
            cart_table_price: "Price",
            cart_table_quantity: "Qty",
            cart_table_total: "Total",
            cart_table_remove: "Remove",
            cart_summary: "Cart Summary",
            subtotal: "Subtotal",
            grand_total: "Grand Total",
            continue_courses: "Back to Courses",
            checkout: "Checkout",
            view_cart: "View Cart",
            mini_cart_title: "Your Cart",
            cart_empty_title: "Your cart is empty",
            cart_empty_text: "Browse the course catalog to choose the right course for your career.",
            checkout_details: "Registration Details",
            field_first_name: "First Name*",
            field_last_name: "Last Name*",
            field_email: "Email*",
            field_phone: "Phone*",
            field_note: "Note",
            field_note_placeholder: "Do you have an additional question about a course?",
            payment_method: "Payment Method",
            payment_card: "Card payment",
            payment_card_desc: "Our manager will send you the payment link.",
            payment_transfer: "Bank transfer",
            payment_transfer_desc: "Bank details will be shared after confirmation.",
            accept_terms: "I accept the terms",
            place_order: "Place order",
            order_success: "Your order has been received! We will contact you soon.",
            footer_about_text: "DatalabAcademy helps you build career-ready skills through practical Data Analytics, SQL, Excel and AI courses.",
            footer_contact_cta: "Contact Us",
            footer_useful_links: "Useful Links",
            footer_our_company: "Our Courses",
            footer_link_home: "Home",
            footer_link_courses: "Courses",
            footer_link_about: "About",
            footer_link_blog: "Blog",
            footer_link_contact: "Contact",
            get_contact: "Contact Info",
            phone: "Phone:",
            email: "Email:",
            footer_address_label: "Address:",
            footer_address: "Baku, Azerbaijan",
            newsletter: "Newsletter",
            newsletter_desc: "Add your email for new courses and useful learning materials.",
            enter_email: "Enter your email",
            submit_now: "Submit",
            terms_service: "Terms of service",
            privacy_policy: "Privacy policy",
            login_register: "Login & Register",
            copyright_text: "Copyright © 2026 DatalabAcademy. All rights reserved.",
            toast: "Thank you! We will contact you soon.",
            detail_bc_home: "Home",
            detail_bc_category: "Online training",
            detail_students_enrolled: "Students enrolled",
            detail_title: "Online training: Data, SQL, Excel and AI",
            detail_subtitle: "A career-focused program with practical tasks, mentor support and real workflows from DatalabAcademy.",
            detail_author_prefix: "Instructor",
            detail_author: "DatalabAcademy",
            detail_date: "May 31, 2026",
            detail_reviews: "(84 Reviews)",
            detail_tab_overview: "Overview",
            detail_tab_content: "Course Content",
            detail_tab_details: "Details",
            detail_tab_instructor: "Instructor",
            detail_tab_review: "Reviews",
            detail_learn_title: "What you'll learn",
            detail_learn_body: "This online program is for learners who want to practice data analytics, SQL, Excel and AI tools through real work scenarios. Lessons combine practical tasks, mentor explanations and projects you can add to your portfolio.",
            detail_point_1: "Clean, analyze and report data with Excel and SQL.",
            detail_point_2: "Build dashboards and queries that answer real business questions.",
            detail_point_3: "Use AI tools with effective prompts and validation methods to speed up your workflow.",
            detail_point_4: "Work on projects that can be added to your portfolio after the course.",
            detail_point_5: "Review topics at your own pace with live lesson recordings and notes.",
            detail_point_6: "Strengthen difficult topics through mentor-led Q&A.",
            detail_point_7: "Prepare for interviews and workplace tasks with career-focused exercises.",
            detail_point_8: "Validate your progress with a DatalabAcademy certificate.",
            detail_learn_more: "The program starts from the basics and reinforces every module with short practical assignments. The goal is not only watching lessons, but producing real outcomes.",
            detail_show_more: "Show More",
            detail_discount_time: "3 days left!",
            detail_add_cart: "Add to Cart",
            detail_buy_now: "Buy Now",
            detail_guarantee: "30-Day Money-Back Guarantee",
            detail_start_date: "Start Date",
            detail_start_date_value: "New group",
            detail_enrolled: "Enrolled",
            detail_lectures: "Lectures",
            detail_skill_level: "Skill Level",
            detail_skill_level_value: "Beginner",
            detail_language: "Language",
            detail_language_value: "Azerbaijani",
            detail_quizzes: "Assignments",
            detail_certificate: "Certificate",
            detail_yes: "Yes",
            detail_pass_percentage: "Practice completion",
            detail_contact_text: "For details about the course",
            detail_call_us: "Call Us:",
            lesson_course_content: "Course Content",
            lesson_search_placeholder: "Search lesson",
            lesson_module_intro: "Getting Started",
            lesson_course_intro: "Course Intro",
            lesson_intro_text: "Introduction",
            lesson_module_lessons: "Lessons",
            lesson_hello_world: "First practical lesson",
            lesson_values_variables: "Data and variables",
            lesson_basic_operators: "Basic operators",
            lesson_module_practice: "Practice check",
            lesson_task_1: "Task types",
            lesson_task_2: "Sequential questions",
            lesson_task_3: "Step-by-step test format",
            lesson_task_4: "Single question",
            lesson_task_5: "Scored practice test",
            lesson_task_6: "Timed test",
            lesson_task_7: "Start practice",
            lesson_task_8: "Practice result",
            lesson_module_assignments: "Assignments",
            lesson_assignment: "Practical assignments",
            lesson_assignment_submit: "Submit assignment",
            lesson_top_title: "Online training: Data, SQL, Excel and AI",
            lesson_back_title: "Back to course",
            lesson_about_title: "About Lesson",
            lesson_about_text: "In this lesson, we explain the topic step by step through a real work example and highlight the key points for practical use.",
            lesson_previous: "Previous",
            lesson_next: "Next",
            lesson_player_loading: "Loading video..."
        }
    };

    function courseTitle(course, lang) {
        return lang === "en" ? course.titleEn : course.titleAz;
    }

    function defaultCartIds() {
        return [];
    }

    function getCartIds() {
        var raw = localStorage.getItem(CART_KEY);
        if (!raw) return defaultCartIds();
        try {
            var ids = JSON.parse(raw);
            if (!Array.isArray(ids)) return defaultCartIds();
            return ids.map(String).filter(function (id, index, arr) {
                return courses.some(function (course) { return course.id === id; }) && arr.indexOf(id) === index;
            });
        } catch (err) {
            return defaultCartIds();
        }
    }

    function setCartIds(ids) {
        localStorage.setItem(CART_KEY, JSON.stringify(ids.map(String)));
    }

    function getCartCourses() {
        var ids = getCartIds();
        return courses.filter(function (course) { return ids.indexOf(course.id) !== -1; });
    }

    function formatPrice(usd, currency) {
        if (currency === "AZN") return Math.round(Number(usd) * 1.7) + " ₼";
        return "$" + Number(usd);
    }

    function renderHeader() {
        return [
            '<header class="rbt-header rbt-header-9 datalab-header">',
            '<div class="rbt-sticky-placeholder"></div>',
            '<div class="rbt-header-middle position-relative rbt-header-mid-1 bg-color-white rbt-border-bottom">',
            '<div class="container"><div class="rbt-header-sec align-items-center">',
            '<div class="rbt-header-sec-col rbt-header-left"><div class="rbt-header-content">',
            '<div class="header-info"><ul class="rbt-dropdown-menu switcher-language"><li class="has-child-menu"><a href="#" class="js-lang-current"><span class="menu-item js-current-lang">Azərbaycan</span><i class="right-icon feather-chevron-down"></i></a><ul class="sub-menu"><li><a href="#" data-lang="az"><span class="menu-item" data-i18n="lang_az">Azərbaycan</span></a></li><li><a href="#" data-lang="en"><span class="menu-item" data-i18n="lang_en">English</span></a></li></ul></li></ul></div>',
            '<div class="header-info"><ul class="rbt-dropdown-menu currency-menu"><li class="has-child-menu"><a href="#"><span class="menu-item js-current-currency">USD</span><i class="right-icon feather-chevron-down"></i></a><ul class="sub-menu hover-reverse"><li><a href="#" data-currency="USD"><span class="menu-item">USD</span></a></li><li><a href="#" data-currency="AZN"><span class="menu-item">AZN</span></a></li></ul></li></ul></div>',
            '</div></div>',
            '<div class="rbt-header-sec-col rbt-header-center d-none d-md-block"><div class="rbt-header-content"><div class="header-info"><div class="rbt-search-field"><div class="search-field"><input type="text" placeholder="Kurs axtarın" data-i18n-placeholder="search_placeholder"><button class="rbt-round-btn serach-btn" type="submit"><i class="feather-search"></i></button></div></div></div></div></div>',
            '<div class="rbt-header-sec-col rbt-header-right"><div class="rbt-header-content"><div class="header-info"><ul class="quick-access"><li><a class="d-none d-xl-block datalab-cart-trigger" href="cart.php"><i class="feather-shopping-cart"></i><span data-i18n="cart">Səbət</span><span class="datalab-cart-count" data-cart-count style="display:none">0</span></a><a class="d-block d-xl-none datalab-cart-trigger" href="cart.php"><i class="feather-shopping-cart"></i><span class="datalab-cart-count" data-cart-count style="display:none">0</span></a></li><li class="account-access rbt-user-wrapper right-align-dropdown" data-student-menu><a href="#"><i class="feather-user"></i></a><div class="rbt-user-menu-list-wrapper"><div class="inner"></div></div></li></ul></div><button class="rbt-round-btn datalab-mobile-toggle" type="button" aria-label="Menu"><i class="feather-menu"></i></button></div></div>',
            '</div></div></div>',
            '<div class="rbt-header-wrapper header-space-betwween bg-color-white header-sticky"><div class="container"><div class="mainbar-row rbt-navigation-center align-items-center">',
            '<div class="header-left"><div class="logo logo-dark"><a href="index.php"><img class="datalab-logo" src="assets/images/logo/datalab-logo-transparent.png" alt="DatalabAcademy"></a></div><div class="logo d-none logo-light"><a href="index.php"><img class="datalab-logo" src="assets/images/logo/datalab-logo-transparent.png" alt="DatalabAcademy"></a></div></div>',
            '<div class="rbt-main-navigation d-none d-xl-block"><nav class="mainmenu-nav"><ul class="mainmenu"><li><a href="index.php" data-i18n="nav_home">Ana səhifə</a></li><li class="has-dropdown has-menu-child-item"><a href="course-filter-one-open.html"><span data-i18n="nav_courses">Kurslar</span> <i class="feather-chevron-down"></i></a><ul class="submenu">' + renderCourseMenuItems() + '</ul></li><li><a href="index.php#about" data-i18n="nav_about">Haqqımızda</a></li><li><a href="index.php#blog" data-i18n="nav_blog">Bloq</a></li><li><a href="contact.php" data-i18n="nav_contact">Əlaqə</a></li></ul></nav></div>',
            '<div class="header-right"><a class="rbt-btn btn-gradient hover-icon-reverse" href="course-filter-one-open.html"><span class="icon-reverse-wrapper"><span class="btn-text" data-i18n="join_now">Qoşul</span><span class="btn-icon"><i class="feather-arrow-right"></i></span><span class="btn-icon"><i class="feather-arrow-right"></i></span></span></a></div>',
            '<div class="datalab-mobile-panel"><a href="index.php" data-i18n="nav_home">Ana səhifə</a><a href="course-filter-one-open.html" data-i18n="nav_courses">Kurslar</a><a href="course-filter-two-toggle.php" data-i18n="nav_online_training">Online təlimlər</a><a href="index.php#about" data-i18n="nav_about">Haqqımızda</a><a href="index.php#blog" data-i18n="nav_blog">Bloq</a><a href="contact.php" data-i18n="nav_contact">Əlaqə</a></div>',
            '</div></div></div>',
            '</header>'
        ].join("");
    }

    function renderMiniCart() {
        var cartCourses = getCartCourses();
        return [
            '<div class="rbt-cart-side-menu datalab-cart-side"><div class="inner-wrapper"><div class="inner-top"><div class="content"><div class="title"><h4 class="title mb--0" data-i18n="mini_cart_title">Səbətiniz</h4></div><div class="rbt-btn-close"><button class="minicart-close-button rbt-round-btn"><i class="feather-x"></i></button></div></div></div>',
            '<nav class="side-nav w-100"><ul class="rbt-minicart-wrapper">' + cartCourses.map(function (course) {
                return '<li class="minicart-item" data-mini-course-id="' + course.id + '"><div class="thumbnail"><a href="course-details-4.php?id=' + encodeURIComponent(course.id) + '"><img src="' + course.img + '" alt="' + course.titleAz + '"></a></div><div class="product-content"><h6 class="title"><a href="course-details-4.php?id=' + encodeURIComponent(course.id) + '">' + course.titleAz + '</a></h6><span class="quantity">1 * <span class="price" data-usd="' + course.price + '">$' + course.price + '</span></span></div><div class="close-btn"><button class="rbt-round-btn" type="button" data-mini-cart-remove><i class="feather-x"></i></button></div></li>';
            }).join("") + '</ul><div class="datalab-mini-cart-empty" data-mini-cart-empty data-i18n="cart_empty_title">Səbət boşdur</div></nav>',
            '<div class="rbt-minicart-footer"><hr class="mb--0"><div class="rbt-cart-subttotal"><p class="subtotal"><strong data-i18n="cart_table_total">Yekun</strong></p><p class="price" data-usd="590" data-cart-total-usd>$590</p></div><hr class="mb--0"><div class="rbt-minicart-bottom mt--20"><div class="view-cart-btn"><a class="rbt-btn btn-border icon-hover w-100 text-center" href="cart.html"><span class="btn-text" data-i18n="view_cart">Səbətə bax</span><span class="btn-icon"><i class="feather-arrow-right"></i></span></a></div><div class="checkout-btn mt--20"><a class="rbt-btn btn-gradient icon-hover w-100 text-center" href="checkout.html"><span class="btn-text" data-i18n="checkout">Ödənişə keç</span><span class="btn-icon"><i class="feather-arrow-right"></i></span></a></div></div></div>',
            '</div></div><a class="close_side_menu" href="javascript:void(0);"></a>'
        ].join("");
    }

    function renderFooter() {
        return [
            '<footer class="rbt-footer footer-style-1 bg-color-white overflow-hidden datalab-footer"><div class="footer-top"><div class="container"><div class="row g-5">',
            '<div class="col-lg-4 col-md-6 col-sm-6 col-12"><div class="footer-widget"><div class="logo logo-dark"><a href="index.php"><img class="datalab-logo" src="assets/images/logo/datalab-logo-transparent.png" alt="DatalabAcademy"></a></div><div class="logo d-none logo-light"><a href="index.php"><img class="datalab-logo" src="assets/images/logo/datalab-logo-transparent.png" alt="DatalabAcademy"></a></div><p class="description mt--20" data-i18n="footer_about_text">DatalabAcademy praktiki Data Analitika, SQL, Excel və AI təlimləri ilə karyera bacarıqlarınızı inkişaf etdirir.</p><ul class="social-icon social-default justify-content-start"><li><a href="#"><i class="feather-facebook"></i></a></li><li><a href="#"><i class="feather-instagram"></i></a></li><li><a href="#"><i class="feather-linkedin"></i></a></li></ul><div class="contact-btn mt--30"><a class="rbt-btn hover-icon-reverse btn-border-gradient radius-round" href="index.php#contact"><div class="icon-reverse-wrapper"><span class="btn-text" data-i18n="footer_contact_cta">Bizimlə əlaqə</span><span class="btn-icon"><i class="feather-arrow-right"></i></span><span class="btn-icon"><i class="feather-arrow-right"></i></span></div></a></div></div></div>',
            '<div class="col-lg-2 col-md-6 col-sm-6 col-12"><div class="footer-widget"><h5 class="ft-title" data-i18n="footer_useful_links">Faydalı linklər</h5><ul class="ft-link"><li><a href="index.php" data-i18n="footer_link_home">Ana səhifə</a></li><li><a href="course-filter-one-open.html" data-i18n="footer_link_courses">Kurslar</a></li><li><a href="index.php#about" data-i18n="footer_link_about">Haqqımızda</a></li><li><a href="index.php#blog" data-i18n="footer_link_blog">Bloq</a></li><li><a href="index.php#contact" data-i18n="footer_link_contact">Əlaqə</a></li><li><a href="privacy-policy.html" data-i18n="privacy_policy">Məxfilik siyasəti</a></li></ul></div></div>',
            '<div class="col-lg-2 col-md-6 col-sm-6 col-12"><div class="footer-widget"><h5 class="ft-title" data-i18n="footer_our_company">Kurslarımız</h5><ul class="ft-link">' + courses.map(function (course) { return '<li><a href="course-details-4.php?id=' + encodeURIComponent(course.id) + '">' + course.titleAz + '</a></li>'; }).join("") + '</ul></div></div>',
            '<div class="col-lg-4 col-md-6 col-sm-6 col-12"><div class="footer-widget"><h5 class="ft-title" data-i18n="get_contact">Əlaqə məlumatı</h5><ul class="ft-link"><li><span data-i18n="phone">Telefon:</span> <a href="tel:+994506549737">+994 50 654 97 37</a></li><li><span data-i18n="email">E-poçt:</span> <a href="mailto:info@datalabacademy.az">info@datalabacademy.az</a></li><li><span data-i18n="footer_address_label">Ünvan:</span> <span data-i18n="footer_address">Bakı, Azərbaycan</span></li></ul><form class="newsletter-form mt--20" action="#"><h6 class="w-600" data-i18n="newsletter">Yeniliklər</h6><p class="description" data-i18n="newsletter_desc">Yeni kurslar və faydalı materiallar üçün e-poçtunuzu qeyd edin.</p><div class="form-group right-icon icon-email mb--20"><label for="datalab-email" data-i18n="enter_email">E-poçtunuzu daxil edin</label><input id="datalab-email" type="email"></div><div class="form-group mb--0"><button class="rbt-btn rbt-switch-btn btn-gradient radius-round btn-sm" type="submit"><span data-text="Göndər" data-i18n="submit_now" data-i18n-data-text="submit_now">Göndər</span></button></div></form></div></div>',
            '</div></div></div><div class="rbt-separator-mid"><div class="container"><hr class="rbt-separator m-0"></div></div><div class="copyright-area copyright-style-1 ptb--20"><div class="container"><div class="row align-items-center"><div class="col-lg-6 col-12"><p class="rbt-link-hover text-center text-lg-start" data-i18n="copyright_text">Copyright © 2026 DatalabAcademy. Bütün hüquqlar qorunur.</p></div><div class="col-lg-6 col-12"><ul class="copyright-link rbt-link-hover justify-content-center justify-content-lg-end mt_sm--10 mt_md--10"><li><a href="#" data-i18n="terms_service">İstifadə şərtləri</a></li><li><a href="privacy-policy.html" data-i18n="privacy_policy">Məxfilik siyasəti</a></li><li><a href="login.html" data-i18n="login_register">Giriş və qeydiyyat</a></li></ul></div></div></div></div></footer>'
        ].join("");
    }

    function includeSharedBlocks() {
        document.querySelectorAll("[data-datalab-header]").forEach(function (el) { el.innerHTML = renderHeader(); });
        document.querySelectorAll("[data-datalab-cart]").forEach(function (el) { el.innerHTML = renderMiniCart(); });
        document.querySelectorAll("[data-datalab-footer]").forEach(function (el) { el.innerHTML = renderFooter(); });
    }

    function syncCartDom() {
        var ids = getCartIds();
        document.querySelectorAll(".datalab-cart-table tbody tr[data-course-id]").forEach(function (row) {
            if (ids.indexOf(row.getAttribute("data-course-id")) === -1) row.remove();
        });
        document.querySelectorAll("[data-checkout-items] li[data-course-id]").forEach(function (row) {
            if (ids.indexOf(row.getAttribute("data-course-id")) === -1) row.remove();
        });
        document.querySelectorAll("[data-mini-course-id]").forEach(function (item) {
            if (ids.indexOf(item.getAttribute("data-mini-course-id")) === -1) item.remove();
        });
    }

    function applyLang(lang) {
        var bag = dict[lang] || dict.az;
        document.documentElement.lang = lang;
        localStorage.setItem("siteLang", lang);
        document.querySelectorAll(".js-current-lang").forEach(function (el) {
            el.textContent = lang === "en" ? "English" : "Azərbaycan";
        });
        document.querySelectorAll("[data-i18n]").forEach(function (el) {
            var key = el.getAttribute("data-i18n");
            if (bag[key]) el.textContent = bag[key];
        });
        document.querySelectorAll("[data-i18n-html]").forEach(function (el) {
            var key = el.getAttribute("data-i18n-html");
            if (bag[key]) el.innerHTML = bag[key];
        });
        document.querySelectorAll("[data-i18n-placeholder]").forEach(function (el) {
            var key = el.getAttribute("data-i18n-placeholder");
            if (bag[key]) el.setAttribute("placeholder", bag[key]);
        });
        document.querySelectorAll("[data-i18n-title]").forEach(function (el) {
            var key = el.getAttribute("data-i18n-title");
            if (bag[key]) el.setAttribute("title", bag[key]);
        });
        document.querySelectorAll("[data-i18n-data-text]").forEach(function (el) {
            var key = el.getAttribute("data-i18n-data-text");
            if (bag[key]) el.setAttribute("data-text", bag[key]);
        });
    }

    function applyCurrency(currency) {
        localStorage.setItem("siteCurrency", currency);
        document.querySelectorAll(".js-current-currency").forEach(function (el) { el.textContent = currency; });
        document.querySelectorAll("[data-usd]").forEach(function (el) {
            el.textContent = formatPrice(el.getAttribute("data-usd"), currency);
        });
    }

    function updateCartTotals() {
        var table = document.querySelector(".datalab-cart-table");
        var rows = table ? Array.prototype.slice.call(table.querySelectorAll("tbody tr")) : [];
        var miniItems = Array.prototype.slice.call(document.querySelectorAll("[data-mini-course-id]"));
        var sourceItems = table ? rows : miniItems;
        var total = sourceItems.reduce(function (sum, item) {
            var priceEl = item.querySelector(".pro-subtotal [data-usd], .pro-price [data-usd], .quantity [data-usd]");
            return sum + (priceEl ? Number(priceEl.getAttribute("data-usd")) : 0);
        }, 0);
        var hasRows = sourceItems.length > 0;
        var empty = document.querySelector("[data-cart-empty]");
        var currency = localStorage.getItem("siteCurrency") || "USD";

        document.querySelectorAll("[data-cart-total-usd]").forEach(function (el) {
            el.setAttribute("data-usd", String(total));
            el.textContent = formatPrice(total, currency);
        });

        if (table) table.style.display = hasRows ? "" : "none";
        if (table) {
            document.querySelectorAll(".cart-summary").forEach(function (el) {
                el.style.display = hasRows ? "" : "none";
            });
        }
        if (empty) empty.classList.toggle("is-visible", !hasRows);
        document.querySelectorAll("[data-checkout-items]").forEach(function (list) {
            list.style.display = hasRows ? "" : "none";
        });
        document.querySelectorAll("[data-datalab-checkout] button[type='submit']").forEach(function (button) {
            button.disabled = !hasRows;
        });
        document.querySelectorAll("[data-mini-cart-empty]").forEach(function (el) {
            el.style.display = miniItems.length ? "none" : "block";
        });
        document.querySelectorAll(".rbt-minicart-footer").forEach(function (el) {
            el.style.display = miniItems.length ? "" : "none";
        });
        document.querySelectorAll('a[href="checkout.html"]').forEach(function (link) {
            var disabled = table ? !hasRows : miniItems.length === 0;
            link.classList.toggle("disabled", disabled);
            link.setAttribute("aria-disabled", disabled ? "true" : "false");
        });
    }

    function showToast(message) {
        var toast = document.querySelector(".datalab-toast");
        if (!toast) {
            toast = document.createElement("div");
            toast.className = "datalab-toast";
            document.body.appendChild(toast);
        }
        toast.textContent = message;
        toast.classList.add("is-open");
        window.clearTimeout(showToast.timer);
        showToast.timer = window.setTimeout(function () { toast.classList.remove("is-open"); }, 3000);
    }

    function optimizeImages() {
        document.querySelectorAll("img").forEach(function (img, index) {
            if (!img.hasAttribute("decoding")) img.setAttribute("decoding", "async");
            if (index > 2 && !img.hasAttribute("loading")) img.setAttribute("loading", "lazy");
        });
    }

    function getYouTubeId(url) {
        var match = String(url || "").match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{6,})/);
        return match ? match[1] : "";
    }

    function getDriveId(url) {
        var text = String(url || "");
        var match = text.match(/drive\.google\.com\/file\/d\/([^/?#]+)/) || text.match(/[?&]id=([^&#]+)/);
        return match ? match[1] : "";
    }

    // Bunny Stream embed linkini tanı (iframe.mediadelivery.net/embed/LIBRARY/GUID)
    function getBunnyEmbed(url) {
        var text = String(url || "");
        var m = text.match(/mediadelivery\.net\/(?:embed|play)\/(\d+)\/([a-f0-9-]{16,})/i);
        if (m) {
            return "https://iframe.mediadelivery.net/embed/" + m[1] + "/" + m[2];
        }
        // Tam embed iframe HTML yapışdırılıbsa, src-i çıxar
        var src = text.match(/src\s*=\s*["']([^"']*mediadelivery\.net[^"']+)["']/i);
        if (src) {
            return src[1];
        }
        return "";
    }

    function appendBunnyPlayer(container, src, title) {
        var sep = src.indexOf("?") >= 0 ? "&" : "?";
        var full = src + sep + "autoplay=false&preload=true&responsive=true";
        // Birbaşa iframe — .datalab-lesson-player CSS-i (width/height 100%) onu tam doldurur
        var iframe = document.createElement("iframe");
        iframe.setAttribute("src", full);
        iframe.setAttribute("title", title || "");
        iframe.setAttribute("loading", "lazy");
        iframe.setAttribute("allow", "accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; fullscreen");
        iframe.setAttribute("allowfullscreen", "");
        container.innerHTML = "";
        container.appendChild(iframe);
    }

    function appendPlayerFrame(container, src, title) {
        var iframe = document.createElement("iframe");
        iframe.setAttribute("src", src);
        iframe.setAttribute("title", title);
        iframe.setAttribute("loading", "lazy");
        iframe.setAttribute("allow", "autoplay; encrypted-media; picture-in-picture; fullscreen");
        iframe.setAttribute("allowfullscreen", "");
        container.innerHTML = "";
        container.appendChild(iframe);
    }

    function youtubeCommand(iframe, func, args) {
        if (!iframe || !iframe.contentWindow) return;
        iframe.contentWindow.postMessage(JSON.stringify({
            event: "command",
            func: func,
            args: args || []
        }), "*");
    }

    function bindYouTubeControls(player) {
        var iframe = player.querySelector("iframe");
        var playButtons = Array.prototype.slice.call(player.querySelectorAll("[data-video-play]"));
        var pauseButtons = Array.prototype.slice.call(player.querySelectorAll("[data-video-pause]"));
        var playIcons = Array.prototype.slice.call(player.querySelectorAll("[data-video-play-icon]"));
        var progress = player.querySelector("[data-video-progress]");
        var current = player.querySelector("[data-video-current]");
        var duration = player.querySelector("[data-video-duration]");
        var backBtn = player.querySelector("[data-video-back]");
        var forwardBtn = player.querySelector("[data-video-forward]");
        var quality = player.querySelector("[data-video-quality]");
        var fullscreenBtn = player.querySelector("[data-video-fullscreen]");
        var videoKey = player.getAttribute("data-video-key") || iframe.src;
        var currentTime = 0;
        var totalTime = 0;
        var isPlaying = false;
        var restoreDone = false;

        bindPlayerLayoutMode(player);

        function setPlaying(next) {
            isPlaying = next;
            player.classList.toggle("is-playing", isPlaying);
            playIcons.forEach(function (icon) {
                icon.className = isPlaying ? "feather-pause" : "feather-play";
            });
        }

        function save() {
            if (!totalTime || totalTime < 10) return;
            try {
                localStorage.setItem("dlVideoProgress:" + videoKey, JSON.stringify({
                    time: currentTime,
                    duration: totalTime,
                    savedAt: Date.now()
                }));
            } catch (err) {}
        }

        function restore() {
            if (restoreDone || !totalTime) return;
            restoreDone = true;
            try {
                var raw = localStorage.getItem("dlVideoProgress:" + videoKey);
                var value = raw ? JSON.parse(raw) : null;
                var time = value ? Number(value.time) : 0;
                if (time > 5 && time < totalTime - 5) {
                    youtubeCommand(iframe, "seekTo", [time, true]);
                }
            } catch (err) {}
        }

        playButtons.forEach(function (button) {
            button.addEventListener("click", function () {
                if (isPlaying) {
                    setPlaying(false);
                    youtubeCommand(iframe, "pauseVideo");
                } else {
                    setPlaying(true);
                    youtubeCommand(iframe, "playVideo");
                }
            });
        });
        player.addEventListener("click", function (event) {
            var target = event.target;
            if (target.closest && target.closest(".datalab-video-controls")) return;
            if (target.closest && target.closest(".datalab-video-center")) return;
            if (isPlaying) {
                setPlaying(false);
                youtubeCommand(iframe, "pauseVideo");
            } else {
                setPlaying(true);
                youtubeCommand(iframe, "playVideo");
            }
        });
        pauseButtons.forEach(function (button) {
            button.addEventListener("click", function () {
                setPlaying(false);
                youtubeCommand(iframe, "pauseVideo");
            });
        });
        backBtn.addEventListener("click", function () {
            youtubeCommand(iframe, "seekTo", [Math.max(0, currentTime - 30), true]);
        });
        forwardBtn.addEventListener("click", function () {
            youtubeCommand(iframe, "seekTo", [totalTime ? Math.min(totalTime, currentTime + 30) : currentTime + 30, true]);
        });
        progress.addEventListener("input", function () {
            if (totalTime) youtubeCommand(iframe, "seekTo", [(Number(progress.value) / 100) * totalTime, true]);
        });
        if (quality) {
            quality.addEventListener("change", function () {
                if (quality.value) youtubeCommand(iframe, "setPlaybackQuality", [quality.value]);
            });
        }
        fullscreenBtn.addEventListener("click", function () {
            if (player.requestFullscreen) player.requestFullscreen();
        });

        window.addEventListener("message", function (event) {
            var data = event.data;
            if (typeof data === "string") {
                try { data = JSON.parse(data); } catch (err) { return; }
            }
            if (!data || data.event !== "infoDelivery" || !data.info) return;
            if (Number.isFinite(Number(data.info.duration))) totalTime = Number(data.info.duration);
            if (Number.isFinite(Number(data.info.currentTime))) currentTime = Number(data.info.currentTime);
            if (Number.isFinite(Number(data.info.playerState))) {
                setPlaying(Number(data.info.playerState) === 1);
            }
            if (totalTime) {
                current.textContent = formatVideoTime(currentTime);
                duration.textContent = formatVideoTime(totalTime);
                progress.value = String((currentTime / totalTime) * 100);
                restore();
                save();
            }
        });

        iframe.addEventListener("load", function () {
            youtubeCommand(iframe, "addEventListener", ["onStateChange"]);
            youtubeCommand(iframe, "addEventListener", ["onReady"]);
        });
    }

    function bindPlayerLayoutMode(player) {
        function updateMode() {
            player.classList.toggle("is-compact", player.clientWidth < 680);
            player.classList.toggle("is-tight", player.clientWidth < 480);
        }

        updateMode();
        if ("ResizeObserver" in window) {
            var observer = new ResizeObserver(updateMode);
            observer.observe(player);
        } else {
            window.addEventListener("resize", updateMode);
        }
    }

    function appendYouTubePlayer(container, videoId, title) {
        var player = document.createElement("div");
        player.className = "datalab-video-shell datalab-youtube-shell is-youtube";
        player.setAttribute("data-video-key", container.getAttribute("data-video-key") || ("youtube:" + videoId));

        var iframe = document.createElement("iframe");
        iframe.setAttribute("src", "https://www.youtube-nocookie.com/embed/" + encodeURIComponent(videoId) + "?enablejsapi=1&controls=0&rel=0&modestbranding=1&iv_load_policy=3&playsinline=1&origin=" + encodeURIComponent(window.location.origin));
        iframe.setAttribute("title", title);
        iframe.setAttribute("allow", "autoplay; encrypted-media; picture-in-picture; fullscreen");
        iframe.setAttribute("allowfullscreen", "");
        player.appendChild(iframe);
        player.insertAdjacentHTML("beforeend", [
            '<button class="datalab-video-center" type="button" data-video-play aria-label="Play video"><i data-video-play-icon class="feather-play"></i></button>',
            '<div class="datalab-video-controls">',
            '<button type="button" data-video-play aria-label="Play video"><i data-video-play-icon class="feather-play"></i></button>',
            '<button type="button" data-video-pause aria-label="Pause video"><i class="feather-pause"></i></button>',
            '<button type="button" data-video-back aria-label="Back 30 seconds"><span>-30</span></button>',
            '<button type="button" data-video-forward aria-label="Forward 30 seconds"><span>+30</span></button>',
            '<span data-video-current>0:00</span>',
            '<div class="datalab-video-progress-wrap" data-video-progress-wrap>',
            '<input type="range" min="0" max="100" value="0" step="0.1" data-video-progress aria-label="Video progress">',
            '</div>',
            '<span data-video-duration>0:00</span>',
            '<label class="datalab-video-quality"><select data-video-quality aria-label="Video quality">',
            '<option value="">Auto</option><option value="hd1080">1080p</option><option value="hd720">720p</option><option value="large">480p</option><option value="medium">360p</option>',
            '</select></label>',
            '<button type="button" data-video-fullscreen aria-label="Fullscreen"><i class="feather-maximize"></i></button>',
            '</div>'
        ].join(""));

        container.innerHTML = "";
        container.appendChild(player);
        bindYouTubeControls(player);
    }

    function appendDrivePlayer(container, fileId, title) {
        appendNativeVideo(container, "video-proxy.php?id=" + encodeURIComponent(fileId), title);
    }

    function formatVideoTime(seconds) {
        if (!Number.isFinite(seconds)) return "0:00";
        var mins = Math.floor(seconds / 60);
        var secs = Math.floor(seconds % 60);
        return mins + ":" + String(secs).padStart(2, "0");
    }

    function storageKeyForVideo(video, player) {
        return "dlVideoProgress:" + (player.getAttribute("data-video-key") || video.currentSrc || video.src || "");
    }

    function saveVideoProgress(video, player) {
        if (!video.duration || video.duration < 10) return;
        try {
            localStorage.setItem(storageKeyForVideo(video, player), JSON.stringify({
                time: video.currentTime,
                duration: video.duration,
                src: video.currentSrc || video.src,
                savedAt: Date.now()
            }));
        } catch (err) {}
    }

    function restoreVideoProgress(video, player) {
        try {
            var raw = localStorage.getItem(storageKeyForVideo(video, player));
            var value = raw ? JSON.parse(raw) : null;
            var time = value ? Number(value.time) : 0;
            if (video.duration && time > 5 && time < video.duration - 5) {
                video.currentTime = time;
            }
        } catch (err) {}
    }

    function bindCustomVideoControls(player) {
        var video = player.querySelector("video");
        var playButtons = Array.prototype.slice.call(player.querySelectorAll("[data-video-play]"));
        var pauseButtons = Array.prototype.slice.call(player.querySelectorAll("[data-video-pause]"));
        var playIcons = Array.prototype.slice.call(player.querySelectorAll("[data-video-play-icon]"));
        var progress = player.querySelector("[data-video-progress]");
        var progressWrap = player.querySelector("[data-video-progress-wrap]");
        var progressHover = player.querySelector("[data-video-progress-hover]");
        var current = player.querySelector("[data-video-current]");
        var duration = player.querySelector("[data-video-duration]");
        var backBtn = player.querySelector("[data-video-back]");
        var forwardBtn = player.querySelector("[data-video-forward]");
        var muteBtn = player.querySelector("[data-video-mute]");
        var muteIcon = player.querySelector("[data-video-mute-icon]");
        var volume = player.querySelector("[data-video-volume]");
        var quality = player.querySelector("[data-video-quality]");
        var fullscreenBtn = player.querySelector("[data-video-fullscreen]");
        var idleTimer = null;
        var saveTimer = null;

        bindPlayerLayoutMode(player);

        function showControls() {
            player.classList.remove("is-idle");
            window.clearTimeout(idleTimer);
            if (!video.paused) {
                idleTimer = window.setTimeout(function () {
                    player.classList.add("is-idle");
                }, 1800);
            }
        }

        function updatePlayState() {
            player.classList.toggle("is-playing", !video.paused);
            playIcons.forEach(function (icon) {
                icon.className = video.paused ? "feather-play" : "feather-pause";
            });
            showControls();
        }

        function updateMuteState() {
            muteIcon.className = video.muted || video.volume === 0 ? "feather-volume-x" : "feather-volume-2";
            if (volume) volume.value = String(video.muted ? 0 : Math.round(video.volume * 100));
        }

        function togglePlay() {
            if (video.paused) {
                video.play();
            } else {
                video.pause();
            }
        }

        playButtons.forEach(function (button) {
            button.addEventListener("click", togglePlay);
        });
        pauseButtons.forEach(function (button) {
            button.addEventListener("click", function () {
                video.pause();
            });
        });

        player.addEventListener("click", function (event) {
            var target = event.target;
            if (target.closest && target.closest(".datalab-video-controls")) return;
            if (target.closest && target.closest(".datalab-video-center")) return;
            togglePlay();
        });

        ["mousemove", "mousedown", "touchstart", "keydown"].forEach(function (eventName) {
            player.addEventListener(eventName, showControls);
        });

        player.addEventListener("mouseleave", function () {
            if (!video.paused) player.classList.add("is-idle");
        });

        video.addEventListener("play", updatePlayState);
        video.addEventListener("pause", updatePlayState);
        video.addEventListener("error", function () {
            player.classList.add("has-error");
        });
        video.addEventListener("loadedmetadata", function () {
            player.classList.remove("has-error");
            duration.textContent = formatVideoTime(video.duration);
            restoreVideoProgress(video, player);
        });
        video.addEventListener("timeupdate", function () {
            current.textContent = formatVideoTime(video.currentTime);
            progress.value = video.duration ? String((video.currentTime / video.duration) * 100) : "0";
            window.clearTimeout(saveTimer);
            saveTimer = window.setTimeout(function () {
                saveVideoProgress(video, player);
            }, 400);
        });
        video.addEventListener("pause", function () { saveVideoProgress(video, player); });
        video.addEventListener("ended", function () {
            try { localStorage.removeItem(storageKeyForVideo(video, player)); } catch (err) {}
        });

        progress.addEventListener("input", function () {
            if (video.duration) video.currentTime = (Number(progress.value) / 100) * video.duration;
        });

        progressWrap.addEventListener("mousemove", function (event) {
            if (!video.duration) return;
            var rect = progressWrap.getBoundingClientRect();
            var ratio = Math.max(0, Math.min(1, (event.clientX - rect.left) / rect.width));
            progressHover.textContent = formatVideoTime(ratio * video.duration);
            progressHover.style.left = (ratio * 100) + "%";
            progressWrap.classList.add("is-hovering");
        });

        progressWrap.addEventListener("mouseleave", function () {
            progressWrap.classList.remove("is-hovering");
        });

        backBtn.addEventListener("click", function () {
            video.currentTime = Math.max(0, video.currentTime - 30);
        });

        forwardBtn.addEventListener("click", function () {
            if (!video.duration) return;
            video.currentTime = Math.min(video.duration, video.currentTime + 30);
        });

        muteBtn.addEventListener("click", function () {
            video.muted = !video.muted;
            if (!video.muted && video.volume === 0) video.volume = 0.7;
            updateMuteState();
        });

        volume.addEventListener("input", function () {
            var nextVolume = Math.max(0, Math.min(100, Number(volume.value))) / 100;
            video.volume = nextVolume;
            video.muted = nextVolume === 0;
            updateMuteState();
        });

        if (quality) {
            quality.addEventListener("change", function () {
                var nextSrc = quality.value;
                if (!nextSrc || nextSrc === video.currentSrc || nextSrc === video.src) return;
                var currentTime = video.currentTime;
                var wasPlaying = !video.paused;
                saveVideoProgress(video, player);
                video.src = nextSrc;
                video.load();
                video.addEventListener("loadedmetadata", function restoreAfterQualityChange() {
                    video.removeEventListener("loadedmetadata", restoreAfterQualityChange);
                    if (currentTime && video.duration && currentTime < video.duration) {
                        video.currentTime = currentTime;
                    }
                    if (wasPlaying) video.play();
                });
            });
        }

        fullscreenBtn.addEventListener("click", function () {
            if (player.requestFullscreen) player.requestFullscreen();
        });

        updatePlayState();
        updateMuteState();
    }

    function videoQualityOptions(container, src) {
        var raw = container.getAttribute("data-video-qualities") || "";
        var parsed = [];
        if (raw) {
            try {
                parsed = JSON.parse(raw);
            } catch (err) {
                parsed = [];
            }
        }
        if (!Array.isArray(parsed) || !parsed.length) {
            parsed = [
                { label: "Auto", src: src },
                { label: "Original", src: src }
            ];
        }
        return parsed.filter(function (item) {
            return item && item.src;
        });
    }

    function optionHtml(value, label) {
        return '<option value="' + String(value).replace(/&/g, "&amp;").replace(/"/g, "&quot;") + '">' +
            String(label || "Auto").replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;") +
            '</option>';
    }

    function appendNativeVideo(container, src, title) {
        var qualities = videoQualityOptions(container, src);
        var player = document.createElement("div");
        player.className = "datalab-video-shell";
        player.setAttribute("data-video-key", container.getAttribute("data-video-key") || title || src);

        var video = document.createElement("video");
        video.setAttribute("playsinline", "");
        video.setAttribute("preload", "metadata");
        video.setAttribute("title", title);
        video.src = src;

        player.appendChild(video);
        player.insertAdjacentHTML("beforeend", [
            '<button class="datalab-video-center" type="button" data-video-play aria-label="Play video"><i data-video-play-icon class="feather-play"></i></button>',
            '<div class="datalab-video-controls">',
            '<button type="button" data-video-play aria-label="Play video"><i data-video-play-icon class="feather-play"></i></button>',
            '<button type="button" data-video-pause aria-label="Pause video"><i class="feather-pause"></i></button>',
            '<button type="button" data-video-back aria-label="Back 30 seconds"><span>-30</span></button>',
            '<button type="button" data-video-forward aria-label="Forward 30 seconds"><span>+30</span></button>',
            '<span data-video-current>0:00</span>',
            '<div class="datalab-video-progress-wrap" data-video-progress-wrap>',
            '<input type="range" min="0" max="100" value="0" step="0.1" data-video-progress aria-label="Video progress">',
            '<span class="datalab-video-progress-hover" data-video-progress-hover>0:00</span>',
            '</div>',
            '<span data-video-duration>0:00</span>',
            '<div class="datalab-video-volume">',
            '<button type="button" data-video-mute aria-label="Mute video"><i data-video-mute-icon class="feather-volume-2"></i></button>',
            '<input type="range" min="0" max="100" value="100" step="1" data-video-volume aria-label="Video volume">',
            '</div>',
            '<label class="datalab-video-quality"><select data-video-quality aria-label="Video quality">' + qualities.map(function (item) {
                return optionHtml(item.src, item.label);
            }).join("") + '</select></label>',
            '<button type="button" data-video-fullscreen aria-label="Fullscreen"><i class="feather-maximize"></i></button>',
            '</div>',
            '<div class="datalab-video-error">Video açıla bilmir. Səhifəni PHP server üzərindən açın və Drive faylının linklə baxış icazəsini aktiv edin.</div>'
        ].join(""));

        container.innerHTML = "";
        container.appendChild(player);
        bindCustomVideoControls(player);
    }

    function addIntroOverlay(container) {
        if (container.querySelector(".dl-video-intro")) return;
        var ov = document.createElement("div");
        ov.className = "dl-video-intro";
        ov.innerHTML = '<div class="dl-video-intro-inner">' +
            '<img class="dl-video-intro-logo" src="assets/images/logo/datalab-logo-transparent.png" alt="DatalabAcademy">' +
            '<div class="dl-video-intro-bar"><span></span></div>' +
            '</div>';
        container.appendChild(ov);
        // ~2.6s branded intro, sonra solub pleyeri açır; klikləyəndə də keçilə bilər
        var done = false;
        function dismiss() {
            if (done) return;
            done = true;
            ov.classList.add("is-out");
            setTimeout(function () { if (ov.parentNode) ov.parentNode.removeChild(ov); }, 650);
        }
        ov.addEventListener("click", dismiss);
        setTimeout(dismiss, 2600);
    }

    function renderLessonVideo(container) {
            var url = container.getAttribute("data-video-url") || "";
            var title = container.getAttribute("data-video-title") || "DatalabAcademy lesson video";
            var bunnyEmbed = getBunnyEmbed(url);
            var youtubeId = getYouTubeId(url);
            var driveId = getDriveId(url);
            var rendered = false;

            if (bunnyEmbed) {
                appendBunnyPlayer(container, bunnyEmbed, title);
                rendered = true;
            } else if (driveId) {
                appendDrivePlayer(container, driveId, title);
                rendered = true;
            } else if (youtubeId) {
                appendYouTubePlayer(container, youtubeId, title);
                rendered = true;
            } else if (/\.(mp4|webm|ogg)(\?.*)?$/i.test(url)) {
                appendNativeVideo(container, url, title);
                rendered = true;
            }

            // Branded intro animasiyası (admin paneldən aktiv edilibsə)
            if (rendered && container.getAttribute("data-video-intro") === "1") {
                addIntroOverlay(container);
            }
    }

    function initLessonPlayers() {
        document.querySelectorAll("[data-video-url]").forEach(function (container) {
            renderLessonVideo(container);
        });
    }

    function bindEvents() {
        document.addEventListener("click", function (event) {
            var langLink = event.target.closest("[data-lang]");
            if (langLink) {
                event.preventDefault();
                applyLang(langLink.getAttribute("data-lang"));
            }

            var currencyLink = event.target.closest("[data-currency]");
            if (currencyLink) {
                event.preventDefault();
                applyCurrency(currencyLink.getAttribute("data-currency"));
            }

            if (event.target.closest(".datalab-mobile-toggle")) {
                event.preventDefault();
                document.querySelectorAll(".datalab-mobile-panel").forEach(function (panel) {
                    panel.classList.toggle("is-open");
                });
            }

            if (event.target.closest(".rbt-cart-sidenav-activation")) {
                event.preventDefault();
                document.body.classList.add("cart-sidenav-menu-active");
                document.querySelectorAll(".rbt-cart-side-menu").forEach(function (menu) {
                    menu.classList.add("side-menu-active");
                });
            }

            if (event.target.closest(".minicart-close-button") || event.target.closest(".close_side_menu")) {
                event.preventDefault();
                document.body.classList.remove("cart-sidenav-menu-active");
                document.querySelectorAll(".rbt-cart-side-menu").forEach(function (menu) {
                    menu.classList.remove("side-menu-active");
                });
            }

            var removeLink = event.target.closest("[data-cart-remove]");
            if (removeLink) {
                event.preventDefault();
                var row = removeLink.closest("tr");
                if (!row) return;
                var courseId = row.getAttribute("data-course-id");
                row.classList.add("is-removing");
                window.setTimeout(function () {
                    row.remove();
                    if (courseId) {
                        setCartIds(getCartIds().filter(function (id) { return id !== courseId; }));
                        document.querySelectorAll('[data-mini-course-id="' + courseId + '"]').forEach(function (item) {
                            item.remove();
                        });
                    }
                    updateCartTotals();
                }, 170);
            }

            var miniRemove = event.target.closest("[data-mini-cart-remove]");
            if (miniRemove) {
                event.preventDefault();
                var miniItem = miniRemove.closest("[data-mini-course-id]");
                if (!miniItem) return;
                var miniCourseId = miniItem.getAttribute("data-mini-course-id");
                miniItem.remove();
                setCartIds(getCartIds().filter(function (id) { return id !== miniCourseId; }));
                document.querySelectorAll('.datalab-cart-table tbody tr[data-course-id="' + miniCourseId + '"]').forEach(function (row) {
                    row.remove();
                });
                updateCartTotals();
            }

            var checkoutLink = event.target.closest('a[href="checkout.html"][aria-disabled="true"]');
            if (checkoutLink) {
                event.preventDefault();
            }
        });

        document.addEventListener("submit", function (event) {
            var lang = localStorage.getItem("siteLang") || "az";
            var bag = dict[lang] || dict.az;

            if (event.target.matches(".newsletter-form")) {
                event.preventDefault();
                var nlForm = event.target;
                var nlEmail = nlForm.querySelector('input[type="email"]');
                var nlValue = nlEmail ? nlEmail.value.trim() : "";
                if (!nlValue) return;
                fetch("api/admin.php?action=submit-lead", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ name: nlValue.split("@")[0] || "Abunəçi", email: nlValue, interest: "Newsletter", source: "Newsletter" })
                })
                    .then(function (response) { return response.json(); })
                    .then(function (payload) {
                        showToast(payload.ok ? bag.toast : (payload.message || bag.toast));
                        if (payload.ok) nlForm.reset();
                    })
                    .catch(function () { showToast(bag.toast); });
            }

            if (event.target.matches("[data-datalab-checkout]")) {
                event.preventDefault();
                var coForm = event.target;
                var cartIds = getCartIds();
                if (!cartIds.length) {
                    showToast(bag.cart_empty_title || "Səbət boşdur");
                    return;
                }
                var first = coForm.elements.firstName ? coForm.elements.firstName.value.trim() : "";
                var last = coForm.elements.lastName ? coForm.elements.lastName.value.trim() : "";
                var paymentInput = coForm.querySelector('input[name="payment-method"]:checked');
                var submitBtn = coForm.querySelector('button[type="submit"]');
                if (submitBtn) submitBtn.disabled = true;

                fetch("api/admin.php?action=submit-order", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        customer: (first + " " + last).trim(),
                        phone: coForm.elements.phone ? coForm.elements.phone.value.trim() : "",
                        email: coForm.elements.email ? coForm.elements.email.value.trim() : "",
                        note: coForm.elements.note ? coForm.elements.note.value.trim() : "",
                        payment: paymentInput ? paymentInput.value : "card",
                        courseIds: cartIds
                    })
                })
                    .then(function (response) { return response.json(); })
                    .then(function (payload) {
                        if (!payload.ok) {
                            showToast(payload.message || "Sifariş göndərilmədi. Yenidən yoxla.");
                            return;
                        }
                        setCartIds([]);
                        syncCartDom();
                        updateCartTotals();
                        coForm.reset();
                        showToast(payload.message || bag.order_success);
                    })
                    .catch(function () {
                        showToast("Server əlçatan deyil. Bir az sonra yenidən yoxla.");
                    })
                    .finally(function () {
                        if (submitBtn) submitBtn.disabled = false;
                    });
            }
        });
    }

    function boot() {
        window.DatalabVideo = window.DatalabVideo || {};
        window.DatalabVideo.render = renderLessonVideo;
        window.DatalabVideo.init = initLessonPlayers;
        includeSharedBlocks();
        syncCartDom();
        applyLang(localStorage.getItem("siteLang") || "az");
        applyCurrency(localStorage.getItem("siteCurrency") || "USD");
        updateCartTotals();
        initLessonPlayers();
        optimizeImages();
        bindEvents();
        if (window.feather && typeof window.feather.replace === "function") window.feather.replace();
        // AI mentor chat widget (self-contained; widget guards against double init)
        if (!window.__dlAiChatLoaded) {
            window.__dlAiChatLoaded = true;
            var aiScript = document.createElement("script");
            aiScript.src = "assets/js/datalab-ai-chat.js?v=20260626-ai-2";
            document.body.appendChild(aiScript);
        }
        // Tələbə hesab menyusu (profil/çıxış) — header-dəki [data-student-menu] üçün
        if (!window.__dlStudentAuthLoaded && document.querySelector("[data-student-menu]")) {
            window.__dlStudentAuthLoaded = true;
            var saScript = document.createElement("script");
            saScript.src = "assets/js/datalab-student-auth.js?v=20260627-shop-2";
            document.body.appendChild(saScript);
        }
        // Səbət sayğacı + mağaza düymələri
        if (!window.__dlShopLoaded) {
            window.__dlShopLoaded = true;
            var shopScript = document.createElement("script");
            shopScript.src = "assets/js/datalab-shop.js?v=20260627-shop-3";
            document.body.appendChild(shopScript);
        }
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", boot);
    } else {
        boot();
    }
}());
