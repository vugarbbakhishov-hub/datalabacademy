<?php
declare(strict_types=1);

function dl_student_asset(string $path): string
{
    $file = __DIR__ . '/' . $path;
    $version = is_file($file) ? (string) filemtime($file) : '1';
    return htmlspecialchars($path . '?v=' . $version, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="az">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DatalabAcademy | Tələbə paneli</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
    <link rel="stylesheet" href="<?php echo dl_student_asset('assets/css/vendor/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo dl_student_asset('assets/css/plugins/feather.css'); ?>">
    <link rel="stylesheet" href="<?php echo dl_student_asset('assets/css/plugins/euclid-circulara.css'); ?>">
    <link rel="stylesheet" href="<?php echo dl_student_asset('assets/css/datalab-student-dashboard.css'); ?>">
</head>
<body class="student-portal" data-student-protected>
    <header class="student-site-header">
        <div class="student-header-inner">
            <a class="student-header-logo" href="index.php" aria-label="DatalabAcademy">
                <img src="assets/images/logo/datalab-logo-transparent.png" alt="DatalabAcademy">
            </a>
            <nav class="student-header-nav" aria-label="Əsas naviqasiya">
                <a href="index.php" data-i18n="nav_home">Ana səhifə</a>
                <a href="course-filter-one-open.html" data-i18n="nav_courses">Kurslar</a>
                <a href="blog-with-sidebar" data-i18n="nav_blog">Bloq</a>
                <a href="index.php#contact" data-i18n="nav_contact">Əlaqə</a>
            </nav>
            <div class="student-header-actions">
                <div class="student-language" aria-label="Dil seçimi">
                    <button class="is-active" type="button" data-student-lang="az">AZ</button>
                    <button type="button" data-student-lang="en">EN</button>
                </div>
                <button class="student-icon-button" type="button" data-student-theme aria-label="Tünd rejim">
                    <i class="feather-moon" data-student-theme-icon></i>
                </button>
                <button class="student-mobile-menu" type="button" data-student-menu aria-label="Menyunu aç">
                    <i class="feather-menu"></i>
                </button>
                <div class="student-header-user">
                    <span class="student-avatar" data-dashboard-avatar>DL</span>
                    <div><strong data-dashboard-user-name>Tələbə</strong><small data-dashboard-email></small></div>
                </div>
            </div>
        </div>
    </header>

    <main class="student-portal-main">
        <section class="student-profile-hero" aria-label="Tələbə profili">
            <div class="student-hero-pattern" aria-hidden="true"></div>
            <div class="student-hero-profile">
                <span class="student-avatar student-avatar-large" data-dashboard-avatar>DL</span>
                <div>
                    <span class="student-kicker" data-i18n="student_panel">Tələbə paneli</span>
                    <h1 data-dashboard-user-name>Tələbə</h1>
                    <p><span data-stat-active>0</span> <span data-i18n="active_courses">aktiv kurs</span> · <span data-stat-certificates>0</span> <span data-i18n="certificates">sertifikat</span></p>
                </div>
            </div>
            <a class="student-hero-action" href="course-filter-one-open.html"><span data-i18n="choose_course">Yeni kurs seç</span><i class="feather-arrow-right"></i></a>
        </section>

        <div class="student-dashboard-layout">
            <aside class="student-dashboard-sidebar" data-dashboard-sidebar>
                <div class="student-sidebar-welcome"><span data-i18n="welcome">Xoş gəldin</span>, <strong data-dashboard-first-name>Tələbə</strong></div>
                <nav aria-label="Tələbə paneli">
                    <button class="is-active" type="button" data-student-view="overview"><i class="feather-home"></i><span data-i18n="overview">İcmal</span></button>
                    <button type="button" data-student-view="profile"><i class="feather-user"></i><span data-i18n="profile">Profilim</span></button>
                    <button type="button" data-student-view="courses"><i class="feather-book-open"></i><span data-i18n="my_courses">Kurslarım</span></button>
                    <button type="button" data-student-view="orders"><i class="feather-shopping-bag"></i><span data-i18n="my_orders">Sifarişlərim</span></button>
                    <button type="button" data-student-view="certificates"><i class="feather-award"></i><span data-i18n="certificates">Sertifikatlar</span></button>
                    <a href="student-wishlist.html"><i class="feather-bookmark"></i><span data-i18n="wishlist">İstək siyahısı</span></a>
                    <a href="student-reviews.html"><i class="feather-star"></i><span data-i18n="reviews">Rəylər</span></a>
                    <a href="student-my-quiz-attempts.html"><i class="feather-help-circle"></i><span data-i18n="quiz_attempts">Quiz cəhdlərim</span></a>
                    <button type="button" data-student-view="settings"><i class="feather-settings"></i><span data-i18n="settings">Parametrlər</span></button>
                </nav>
                <button class="student-logout" type="button" data-student-logout><i class="feather-log-out"></i><span data-i18n="logout">Çıxış</span></button>
            </aside>

            <div class="student-dashboard-content">
                <section class="student-view is-active" data-student-panel="overview">
                    <div class="student-section-title"><span data-i18n="learning_center">Öyrənmə mərkəzi</span><h2 data-i18n="overview">İcmal</h2></div>
                    <div class="student-stats">
                        <article><i class="feather-play-circle"></i><div><strong data-stat-active>0</strong><span data-i18n="active_courses">Aktiv kurs</span></div></article>
                        <article><i class="feather-clock"></i><div><strong data-stat-pending>0</strong><span data-i18n="pending_courses">Gözləyən qeydiyyat</span></div></article>
                        <article><i class="feather-trending-up"></i><div><strong data-stat-progress>0%</strong><span data-i18n="average_progress">Orta irəliləyiş</span></div></article>
                        <article><i class="feather-award"></i><div><strong data-stat-certificates>0</strong><span data-i18n="certificates">Sertifikat</span></div></article>
                    </div>
                    <div class="student-section-title student-section-title-row"><div><span data-i18n="course_library">Kurs kitabxanası</span><h2 data-i18n="my_courses">Kurslarım</h2></div><button type="button" data-student-view="courses"><span data-i18n="view_all">Hamısına bax</span><i class="feather-arrow-right"></i></button></div>
                    <div class="student-course-grid" data-dashboard-courses-preview><div class="student-loading">Kurslar yüklənir...</div></div>
                </section>

                <section class="student-view" data-student-panel="profile" hidden>
                    <div class="student-section-title"><span data-i18n="personal_area">Şəxsi sahə</span><h2 data-i18n="profile_settings">Profil məlumatları</h2></div>
                    <div class="student-profile-cover">
                        <div class="student-cover-copy"><strong data-dashboard-user-name>Tələbə</strong><span data-i18n="profile_cover_text">Öyrənmə profilinizi fərdiləşdirin</span></div>
                        <label class="student-avatar-upload" title="Profil şəkli yüklə">
                            <span class="student-avatar student-avatar-xl" data-dashboard-avatar>DL</span>
                            <i class="feather-camera"></i>
                            <input type="file" accept="image/jpeg,image/png,image/webp" data-avatar-input>
                        </label>
                    </div>
                    <form class="student-profile-form" data-profile-form>
                        <label><span data-i18n="full_name">Ad və soyad</span><input name="name" type="text" required maxlength="190"></label>
                        <label><span data-i18n="email">E-poçt</span><input name="email" type="email" readonly></label>
                        <label><span data-i18n="phone">Telefon</span><input name="phone" type="tel" maxlength="40" placeholder="+994 50 000 00 00"></label>
                        <label class="student-form-wide"><span data-i18n="bio">Haqqınızda</span><textarea name="bio" rows="5" maxlength="600"></textarea></label>
                        <div class="student-form-wide"><button class="student-primary-button" type="submit"><i class="feather-save"></i><span data-i18n="save_changes">Dəyişiklikləri saxla</span></button></div>
                    </form>
                </section>

                <section class="student-view" data-student-panel="courses" hidden>
                    <div class="student-section-title student-section-title-row"><div><span data-i18n="course_library">Kurs kitabxanası</span><h2 data-i18n="my_courses">Kurslarım</h2></div><a href="course-filter-one-open.html"><span data-i18n="choose_course">Yeni kurs seç</span><i class="feather-arrow-right"></i></a></div>
                    <div class="student-course-grid" data-dashboard-courses><div class="student-loading">Kurslar yüklənir...</div></div>
                </section>

                <section class="student-view" data-student-panel="orders" hidden>
                    <div class="student-section-title"><span data-i18n="payment_tracking">Ödəniş izləmə</span><h2 data-i18n="my_orders">Sifarişlərim</h2></div>
                    <div class="student-table-wrap"><table><thead><tr><th data-i18n="order">Sifariş</th><th data-i18n="course">Kurs</th><th data-i18n="amount">Məbləğ</th><th data-i18n="payment_method">Ödəniş üsulu</th><th>Status</th><th data-i18n="date">Tarix</th></tr></thead><tbody data-dashboard-orders><tr><td colspan="6">Sifarişlər yüklənir...</td></tr></tbody></table></div>
                </section>

                <section class="student-view" data-student-panel="certificates" hidden>
                    <div class="student-section-title"><span data-i18n="achievements">Nailiyyətlər</span><h2 data-i18n="certificates">Sertifikatlar</h2></div>
                    <div class="student-certificates" data-dashboard-certificates></div>
                </section>

                <section class="student-view" data-student-panel="settings" hidden>
                    <div class="student-section-title"><span data-i18n="security">Təhlükəsizlik</span><h2 data-i18n="password_settings">Şifrə parametrləri</h2></div>
                    <form class="student-password-form" data-password-form>
                        <label><span data-i18n="current_password">Cari şifrə</span><input name="currentPassword" type="password" autocomplete="current-password"></label>
                        <label><span data-i18n="new_password">Yeni şifrə</span><input name="newPassword" type="password" minlength="8" required autocomplete="new-password"></label>
                        <label><span data-i18n="confirm_password">Yeni şifrəni təkrar et</span><input name="confirmPassword" type="password" minlength="8" required autocomplete="new-password"></label>
                        <button class="student-primary-button" type="submit"><i class="feather-lock"></i><span data-i18n="update_password">Şifrəni yenilə</span></button>
                    </form>
                </section>
            </div>
        </div>
    </main>

    <footer class="student-site-footer">
        <div class="student-footer-grid">
            <div><img src="assets/images/logo/datalab-logo-transparent.png" alt="DatalabAcademy"><p data-i18n="footer_about">Praktiki Data Analitika, SQL, Excel və AI təlimləri ilə karyera bacarıqlarınızı inkişaf etdirin.</p></div>
            <div><h3 data-i18n="useful_links">Faydalı linklər</h3><a href="index.php" data-i18n="nav_home">Ana səhifə</a><a href="course-filter-one-open.html" data-i18n="nav_courses">Kurslar</a><a href="blog-with-sidebar" data-i18n="nav_blog">Bloq</a></div>
            <div><h3 data-i18n="contact_info">Əlaqə məlumatı</h3><a href="tel:+994506549737">+994 50 654 97 37</a><a href="mailto:info@datalabacademy.az">info@datalabacademy.az</a><span data-i18n="address">Bakı, Azərbaycan</span></div>
        </div>
        <div class="student-footer-bottom"><span data-i18n="copyright">© 2026 DatalabAcademy. Bütün hüquqlar qorunur.</span><a href="privacy-policy.html" data-i18n="privacy">Məxfilik siyasəti</a></div>
    </footer>

    <div class="student-toast" data-dashboard-toast hidden></div>
    <script src="<?php echo dl_student_asset('assets/js/datalab-student-auth.js'); ?>"></script>
    <script src="<?php echo dl_student_asset('assets/js/datalab-student-dashboard.js'); ?>"></script>
</body>
</html>
