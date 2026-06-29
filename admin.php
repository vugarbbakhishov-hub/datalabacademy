<?php
declare(strict_types=1);

// Admin panel entry. Auth is enforced server-side in api/admin.php:
// all data endpoints require a logged-in session; this page shows the login screen first.
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('X-Robots-Tag: noindex, nofollow');
?>
<!DOCTYPE html>
<html lang="az">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>DatalabAcademy | Admin Panel</title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="theme-color" content="#0b1020">

    <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
    <link rel="shortcut icon" type="image/svg+xml" href="assets/images/favicon.svg">

    <link rel="stylesheet" href="assets/css/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/plugins/feather.css">
    <link rel="stylesheet" href="assets/css/plugins/fontawesome.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/datalab-admin.css?v=20260624-admin-phase4">
</head>

<body class="datalab-admin-body">
    <div class="dl-login-overlay" data-login-overlay>
        <div class="dl-login-card">
            <img class="dl-login-logo" src="assets/images/logo/logo.png" alt="DatalabAcademy">
            <h2>Admin girişi</h2>
            <p>Panelə daxil olmaq üçün hesab məlumatlarını daxil et.</p>
            <form class="dl-login-form" data-login-form>
                <label>E-poçt <input name="email" type="email" required autocomplete="username" placeholder="admin@datalabacademy.az"></label>
                <label>Şifrə <input name="password" type="password" required autocomplete="current-password" placeholder="••••••••"></label>
                <p class="dl-login-error" data-login-error hidden></p>
                <button class="dl-admin-primary dl-login-submit" type="submit"><i class="feather-log-in"></i>Daxil ol</button>
                <button class="dl-login-local" type="button" data-login-local hidden><i class="feather-hard-drive"></i>Local demo rejimində davam et</button>
            </form>
        </div>
    </div>

    <div class="dl-admin-shell">
        <aside class="dl-admin-sidebar" aria-label="Admin navigation">
            <a class="dl-admin-brand" href="admin.php" aria-label="DatalabAcademy Admin">
                <img src="assets/images/logo/logo.png" alt="DatalabAcademy">
                <span>Admin</span>
            </a>

            <p class="dl-admin-nav-label">İdarəetmə</p>
            <nav class="dl-admin-nav">
                <button class="is-active" type="button" data-admin-tab="dashboard">
                    <span class="dl-nav-ico tone-indigo"><i class="feather-grid"></i></span>
                    <span>Dashboard</span>
                </button>
                <button type="button" data-admin-tab="courses">
                    <span class="dl-nav-ico tone-blue"><i class="feather-book-open"></i></span>
                    <span>Kurslar</span>
                </button>
                <button type="button" data-admin-tab="online">
                    <span class="dl-nav-ico tone-violet"><i class="feather-monitor"></i></span>
                    <span>Online Təlimlər</span>
                </button>
                <button type="button" data-admin-tab="online-lessons">
                    <span class="dl-nav-ico tone-cyan"><i class="feather-monitor"></i></span>
                    <span>Online dərslər</span>
                </button>
                <button type="button" data-admin-tab="online-reviews">
                    <span class="dl-nav-ico tone-amber"><i class="feather-star"></i></span>
                    <span>Online Rəylər</span>
                    <span class="dl-nav-badge" data-nav-online-reviews>0</span>
                </button>
                <button type="button" data-admin-tab="orders">
                    <span class="dl-nav-ico tone-emerald"><i class="feather-shopping-bag"></i></span>
                    <span>Sifarişlər</span>
                    <span class="dl-nav-badge" data-nav-orders>0</span>
                </button>
                <button type="button" data-admin-tab="leads">
                    <span class="dl-nav-ico tone-amber"><i class="feather-users"></i></span>
                    <span>Lead-lər</span>
                    <span class="dl-nav-badge" data-nav-leads>0</span>
                </button>
                <button type="button" data-admin-tab="students">
                    <span class="dl-nav-ico tone-cyan"><i class="feather-user-check"></i></span>
                    <span>Tələbələr</span>
                </button>
                <button type="button" data-admin-tab="payments">
                    <span class="dl-nav-ico tone-emerald"><i class="feather-credit-card"></i></span>
                    <span>Ödəniş sorğuları</span>
                    <span class="dl-nav-badge" data-nav-payments>0</span>
                </button>
                <button type="button" data-admin-tab="payment-cards">
                    <span class="dl-nav-ico tone-violet"><i class="feather-layers"></i></span>
                    <span>Ödəniş kartları</span>
                </button>
                <button type="button" data-admin-tab="enrollments">
                    <span class="dl-nav-ico tone-cyan"><i class="feather-book-open"></i></span>
                    <span>Dərs izləmə</span>
                </button>
                <button type="button" data-admin-tab="blog">
                    <span class="dl-nav-ico tone-rose"><i class="feather-file-text"></i></span>
                    <span>Blog</span>
                </button>
            </nav>

            <p class="dl-admin-nav-label">Sayt</p>
            <nav class="dl-admin-nav">
                <button type="button" data-admin-tab="home">
                    <span class="dl-nav-ico tone-indigo"><i class="feather-home"></i></span>
                    <span>Ana səhifə</span>
                </button>
                <button type="button" data-admin-tab="ai-quiz">
                    <span class="dl-nav-ico tone-violet"><i class="feather-cpu"></i></span>
                    <span>AI Quiz</span>
                </button>
                <button type="button" data-admin-tab="ai-chat">
                    <span class="dl-nav-ico tone-blue"><i class="feather-message-square"></i></span>
                    <span>AI Chat Bazası</span>
                </button>
                <button type="button" data-admin-tab="contact">
                    <span class="dl-nav-ico tone-emerald"><i class="feather-phone"></i></span>
                    <span>Əlaqə</span>
                </button>
                <button type="button" data-admin-tab="content">
                    <span class="dl-nav-ico tone-rose"><i class="feather-edit-3"></i></span>
                    <span>Kontent</span>
                </button>
                <button type="button" data-admin-tab="seo">
                    <span class="dl-nav-ico tone-cyan"><i class="feather-search"></i></span>
                    <span>SEO</span>
                </button>
                <button type="button" data-admin-tab="settings">
                    <span class="dl-nav-ico tone-slate"><i class="feather-sliders"></i></span>
                    <span>Ayarlar</span>
                </button>
            </nav>

            <div class="dl-admin-sidebar-footer">
                <a href="index.php">
                    <span class="dl-nav-ico tone-glass"><i class="feather-external-link"></i></span>
                    <span>Sayta bax</span>
                </a>
            </div>
        </aside>

        <main class="dl-admin-main">
            <header class="dl-admin-topbar">
                <button class="dl-admin-menu" type="button" data-admin-menu aria-label="Menyu">
                    <i class="feather-menu"></i>
                </button>
                <div>
                    <p class="dl-admin-kicker">DatalabAcademy · İdarəetmə paneli</p>
                    <h1 id="adminPageTitle">Dashboard</h1>
                </div>
                <div class="dl-admin-top-actions">
                    <span class="dl-admin-live" data-admin-db-state><i></i>Local rejim</span>
                    <button class="dl-admin-icon-btn" type="button" data-admin-export title="Export JSON">
                        <i class="feather-download"></i>
                    </button>
                    <label class="dl-admin-icon-btn" title="Import JSON">
                        <input type="file" accept="application/json" data-admin-import hidden>
                        <i class="feather-upload"></i>
                    </label>
                    <span class="dl-admin-avatar" data-admin-user title="Admin">DA</span>
                    <button class="dl-admin-icon-btn" type="button" data-admin-logout title="Çıxış">
                        <i class="feather-log-out"></i>
                    </button>
                </div>
            </header>

            <!-- ======================= DASHBOARD ======================= -->
            <section class="dl-admin-view is-active" data-admin-view="dashboard">
                <div class="dl-admin-stats">
                    <article class="tone-indigo">
                        <div class="dl-stat-ico"><i class="feather-book-open"></i></div>
                        <span>Kurslar</span>
                        <strong data-stat-courses>0</strong>
                        <svg class="dl-spark" viewBox="0 0 100 32" preserveAspectRatio="none"><polyline data-spark="courses" points="0,26 14,22 28,24 42,16 56,18 70,10 84,12 100,4"/></svg>
                    </article>
                    <article class="tone-emerald">
                        <div class="dl-stat-ico"><i class="feather-shopping-bag"></i></div>
                        <span>Aktiv sifariş</span>
                        <strong data-stat-orders>0</strong>
                        <svg class="dl-spark" viewBox="0 0 100 32" preserveAspectRatio="none"><polyline data-spark="orders" points="0,28 14,24 28,26 42,20 56,14 70,16 84,8 100,6"/></svg>
                    </article>
                    <article class="tone-amber">
                        <div class="dl-stat-ico"><i class="feather-users"></i></div>
                        <span>Lead</span>
                        <strong data-stat-leads>0</strong>
                        <svg class="dl-spark" viewBox="0 0 100 32" preserveAspectRatio="none"><polyline data-spark="leads" points="0,24 14,26 28,18 42,20 56,12 70,14 84,10 100,8"/></svg>
                    </article>
                    <article class="tone-violet">
                        <div class="dl-stat-ico"><i class="feather-trending-up"></i></div>
                        <span>Gəlir potensialı</span>
                        <strong data-stat-revenue>$0</strong>
                        <svg class="dl-spark" viewBox="0 0 100 32" preserveAspectRatio="none"><polyline data-spark="revenue" points="0,30 14,26 28,22 42,24 56,16 70,12 84,10 100,2"/></svg>
                    </article>
                </div>

                <div class="dl-admin-grid">
                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head">
                            <h2><span class="dl-h-ico tone-emerald"><i class="feather-shopping-bag"></i></span>Son sifarişlər</h2>
                            <button type="button" data-admin-tab-jump="orders">Hamısı<i class="feather-arrow-right"></i></button>
                        </div>
                        <div class="dl-admin-table-wrap">
                            <table class="dl-admin-table">
                                <thead><tr><th>Müştəri</th><th>Kurs</th><th>Status</th><th>Məbləğ</th></tr></thead>
                                <tbody data-dashboard-orders></tbody>
                            </table>
                        </div>
                    </section>

                    <div class="dl-admin-side-stack">
                        <section class="dl-admin-panel">
                            <div class="dl-admin-panel-head">
                                <h2><span class="dl-h-ico tone-indigo"><i class="feather-zap"></i></span>Tez əməliyyatlar</h2>
                            </div>
                            <div class="dl-admin-actions-list">
                                <button type="button" data-admin-tab-jump="courses"><i class="feather-plus-circle"></i>Kurs əlavə et</button>
                                <button type="button" data-admin-tab-jump="blog"><i class="feather-file-text"></i>Blog yazısı əlavə et</button>
                                <button type="button" data-admin-tab-jump="lessons"><i class="feather-play-circle"></i>Dərs proqramını qur</button>
                                <button type="button" data-admin-export><i class="feather-download"></i>Backup çıxart</button>
                            </div>
                        </section>

                        <section class="dl-admin-panel">
                            <div class="dl-admin-panel-head">
                                <h2><span class="dl-h-ico tone-violet"><i class="feather-bar-chart-2"></i></span>Kurs dolması</h2>
                            </div>
                            <div class="dl-admin-meters" data-dashboard-meters></div>
                        </section>

                        <section class="dl-admin-panel">
                            <div class="dl-admin-panel-head">
                                <h2><span class="dl-h-ico tone-emerald"><i class="feather-filter"></i></span>Konversiya funnel</h2>
                            </div>
                            <div class="dl-admin-funnel" data-dashboard-funnel></div>
                        </section>
                    </div>
                </div>

                <!-- Aylıq gəlir qrafiki + Lead mənbəyi -->
                <div class="dl-admin-grid dl-dash-bottom">
                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head">
                            <h2><span class="dl-h-ico tone-indigo"><i class="feather-trending-up"></i></span>Aylıq gəlir (son 6 ay)</h2>
                        </div>
                        <canvas id="dlRevenueChart" width="600" height="200" style="width:100%;height:200px;display:block;"></canvas>
                    </section>
                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head">
                            <h2><span class="dl-h-ico tone-amber"><i class="feather-pie-chart"></i></span>Lead mənbəyi</h2>
                        </div>
                        <div class="dl-dash-tags" data-dash-lead-sources></div>
                    </section>
                </div>
            </section>

            <!-- ======================= KURSLAR ======================= -->
            <section class="dl-admin-view" data-admin-view="courses">
                <div class="dl-admin-two-col">
                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head">
                            <h2><span class="dl-h-ico tone-blue"><i class="feather-book-open"></i></span>Kurs siyahısı</h2>
                            <button class="dl-admin-primary" type="button" data-new-course><i class="feather-plus"></i>Yeni kurs</button>
                        </div>
                        <div class="dl-admin-table-wrap">
                            <table class="dl-admin-table">
                                <thead><tr><th>Kurs</th><th>Qiymət</th><th>Status</th><th></th></tr></thead>
                                <tbody data-courses-table></tbody>
                            </table>
                        </div>
                    </section>

                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head">
                            <h2 data-course-form-title><span class="dl-h-ico tone-rose"><i class="feather-edit-2"></i></span>Kurs redaktəsi</h2>
                        </div>
                        <form class="dl-admin-form" data-course-form>
                            <input type="hidden" name="id">
                            <label>Başlıq <input name="title" type="text" required></label>
                            <label>Kateqoriya <input name="category" type="text" required></label>
                            <label>Qiymət USD <input name="price" type="number" min="0" step="1" required></label>
                            <label>Dərs sayı <input name="lessons" type="number" min="1" step="1" required></label>
                            <label>Tələbə sayı <input name="students" type="number" min="0" step="1" required></label>
                            <label>Reytinq (0-5) <input name="rating" type="number" min="0" max="5" step="0.1" required></label>
                            <label>Rəy sayı <input name="reviewCount" type="number" min="0" step="1" required></label>
                            <label>Status
                                <select name="status">
                                    <option value="active">Aktiv</option>
                                    <option value="draft">Qaralama</option>
                                    <option value="archived">Arxiv</option>
                                </select>
                            </label>
                            <label class="dl-admin-full">Şəkil yolu <input name="image" type="text" required></label>
                            <label class="dl-admin-full dl-admin-upload">Şəkil yüklə (PNG, JPG, WEBP, SVG — maks 2MB)
                                <input type="file" accept="image/png,image/jpeg,image/webp,image/svg+xml" data-image-upload>
                            </label>
                            <label class="dl-admin-full">Qısa açıqlama <textarea name="description" rows="4" required></textarea></label>
                            <div class="dl-admin-full dl-admin-section-label"><strong>Offline kurs təqdimatı</strong><span>3D detal səhifəsində görünən məlumatlar</span></div>
                            <label>Format <input name="format" type="text" placeholder="Əyani / Offline"></label>
                            <label>Müddət <input name="duration" type="text" placeholder="8 həftə"></label>
                            <label>Dərs cədvəli <input name="schedule" type="text" placeholder="Həftədə 2 dəfə"></label>
                            <label>Məkan <input name="location" type="text" placeholder="Bakı, təlim mərkəzi"></label>
                            <label>Başlanğıc tarixi <input name="startDate" type="date"></label>
                            <label>Səviyyə <input name="level" type="text" placeholder="Başlanğıc"></label>
                            <label>Dil <input name="language" type="text" placeholder="Azərbaycan"></label>
                            <label>Yer sayı <input name="seats" type="number" min="1" step="1" placeholder="15"></label>
                            <label class="dl-admin-full">Təlimçi <input name="instructor" type="text" placeholder="DatalabAcademy mentor komandası"></label>
                            <label class="dl-admin-full">Spline səhnə URL-i <input name="splineScene" type="url" placeholder="https://prod.spline.design/.../scene.splinecode"></label>
                            <label class="dl-admin-full">Geniş icmal <textarea name="overview" rows="5" placeholder="Kurs kimlər üçündür və hansı nəticəni verir?"></textarea></label>
                            <label class="dl-admin-full">Öyrənmə nəticələri (hər sətirdə bir nəticə) <textarea name="outcomes" rows="5"></textarea></label>
                            <label class="dl-admin-full">Kurs proqramı (hər sətirdə bir mövzu) <textarea name="syllabus" rows="6"></textarea></label>
                            <div class="dl-admin-form-actions">
                                <button class="dl-admin-primary" type="submit"><i class="feather-save"></i>Yadda saxla</button>
                                <button type="button" data-course-reset><i class="feather-rotate-ccw"></i>Sıfırla</button>
                            </div>
                        </form>
                    </section>
                </div>
            </section>

            <!-- ======================= ONLINE TƏLİMLƏR ======================= -->
            <section class="dl-admin-view" data-admin-view="online">
                <div class="dl-admin-two-col">
                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head">
                            <h2><span class="dl-h-ico tone-violet"><i class="feather-monitor"></i></span>Online təlim siyahısı</h2>
                            <button class="dl-admin-primary" type="button" data-new-online><i class="feather-plus"></i>Yeni online təlim</button>
                        </div>
                        <div class="dl-admin-table-wrap">
                            <table class="dl-admin-table">
                                <thead><tr><th>Online təlim</th><th>Qiymət</th><th>Status</th><th></th></tr></thead>
                                <tbody data-online-table></tbody>
                            </table>
                        </div>
                    </section>

                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head">
                            <h2 data-online-form-title><span class="dl-h-ico tone-rose"><i class="feather-edit-2"></i></span>Online təlim redaktəsi</h2>
                        </div>
                        <form class="dl-admin-form" data-online-form>
                            <input type="hidden" name="id">
                            <label>Başlıq <input name="title" type="text" required></label>
                            <label>Slug (URL) <input name="slug" type="text" placeholder="data-analitika-online"></label>
                            <label>Kateqoriya <input name="category" type="text" required></label>
                            <label>Səviyyə <input name="level" type="text" placeholder="Başlanğıc"></label>
                            <label>Qiymət USD <input name="price" type="number" min="0" step="1" required></label>
                            <label>Dərs sayı <input name="lessons" type="number" min="1" step="1" required></label>
                            <label>Tələbə sayı <input name="students" type="number" min="0" step="1" required></label>
                            <label>Reytinq (0-5) <input name="rating" type="number" min="0" max="5" step="0.1" required></label>
                            <label>Rəy sayı <input name="reviewCount" type="number" min="0" step="1" required></label>
                            <label>Status
                                <select name="status">
                                    <option value="active">Aktiv</option>
                                    <option value="draft">Qaralama</option>
                                    <option value="archived">Arxiv</option>
                                </select>
                            </label>
                            <label class="dl-admin-full">Şəkil yolu <input name="image" type="text" required></label>
                            <label class="dl-admin-full dl-admin-upload">Şəkil yüklə (PNG, JPG, WEBP, SVG — maks 2MB)
                                <input type="file" accept="image/png,image/jpeg,image/webp,image/svg+xml" data-online-image-upload>
                            </label>
                            <label class="dl-admin-full">Qısa açıqlama <textarea name="description" rows="4" required></textarea></label>
                            <div class="dl-admin-form-actions">
                                <button class="dl-admin-primary" type="submit"><i class="feather-save"></i>Yadda saxla</button>
                                <button type="button" data-online-reset><i class="feather-rotate-ccw"></i>Sıfırla</button>
                            </div>
                        </form>
                    </section>
                </div>
            </section>

            <!-- ======================= DƏRSLƏR (KURIKULUM) ======================= -->
            <section class="dl-admin-view" data-admin-view="lessons">
                <div class="dl-admin-cur-toolbar">
                    <label class="dl-admin-cur-course">
                        <span>Kurs seç</span>
                        <select data-cur-course></select>
                    </label>
                    <div class="dl-admin-cur-summary" data-cur-summary></div>
                    <div class="dl-admin-cur-actions">
                        <button type="button" data-new-section><i class="feather-folder-plus"></i>Yeni bölmə</button>
                        <button class="dl-admin-primary" type="button" data-new-lesson><i class="feather-plus"></i>Yeni dərs</button>
                    </div>
                </div>

                <div class="dl-admin-two-col dl-admin-cur-grid">
                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head">
                            <h2><span class="dl-h-ico tone-violet"><i class="feather-layers"></i></span>Kurs proqramı</h2>
                        </div>
                        <div class="dl-admin-curriculum" data-curriculum-list></div>
                    </section>

                    <div class="dl-admin-side-stack">
                        <section class="dl-admin-panel" data-section-editor hidden>
                            <div class="dl-admin-panel-head">
                                <h2><span class="dl-h-ico tone-indigo"><i class="feather-folder"></i></span><span data-section-form-title>Yeni bölmə</span></h2>
                            </div>
                            <form class="dl-admin-form" data-section-form>
                                <input type="hidden" name="id">
                                <label>Bölmə başlığı <input name="title" type="text" required placeholder="Məs: Başlanğıc"></label>
                                <label>Müddət etiketi <input name="duration" type="text" placeholder="40 min"></label>
                                <label class="dl-admin-check"><input name="locked" type="checkbox"><span>Kilidli bölmə</span></label>
                                <div class="dl-admin-form-actions">
                                    <button class="dl-admin-primary" type="submit"><i class="feather-save"></i>Bölməni saxla</button>
                                    <button type="button" data-editor-close>Bağla</button>
                                </div>
                            </form>
                        </section>

                        <section class="dl-admin-panel" data-lesson-editor hidden>
                            <div class="dl-admin-panel-head">
                                <h2><span class="dl-h-ico tone-blue"><i class="feather-play"></i></span><span data-lesson-form-title>Yeni dərs</span></h2>
                            </div>
                            <form class="dl-admin-form" data-lesson-form>
                                <input type="hidden" name="id">
                                <label>Bölmə
                                    <select name="sectionId" required></select>
                                </label>
                                <label>Dərs adı <input name="title" type="text" required placeholder="Məs: Kursa giriş"></label>
                                <label>Tip
                                    <select name="type" data-lesson-type>
                                        <option value="video">Video</option>
                                        <option value="article">Mətn dərs</option>
                                        <option value="quiz">Quiz</option>
                                        <option value="assignment">Tapşırıq</option>
                                    </select>
                                </label>
                                <label>Müddət etiketi <input name="duration" type="text" placeholder="30 min"></label>
                                <label data-lesson-video-fields>Video mənbəyi
                                    <select name="source">
                                        <option value="auto">Avtomatik</option>
                                        <option value="youtube">YouTube</option>
                                        <option value="drive">Google Drive</option>
                                        <option value="mp4">MP4</option>
                                    </select>
                                </label>
                                <label class="dl-admin-full" data-lesson-link-field>Video linki <input name="link" type="text" placeholder="Google Drive, MP4 və ya YouTube link"></label>
                                <label class="dl-admin-full">Kontent başlığı / qeyd <input name="note" type="text" placeholder="İstəyə bağlı"></label>
                                <label class="dl-admin-check"><input name="preview" type="checkbox"><span>Pulsuz önizləmə (Preview)</span></label>
                                <div class="dl-admin-form-actions">
                                    <button class="dl-admin-primary" type="submit"><i class="feather-save"></i>Dərsi saxla</button>
                                    <button type="button" data-editor-close>Bağla</button>
                                </div>
                            </form>
                        </section>

                        <section class="dl-admin-panel dl-admin-cur-hint" data-cur-hint>
                            <div class="dl-admin-panel-head">
                                <h2><span class="dl-h-ico tone-amber"><i class="feather-info"></i></span>Necə işləyir?</h2>
                            </div>
                            <ul class="dl-admin-hint-list">
                                <li><i class="feather-folder-plus"></i>Əvvəlcə bölmə yarat, sonra içinə dərs əlavə et.</li>
                                <li><i class="feather-move"></i>Ox düymələri ilə bölmə və dərslərin sırasını dəyiş.</li>
                                <li><i class="feather-eye"></i>"Preview" dərslər saytda pulsuz açıq görünür.</li>
                                <li><i class="feather-save"></i>Bütün dəyişikliklər avtomatik database-ə yazılır.</li>
                            </ul>
                        </section>
                    </div>
                </div>
            </section>

            <!-- ======================= ONLINE DƏRSLƏR ======================= -->
            <section class="dl-admin-view" data-admin-view="online-lessons">
                <div class="dl-admin-cur-toolbar">
                    <label class="dl-admin-cur-course">
                        <span>Online təlim seç</span>
                        <select data-ol-course></select>
                    </label>
                    <div class="dl-admin-cur-summary" data-ol-summary></div>
                    <div class="dl-admin-cur-actions">
                        <button type="button" data-ol-new-section><i class="feather-folder-plus"></i>Yeni bölmə</button>
                        <button class="dl-admin-primary" type="button" data-ol-new-lesson><i class="feather-plus"></i>Yeni dərs</button>
                    </div>
                </div>

                <div class="dl-admin-two-col dl-admin-cur-grid">
                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head">
                            <h2><span class="dl-h-ico tone-cyan"><i class="feather-layers"></i></span>Kurs proqramı</h2>
                        </div>
                        <div class="dl-admin-curriculum" data-ol-curriculum></div>
                    </section>

                    <div class="dl-admin-side-stack">
                        <section class="dl-admin-panel" data-ol-section-editor hidden>
                            <div class="dl-admin-panel-head">
                                <h2><span class="dl-h-ico tone-indigo"><i class="feather-folder"></i></span><span data-ol-section-form-title>Yeni bölmə</span></h2>
                            </div>
                            <form class="dl-admin-form" data-ol-section-form>
                                <input type="hidden" name="id">
                                <label>Bölmə başlığı <input name="title" type="text" required placeholder="Məs: Intro to Course"></label>
                                <label>Müddət etiketi <input name="duration" type="text" placeholder="1hr 30min"></label>
                                <label class="dl-admin-check"><input name="locked" type="checkbox"><span>Kilidli bölmə</span></label>
                                <div class="dl-admin-form-actions">
                                    <button class="dl-admin-primary" type="submit"><i class="feather-save"></i>Bölməni saxla</button>
                                    <button type="button" data-ol-editor-close>Bağla</button>
                                </div>
                            </form>
                        </section>

                        <section class="dl-admin-panel" data-ol-lesson-editor hidden>
                            <div class="dl-admin-panel-head">
                                <h2><span class="dl-h-ico tone-blue"><i class="feather-play"></i></span><span data-ol-lesson-form-title>Yeni dərs</span></h2>
                            </div>
                            <form class="dl-admin-form" data-ol-lesson-form>
                                <input type="hidden" name="id">
                                <label>Bölmə <select name="sectionId" required></select></label>
                                <label>Dərs adı <input name="title" type="text" required placeholder="Məs: Course Intro"></label>
                                <label>Tip
                                    <select name="type">
                                        <option value="video">Video</option>
                                        <option value="article">Mətn dərs</option>
                                        <option value="quiz">Quiz</option>
                                        <option value="assignment">Tapşırıq</option>
                                    </select>
                                </label>
                                <label>Müddət etiketi <input name="duration" type="text" placeholder="30 min"></label>
                                <label class="dl-admin-full">Video / link <input name="link" type="text" placeholder="YouTube, Drive və ya MP4 link (istəyə bağlı)"></label>
                                <label class="dl-admin-check"><input name="preview" type="checkbox"><span>Pulsuz önizləmə (Preview)</span></label>
                                <label class="dl-admin-check"><input name="locked" type="checkbox"><span>Kilidli dərs</span></label>
                                <div class="dl-admin-form-actions">
                                    <button class="dl-admin-primary" type="submit"><i class="feather-save"></i>Dərsi saxla</button>
                                    <button type="button" data-ol-editor-close>Bağla</button>
                                </div>
                            </form>
                        </section>

                        <section class="dl-admin-panel dl-admin-cur-hint">
                            <div class="dl-admin-panel-head">
                                <h2><span class="dl-h-ico tone-amber"><i class="feather-info"></i></span>Necə işləyir?</h2>
                            </div>
                            <ul class="dl-admin-hint-list">
                                <li><i class="feather-folder-plus"></i>Əvvəlcə bölmə yarat, sonra içinə dərs əlavə et.</li>
                                <li><i class="feather-eye"></i>"Preview" dərslər saytda pulsuz açıq görünür.</li>
                                <li><i class="feather-edit-3"></i>Aşağıdakı "Səhifə məlumatları" ilə detal səhifəsini idarə et.</li>
                                <li><i class="feather-save"></i>Bütün dəyişikliklər avtomatik database-ə yazılır.</li>
                            </ul>
                        </section>
                    </div>
                </div>

                <section class="dl-admin-panel" style="margin-top:24px">
                    <div class="dl-admin-panel-head">
                        <h2><span class="dl-h-ico tone-rose"><i class="feather-edit-3"></i></span>Səhifə məlumatları (course-details-3)</h2>
                    </div>
                    <form class="dl-admin-form" data-ol-detail-form>
                        <label>Alt başlıq (subtitle) <input name="subtitle" type="text" placeholder="Qısa təsvir"></label>
                        <label>Təlimçi <input name="instructor" type="text" placeholder="DatalabAcademy"></label>
                        <label>Tarix <input name="date" type="text" placeholder="31 May 2026"></label>
                        <label>Önizləmə video (URL) <input name="previewVideo" type="url" placeholder="https://youtu.be/..."></label>
                        <label>Video intro animasiyası (DatalabAcademy logosu)
                            <select name="videoIntro">
                                <option value="">Xeyr — göstərilməsin</option>
                                <option value="1">Bəli — videodan əvvəl göstərilsin</option>
                            </select>
                        </label>
                        <label>Köhnə qiymət USD <input name="oldPrice" type="number" min="0" step="1" placeholder="220"></label>
                        <label>Endirim mətni <input name="discountText" type="text" placeholder="3 gün qaldı!"></label>
                        <label>Başlama tarixi (etiket) <input name="startDate" type="text" placeholder="Yeni qrup"></label>
                        <label>Qeydiyyat (tutum) <input name="registration" type="text" placeholder="100"></label>
                        <label>Hero tələbə sayı <input name="enrolled" type="text" placeholder="120"></label>
                        <label>Dil <input name="language" type="text" placeholder="Azərbaycan"></label>
                        <label>Tapşırıqlar <input name="quizzes" type="text" placeholder="10"></label>
                        <label>Sertifikat <input name="certificate" type="text" placeholder="Bəli"></label>
                        <label>Praktiki tamamlanma <input name="passPercentage" type="text" placeholder="95%"></label>
                        <label class="dl-admin-full">İcmal — giriş paraqrafı <textarea name="overview" rows="3"></textarea></label>
                        <label class="dl-admin-full">İcmal — yekun paraqraf <textarea name="overviewMore" rows="3"></textarea></label>
                        <label class="dl-admin-full">Nə öyrənəcəksiniz (hər sətirdə bir bənd) <textarea name="outcomes" rows="6"></textarea></label>
                        <label class="dl-admin-full">Tələblər (hər sətirdə bir bənd) <textarea name="requirements" rows="5"></textarea></label>
                        <label class="dl-admin-full">Açıqlama (hər sətirdə bir bənd) <textarea name="descriptionPoints" rows="5"></textarea></label>
                        <div class="dl-admin-form-actions">
                            <button class="dl-admin-primary" type="submit"><i class="feather-save"></i>Səhifə məlumatlarını saxla</button>
                        </div>
                    </form>
                </section>
            </section>

            <!-- ======================= ONLINE RƏYLƏR ======================= -->
            <section class="dl-admin-view" data-admin-view="online-reviews">
                <section class="dl-admin-panel">
                    <div class="dl-admin-panel-head">
                        <h2><span class="dl-h-ico tone-amber"><i class="feather-star"></i></span>Online təlim rəyləri</h2>
                        <span class="dl-admin-note" data-ol-reviews-summary></span>
                    </div>
                    <div class="dl-admin-table-wrap">
                        <table class="dl-admin-table">
                            <thead><tr><th>Tələbə</th><th>Kurs</th><th>Reytinq</th><th>Rəy</th><th>Status</th><th>Tarix</th><th></th></tr></thead>
                            <tbody data-ol-reviews-table></tbody>
                        </table>
                    </div>
                </section>
            </section>

            <!-- ======================= ANA SƏHİFƏ ======================= -->
            <section class="dl-admin-view" data-admin-view="home">
                <div class="dl-admin-two-col">
                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head">
                            <h2><span class="dl-h-ico tone-amber"><i class="feather-message-circle"></i></span>Tələbə rəyləri</h2>
                            <button class="dl-admin-primary" type="button" data-new-ht><i class="feather-plus"></i>Yeni</button>
                        </div>
                        <div class="dl-admin-table-wrap">
                            <table class="dl-admin-table"><thead><tr><th>Ad</th><th>Rol</th><th>Reytinq</th><th></th></tr></thead><tbody data-ht-table></tbody></table>
                        </div>
                    </section>
                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head"><h2 data-ht-form-title><span class="dl-h-ico tone-rose"><i class="feather-edit-2"></i></span>Rəy redaktəsi</h2></div>
                        <form class="dl-admin-form" data-ht-form>
                            <input type="hidden" name="id">
                            <label>Ad <input name="name" type="text" required></label>
                            <label>Rol <input name="role" type="text" placeholder="SQL kursunun tələbəsi"></label>
                            <label>Reytinq (1-5) <input name="rating" type="number" min="1" max="5" step="1" value="5"></label>
                            <label class="dl-admin-full">Şəkil yolu <input name="image" type="text" placeholder="assets/images/testimonial/client-01.png"></label>
                            <label class="dl-admin-full dl-admin-upload">Şəkil yüklə <input type="file" accept="image/*" data-ht-image-upload></label>
                            <label class="dl-admin-full">Mətn <textarea name="text" rows="4" required></textarea></label>
                            <div class="dl-admin-form-actions"><button class="dl-admin-primary" type="submit"><i class="feather-save"></i>Saxla</button><button type="button" data-ht-reset>Sıfırla</button></div>
                        </form>
                    </section>
                </div>

                <div class="dl-admin-two-col" style="margin-top:24px">
                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head">
                            <h2><span class="dl-h-ico tone-violet"><i class="feather-award"></i></span>Sertifikatlar</h2>
                            <button class="dl-admin-primary" type="button" data-new-hc><i class="feather-plus"></i>Yeni</button>
                        </div>
                        <div class="dl-admin-table-wrap">
                            <table class="dl-admin-table"><thead><tr><th>Başlıq</th><th>Etiket</th><th></th></tr></thead><tbody data-hc-table></tbody></table>
                        </div>
                    </section>
                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head"><h2 data-hc-form-title><span class="dl-h-ico tone-rose"><i class="feather-edit-2"></i></span>Sertifikat redaktəsi</h2></div>
                        <form class="dl-admin-form" data-hc-form>
                            <input type="hidden" name="id">
                            <label class="dl-admin-full">Başlıq <input name="title" type="text" required></label>
                            <label class="dl-admin-full">Etiket <input name="tag" type="text" placeholder="Beynəlxalq sertifikat"></label>
                            <label class="dl-admin-full">Şəkil yolu <input name="image" type="text"></label>
                            <label class="dl-admin-full dl-admin-upload">Şəkil yüklə <input type="file" accept="image/*" data-hc-image-upload></label>
                            <label class="dl-admin-full">Açıqlama <textarea name="desc" rows="4"></textarea></label>
                            <div class="dl-admin-form-actions"><button class="dl-admin-primary" type="submit"><i class="feather-save"></i>Saxla</button><button type="button" data-hc-reset>Sıfırla</button></div>
                        </form>
                    </section>
                </div>

                <div class="dl-admin-two-col" style="margin-top:24px">
                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head">
                            <h2><span class="dl-h-ico tone-blue"><i class="feather-grid"></i></span>Portfolio</h2>
                            <button class="dl-admin-primary" type="button" data-new-hp><i class="feather-plus"></i>Yeni</button>
                        </div>
                        <div class="dl-admin-table-wrap">
                            <table class="dl-admin-table"><thead><tr><th>Başlıq</th><th>Etiket</th><th></th></tr></thead><tbody data-hp-table></tbody></table>
                        </div>
                    </section>
                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head"><h2 data-hp-form-title><span class="dl-h-ico tone-rose"><i class="feather-edit-2"></i></span>Portfolio redaktəsi</h2></div>
                        <form class="dl-admin-form" data-hp-form>
                            <input type="hidden" name="id">
                            <label class="dl-admin-full">Başlıq <input name="title" type="text" required></label>
                            <label>Etiket <input name="tag" type="text" placeholder="Power BI"></label>
                            <label>Link (istəyə bağlı) <input name="link" type="text"></label>
                            <label class="dl-admin-full">Şəkil yolu <input name="image" type="text"></label>
                            <label class="dl-admin-full dl-admin-upload">Şəkil yüklə <input type="file" accept="image/*" data-hp-image-upload></label>
                            <label class="dl-admin-full">Açıqlama <textarea name="desc" rows="3"></textarea></label>
                            <div class="dl-admin-form-actions"><button class="dl-admin-primary" type="submit"><i class="feather-save"></i>Saxla</button><button type="button" data-hp-reset>Sıfırla</button></div>
                        </form>
                    </section>
                </div>

                <section class="dl-admin-panel" style="margin-top:24px">
                    <div class="dl-admin-panel-head"><h2><span class="dl-h-ico tone-emerald"><i class="feather-zap"></i></span>Hero badge-lər və statistika</h2></div>
                    <form class="dl-admin-form" data-hs-form>
                        <label>Aktiv tələbə (rəqəm) <input name="statActive" type="text" placeholder="1000+"></label>
                        <label>Aktiv tələbə etiketi <input name="statActiveLabel" type="text" placeholder="Aktiv Tələbə"></label>
                        <label>Reytinq <input name="statRating" type="text" placeholder="⭐ 4.9"></label>
                        <label>Reytinq etiketi <input name="statRatingLabel" type="text" placeholder="Ortalama Reytinq"></label>
                        <label class="dl-admin-full">"Yeni" etiketi <input name="newTag" type="text" placeholder="🔥 Yeni Kurslar!"></label>
                        <div class="dl-admin-full dl-admin-section-label"><strong>Üzən badge-lər (4 ədəd)</strong><span>İkon adı (feather-*), rəng və mətn</span></div>
                        <?php for ($bi = 0; $bi < 4; $bi++): ?>
                        <label>Badge <?php echo $bi + 1; ?> ikon <input name="badge<?php echo $bi; ?>icon" type="text" placeholder="feather-bar-chart-2"></label>
                        <label>Badge <?php echo $bi + 1; ?> rəng <input name="badge<?php echo $bi; ?>color" type="text" placeholder="#2f57ef"></label>
                        <label class="dl-admin-full">Badge <?php echo $bi + 1; ?> mətn <input name="badge<?php echo $bi; ?>text" type="text"></label>
                        <?php endfor; ?>
                        <div class="dl-admin-form-actions"><button class="dl-admin-primary" type="submit"><i class="feather-save"></i>Statistikanı saxla</button></div>
                    </form>
                </section>
            </section>

            <!-- ======================= AI QUIZ ======================= -->
            <section class="dl-admin-view" data-admin-view="ai-quiz">
                <div class="dl-admin-two-col">
                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head">
                            <h2><span class="dl-h-ico tone-violet"><i class="feather-cpu"></i></span>AI ilə quiz yarat</h2>
                        </div>
                        <form class="dl-admin-form" data-aiq-form>
                            <label class="dl-admin-full">Dərs mətni <textarea name="text" rows="10" placeholder="Dərs mətnini bura yapışdırın — AI bundan suallar hazırlayacaq." required></textarea></label>
                            <label>Sual sayı <input name="count" type="number" min="1" max="10" step="1" value="5"></label>
                            <label>Başlıq (saxlamaq üçün) <input name="title" type="text" placeholder="Məs: SQL JOIN-lar"></label>
                            <div class="dl-admin-form-actions">
                                <button class="dl-admin-primary" type="submit" data-aiq-generate><i class="feather-zap"></i>Generasiya et</button>
                                <button type="button" data-aiq-save hidden><i class="feather-save"></i>Saxla</button>
                            </div>
                            <p class="dl-admin-note" data-aiq-msg></p>
                        </form>
                        <div data-aiq-result></div>
                    </section>

                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head">
                            <h2><span class="dl-h-ico tone-blue"><i class="feather-list"></i></span>Saxlanmış quizlər</h2>
                        </div>
                        <div data-aiq-saved></div>
                    </section>
                </div>
            </section>

            <!-- ======================= AI CHAT BAZASI ======================= -->
            <section class="dl-admin-view" data-admin-view="ai-chat">
                <div class="dl-admin-two-col">
                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head">
                            <h2><span class="dl-h-ico tone-blue"><i class="feather-message-square"></i></span>Bilik bazası</h2>
                            <button class="dl-admin-primary" type="button" data-new-ak><i class="feather-plus"></i>Yeni</button>
                        </div>
                        <p class="dl-admin-note">Buraya əlavə etdiyiniz məlumatlar (FAQ, qaydalar, kampaniyalar, əlavə izahlar) chatbot tərəfindən istifadə olunur. Kurslar və əlaqə avtomatik əlavə olunur.</p>
                        <div class="dl-admin-table-wrap">
                            <table class="dl-admin-table"><thead><tr><th>Başlıq</th><th>Məzmun</th><th></th></tr></thead><tbody data-ak-table></tbody></table>
                        </div>
                    </section>
                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head"><h2 data-ak-form-title><span class="dl-h-ico tone-rose"><i class="feather-edit-2"></i></span>Bilik redaktəsi</h2></div>
                        <form class="dl-admin-form" data-ak-form>
                            <input type="hidden" name="id">
                            <label class="dl-admin-full">Başlıq <input name="title" type="text" placeholder="Məs: Ödəniş şərtləri"></label>
                            <label class="dl-admin-full">Məzmun <textarea name="content" rows="6" required placeholder="Chatbot-un bilməli olduğu məlumatı yazın."></textarea></label>
                            <div class="dl-admin-form-actions"><button class="dl-admin-primary" type="submit"><i class="feather-save"></i>Saxla</button><button type="button" data-ak-reset>Sıfırla</button></div>
                        </form>
                    </section>
                </div>
            </section>

            <!-- ======================= ƏLAQƏ ======================= -->
            <section class="dl-admin-view" data-admin-view="contact">
                <section class="dl-admin-panel">
                    <div class="dl-admin-panel-head"><h2><span class="dl-h-ico tone-emerald"><i class="feather-phone"></i></span>Əlaqə məlumatı</h2></div>
                    <p class="dl-admin-note">Bu məlumatlar saytın footer-ində, əlaqə bölməsində və AI chatbot-da istifadə olunur.</p>
                    <form class="dl-admin-form" data-contact-form>
                        <label>Telefon <input name="phone" type="text" placeholder="+994 50 654 97 37"></label>
                        <label>E-poçt <input name="email" type="email" placeholder="info@datalabacademy.az"></label>
                        <label class="dl-admin-full">Ünvan <input name="address" type="text" placeholder="Bakı, Azərbaycan"></label>
                        <label>Facebook <input name="facebook" type="text" placeholder="https://facebook.com/..."></label>
                        <label>Instagram <input name="instagram" type="text" placeholder="https://instagram.com/..."></label>
                        <label>LinkedIn <input name="linkedin" type="text" placeholder="https://linkedin.com/..."></label>
                        <label>Twitter / X <input name="twitter" type="text" placeholder="https://x.com/..."></label>
                        <label>İş saatları <input name="workingHours" type="text" placeholder="B.e – Cümə: 10:00 – 19:00"></label>
                        <label class="dl-admin-full">Footer haqqında mətn <textarea name="footerAbout" rows="3"></textarea></label>
                        <label class="dl-admin-full">Xəbər bülleteni başlığı <input name="newsletterTitle" type="text" placeholder="Xəbər bülleteni"></label>
                        <label class="dl-admin-full">Xəbər bülleteni açıqlaması <textarea name="newsletterDesc" rows="2"></textarea></label>
                        <div class="dl-admin-full dl-admin-section-label"><strong>Əlaqə səhifəsi (contact.php)</strong><span>Ana səhifədə "Əlaqə"yə klik edəndə açılan səhifə</span></div>
                        <label class="dl-admin-full">Səhifə alt başlığı <textarea name="pageSubtitle" rows="2" placeholder="Suallarınız üçün bizimlə əlaqə saxlayın..."></textarea></label>
                        <label class="dl-admin-full">Xəritə embed kodu (Google Maps iframe) <textarea name="mapEmbed" rows="3" placeholder="&lt;iframe src=... &gt;&lt;/iframe&gt; (boş olsa Bakı xəritəsi göstərilir)"></textarea></label>
                        <div class="dl-admin-form-actions"><button class="dl-admin-primary" type="submit"><i class="feather-save"></i>Yadda saxla</button></div>
                    </form>
                </section>
            </section>

            <!-- ======================= SİFARİŞLƏR ======================= -->
            <section class="dl-admin-view" data-admin-view="orders">
                <section class="dl-admin-panel">
                    <div class="dl-admin-panel-head">
                        <h2><span class="dl-h-ico tone-emerald"><i class="feather-shopping-bag"></i></span>Sifarişlər</h2>
                        <div class="dl-admin-panel-actions">
                            <button type="button" data-export-orders-csv><i class="feather-download"></i>CSV</button>
                            <button class="dl-admin-primary" type="button" data-new-order><i class="feather-plus"></i>Demo sifariş</button>
                        </div>
                    </div>
                    <div class="dl-admin-filterbar">
                        <div class="dl-admin-search">
                            <i class="feather-search"></i>
                            <input type="search" placeholder="Axtar: müştəri, ID, telefon, kurs" data-order-search>
                        </div>
                        <select data-order-filter>
                            <option value="all">Bütün statuslar</option>
                            <option value="new">Yeni</option>
                            <option value="pending">Gözləyir</option>
                            <option value="paid">Ödənilib</option>
                            <option value="cancelled">Ləğv</option>
                        </select>
                    </div>
                    <div class="dl-admin-bulk" data-bulk="orders" hidden>
                        <span data-bulk-count="orders">0 seçilib</span>
                        <button type="button" data-bulk-cycle="orders"><i class="feather-repeat"></i>Status dəyiş</button>
                        <button type="button" class="dl-bulk-danger" data-bulk-del="orders"><i class="feather-trash-2"></i>Sil</button>
                    </div>
                    <div class="dl-admin-table-wrap">
                        <table class="dl-admin-table">
                            <thead><tr><th class="dl-admin-check"><input type="checkbox" data-selall="orders" aria-label="Hamısını seç"></th><th>ID</th><th>Müştəri</th><th>Kurs</th><th>Əlaqə</th><th>Tarix</th><th>Status</th><th>Məbləğ</th><th></th></tr></thead>
                            <tbody data-orders-table></tbody>
                        </table>
                    </div>
                    <div class="dl-admin-pager" data-pager="orders"></div>
                </section>
            </section>

            <!-- ======================= LEAD-LƏR ======================= -->
            <section class="dl-admin-view" data-admin-view="leads">
                <section class="dl-admin-panel">
                    <div class="dl-admin-panel-head">
                        <h2><span class="dl-h-ico tone-amber"><i class="feather-users"></i></span>Lead-lər</h2>
                        <div class="dl-admin-panel-actions">
                            <div class="dl-lead-viewtoggle" role="group" aria-label="Görünüş">
                                <button type="button" class="is-active" data-lead-mode="table" title="Cədvəl"><i class="feather-list"></i></button>
                                <button type="button" data-lead-mode="kanban" title="Kanban"><i class="feather-trello"></i></button>
                            </div>
                            <button type="button" data-export-leads-csv><i class="feather-download"></i>CSV</button>
                            <button class="dl-admin-primary" type="button" data-new-lead><i class="feather-plus"></i>Lead əlavə et</button>
                        </div>
                    </div>
                    <div class="dl-admin-filterbar">
                        <div class="dl-admin-search">
                            <i class="feather-search"></i>
                            <input type="search" placeholder="Axtar: ad, mənbə, maraq" data-lead-search>
                        </div>
                        <select data-lead-filter>
                            <option value="all">Bütün statuslar</option>
                            <option value="new">Yeni</option>
                            <option value="pending">Gözləyir</option>
                            <option value="paid">Ödənilib</option>
                            <option value="cancelled">Ləğv</option>
                        </select>
                    </div>
                    <div class="dl-admin-bulk" data-bulk="leads" hidden>
                        <span data-bulk-count="leads">0 seçilib</span>
                        <button type="button" data-bulk-cycle="leads"><i class="feather-repeat"></i>Status dəyiş</button>
                        <button type="button" class="dl-bulk-danger" data-bulk-del="leads"><i class="feather-trash-2"></i>Sil</button>
                    </div>
                    <div data-lead-view="table">
                        <div class="dl-admin-table-wrap">
                            <table class="dl-admin-table">
                                <thead><tr><th class="dl-admin-check"><input type="checkbox" data-selall="leads" aria-label="Hamısını seç"></th><th>Ad</th><th>Mənbə</th><th>Maraq</th><th>Status</th><th>Tarix</th><th></th></tr></thead>
                                <tbody data-leads-table></tbody>
                            </table>
                        </div>
                        <div class="dl-admin-pager" data-pager="leads"></div>
                    </div>
                    <div class="dl-lead-kanban" data-lead-view="kanban" data-lead-kanban hidden></div>
                </section>
            </section>
            <!-- ======================= TƏLƏBƏLƏRr ======================= -->
            <section class="dl-admin-view" data-admin-view="students">
                <section class="dl-admin-panel">
                    <div class="dl-admin-panel-head">
                        <h2><span class="dl-h-ico tone-cyan"><i class="feather-user-check"></i></span>Tələbələr <span class="dl-admin-count" data-student-count>0</span></h2>
                        <div class="dl-admin-panel-actions">
                            <button type="button" data-export-students-csv><i class="feather-download"></i>CSV</button>
                            <button type="button" data-students-refresh><i class="feather-refresh-cw"></i>Yenilə</button>
                        </div>
                    </div>
                    <div class="dl-admin-filterbar">
                        <div class="dl-admin-search">
                            <i class="feather-search"></i>
                            <input type="search" placeholder="Axtar: ad, email, telefon" data-student-search>
                        </div>
                    </div>
                    <div class="dl-admin-table-wrap">
                        <table class="dl-admin-table">
                            <thead><tr><th></th><th>Ad / Email</th><th>Telefon</th><th>Giriş</th><th>Status</th><th>Son giriş</th><th></th></tr></thead>
                            <tbody data-students-table>
                                <tr><td colspan="7" class="dl-admin-empty-cell">Tələbələr tabını açdıqda yüklənəcək...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </section>

            <!-- ======================= ÖDƏNİŞ SORĞULARI (kart-kart) ======================= -->
            <section class="dl-admin-view" data-admin-view="payments">
                <section class="dl-admin-panel">
                    <div class="dl-admin-panel-head">
                        <h2><span class="dl-h-ico tone-emerald"><i class="feather-credit-card"></i></span>Ödəniş sorğuları (kart-kart)</h2>
                        <div class="dl-admin-panel-actions">
                            <select data-shoporder-filter>
                                <option value="pending">Gözləyən</option>
                                <option value="all">Hamısı</option>
                                <option value="paid">Təsdiqlənmiş</option>
                                <option value="rejected">Rədd edilmiş</option>
                            </select>
                        </div>
                    </div>
                    <p class="dl-admin-hint">Tələbə kart-kart ödəniş edib “Ödənişi etdim” deyəndə sorğu burada görünür. Ödənişi yoxlayıb <b>Təsdiqlə</b>-yə bassanız tələbəyə kurslara giriş açılır.</p>
                    <div class="dl-admin-table-wrap">
                        <table class="dl-admin-table">
                            <thead><tr><th>Sifariş</th><th>Tələbə</th><th>Kurslar</th><th>Kart</th><th>Məbləğ</th><th>Tarix</th><th>Status</th><th></th></tr></thead>
                            <tbody data-shoporders-table><tr><td colspan="8" class="dl-admin-empty-cell">Yüklənir…</td></tr></tbody>
                        </table>
                    </div>
                </section>
            </section>

            <!-- ======================= ÖDƏNİŞ KARTLARI ======================= -->
            <section class="dl-admin-view" data-admin-view="payment-cards">
                <section class="dl-admin-panel">
                    <div class="dl-admin-panel-head">
                        <h2><span class="dl-h-ico tone-violet"><i class="feather-layers"></i></span>Ödəniş kartları</h2>
                        <div class="dl-admin-panel-actions">
                            <button class="dl-admin-primary" type="button" data-card-new><i class="feather-plus"></i>Yeni kart</button>
                        </div>
                    </div>
                    <p class="dl-admin-hint">Bu kartlar checkout səhifəsində tələbələrə kart-kart ödəniş üçün göstərilir. Hər kart üçün dizayn rəngi seçə bilərsiniz.</p>
                    <div data-cards-grid class="dl-cards-admin-grid"><div class="dl-admin-empty-cell">Yüklənir…</div></div>
                    <form class="dl-admin-form" data-card-form hidden>
                        <input type="hidden" name="id">
                        <div class="dl-admin-form-grid">
                            <label>Bank adı <input name="bank" type="text" placeholder="Kapital Bank" required></label>
                            <label>Kart nömrəsi <input name="number" type="text" placeholder="4169 7388 0000 0000" required></label>
                            <label>Kart sahibi <input name="holder" type="text" placeholder="VUGAR BAKHISHOV"></label>
                            <label>Qeyd <input name="note" type="text" placeholder="m10 / köçürmədə ad yazın"></label>
                            <label>Dizayn rəngi
                                <select name="theme">
                                    <option value="violet">Bənövşəyi</option>
                                    <option value="ocean">Mavi (ocean)</option>
                                    <option value="emerald">Yaşıl (emerald)</option>
                                    <option value="sunset">Narıncı (sunset)</option>
                                    <option value="dark">Tünd (dark)</option>
                                    <option value="gold">Qızılı (gold)</option>
                                </select>
                            </label>
                            <label>Status
                                <select name="status"><option value="active">Aktiv</option><option value="hidden">Gizli</option></select>
                            </label>
                        </div>
                        <div class="dl-admin-form-actions">
                            <button class="dl-admin-primary" type="submit"><i class="feather-save"></i>Yadda saxla</button>
                            <button type="button" class="dl-admin-ghost" data-card-cancel>Ləğv et</button>
                        </div>
                    </form>
                </section>
            </section>

            <!-- ======================= DƏRS İZLƏMƏ (enrollments) ======================= -->
            <section class="dl-admin-view" data-admin-view="enrollments">
                <section class="dl-admin-panel">
                    <div class="dl-admin-panel-head">
                        <h2><span class="dl-h-ico tone-cyan"><i class="feather-book-open"></i></span>Dərs izləmə və qeydiyyatlar</h2>
                        <div class="dl-admin-panel-actions">
                            <div class="dl-admin-search"><i class="feather-search"></i><input type="search" placeholder="Tələbə adı / email" data-enroll-search></div>
                        </div>
                    </div>
                    <p class="dl-admin-hint">Hər tələbənin qoşulduğu kurslar və neçə dərsə baxdığı / tamamladığı. Girişi ləğv edə və ya bərpa edə bilərsiniz.</p>
                    <div data-enrollments-root><div class="dl-admin-empty-cell">Yüklənir…</div></div>
                </section>
            </section>

            <!-- ======================= BLOG ======================= -->
            <section class="dl-admin-view" data-admin-view="blog">
                <div class="dl-admin-two-col">
                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head">
                            <h2><span class="dl-h-ico tone-rose"><i class="feather-file-text"></i></span>Blog yazıları</h2>
                            <button type="button" data-new-blog class="dl-admin-primary"><i class="feather-plus"></i>Yeni yazı</button>
                        </div>
                        <div class="dl-admin-filterbar">
                            <div class="dl-admin-search">
                                <i class="feather-search"></i>
                                <input type="search" placeholder="Axtar: başlıq, kateqoriya" data-blog-search>
                            </div>
                            <select data-blog-filter>
                                <option value="all">Bütün statuslar</option>
                                <option value="active">Aktiv</option>
                                <option value="draft">Qaralama</option>
                                <option value="archived">Arxiv</option>
                            </select>
                        </div>
                        <div class="dl-admin-table-wrap">
                            <table class="dl-admin-table">
                                <thead><tr><th></th><th>Başlıq / Kateqoriya</th><th>Tarix</th><th>Status</th><th></th></tr></thead>
                                <tbody data-blog-table></tbody>
                            </table>
                        </div>
                    </section>

                    <section class="dl-admin-panel" data-blog-editor hidden>
                        <div class="dl-admin-panel-head">
                            <h2><span class="dl-h-ico tone-rose"><i class="feather-edit-3"></i></span><span data-blog-form-title>Yeni blog yazısı</span></h2>
                            <button type="button" data-blog-editor-close>Bağla</button>
                        </div>
                        <form class="dl-admin-form" data-blog-form>
                            <input type="hidden" name="id">
                            <div class="dl-admin-full dl-admin-section-label"><strong>Azərbaycan dili</strong></div>
                            <label class="dl-admin-full">Başlıq (Az) <input name="titleAz" type="text" required placeholder="Yazının başlığı"></label>
                            <label class="dl-admin-full">Qısa açıqlama (Az) <textarea name="excerptAz" rows="3" placeholder="Qısa xülasə"></textarea></label>
                            <label class="dl-admin-full">Məzmun (Az) <textarea name="contentAz" rows="8" placeholder="HTML və ya düz mətn"></textarea></label>
                            <div class="dl-admin-full dl-admin-section-label"><strong>English</strong></div>
                            <label class="dl-admin-full">Title (En) <input name="titleEn" type="text" placeholder="English title"></label>
                            <label class="dl-admin-full">Excerpt (En) <textarea name="excerptEn" rows="3" placeholder="Short summary"></textarea></label>
                            <label class="dl-admin-full">Content (En) <textarea name="contentEn" rows="8" placeholder="HTML or plain text"></textarea></label>
                            <div class="dl-admin-full dl-admin-section-label"><strong>Metadata</strong></div>
                            <label>Slug <input name="slug" type="text" placeholder="blog-yazisi-adi"></label>
                            <label>Kateqoriya <input name="category" type="text" placeholder="Data, SQL, AI..."></label>
                            <label class="dl-admin-full">Şəkil URL <input name="image" type="text" placeholder="assets/images/..."></label>
                            <label class="dl-admin-full dl-admin-upload">Şəkil yüklə (PNG, JPG, WEBP — maks 2MB)
                                <input type="file" accept="image/png,image/jpeg,image/webp" data-image-upload>
                            </label>
                            <label>Müəllif <input name="author" type="text" placeholder="DatalabAcademy"></label>
                            <label>Oxuma vaxtı (Az) <input name="readTimeAz" type="text" placeholder="5 dəq"></label>
                            <label>Read time (En) <input name="readTimeEn" type="text" placeholder="5 min"></label>
                            <label>Nəşr tarixi <input name="publishedDate" type="date"></label>
                            <label>Status
                                <select name="status">
                                    <option value="active">Aktiv</option>
                                    <option value="draft">Qaralama</option>
                                    <option value="archived">Arxiv</option>
                                </select>
                            </label>
                            <div class="dl-admin-full dl-admin-section-label"><strong>SEO</strong></div>
                            <label class="dl-admin-full">SEO Başlıq <input name="seoTitle" type="text"></label>
                            <label class="dl-admin-full">SEO Açıqlama <textarea name="seoDescription" rows="2"></textarea></label>
                            <label class="dl-admin-full">Keywords <input name="seoKeywords" type="text"></label>
                            <div class="dl-admin-form-actions">
                                <button class="dl-admin-primary" type="submit"><i class="feather-save"></i>Yadda saxla</button>
                                <button type="button" data-blog-editor-close><i class="feather-x"></i>Ləğv et</button>
                            </div>
                        </form>
                    </section>
                </div>
            </section>

            <!-- ======================= KONTENT ======================= -->

            <section class="dl-admin-view" data-admin-view="content">
                <section class="dl-admin-panel">
                    <div class="dl-admin-panel-head">
                        <h2><span class="dl-h-ico tone-rose"><i class="feather-edit-3"></i></span>Sayt kontenti</h2>
                    </div>
                    <form class="dl-admin-form dl-admin-content-form" data-content-form>
                        <label>Hero başlıq <input name="heroTitle" type="text"></label>
                        <label>Hero CTA <input name="heroCta" type="text"></label>
                        <label class="dl-admin-full">Hero açıqlama <textarea name="heroText" rows="4"></textarea></label>
                        <label>Telefon <input name="phone" type="text"></label>
                        <label>E-poçt <input name="email" type="email"></label>
                        <label class="dl-admin-full">Footer haqqında <textarea name="footerText" rows="4"></textarea></label>
                        <div class="dl-admin-form-actions">
                            <button class="dl-admin-primary" type="submit"><i class="feather-save"></i>Kontenti saxla</button>
                        </div>
                    </form>
                </section>
            </section>

            <!-- ======================= SEO ======================= -->
            <section class="dl-admin-view" data-admin-view="seo">
                <section class="dl-admin-panel">
                    <div class="dl-admin-panel-head">
                        <h2><span class="dl-h-ico tone-cyan"><i class="feather-search"></i></span>SEO ayarları</h2>
                    </div>
                    <form class="dl-admin-form" data-seo-form>
                        <label>Site title <input name="siteTitle" type="text"></label>
                        <label>Canonical domain <input name="domain" type="url"></label>
                        <label class="dl-admin-full">Meta description <textarea name="description" rows="3"></textarea></label>
                        <label class="dl-admin-full">Keywords <textarea name="keywords" rows="3"></textarea></label>
                        <label>OG image <input name="ogImage" type="text"></label>
                        <label>Robots <input name="robots" type="text"></label>
                        <div class="dl-admin-form-actions">
                            <button class="dl-admin-primary" type="submit"><i class="feather-save"></i>SEO saxla</button>
                        </div>
                    </form>
                </section>
            </section>

            <!-- ======================= AYARLAR ======================= -->
            <section class="dl-admin-view" data-admin-view="settings">
                <div class="dl-admin-two-col">
                    <div class="dl-admin-side-stack">
                        <section class="dl-admin-panel">
                            <div class="dl-admin-panel-head">
                                <h2><span class="dl-h-ico tone-slate"><i class="feather-sliders"></i></span>Sistem</h2>
                            </div>
                            <div class="dl-admin-settings">
                                <button type="button" data-seed-reset><i class="feather-refresh-cw"></i>Demo datanı bərpa et</button>
                                <button type="button" data-clear-data><i class="feather-trash-2"></i>Admin datasını təmizlə</button>
                                <a href="robots.txt" target="_blank" rel="noopener"><i class="feather-file-text"></i>robots.txt</a>
                                <a href="sitemap.xml" target="_blank" rel="noopener"><i class="feather-map"></i>sitemap.xml</a>
                            </div>
                            <p class="dl-admin-note">Admin panel MySQL API ilə işləyir. Hər saxlamada gündəlik backup <code>api/backups/</code> qovluğuna yazılır (son 14 gün saxlanır).</p>
                        </section>

                        <section class="dl-admin-panel">
                            <div class="dl-admin-panel-head">
                                <h2><span class="dl-h-ico tone-rose"><i class="feather-key"></i></span>Şifrəni dəyiş</h2>
                            </div>
                            <!-- Məcburi şifrə dəyişim xəbərdarlığı -->
                            <div class="dl-admin-alert dl-admin-alert--danger" data-must-change-pw hidden>
                                <i class="feather-alert-triangle"></i>
                                <div>
                                    <strong>Təhlükəsizlik xəbərdarlığı!</strong>
                                    <span>Sistemə ilk dəfə daxil oldunuz. Default şifrəni aşağıda dərhal dəyişdirin. Şifrə: ən azı 8 simvol, 1 böyük hərf, 1 kiçik hərf, 1 rəqəm.</span>
                                </div>
                            </div>
                            <form class="dl-admin-form" data-password-form>
                                <label class="dl-admin-full">Cari şifrə <input name="oldPassword" type="password" required autocomplete="current-password"></label>
                                <label>Yeni şifrə <input name="newPassword" type="password" required minlength="8" autocomplete="new-password"></label>
                                <label>Yeni şifrə (təkrar) <input name="newPassword2" type="password" required minlength="8" autocomplete="new-password"></label>
                                <div class="dl-admin-form-actions">
                                    <button class="dl-admin-primary" type="submit"><i class="feather-save"></i>Şifrəni yenilə</button>
                                </div>
                            </form>
                        </section>
                    </div>

                    <section class="dl-admin-panel">
                        <div class="dl-admin-panel-head">
                            <h2><span class="dl-h-ico tone-cyan"><i class="feather-activity"></i></span>Son əməliyyatlar</h2>
                            <button type="button" data-audit-refresh><i class="feather-refresh-cw"></i>Yenilə</button>
                        </div>
                        <div class="dl-admin-audit" data-audit-list>
                            <p class="dl-admin-note">Yüklənir...</p>
                        </div>
                    </section>
                </div>
            </section>
        </main>
    </div>

    <div class="dl-admin-toast" data-admin-toast></div>

    <!-- Sifariş detallı modalı -->
    <div class="dl-order-modal" id="dlOrderModal" hidden>
        <div class="dl-order-modal-backdrop" data-modal-close></div>
        <div class="dl-order-modal-card">
            <div class="dl-order-modal-head">
                <h2><i class="feather-shopping-bag"></i> Sifariş <span data-modal-order-id></span></h2>
                <button type="button" class="dl-admin-icon-btn" data-modal-close><i class="feather-x"></i></button>
            </div>
            <div class="dl-order-modal-body">
                <div class="dl-order-modal-row"><span>Müştəri</span><strong data-modal-customer></strong></div>
                <div class="dl-order-modal-row"><span>Kurs</span><strong data-modal-course></strong></div>
                <div class="dl-order-modal-row"><span>Telefon</span><strong data-modal-phone></strong></div>
                <div class="dl-order-modal-row"><span>Məbləğ</span><strong data-modal-amount></strong></div>
                <div class="dl-order-modal-row"><span>Tarix</span><strong data-modal-date></strong></div>
                <div class="dl-order-modal-row"><span>Status</span><span data-modal-status></span></div>
            </div>
            <div class="dl-order-modal-foot">
                <a href="#" class="dl-admin-primary dl-wa-btn" data-modal-wa target="_blank" rel="noopener">
                    <i class="feather-message-circle"></i>WhatsApp
                </a>
                <button type="button" data-modal-close>Bağla</button>
            </div>
        </div>
    </div>

    <style>
        .dl-cards-admin-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;margin-bottom:20px}
        .dl-admin-bankcard{position:relative;border-radius:16px;padding:18px;color:#fff;min-height:160px;box-shadow:0 10px 26px rgba(15,23,42,.18);display:flex;flex-direction:column;gap:8px;overflow:hidden}
        .dl-admin-bankcard .bc-bank{font-weight:800;font-size:15px}
        .dl-admin-bankcard .bc-num{font-family:"Courier New",monospace;font-size:18px;letter-spacing:1.5px;font-weight:700;margin-top:auto}
        .dl-admin-bankcard .bc-bottom{font-size:12px;text-transform:uppercase;letter-spacing:.5px;opacity:.92}
        .dl-admin-bankcard .bc-note{font-size:11px;opacity:.82}
        .dl-admin-bankcard .bc-actions{position:absolute;top:10px;right:12px;display:flex;gap:6px;z-index:2}
        .dl-admin-bankcard .bc-actions button{background:rgba(255,255,255,.22);border:0;color:#fff;border-radius:8px;padding:5px 7px;cursor:pointer}
        .dl-admin-hint{color:#64748b;font-size:13px;margin:0 0 14px}
    </style>
    <script src="assets/js/datalab-admin.js?v=20260627-admin-phase14"></script>
    <script src="assets/js/datalab-shop-admin.js?v=20260627-shop-5"></script>
</body>

</html>
