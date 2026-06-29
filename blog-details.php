<?php
require_once __DIR__ . '/includes/blog-data.php';
$dlBlogPosts = dl_blog_posts(true);
$dlBlogPost = dl_blog_post_by_slug((string) ($_GET['slug'] ?? ''));
$dlRelatedPosts = array_values(array_filter($dlBlogPosts, static function ($post) use ($dlBlogPost) { return ($post['slug'] ?? '') !== ($dlBlogPost['slug'] ?? ''); }));
$dlDetailTitleAz = (string) ($dlBlogPost['titleAz'] ?? '');
$dlDetailTitleEn = (string) (($dlBlogPost['titleEn'] ?? '') ?: $dlDetailTitleAz);
$dlDetailExcerptAz = (string) ($dlBlogPost['excerptAz'] ?? '');
$dlDetailExcerptEn = (string) (($dlBlogPost['excerptEn'] ?? '') ?: $dlDetailExcerptAz);
$dlDetailContentAz = (string) ($dlBlogPost['contentAz'] ?? '');
$dlDetailContentEn = (string) (($dlBlogPost['contentEn'] ?? '') ?: $dlDetailContentAz);
$dlDetailReadAz = (string) ($dlBlogPost['readTimeAz'] ?? '');
$dlDetailReadEn = (string) (($dlBlogPost['readTimeEn'] ?? '') ?: $dlDetailReadAz);
$dlSeoTitle = trim((string) ($dlBlogPost['seoTitle'] ?? '')) ?: $dlDetailTitleAz . ' | DatalabAcademy Bloq';
$dlSeoDescription = trim((string) ($dlBlogPost['seoDescription'] ?? '')) ?: $dlDetailExcerptAz;
$dlSeoKeywords = trim((string) ($dlBlogPost['seoKeywords'] ?? '')) ?: implode(', ', array_filter([(string) ($dlBlogPost['category'] ?? ''), 'DatalabAcademy', 'Data Analitika']));
$dlSeoImage = dl_blog_image($dlBlogPost);
$dlSeoImage = preg_match('#^https?://#i', $dlSeoImage) ? $dlSeoImage : 'https://datalabacademy.az/' . ltrim($dlSeoImage, '/');
$dlCanonical = 'https://datalabacademy.az/blog-details?slug=' . rawurlencode((string) ($dlBlogPost['slug'] ?? ''));
?>
<!DOCTYPE html>
<html lang="az">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo dl_blog_e($dlSeoTitle); ?></title>
    <meta name="robots" content="index, follow">
    <meta name="description" content="<?php echo dl_blog_e($dlSeoDescription); ?>">
    <meta name="keywords" content="<?php echo dl_blog_e($dlSeoKeywords); ?>">
    <meta name="author" content="Datalab Academy">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="theme-color" content="#6366f1">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo dl_blog_e($dlSeoTitle); ?>">
    <meta property="og:description" content="<?php echo dl_blog_e($dlSeoDescription); ?>">
    <meta property="og:image" content="<?php echo dl_blog_e($dlSeoImage); ?>">
    <meta property="og:url" content="<?php echo dl_blog_e($dlCanonical); ?>">
    <meta property="og:locale" content="az_AZ">
    <meta property="og:locale:alternate" content="en_US">
    <meta property="og:site_name" content="DatalabAcademy">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo dl_blog_e($dlSeoTitle); ?>">
    <meta name="twitter:description" content="<?php echo dl_blog_e($dlSeoDescription); ?>">
    <meta name="twitter:image" content="<?php echo dl_blog_e($dlSeoImage); ?>">
    <link rel="canonical" href="<?php echo dl_blog_e($dlCanonical); ?>">
    <link rel="alternate" hreflang="az-AZ" href="<?php echo dl_blog_e($dlCanonical); ?>">
    <link rel="alternate" hreflang="x-default" href="https://datalabacademy.az/">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
    <link rel="shortcut icon" type="image/svg+xml" href="assets/images/favicon.svg">
    <link rel="preload" as="image" href="assets/images/logo/datalab-logo-transparent.png">

    <!-- CSS
	============================================ -->
    <link rel="stylesheet" href="assets/css/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/vendor/slick.css">
    <link rel="stylesheet" href="assets/css/vendor/slick-theme.css">
    <link rel="stylesheet" href="assets/css/plugins/sal.css">
    <link rel="stylesheet" href="assets/css/plugins/feather.css">
    <link rel="stylesheet" href="assets/css/plugins/fontawesome.min.css">
    <link rel="stylesheet" href="assets/css/plugins/euclid-circulara.css">
    <link rel="stylesheet" href="assets/css/plugins/swiper.css">
    <link rel="stylesheet" href="assets/css/plugins/odometer.css">
    <link rel="stylesheet" href="assets/css/plugins/animation.css">
    <link rel="stylesheet" href="assets/css/plugins/bootstrap-select.min.css">
    <link rel="stylesheet" href="assets/css/plugins/jquery-ui.css">
    <link rel="stylesheet" href="assets/css/plugins/magnigy-popup.min.css">
    <link rel="stylesheet" href="assets/css/plugins/plyr.css">
    <link rel="stylesheet" href="assets/css/plugins/jodit.min.css">

    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/datalab-shared.css?v=20260622-data-cover-1">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@graph": [
            {
                "@type": "Organization",
                "@id": "https://datalabacademy.az/#organization",
                "name": "DatalabAcademy",
                "url": "https://datalabacademy.az/",
                "logo": "https://datalabacademy.az/assets/images/logo/datalab-logo-transparent.png",
                "contactPoint": {
                    "@type": "ContactPoint",
                    "telephone": "+994506549737",
                    "contactType": "customer support",
                    "areaServed": "AZ",
                    "availableLanguage": ["az", "en"]
                }
            },
            {
                "@type": "WebSite",
                "@id": "https://datalabacademy.az/#website",
                "url": "https://datalabacademy.az/",
                "name": "DatalabAcademy",
                "publisher": { "@id": "https://datalabacademy.az/#organization" },
                "potentialAction": {
                    "@type": "SearchAction",
                    "target": "https://datalabacademy.az/course-filter-one-open.html?search={search_term_string}",
                    "query-input": "required name=search_term_string"
                }
            }
        ]
    }
    </script>
    <style>
        .rbt-header-9 .header-left .logo a {
            width: 250px;
            height: 44px;
            overflow: visible;
            display: block;
        }

        .rbt-header-9 .header-left .logo a img.datalab-logo {
            width: 250px !important;
            height: 44px !important;
            max-height: none !important;
            object-fit: contain !important;
            object-position: left center !important;
            transform: none;
            transform-origin: center center;
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
        }

        .popup-mobile-menu .inner-top .logo a {
            width: 220px;
            height: 40px;
            overflow: visible;
            display: block;
        }

        .popup-mobile-menu .inner-top .logo a img.datalab-logo {
            width: 220px !important;
            height: 40px !important;
            max-height: none !important;
            object-fit: contain !important;
            object-position: left center !important;
            transform: none;
            transform-origin: center center;
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
        }

        .active-dark-mode .rbt-header-9 .header-left .logo a img.datalab-logo,
        .active-dark-mode .popup-mobile-menu .inner-top .logo a img.datalab-logo {
            filter: brightness(1.22) saturate(1.18) drop-shadow(0 8px 18px rgba(47, 87, 239, 0.22));
        }

        .rbt-course-area .rbt-card .rbt-price {
            display: none !important;
        }

        /* DatalabAcademy - News Modal */
        .dl-news-modal {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: none;
        }

        .dl-news-modal.is-open {
            display: block;
        }

        .dl-news-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .dl-news-dialog {
            position: relative;
            width: min(1120px, calc(100% - 24px));
            margin: 5vh auto;
            border-radius: 22px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 18px 80px rgba(0, 0, 0, 0.28);
            transform: translateY(10px);
            animation: dlNewsIn 220ms ease-out forwards;
            max-height: 92vh;
            display: flex;
            flex-direction: column;
        }

        @keyframes dlNewsIn {
            to {
                transform: translateY(0);
            }
        }

        .dl-news-media {
            position: relative;
            height: 320px;
            background: linear-gradient(135deg, rgba(47, 87, 239, 0.08) 0%, rgba(197, 134, 238, 0.08) 100%);
        }

        .dl-news-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .dl-news-media::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(2, 6, 23, 0.05) 0%, rgba(2, 6, 23, 0.72) 100%);
            pointer-events: none;
        }

        .dl-news-close {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 42px;
            height: 42px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            background: rgba(15, 23, 42, 0.55);
            color: #fff;
            display: grid;
            place-items: center;
            transition: transform 120ms ease, background 120ms ease;
            z-index: 2;
        }

        .dl-news-close:hover {
            transform: scale(1.03);
            background: rgba(15, 23, 42, 0.72);
        }

        .dl-news-content {
            padding: 22px 22px 24px;
            overflow: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: rgba(15, 23, 42, 0.22) transparent;
        }

        .dl-news-content::-webkit-scrollbar {
            width: 10px;
        }

        .dl-news-content::-webkit-scrollbar-thumb {
            background: rgba(15, 23, 42, 0.22);
            border-radius: 999px;
            border: 3px solid transparent;
            background-clip: content-box;
        }

        .dl-news-content::-webkit-scrollbar-track {
            background: transparent;
        }

        .dl-news-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: -50px;
            margin-bottom: 14px;
            position: relative;
            z-index: 2;
        }

        .dl-news-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(90deg, rgba(47, 87, 239, 0.95) 0%, rgba(197, 134, 238, 0.95) 100%);
            box-shadow: 0 10px 30px rgba(47, 87, 239, 0.22);
        }

        .dl-news-date {
            color: rgba(255, 255, 255, 0.92);
            font-weight: 600;
            font-size: 13px;
            text-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        }

        .dl-news-title {
            font-size: clamp(22px, 2.2vw, 30px);
            line-height: 1.15;
            margin: 0 0 10px;
            letter-spacing: -0.02em;
        }

        .dl-news-body {
            color: #4b5563;
            font-size: 16px;
            line-height: 1.7;
        }

        .dl-news-body h4 {
            margin: 18px 0 10px;
            font-size: 18px;
            letter-spacing: -0.01em;
        }

        .dl-news-body hr {
            margin: 18px 0;
            border: 0;
            height: 1px;
            background: rgba(15, 23, 42, 0.10);
        }

        .dl-news-body blockquote {
            margin: 14px 0;
            padding: 12px 14px;
            border-left: 4px solid rgba(47, 87, 239, 0.75);
            background: rgba(47, 87, 239, 0.06);
            border-radius: 12px;
            color: rgba(2, 6, 23, 0.82);
            position: relative;
        }

        /* Remove template quote background icon */
        .dl-news-body blockquote::before,
        .dl-news-body blockquote::after {
            content: none !important;
            display: none !important;
            background-image: none !important;
        }

        .dl-news-body code {
            padding: 2px 6px;
            border-radius: 8px;
            background: rgba(15, 23, 42, 0.06);
            font-size: 0.92em;
        }

        .dl-news-body ul {
            margin: 12px 0 0 18px;
        }

        .dl-news-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 18px;
        }

        .active-dark-mode .dl-news-dialog {
            background: #0b1220;
            box-shadow: 0 18px 80px rgba(0, 0, 0, 0.45);
        }

        .active-dark-mode .dl-news-body {
            color: rgba(229, 231, 235, 0.9);
        }

        .active-dark-mode .dl-news-title {
            color: #f8fafc;
        }

        .active-dark-mode .dl-news-content {
            scrollbar-color: rgba(255, 255, 255, 0.22) transparent;
        }

        .active-dark-mode .dl-news-content::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.22);
        }

        .active-dark-mode .dl-news-body hr {
            background: rgba(255, 255, 255, 0.12);
        }

        .active-dark-mode .dl-news-body blockquote {
            background: rgba(197, 134, 238, 0.10);
            border-left-color: rgba(197, 134, 238, 0.72);
            color: rgba(229, 231, 235, 0.92);
        }

        .active-dark-mode .dl-news-body code {
            background: rgba(255, 255, 255, 0.08);
            color: rgba(229, 231, 235, 0.92);
        }

        @media (max-width: 576px) {
            .dl-news-dialog {
                margin: 6vh auto;
            }
            .dl-news-media {
                height: 240px;
            }
            .dl-news-content {
                padding: 18px 16px 20px;
            }
        }

        /* ===== Ana Səhifə – dropdown sil ===== */
        .mainmenu > li:first-child .rbt-megamenu { display: none !important; }
        .mainmenu > li:first-child > a i.feather-chevron-down { display: none !important; }

        /* ===== Kurslar – sadə hover dropdown ===== */
        .mainmenu .has-dropdown .submenu {
            min-width: 220px;
        }
        .mainmenu .has-dropdown .submenu li a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 500;
            color: #1f1f2c;
            transition: color 0.2s, background 0.2s;
        }
        .mainmenu .has-dropdown .submenu li a:hover {
            color: var(--color-primary, #2563eb);
            background: #f4f7ff;
        }
        .mainmenu .has-dropdown .submenu li a i {
            font-size: 15px;
            flex-shrink: 0;
            color: var(--color-primary, #2563eb);
        }

        /* ===== Banner – position konteyner ===== */
        .rbt-banner-area { position: relative; overflow: hidden; }

        /* ===== Üzən animasiyalı elementlər ===== */
        .dl-banner-floats {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 3;
        }

        /* Kiçik üzən ikonlu badge-lər */
        .dl-float-badge {
            position: absolute;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            border-radius: 14px;
            padding: 10px 16px;
            box-shadow: 0 8px 30px rgba(47,87,239,0.18);
            font-size: 13px;
            font-weight: 600;
            color: #1a1a2e;
            white-space: nowrap;
        }
        .dl-float-badge i { font-size: 18px; }
        .dl-float-badge.fb-1 { top: 8%;  left: 1%;   animation: dlFloat 6s ease-in-out 0s   infinite; }
        .dl-float-badge.fb-2 { bottom: 14%; left: 1%; animation: dlFloat 6s ease-in-out 2s   infinite; }
        .dl-float-badge.fb-3 { top: 6%;  left: 42%;  animation: dlFloat 6s ease-in-out 1s   infinite; }
        .dl-float-badge.fb-4 { bottom: 12%; left: 40%; animation: dlFloat 6s ease-in-out 3s  infinite; }

        /* Stat kartlar */
        .dl-float-stat {
            position: absolute;
            background: #fff;
            border-radius: 16px;
            padding: 12px 18px;
            box-shadow: 0 10px 35px rgba(0,0,0,0.12);
            text-align: center;
            min-width: 110px;
        }
        .dl-float-stat .fs-num  { font-size: 20px; font-weight: 700; line-height: 1; color: #2f57ef; }
        .dl-float-stat .fs-lbl  { font-size: 11px; color: #888; margin-top: 4px; }
        .dl-float-stat.fst-1 { top: 8%;    right: 1%; animation: dlFloat 7s ease-in-out 0.5s infinite; }
        .dl-float-stat.fst-2 { bottom: 12%; right: 1%; animation: dlFloat 7s ease-in-out 2.5s infinite; }

        /* Dekorativ rəngli dairələr */
        .dl-float-dot {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }
        .dl-float-dot.fd-1 { width:90px;  height:90px;  background:rgba(47,87,239,0.12);  top:3%;    left:18%;  animation:dlDot 9s ease-in-out 0s   infinite; }
        .dl-float-dot.fd-2 { width:55px;  height:55px;  background:rgba(197,134,238,0.15); bottom:8%; left:30%;  animation:dlDot 9s ease-in-out 3s   infinite; }
        .dl-float-dot.fd-3 { width:35px;  height:35px;  background:rgba(255,159,67,0.2);   top:45%;   left:8%;   animation:dlDot 7s ease-in-out 1.5s infinite; }
        .dl-float-dot.fd-4 { width:20px;  height:20px;  background:rgba(46,213,115,0.3);   top:20%;   left:52%;  animation:dlDot 8s ease-in-out 4s   infinite; }

        /* Yanıb-sönən "Yeni" tag */
        .dl-new-tag {
            position: absolute;
            top: 18%;
            right: 2%;
            background: linear-gradient(135deg,#ff6b6b,#ff9f43);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 999px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            animation: dlPulse 2s ease-in-out infinite;
        }

        /* Animasiyalar */
        @keyframes dlFloat {
            0%,100% { transform: translateY(0px) rotate(0deg);   }
            33%      { transform: translateY(-12px) rotate(1.5deg); }
            66%      { transform: translateY(6px) rotate(-1deg);  }
        }
        @keyframes dlDot {
            0%,100% { transform: translateY(0) scale(1); opacity:0.9; }
            50%      { transform: translateY(-18px) scale(1.08); opacity:0.6; }
        }
        @keyframes dlPulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(255,107,107,0.5); }
            50%      { box-shadow: 0 0 0 10px rgba(255,107,107,0); }
        }

        /* Kiçik ekranda bəzilərini gizlə */
        @media (max-width: 991px) {
            .dl-float-badge.fb-3,
            .dl-float-badge.fb-4,
            .dl-float-stat.fst-1,
            .dl-float-stat.fst-2,
            .dl-new-tag { display: none; }
        }

        /* ===== Creative News / Blog Section ===== */
        .dl-news-section {
            position: relative;
            overflow: hidden;
            padding: 90px 0;
            background: linear-gradient(135deg, #f5f7ff 0%, #ffffff 55%, #f8f0ff 100%);
        }
        /* Animated background blobs */
        .dl-news-section::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(47,87,239,0.07) 0%, transparent 70%);
            top: -150px; left: -150px;
            animation: dlBlobMove 14s ease-in-out infinite;
            pointer-events: none;
        }
        .dl-news-section::after {
            content: '';
            position: absolute;
            width: 350px; height: 350px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(197,134,238,0.09) 0%, transparent 70%);
            bottom: -80px; right: -80px;
            animation: dlBlobMove 11s ease-in-out 4s infinite reverse;
            pointer-events: none;
        }
        @keyframes dlBlobMove {
            0%,100% { transform: translate(0,0) scale(1); }
            50%      { transform: translate(40px,30px) scale(1.12); }
        }

        /* Header */
        .dl-news-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 48px;
            flex-wrap: wrap;
            gap: 20px;
        }
        .dl-news-live-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(90deg, #2f57ef, #c586ee);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 999px;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin-bottom: 12px;
            width: fit-content;
            animation: dlPulse 2.5s ease-in-out infinite;
        }
        .dl-news-live-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #fff;
            animation: dlDotBlink 1.2s ease-in-out infinite;
        }
        @keyframes dlDotBlink {
            0%,100% { opacity: 1; transform: scale(1); }
            50%      { opacity: 0.4; transform: scale(0.7); }
        }
        .dl-news-main-title {
            font-size: clamp(26px, 3vw, 42px);
            font-weight: 800;
            line-height: 1.15;
            color: #1a1a2e;
            margin: 0;
        }
        .dl-news-main-title .c-blue { color: #2f57ef; }
        .dl-viewall-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(47,87,239,0.08);
            color: #2f57ef;
            font-weight: 600;
            font-size: 14px;
            padding: 12px 24px;
            border-radius: 999px;
            transition: all 0.3s ease;
            white-space: nowrap;
            text-decoration: none;
        }
        .dl-viewall-btn:hover {
            background: #2f57ef;
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(47,87,239,0.3);
        }

        /* Category tags */
        .dl-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 999px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            width: fit-content;
        }
        .dl-tag.t-ai   { background: rgba(47,87,239,0.92);  color: #fff; }
        .dl-tag.t-sql  { background: rgba(46,213,115,0.92); color: #fff; }
        .dl-tag.t-data { background: rgba(255,159,67,0.92); color: #fff; }

        /* Featured BIG card */
        .dl-feat-card {
            position: relative;
            border-radius: 26px;
            overflow: hidden;
            height: 520px;
            display: block;
            text-decoration: none;
        }
        .dl-feat-card img {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform 0.65s cubic-bezier(.25,.46,.45,.94);
        }
        .dl-feat-card:hover img { transform: scale(1.07); }
        .dl-feat-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(10,15,40,0.05) 20%, rgba(10,15,40,0.88) 100%);
            padding: 32px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            gap: 14px;
        }
        .dl-feat-title {
            font-size: 26px;
            font-weight: 800;
            color: #fff;
            line-height: 1.3;
            margin: 0;
        }
        .dl-feat-meta {
            display: flex;
            align-items: center;
            gap: 14px;
            color: rgba(255,255,255,0.7);
            font-size: 13px;
        }
        .dl-feat-meta i { font-size: 13px; }
        .dl-feat-readbtn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            color: #1a1a2e;
            font-weight: 700;
            font-size: 13px;
            padding: 10px 20px;
            border-radius: 999px;
            width: fit-content;
            transition: all 0.3s ease;
            margin-top: 4px;
        }
        .dl-feat-card:hover .dl-feat-readbtn {
            background: #2f57ef;
            color: #fff;
            gap: 14px;
        }

        /* Side stacked small cards */
        .dl-news-side {
            display: flex;
            flex-direction: column;
            gap: 24px;
            height: 520px;
        }
        .dl-small-card {
            flex: 1;
            border-radius: 20px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            display: flex;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.35s ease, box-shadow 0.35s ease;
        }
        .dl-small-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 45px rgba(47,87,239,0.16);
        }
        .dl-small-img {
            width: 155px;
            flex-shrink: 0;
            overflow: hidden;
        }
        .dl-small-img img {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .dl-small-card:hover .dl-small-img img { transform: scale(1.09); }
        .dl-small-body {
            padding: 20px 22px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 8px;
        }
        .dl-small-title {
            font-size: 15px;
            font-weight: 700;
            color: #1a1a2e;
            line-height: 1.4;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .dl-small-desc {
            font-size: 13px;
            color: #888;
            line-height: 1.6;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .dl-small-readlink {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #2f57ef;
            font-size: 13px;
            font-weight: 600;
            transition: gap 0.25s ease;
        }
        .dl-small-card:hover .dl-small-readlink { gap: 10px; }

        .active-dark-mode .dl-news-section {
            background:
                radial-gradient(circle at 12% 8%, rgba(47, 87, 239, 0.20) 0%, transparent 34%),
                radial-gradient(circle at 88% 22%, rgba(197, 134, 238, 0.16) 0%, transparent 30%),
                linear-gradient(135deg, #111a2c 0%, #0f1728 52%, #151e32 100%) !important;
        }

        .active-dark-mode .dl-news-section::before {
            background: radial-gradient(circle, rgba(47, 87, 239, 0.22) 0%, transparent 70%);
            opacity: 0.75;
        }

        .active-dark-mode .dl-news-section::after {
            background: radial-gradient(circle, rgba(197, 134, 238, 0.18) 0%, transparent 70%);
            opacity: 0.85;
        }

        .active-dark-mode .dl-news-main-title {
            color: #f8fafc;
        }

        .active-dark-mode .dl-news-main-title .c-blue {
            color: #7f9cff;
        }

        .active-dark-mode .dl-news-live-badge {
            background: linear-gradient(90deg, rgba(47, 87, 239, 0.95), rgba(197, 134, 238, 0.92));
            box-shadow: 0 10px 28px rgba(47, 87, 239, 0.22);
        }

        .active-dark-mode .dl-viewall-btn {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.10);
            color: #e5edff;
        }

        .active-dark-mode .dl-viewall-btn:hover {
            background: #2f57ef;
            color: #fff;
            border-color: rgba(47, 87, 239, 0.75);
        }

        .active-dark-mode .dl-small-card {
            background: rgba(15, 23, 42, 0.88);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 18px 48px rgba(0, 0, 0, 0.24);
        }

        .active-dark-mode .dl-small-card:hover {
            box-shadow: 0 24px 58px rgba(47, 87, 239, 0.22);
        }

        .active-dark-mode .dl-small-title {
            color: #f8fafc;
        }

        .active-dark-mode .dl-small-desc {
            color: rgba(226, 232, 240, 0.72);
        }

        .active-dark-mode .dl-small-readlink {
            color: #91a8ff;
        }

        .active-dark-mode .dl-feat-card {
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 22px 60px rgba(0, 0, 0, 0.30);
        }

        .active-dark-mode .dl-feat-overlay {
            background: linear-gradient(180deg, rgba(6, 12, 25, 0.10) 18%, rgba(6, 12, 25, 0.92) 100%);
        }

        .active-dark-mode .dl-feat-readbtn {
            background: rgba(255, 255, 255, 0.92);
            color: #0f1728;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .dl-feat-card { height: 380px; }
            .dl-news-side  { height: auto; }
        }
        @media (max-width: 575px) {
            .dl-small-card { flex-direction: column; }
            .dl-small-img  { width: 100%; height: 160px; }
            .dl-news-side  { gap: 16px; }
        }

        /* ===== Kateqoriya İkon Thumbnail ===== */
        .dl-cat-icon {
            width: 72px;
            height: 72px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform 0.35s ease, box-shadow 0.35s ease;
        }
        .rbt-cat-box:hover .dl-cat-icon,
        .rbt-cat-box-1:hover .dl-cat-icon {
            transform: scale(1.10) rotate(-3deg);
            box-shadow: 0 12px 35px rgba(0,0,0,0.18);
        }
        .dl-cat-icon i {
            font-size: 30px;
            color: #fff;
            filter: drop-shadow(0 2px 6px rgba(0,0,0,0.2));
        }
        /* Hər kateqoriya üçün fərqli gradient */
        .dl-cat-icon.ci-web      { background: linear-gradient(135deg,#667eea,#764ba2); }
        .dl-cat-icon.ci-graphic  { background: linear-gradient(135deg,#f093fb,#f5576c); }
        .dl-cat-icon.ci-personal { background: linear-gradient(135deg,#4facfe,#00c6fb); }
        .dl-cat-icon.ci-it       { background: linear-gradient(135deg,#2f57ef,#4f46e5); }
        .dl-cat-icon.ci-sales    { background: linear-gradient(135deg,#fa709a,#ff9f43); }
        .dl-cat-icon.ci-arts     { background: linear-gradient(135deg,#a18cd1,#fbc2eb); }
        .dl-cat-icon.ci-mobile   { background: linear-gradient(135deg,#43e97b,#38f9d7); }
        .dl-cat-icon.ci-finance  { background: linear-gradient(135deg,#f7971e,#ffd200); }
        .dl-cat-icon.ci-ai       { background: linear-gradient(135deg,#a855f7,#d946ef); }

        /* Minicart layout: kompakt sətir + kiçik thumbnail */
        .rbt-minicart-wrapper .minicart-item {
            display: flex !important;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(0,0,0,0.06);
        }
        .rbt-minicart-wrapper .minicart-item .thumbnail {
            flex: 0 0 44px;
            width: 44px;
            height: 44px;
            border-radius: 6px;
            overflow: hidden;
        }
        .rbt-minicart-wrapper .minicart-item .thumbnail a,
        .rbt-minicart-wrapper .minicart-item .thumbnail {
            display: block;
            width: 44px;
            height: 44px;
        }
        .rbt-minicart-wrapper .minicart-item .thumbnail img {
            width: 44px !important;
            height: 44px !important;
            max-width: 44px !important;
            max-height: 44px !important;
            object-fit: cover;
            border-radius: 6px;
            display: block;
        }
        .rbt-minicart-wrapper .minicart-item .product-content {
            flex: 1;
            min-width: 0;
        }
        .rbt-minicart-wrapper .minicart-item .product-content .title {
            font-size: 13px;
            margin: 0 0 2px 0;
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .rbt-minicart-wrapper .minicart-item .product-content .quantity {
            font-size: 11px;
            color: #64748b;
        }
        .rbt-minicart-wrapper .minicart-item .close-btn {
            flex-shrink: 0;
        }
        .rbt-minicart-wrapper .minicart-item .close-btn .rbt-round-btn {
            width: 22px;
            height: 22px;
            min-width: 22px;
            min-height: 22px;
            line-height: 22px;
            font-size: 12px;
        }
        .datalab-mini-cart-empty {
            display: none;
            padding: 28px 16px;
            text-align: center;
            color: #64748b;
            font-weight: 500;
        }
        a.rbt-btn.disabled,
        a.rbt-btn[aria-disabled="true"] {
            opacity: 0.55;
            pointer-events: none;
            cursor: not-allowed;
        }

        /* ══════════════════════════════════════════
           MAGAZINE-STYLE NEWS MODAL  (dl-nm-*)
        ══════════════════════════════════════════ */
        .dl-news-modal {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .dl-news-modal.is-open { display: flex; }

        .dl-news-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(8,12,36,0.72);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            animation: dlNmFade .25s ease;
        }
        @keyframes dlNmFade { from{opacity:0} to{opacity:1} }

        /* Dialog shell – overrides old dl-news-dialog styles */
        .dl-news-dialog {
            position: relative;
            z-index: 2;
            margin: 0 !important;
            width: min(1060px, 100%);
            max-height: 90vh;
            border-radius: 28px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 30px 90px rgba(0,0,0,0.38);
            animation: dlNmUp .32s cubic-bezier(.25,.46,.45,.94) forwards;
            display: flex;
            flex-direction: column;
        }
        @keyframes dlNmUp {
            from { transform:translateY(42px); opacity:0; }
            to   { transform:translateY(0);    opacity:1; }
        }

        /* Close btn */
        .dl-nm-close {
            position: absolute;
            top: 16px; right: 16px;
            z-index: 20;
            width: 42px; height: 42px;
            border-radius: 50%;
            border: none;
            background: rgba(8,12,36,0.52);
            color: #fff;
            display: grid;
            place-items: center;
            cursor: pointer;
            font-size: 18px;
            transition: background .2s, transform .3s;
            backdrop-filter: blur(8px);
        }
        .dl-nm-close:hover {
            background: rgba(239,68,68,0.85);
            transform: rotate(90deg) scale(1.08);
        }

        /* Two-panel inner */
        .dl-nm-inner {
            display: flex;
            flex: 1;
            min-height: 0;
            max-height: 88vh;
        }

        /* LEFT – sticky image panel */
        .dl-nm-left {
            width: 40%;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
        }
        .dl-nm-left > img {
            width: 100%; height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .6s ease;
        }
        .dl-news-dialog:hover .dl-nm-left > img { transform: scale(1.04); }
        .dl-nm-img-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg,
                rgba(8,12,36,0.05) 30%,
                rgba(8,12,36,0.88) 100%);
            padding: 28px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            gap: 10px;
        }
        .dl-nm-img-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: linear-gradient(90deg, #2f57ef, #c586ee);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 5px 13px;
            border-radius: 999px;
            letter-spacing: .6px;
            text-transform: uppercase;
            width: fit-content;
        }
        .dl-nm-img-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,.72);
            font-size: 12px;
        }
        .dl-nm-img-meta i { font-size: 12px; }

        /* RIGHT – scrollable content */
        .dl-nm-right {
            flex: 1;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(47,87,239,.2) transparent;
        }
        .dl-nm-right::-webkit-scrollbar { width: 5px; }
        .dl-nm-right::-webkit-scrollbar-thumb {
            background: rgba(47,87,239,.22);
            border-radius: 999px;
        }
        .dl-nm-content-wrap {
            padding: 44px 44px 40px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        /* Meta strip */
        .dl-nm-meta-strip {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .dl-nm-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 999px;
            letter-spacing: .5px;
            background: linear-gradient(90deg,
                rgba(47,87,239,.12), rgba(197,134,238,.12));
            color: #2f57ef;
        }
        .dl-nm-pill-date {
            color: #9ca3af;
            font-size: 13px;
        }
        .dl-nm-read-time {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #9ca3af;
            font-size: 13px;
        }

        /* Title */
        .dl-nm-title {
            font-size: clamp(20px, 2.4vw, 30px);
            font-weight: 800;
            line-height: 1.22;
            color: #111827;
            margin: 0;
        }

        /* Gradient divider */
        .dl-nm-rule {
            height: 3px;
            border-radius: 999px;
            background: linear-gradient(90deg,
                #2f57ef 0%, #c586ee 50%, transparent 100%);
            opacity: .22;
        }

        /* Body */
        .dl-nm-body {
            font-size: 15px;
            line-height: 1.8;
            color: #374151;
        }
        .dl-nm-body h4 {
            font-size: 17px;
            font-weight: 700;
            color: #111827;
            margin: 22px 0 8px;
        }
        .dl-nm-body strong { color: #111827; }
        .dl-nm-body ul { margin: 10px 0 10px 22px; }
        .dl-nm-body li { margin-bottom: 7px; }
        .dl-nm-body blockquote {
            margin: 18px 0;
            padding: 14px 18px;
            border-left: 4px solid #2f57ef;
            background: rgba(47,87,239,.06);
            border-radius: 0 14px 14px 0;
            color: #374151;
            font-style: italic;
        }
        .dl-nm-body blockquote::before,
        .dl-nm-body blockquote::after { display:none !important; }
        .dl-nm-body code {
            padding: 2px 7px;
            border-radius: 6px;
            background: rgba(15,23,42,.07);
            font-size: .88em;
        }

        /* Action buttons */
        .dl-nm-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            padding-top: 8px;
            border-top: 1px solid #f3f4f6;
        }
        .dl-nm-btn-grad {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #2f57ef, #c586ee);
            color: #fff !important;
            font-weight: 700;
            font-size: 14px;
            padding: 13px 26px;
            border-radius: 999px;
            text-decoration: none;
            transition: transform .28s, box-shadow .28s;
        }
        .dl-nm-btn-grad:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(47,87,239,.35);
        }
        .dl-nm-btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            color: #6b7280;
            font-weight: 600;
            font-size: 14px;
            padding: 13px 22px;
            border-radius: 999px;
            border: 1.5px solid #e5e7eb;
            cursor: pointer;
            transition: all .25s;
        }
        .dl-nm-btn-outline:hover {
            border-color: #ef4444;
            color: #ef4444;
            background: rgba(239,68,68,.05);
        }

        /* Dark mode support */
        .active-dark-mode .dl-news-dialog { background: #0d1428; }
        .active-dark-mode .dl-nm-title    { color: #f8fafc; }
        .active-dark-mode .dl-nm-body     { color: #cbd5e1; }
        .active-dark-mode .dl-nm-body h4  { color: #f1f5f9; }
        .active-dark-mode .dl-nm-actions  { border-color: rgba(255,255,255,.08); }
        .active-dark-mode .dl-nm-btn-outline { border-color: rgba(255,255,255,.15); color:#94a3b8; }
        .active-dark-mode .dl-nm-body blockquote { background:rgba(47,87,239,.12); color:#cbd5e1; }
        .active-dark-mode .dl-nm-body code { background:rgba(255,255,255,.08); color:#cbd5e1; }
        .active-dark-mode .dl-nm-pill { background:rgba(47,87,239,.2); }

        /* Responsive */
        @media (max-width: 767px) {
            .dl-news-modal { padding: 0; align-items: flex-end; }
            .dl-news-dialog { border-radius: 24px 24px 0 0; max-height: 94vh; }
            .dl-nm-inner    { flex-direction: column; max-height: none; }
            .dl-nm-left     { width: 100%; height: 220px; }
            .dl-nm-right    { overflow-y: visible; }
            .dl-nm-content-wrap { padding: 24px 20px 28px; }
        }
    </style>
    <style>
        body.datalab-blog-page .currency-menu,
        body.datalab-blog-page .datalab-sticky-switchers .currency-menu,
        body.datalab-blog-page .rbt-header-middle .currency-menu { display: none !important; }
        body.datalab-blog-page .datalab-sticky-switchers { gap: 0; }
        .datalab-blog-banner .rbt-banner-image { background: linear-gradient(135deg, rgba(47, 87, 239, .12), rgba(197, 134, 238, .12)); }
        .datalab-blog-card .rbt-card-img a,
        .datalab-blog-card .rbt-card-img img { aspect-ratio: 16 / 10; object-fit: cover; }
        .datalab-blog-card .rbt-card-body { padding: 26px; }
        .datalab-blog-chip { display: inline-flex; align-items: center; min-height: 28px; margin-bottom: 14px; padding: 4px 12px; border-radius: 999px; color: #2f57ef; background: rgba(47, 87, 239, .09); font-size: 12px; font-weight: 700; }
        .datalab-blog-sidebar .rbt-single-widget { background: #fff; }
        .datalab-blog-sidebar .rbt-tag-list a { text-transform: none; }
        .active-dark-mode .datalab-blog-banner .rbt-banner-image,
        .active-dark-mode .datalab-blog-area { background: #111c2e !important; }
        .active-dark-mode .datalab-blog-card,
        .active-dark-mode .datalab-blog-sidebar .rbt-single-widget { border-color: rgba(255, 255, 255, .09) !important; background: #162237 !important; }
        .active-dark-mode .datalab-blog-card .rbt-card-title a,
        .active-dark-mode .datalab-blog-sidebar .rbt-widget-title { color: #f8fafc !important; }
        .active-dark-mode .datalab-blog-card .rbt-card-text,
        .active-dark-mode .datalab-blog-card .blog-meta li,
        .active-dark-mode .datalab-blog-sidebar a,
        .active-dark-mode .datalab-blog-sidebar .rbt-meta li { color: rgba(226, 232, 240, .76) !important; }
        .active-dark-mode .datalab-blog-sidebar input { color: #f8fafc; background: rgba(255, 255, 255, .06); border-color: rgba(255, 255, 255, .10); }
    </style>
    <style>
        .datalab-blog-detail-hero { padding: 90px 0 70px; background: linear-gradient(135deg, rgba(47, 87, 239, .10), rgba(197, 134, 238, .12)); }
        .datalab-blog-detail-hero .page-list { justify-content: center; margin-bottom: 22px; }
        .datalab-blog-detail-hero .title { max-width: 920px; margin: 0 auto; text-align: center; font-size: clamp(42px, 5vw, 72px); line-height: 1.05; }
        .datalab-blog-detail-hero .description { max-width: 760px; margin: 24px auto 0; text-align: center; color: #6b7280; font-size: 20px; line-height: 1.65; }
        .datalab-detail-meta { display: flex; justify-content: center; gap: 18px; flex-wrap: wrap; margin-top: 28px; color: #6b7280; }
        .datalab-detail-meta li { display: inline-flex; align-items: center; gap: 8px; list-style: none; }
        .datalab-detail-cover { border-radius: 10px; overflow: hidden; box-shadow: 0 28px 80px rgba(17, 24, 39, .12); }
        .datalab-detail-cover img { width: 100%; aspect-ratio: 16 / 7; object-fit: cover; display: block; }
        .datalab-article-layout { display: grid; grid-template-columns: minmax(0, 1fr) 340px; gap: 42px; align-items: start; }
        .datalab-article-card, .datalab-detail-sidebar .rbt-single-widget { background: #fff; border: 1px solid #edf0f7; border-radius: 8px; box-shadow: 0 18px 55px rgba(17, 24, 39, .06); }
        .datalab-article-card { padding: 42px; }
        .datalab-article-card p { color: #6b7280; font-size: 18px; line-height: 1.85; margin-bottom: 22px; }
        .datalab-article-card h2, .datalab-article-card h3 { margin-top: 34px; margin-bottom: 16px; color: #0f172a; }
        .datalab-article-card ul { padding-left: 0; margin: 0 0 22px; }
        .datalab-article-card li { list-style: none; display: flex; gap: 12px; color: #6b7280; font-size: 17px; line-height: 1.7; margin-bottom: 12px; }
        .datalab-article-card li i { color: #2f57ef; margin-top: 7px; }
        .datalab-article-card blockquote { border-left: 4px solid #2f57ef; background: rgba(47, 87, 239, .07); border-radius: 8px; padding: 24px; margin: 32px 0; color: #334155; font-size: 18px; line-height: 1.75; font-weight: 600; }
        .datalab-article-note { border-left: 4px solid #2f57ef; background: rgba(47, 87, 239, .07); border-radius: 8px; padding: 24px; margin: 32px 0; color: #334155; font-weight: 600; }
        .datalab-detail-sidebar { position: sticky; top: 110px; }
        .datalab-detail-sidebar .rbt-single-widget { padding: 28px; margin-bottom: 24px; }
        .datalab-detail-sidebar .rbt-widget-title { margin-bottom: 16px; }
        .datalab-detail-sidebar .rbt-tag-list a { text-transform: none; }
        .active-dark-mode .datalab-blog-detail-hero, .active-dark-mode .datalab-blog-area { background: #111c2e !important; }
        .active-dark-mode .datalab-blog-detail-hero .title, .active-dark-mode .datalab-article-card h2, .active-dark-mode .datalab-article-card h3, .active-dark-mode .datalab-detail-sidebar .rbt-widget-title { color: #f8fafc !important; }
        .active-dark-mode .datalab-blog-detail-hero .description, .active-dark-mode .datalab-detail-meta, .active-dark-mode .datalab-article-card p, .active-dark-mode .datalab-article-card li, .active-dark-mode .datalab-detail-sidebar a, .active-dark-mode .datalab-detail-sidebar p { color: rgba(226, 232, 240, .78) !important; }
        .active-dark-mode .datalab-article-card, .active-dark-mode .datalab-detail-sidebar .rbt-single-widget { background: #162237 !important; border-color: rgba(255, 255, 255, .09) !important; }
        .active-dark-mode .datalab-article-card blockquote, .active-dark-mode .datalab-article-note { background: rgba(47, 87, 239, .16); color: #dbeafe; }
        @media (max-width: 991px) { .datalab-article-layout { grid-template-columns: 1fr; } .datalab-detail-sidebar { position: static; } .datalab-article-card { padding: 28px; } .datalab-detail-cover img { aspect-ratio: 16 / 10; } }
    </style>
</head>

<body class="rbt-header-sticky datalab-blog-page">
<style>
    body > header.rbt-header-9:not(.datalab-header),
    body > footer.rbt-footer:not(.datalab-footer),
    body > .popup-mobile-menu,
    body > .rbt-cart-side-menu:not(.datalab-cart-side),
    body > .close_side_menu { display: none !important; }
</style>
<div data-datalab-header></div>


    <div id="my_switcher" class="my_switcher">
        <ul>
            <li>
                <a href="javascript: void(0);" data-theme="light" class="setColor light">
                    <img src="assets/images/about/sun-01.svg" alt="Sun images"><span title="Açıq Rejim" data-i18n="theme_light_label" data-i18n-title="theme_light_title"> Açıq</span>
                </a>
            </li>
            <li>
                <a href="javascript: void(0);" data-theme="dark" class="setColor dark">
                    <img src="assets/images/about/vector.svg" alt="Vector Images"><span title="Tünd Rejim" data-i18n="theme_dark_label" data-i18n-title="theme_dark_title"> Tünd</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Start Header Area -->
    <header class="rbt-header rbt-header-9">
        <div class="rbt-sticky-placeholder"></div>

        <div class="rbt-header-campaign rbt-header-campaign-1 rbt-header-top-news bg-image1">
            <div class="wrapper">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="inner justify-content-center">
                                <div class="content">
                                    <span class="rbt-badge variation-02 bg-color-primary color-white radius-round" data-i18n="campaign_badge">Məhdud Vaxt Təklifi</span>
                                    <span class="news-text color-white-off" data-i18n-html="campaign_text"><img src="assets/images/icons/hand-emojji.svg" alt="Hand Emojji Images"> Yeni qrup qeydiyyatı açıqdır. Data, SQL, Excel və AI kurslarına indi qoşulun.</span>
                                </div>
                                <div class="right-button">
                                    <a class="rbt-btn-link color-white" href="course-filter-one-open.html">
                                        <span data-i18n="campaign_cta">İndi alın </span> <i class="feather-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="icon-close position-right">
                <button class="rbt-round-btn btn-white-off bgsection-activation">
                    <i class="feather-x"></i>
                </button>
            </div>
        </div>

        <!-- Start Header Top -->
        <div class="rbt-header-middle position-relative rbt-header-mid-1  bg-color-white rbt-border-bottom">
            <div class="container">
                <div class="rbt-header-sec align-items-center ">

                    <div class="rbt-header-sec-col rbt-header-left">
                        <div class="rbt-header-content">
                            <!-- Start Header Information List  -->
                            <div class="header-info">
                                <ul class="rbt-dropdown-menu switcher-language">
                                    <li class="has-child-menu">
                                        <a href="#" class="js-lang-current">
                                            <span class="menu-item js-current-lang">Azərbaycan</span>
                                            <i class="right-icon feather-chevron-down"></i>
                                        </a>
                                        <ul class="sub-menu">
                                            <li>
                                                <a href="#" data-lang="az">
                                                    <span class="menu-item" data-i18n="lang_az">Azərbaycan</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" data-lang="en">
                                                    <span class="menu-item" data-i18n="lang_en">English</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <!-- End Header Information List  -->

                            <!-- Start Header Information List  -->
                            <div class="header-info">
                                <ul class="rbt-dropdown-menu currency-menu">
                                    <li class="has-child-menu">
                                        <a href="#">
                                            <span class="menu-item js-current-currency">AZN</span>
                                            <i class="right-icon feather-chevron-down"></i>
                                        </a>
                                        <ul class="sub-menu hover-reverse">
                                            <li>
                                                <a href="#" data-currency="USD">
                                                    <span class="menu-item">USD</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" data-currency="AZN">
                                                    <span class="menu-item">AZN</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <!-- End Header Information List  -->
                        </div>
                    </div>

                    <div class="rbt-header-sec-col rbt-header-center d-none d-md-block">
                        <div class="rbt-header-content">
                            <div class="header-info">
                                <div class="rbt-search-field">
                                    <div class="search-field">
                                        <input type="text" placeholder="Kurs axtarın" data-i18n-placeholder="search_placeholder">
                                        <button class="rbt-round-btn serach-btn" type="submit"><i class="feather-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rbt-header-sec-col rbt-header-right">
                        <div class="rbt-header-content">
                            <div class="header-info">
                                <ul class="quick-access">
                                    <li>
                                        <a class="d-none d-xl-block rbt-cart-sidenav-activation datalab-cart-trigger" href="#"><i class="feather-shopping-cart"></i><span data-i18n="cart">Səbət</span><span class="datalab-cart-count" data-cart-count>0</span></a>
                                        <a class="d-block d-xl-none rbt-cart-sidenav-activation datalab-cart-trigger" href="#"><i class="feather-shopping-cart"></i><span class="datalab-cart-count" data-cart-count>0</span></a>
                                    </li>
                                </ul>
                            </div>

                            <div class="header-info">
                                <ul class="quick-access">
                                    <li class="account-access rbt-user-wrapper right-align-dropdown d-none d-xl-block">
                                        <a href="#"><i class="feather-user"></i></a>
                                        <div class="rbt-user-menu-list-wrapper">
                                            <div class="inner">
                                                <div class="rbt-admin-profile">
                                                    <div class="admin-thumbnail">
                                                        <img src="assets/images/team/avatar.jpg" alt="User Images">
                                                    </div>
                                                    <div class="admin-info">
                                                        <span class="name">RainbowIT</span>
                                                        <a class="rbt-btn-link color-primary" href="profile.html" data-i18n="view_profile">Profilə bax</a>
                                                    </div>
                                                </div>
                                                <ul class="user-list-wrapper">
                                                    <li>
                                                        <a href="instructor-dashboard.html">
                                                            <i class="feather-home"></i>
                                                            <span data-i18n="user_dashboard">İdarə panelim</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#">
                                                            <i class="feather-bookmark"></i>
                                                            <span data-i18n="user_bookmark">Əlfəcin</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="instructor-enrolled-courses.html">
                                                            <i class="feather-shopping-bag"></i>
                                                            <span data-i18n="user_enrolled_courses">Qeydiyyatlı kurslar</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="instructor-wishlist.html">
                                                            <i class="feather-heart"></i>
                                                            <span data-i18n="user_wishlist">İstək siyahısı</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="instructor-reviews.html">
                                                            <i class="feather-star"></i>
                                                            <span data-i18n="user_reviews">Rəylər</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="instructor-my-quiz-attempts.html">
                                                            <i class="feather-list"></i>
                                                            <span data-i18n="user_my_quiz_attempts">Quiz cəhdlərim</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="instructor-order-history.html">
                                                            <i class="feather-clock"></i>
                                                            <span data-i18n="user_order_history">Sifariş tarixçəsi</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="instructor-quiz-attempts.html">
                                                            <i class="feather-message-square"></i>
                                                            <span data-i18n="user_qa">Sual &amp; Cavab</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <hr class="mt--10 mb--10">
                                                <ul class="user-list-wrapper">
                                                    <li>
                                                        <a href="#">
                                                            <i class="feather-book-open"></i>
                                                            <span data-i18n="user_getting_started">Başlanğıc</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <hr class="mt--10 mb--10">
                                                <ul class="user-list-wrapper">
                                                    <li>
                                                        <a href="instructor-settings.html">
                                                            <i class="feather-settings"></i>
                                                            <span data-i18n="user_settings">Parametrlər</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="index.php">
                                                            <i class="feather-log-out"></i>
                                                            <span data-i18n="user_logout">Çıxış</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="access-icon rbt-user-wrapper right-align-dropdown d-block d-xl-none">
                                        <a class="rbt-round-btn" href="#"><i class="feather-user"></i></a>
                                        <div class="rbt-user-menu-list-wrapper">
                                            <div class="inner">
                                                <div class="rbt-admin-profile">
                                                    <div class="admin-thumbnail">
                                                        <img src="assets/images/team/avatar.jpg" alt="User Images">
                                                    </div>
                                                    <div class="admin-info">
                                                        <span class="name">RainbowIT</span>
                                                        <a class="rbt-btn-link color-primary" href="profile.html" data-i18n="view_profile">Profilə bax</a>
                                                    </div>
                                                </div>
                                                <ul class="user-list-wrapper">
                                                    <li>
                                                        <a href="instructor-dashboard.html">
                                                            <i class="feather-home"></i>
                                                            <span data-i18n="user_dashboard">İdarə panelim</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#">
                                                            <i class="feather-bookmark"></i>
                                                            <span data-i18n="user_bookmark">Əlfəcin</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="instructor-enrolled-courses.html">
                                                            <i class="feather-shopping-bag"></i>
                                                            <span data-i18n="user_enrolled_courses">Qeydiyyatlı kurslar</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="instructor-wishlist.html">
                                                            <i class="feather-heart"></i>
                                                            <span data-i18n="user_wishlist">İstək siyahısı</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="instructor-reviews.html">
                                                            <i class="feather-star"></i>
                                                            <span data-i18n="user_reviews">Rəylər</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="instructor-my-quiz-attempts.html">
                                                            <i class="feather-list"></i>
                                                            <span data-i18n="user_my_quiz_attempts">Quiz cəhdlərim</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="instructor-order-history.html">
                                                            <i class="feather-clock"></i>
                                                            <span data-i18n="user_order_history">Sifariş tarixçəsi</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="instructor-quiz-attempts.html">
                                                            <i class="feather-message-square"></i>
                                                            <span data-i18n="user_qa">Sual &amp; Cavab</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <hr class="mt--10 mb--10">
                                                <ul class="user-list-wrapper">
                                                    <li>
                                                        <a href="#">
                                                            <i class="feather-book-open"></i>
                                                            <span data-i18n="user_getting_started">Başlanğıc</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <hr class="mt--10 mb--10">
                                                <ul class="user-list-wrapper">
                                                    <li>
                                                        <a href="instructor-settings.html">
                                                            <i class="feather-settings"></i>
                                                            <span data-i18n="user_settings">Parametrlər</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="index.php">
                                                            <i class="feather-log-out"></i>
                                                            <span data-i18n="user_logout">Çıxış</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- End Header Top -->

        <div class="rbt-header-wrapper  header-not-transparent header-sticky">
            <div class="container">
                <div class="mainbar-row rbt-navigation-end align-items-center">
                    <div class="header-left rbt-header-content">
                        <div class="header-info">
                            <div class="logo logo-dark">
                                <a href="index.php">
                                    <img class="datalab-logo" src="assets/images/logo/datalab-logo-transparent.png" alt="DatalabAcademy">
                                </a>
                            </div>

                            <div class="logo d-none logo-light">
                                <a href="index.php">
                                    <img class="datalab-logo" src="assets/images/logo/datalab-logo-transparent.png" alt="DatalabAcademy">
                                </a>
                            </div>
                        </div>
                        <div class="header-info">
                            <div class="rbt-category-menu-wrapper">
                                <div class="rbt-category-btn rbt-side-offcanvas-activation">
                                    <div class="rbt-offcanvas-trigger md-size icon">
                                        <span class="d-none d-xl-block">
                                            <i class="feather-grid"></i>
                                        </span>
                                        <i title="Category" class="feather-grid d-block d-xl-none" data-i18n-title="category_label"></i>
                                    </div>
                                    <span class="category-text d-none d-xl-block" data-i18n="category_label">Category</span>
                                </div>

                                <div class="category-dropdown-menu d-none d-xl-block">
                                    <div class="category-menu-item">
                                        <div class="rbt-vertical-nav">
                                            <ul class="rbt-vertical-nav-list-wrapper vertical-nav-menu">
                                                <li class="vertical-nav-item active">
                                                    <a href="#tab1" data-i18n="cat_panel_analytics">Analitika</a>
                                                </li>
                                                <li class="vertical-nav-item">
                                                    <a href="#tab2" data-i18n="cat_panel_sql">SQL</a>
                                                </li>
                                                <li class="vertical-nav-item">
                                                    <a href="#tab3" data-i18n="cat_panel_office">Ofis</a>
                                                </li>
                                                <li class="vertical-nav-item">
                                                    <a href="#tab4" data-i18n="cat_panel_ai">AI</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="rbt-vertical-nav-content">
                                            <div class="rbt-vertical-inner tab-content" id="tab1" style="display: block">
                                                <div class="rbt-vertical-single">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="vartical-nav-content-menu">
                                                                <h3 class="rbt-short-title" data-i18n="cat_panel_analytics_title">Analitika kursları</h3>
                                                                <ul class="rbt-vertical-nav-list-wrapper">
                                                                    <li><a class="js-course-open" href="#course-1" data-course-id="1">Data Analitika</a></li>
                                                                </ul>
                                                                <div class="read-more-btn">
                                                                    <a class="rbt-btn-link" href="course-filter-one-open.html"><span data-i18n="cat_panel_view_all">Bütün kursları gör</span><i class="feather-arrow-right"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="rbt-vertical-inner tab-content" id="tab2">
                                                <div class="rbt-vertical-single">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="vartical-nav-content-menu">
                                                                <h3 class="rbt-short-title" data-i18n="cat_panel_sql_title">SQL kursları</h3>
                                                                <ul class="rbt-vertical-nav-list-wrapper">
                                                                    <li><a class="js-course-open" href="#course-2" data-course-id="2">SQL Developer</a></li>
                                                                </ul>
                                                                <div class="read-more-btn">
                                                                    <a class="rbt-btn-link" href="course-filter-one-open.html"><span data-i18n="cat_panel_view_all">Bütün kursları gör</span><i class="feather-arrow-right"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="rbt-vertical-inner tab-content" id="tab3">
                                                <div class="rbt-vertical-single">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="vartical-nav-content-menu">
                                                                <h3 class="rbt-short-title" data-i18n="cat_panel_office_title">Ofis kursları</h3>
                                                                <ul class="rbt-vertical-nav-list-wrapper">
                                                                    <li><a class="js-course-open" href="#course-3" data-course-id="3">Excel</a></li>
                                                                </ul>
                                                                <div class="read-more-btn">
                                                                    <a class="rbt-btn-link" href="course-filter-one-open.html"><span data-i18n="cat_panel_view_all">Bütün kursları gör</span><i class="feather-arrow-right"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="rbt-vertical-inner tab-content" id="tab4">
                                                <div class="rbt-vertical-single">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="vartical-nav-content-menu">
                                                                <h3 class="rbt-short-title" data-i18n="cat_panel_ai_title">AI kursları</h3>
                                                                <ul class="rbt-vertical-nav-list-wrapper">
                                                                    <li><a class="js-course-open" href="#course-4" data-course-id="4">AI ilə Effektiv İş</a></li>
                                                                </ul>
                                                                <div class="read-more-btn">
                                                                    <a class="rbt-btn-link" href="course-filter-one-open.html"><span data-i18n="cat_panel_view_all">Bütün kursları gör</span><i class="feather-arrow-right"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rbt-main-navigation d-none d-xl-block">
                        <nav class="mainmenu-nav">
                            <ul class="mainmenu">
                                <li class="">
                                    <a href="index.php"><span data-i18n="nav_home">Ana səhifə</span></a>
                                    <!-- Start Mega Menu  -->
                                    <div class="rbt-megamenu menu-skin-dark">
                                        <div class="wrapper">
                                            <div class="row row--15 home-plesentation-wrapper single-dropdown-menu-presentation">

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h1.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Ana səhifə <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h12.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Kurs kataloqu <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h4.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Excel <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h13.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Data Analitika <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h14.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">DatalabAcademy <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h9.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">AI ilə Effektiv İş <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h3.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Onlayn kurslar <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h6.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Data Analitika <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h15.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Texnologiya kursları <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h7.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Təlimçi dəstəyi <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h8.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Praktiki təlimlər <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h11.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Kurs detalları <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h10.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Onlayn kurs <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h5.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Datalab kursları <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h2.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Kurs kataloqu <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h16.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Karyera kursları <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <span class="new-batch">New Added</span>
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h17.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">DatalabAcademy <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <span class="new-batch">New Added</span>
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h18.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Təlimçi dəstəyi <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <span class="new-batch">New Added</span>
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h19.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Data Analitika <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <span class="new-batch">New Added</span>
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h20.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">AZ/EN tədris <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <span class="new-batch">New Added</span>
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h21.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Excel və dashboard <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <span class="new-batch">New Added</span>
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h22.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Seçilmiş kurslar <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <span class="new-batch">New Added</span>
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h23.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Mentor dəstəyi <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <span class="new-batch">New Added</span>
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h24.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Praktiki bacarıqlar <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <span class="new-batch">New Added</span>
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h25.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Karyera inkişafı <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                                    <div class="demo-single">
                                                        <div class="inner">
                                                            <span class="new-batch">New Added</span>
                                                            <div class="thumbnail">
                                                                <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h26.jpg" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="course-filter-one-open.html">Təlim icması <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item coming-soon">
                                                    <div class="demo-single">
                                                        <div class="inner disable">
                                                            <div class="thumbnail">
                                                                <a href="#"><img src="assets/images/splash/demo/coming-soon-1.png" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="#">Coming Soon <span class="btn-icon"><i class="feather-arrow-right"></i></span></a>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item coming-soon">
                                                    <div class="demo-single">
                                                        <div class="inner disable">
                                                            <div class="thumbnail">
                                                                <a href="#"><img src="assets/images/splash/demo/coming-soon-2.png" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="#">Coming Soon <span class="btn-icon"><i class="feather-arrow-right"></i></span></a>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item coming-soon">
                                                    <div class="demo-single">
                                                        <div class="inner disable">
                                                            <div class="thumbnail">
                                                                <a href="#"><img src="assets/images/splash/demo/coming-soon-3.png" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="#">Coming Soon <span class="btn-icon"><i class="feather-arrow-right"></i></span></a>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <!-- Start Single Demo  -->
                                                <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item coming-soon">
                                                    <div class="demo-single">
                                                        <div class="inner disable">
                                                            <div class="thumbnail">
                                                                <a href="#"><img src="assets/images/splash/demo/coming-soon-1.png" alt="DatalabAcademy kursları"></a>
                                                            </div>
                                                            <div class="content">
                                                                <h4 class="title"><a href="#">Coming Soon <span class="btn-icon"><i class="feather-arrow-right"></i></span></a>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Single Demo  -->

                                                <div class="load-demo-btn-wrap">
                                                    <div class="load-demo-btn text-center">
                                                        <span class="color-white b3">Scroll to view more <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down-up" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M11.5 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L11 2.707V14.5a.5.5 0 0 0 .5.5zm-7-14a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L4 13.293V1.5a.5.5 0 0 1 .5-.5z"/>
                                  </svg></span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Mega Menu  -->
                                </li>

                                <li class="has-dropdown has-menu-child-item">
                                    <a href="course-filter-one-open.html"><span data-i18n="nav_courses">Kurslar</span> <i class="feather-chevron-down"></i></a>
                                    <ul class="submenu">
                                        <li><a href="course-filter-one-open.html"><i class="feather-bar-chart-2"></i> Data Analitika</a></li>
                                        <li><a href="course-filter-one-open.html"><i class="feather-database"></i> SQL Developer</a></li>
                                        <li><a href="course-filter-one-open.html"><i class="feather-grid"></i> Excel</a></li>
                                        <li><a href="course-filter-two-toggle.html"><i class="feather-monitor"></i> <span data-i18n="nav_online_training">Online təlimlər</span></a></li>
                                    </ul>
                                </li>

                                <li><a href="about" data-i18n="nav_about">Haqqımızda</a></li>
                                <li><a href="blog-with-sidebar" data-i18n="nav_blog">Bloq</a></li>
                                <li><a href="index.php#contact" data-i18n="nav_contact">Əlaqə</a></li>
                            </ul>
                        </nav>
                    </div>
                    <div class="header-right">
                        <div class="rbt-btn-wrapper d-none d-xl-block">
                            <a class="rbt-btn rbt-switch-btn btn-gradient btn-sm hover-transform-none" href="#">
                                <span data-text="Join Now" data-i18n="join_now" data-i18n-data-text="join_now">Join Now</span>
                            </a>
                        </div>
                        <div class="datalab-sticky-switchers">
                            <div class="header-info">
                                <ul class="rbt-dropdown-menu switcher-language">
                                    <li class="has-child-menu">
                                        <a href="#" class="js-lang-current">
                                            <span class="menu-item js-current-lang">Azərbaycan</span>
                                            <i class="right-icon feather-chevron-down"></i>
                                        </a>
                                        <ul class="sub-menu hover-reverse">
                                            <li><a href="#" data-lang="az"><span class="menu-item" data-i18n="lang_az">Azərbaycan</span></a></li>
                                            <li><a href="#" data-lang="en"><span class="menu-item" data-i18n="lang_en">English</span></a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="header-info">
                                <ul class="rbt-dropdown-menu currency-menu">
                                    <li class="has-child-menu">
                                        <a href="#">
                                            <span class="menu-item js-current-currency">AZN</span>
                                            <i class="right-icon feather-chevron-down"></i>
                                        </a>
                                        <ul class="sub-menu hover-reverse">
                                            <li><a href="#" data-currency="USD"><span class="menu-item">USD</span></a></li>
                                            <li><a href="#" data-currency="AZN"><span class="menu-item">AZN</span></a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- Start Mobile-Menu-Bar -->
                        <div class="mobile-menu-bar d-block d-xl-none">
                            <div class="hamberger">
                                <button class="hamberger-button rbt-round-btn">
                                    <i class="feather-menu"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Start Mobile-Menu-Bar -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Start Side Vav -->
        <div class="rbt-offcanvas-side-menu rbt-category-sidemenu">
            <div class="inner-wrapper">
                <div class="inner-top">
                    <div class="inner-title">
                        <h4 class="title" data-i18n="course_category">Course Category</h4>
                    </div>
                    <div class="rbt-btn-close">
                        <button class="rbt-close-offcanvas rbt-round-btn"><i class="feather-x"></i></button>
                    </div>
                </div>
                <nav class="side-nav w-100">
                    <ul class="rbt-vertical-nav-list-wrapper vertical-nav-menu">
                        <li class="vertical-nav-item">
                            <a href="#">Kurs kataloqu</a>
                            <div class="vartical-nav-content-menu-wrapper">
                                <div class="vartical-nav-content-menu">
                                    <h3 class="rbt-short-title">Kurslar</h3>
                                    <ul class="rbt-vertical-nav-list-wrapper">
                                        <li><a href="#">Data Analitika</a></li>
                                        <li><a href="#">SQL Developer</a></li>
                                        <li><a href="#">Excel</a></li>
                                        <li><a href="#">AI ilə Effektiv İş</a></li>
                                    </ul>
                                </div>
                                <div class="vartical-nav-content-menu">
                                    <h3 class="rbt-short-title">Kurslar</h3>
                                    <ul class="rbt-vertical-nav-list-wrapper">
                                        <li><a href="#">Data Analitika</a></li>
                                        <li><a href="#">SQL Developer</a></li>
                                        <li><a href="#">Excel</a></li>
                                        <li><a href="#">AI ilə Effektiv İş</a></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li class="vertical-nav-item">
                            <a href="#">Onlayn kurslar</a>
                            <div class="vartical-nav-content-menu-wrapper">
                                <div class="vartical-nav-content-menu">
                                    <h3 class="rbt-short-title">Kurslar</h3>
                                    <ul class="rbt-vertical-nav-list-wrapper">
                                        <li><a href="#">Data Analitika</a></li>
                                        <li><a href="#">SQL Developer</a></li>
                                        <li><a href="#">Excel</a></li>
                                        <li><a href="#">AI ilə Effektiv İş</a></li>
                                    </ul>
                                </div>
                                <div class="vartical-nav-content-menu">
                                    <h3 class="rbt-short-title">Kurslar</h3>
                                    <ul class="rbt-vertical-nav-list-wrapper">
                                        <li><a href="#">Data Analitika</a></li>
                                        <li><a href="#">SQL Developer</a></li>
                                        <li><a href="#">Excel</a></li>
                                        <li><a href="#">AI ilə Effektiv İş</a></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li class="vertical-nav-item">
                            <a href="#">Excel</a>
                            <div class="vartical-nav-content-menu-wrapper">
                                <div class="vartical-nav-content-menu">
                                    <h3 class="rbt-short-title">Kurslar</h3>
                                    <ul class="rbt-vertical-nav-list-wrapper">
                                        <li><a href="#">Data Analitika</a></li>
                                        <li><a href="#">SQL Developer</a></li>
                                        <li><a href="#">Excel</a></li>
                                        <li><a href="#">AI ilə Effektiv İş</a></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li class="vertical-nav-item">
                            <a href="#">Datalab kursları</a>
                            <div class="vartical-nav-content-menu-wrapper">
                                <div class="vartical-nav-content-menu">
                                    <h3 class="rbt-short-title">Kurslar</h3>
                                    <ul class="rbt-vertical-nav-list-wrapper">
                                        <li><a href="#">Data Analitika</a></li>
                                        <li><a href="#">SQL Developer</a></li>
                                        <li><a href="#">Excel</a></li>
                                        <li><a href="#">AI ilə Effektiv İş</a></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="read-more-btn">
                        <div class="rbt-btn-wrapper mt--20">
                            <a class="rbt-btn btn-border-gradient radius-round btn-sm hover-transform-none w-100 justify-content-center text-center" href="#">
                                <span>Kurslara bax</span>
                            </a>
                        </div>
                    </div>
                </nav>
                <div class="rbt-offcanvas-footer">

                </div>
            </div>
        </div>
        <!-- End Side Vav -->
        <a class="rbt-close_side_menu" href="javascript:void(0);"></a>
    </header>
    <!-- Mobile Menu Section -->
    <div class="popup-mobile-menu">
        <div class="inner-wrapper">
            <div class="inner-top">
                <div class="content">
                    <div class="logo">
                        <div class="logo logo-dark">
                            <a href="index.php">
                                <img class="datalab-logo" src="assets/images/logo/datalab-logo-transparent.png" alt="DatalabAcademy">
                            </a>
                        </div>

                        <div class="logo d-none logo-light">
                            <a href="index.php">
                                <img class="datalab-logo" src="assets/images/logo/datalab-logo-transparent.png" alt="DatalabAcademy">
                            </a>
                        </div>
                    </div>
                    <div class="rbt-btn-close">
                        <button class="close-button rbt-round-btn"><i class="feather-x"></i></button>
                    </div>
                </div>
                <p class="description">DatalabAcademy praktiki Data Analitika, SQL, Excel və AI kursları təqdim edir.</p>
                <ul class="navbar-top-left rbt-information-list justify-content-start">
                    <li>
                        <a href="mailto:hello@example.com"><i class="feather-mail"></i>example@gmail.com</a>
                    </li>
                    <li>
                        <a href="#"><i class="feather-phone"></i>(302) 555-0107</a>
                    </li>
                </ul>
            </div>

            <nav class="mainmenu-nav">
                <ul class="mainmenu">
                    <li class="">
                        <a href="index.php"><span data-i18n="nav_home">Ana səhifə</span></a>
                        <!-- Start Mega Menu  -->
                        <div class="rbt-megamenu menu-skin-dark">
                            <div class="wrapper">
                                <div class="row row--15 home-plesentation-wrapper single-dropdown-menu-presentation">

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h1.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Ana səhifə <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h12.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Kurs kataloqu <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h4.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Excel <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h13.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Data Analitika <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h14.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">DatalabAcademy <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h9.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">AI ilə Effektiv İş <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h3.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Onlayn kurslar <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h6.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Data Analitika <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h15.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Texnologiya kursları <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h7.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Təlimçi dəstəyi <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h8.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Praktiki təlimlər <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h11.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Kurs detalları <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h10.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Onlayn kurs <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h5.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Datalab kursları <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h2.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Kurs kataloqu <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h16.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Karyera kursları <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <span class="new-batch">New Added</span>
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h17.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">DatalabAcademy <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <span class="new-batch">New Added</span>
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h18.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Təlimçi dəstəyi <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <span class="new-batch">New Added</span>
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h19.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Data Analitika <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <span class="new-batch">New Added</span>
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h20.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">AZ/EN tədris <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <span class="new-batch">New Added</span>
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h21.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Excel və dashboard <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <span class="new-batch">New Added</span>
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h22.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Seçilmiş kurslar <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <span class="new-batch">New Added</span>
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h23.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Mentor dəstəyi <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <span class="new-batch">New Added</span>
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h24.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Praktiki bacarıqlar <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <span class="new-batch">New Added</span>
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h25.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Karyera inkişafı <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item">
                                        <div class="demo-single">
                                            <div class="inner">
                                                <span class="new-batch">New Added</span>
                                                <div class="thumbnail">
                                                    <a href="course-filter-one-open.html"><img src="assets/images/splash/demo/h26.jpg" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="course-filter-one-open.html">Təlim icması <span class="btn-icon"><i class="feather-arrow-right"></i></span></a></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item coming-soon">
                                        <div class="demo-single">
                                            <div class="inner disable">
                                                <div class="thumbnail">
                                                    <a href="#"><img src="assets/images/splash/demo/coming-soon-1.png" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="#">Coming Soon <span class="btn-icon"><i class="feather-arrow-right"></i></span></a>
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item coming-soon">
                                        <div class="demo-single">
                                            <div class="inner disable">
                                                <div class="thumbnail">
                                                    <a href="#"><img src="assets/images/splash/demo/coming-soon-2.png" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="#">Coming Soon <span class="btn-icon"><i class="feather-arrow-right"></i></span></a>
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item coming-soon">
                                        <div class="demo-single">
                                            <div class="inner disable">
                                                <div class="thumbnail">
                                                    <a href="#"><img src="assets/images/splash/demo/coming-soon-3.png" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="#">Coming Soon <span class="btn-icon"><i class="feather-arrow-right"></i></span></a>
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <!-- Start Single Demo  -->
                                    <div class="col-lg-12 col-xl-2 col-xxl-2 col-md-12 col-sm-12 col-12 single-mega-item coming-soon">
                                        <div class="demo-single">
                                            <div class="inner disable">
                                                <div class="thumbnail">
                                                    <a href="#"><img src="assets/images/splash/demo/coming-soon-1.png" alt="DatalabAcademy kursları"></a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="#">Coming Soon <span class="btn-icon"><i class="feather-arrow-right"></i></span></a>
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Demo  -->

                                    <div class="load-demo-btn-wrap">
                                        <div class="load-demo-btn text-center">
                                            <span class="color-white b3">Scroll to view more <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down-up" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M11.5 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L11 2.707V14.5a.5.5 0 0 0 .5.5zm-7-14a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L4 13.293V1.5a.5.5 0 0 1 .5-.5z"/>
                                  </svg></span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- End Mega Menu  -->
                    </li>

                    <li class="has-dropdown has-menu-child-item">
                        <a href="course-filter-one-open.html"><span data-i18n="nav_courses">Kurslar</span> <i class="feather-chevron-down"></i></a>
                        <ul class="submenu">
                            <li><a href="course-filter-one-open.html"><i class="feather-bar-chart-2"></i> Data Analitika</a></li>
                            <li><a href="course-filter-one-open.html"><i class="feather-database"></i> SQL Developer</a></li>
                            <li><a href="course-filter-one-open.html"><i class="feather-grid"></i> Excel</a></li>
                            <li><a href="course-filter-two-toggle.html"><i class="feather-monitor"></i> <span data-i18n="nav_online_training">Online təlimlər</span></a></li>
                        </ul>
                    </li>

                    <li><a href="about" data-i18n="nav_about">Haqqımızda</a></li>
                    <li><a href="blog-with-sidebar" data-i18n="nav_blog">Bloq</a></li>
                    <li><a href="index.php#contact" data-i18n="nav_contact">Əlaqə</a></li>
                </ul>
            </nav>

            <div class="mobile-menu-bottom">
                <div class="rbt-btn-wrapper mb--20">
                    <a class="rbt-btn btn-border-gradient radius-round btn-sm hover-transform-none w-100 justify-content-center text-center" href="#">
                        <span>Enroll Now</span>
                    </a>
                </div>

                <div class="social-share-wrapper">
                    <span class="rbt-short-title d-block">Find With Us</span>
                    <ul class="social-icon social-default transparent-with-border justify-content-start mt--20">
                        <li><a href="https://www.facebook.com/">
                                <i class="feather-facebook"></i>
                            </a>
                        </li>
                        <li><a href="https://www.twitter.com">
                                <i class="feather-twitter"></i>
                            </a>
                        </li>
                        <li><a href="https://www.instagram.com/">
                                <i class="feather-instagram"></i>
                            </a>
                        </li>
                        <li><a href="https://www.linkdin.com/">
                                <i class="feather-linkedin"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
    <!-- Start Side Vav -->
    <div class="rbt-cart-side-menu">
        <div class="inner-wrapper">
            <div class="inner-top">
                <div class="content">
                    <div class="title">
                        <h4 class="title mb--0">Your shopping cart</h4>
                    </div>
                    <div class="rbt-btn-close" id="btn_sideNavClose">
                        <button class="minicart-close-button rbt-round-btn"><i class="feather-x"></i></button>
                    </div>
                </div>
            </div>
            <nav class="side-nav w-100">
                <ul class="rbt-minicart-wrapper">
                    <li class="minicart-item">
                        <div class="thumbnail">
                            <a class="js-course-open" href="#course-1" data-course-id="1">
                                <img src="assets/images/course/datalab-data-analitika.svg" alt="Data Analitika">
                            </a>
                        </div>
                        <div class="product-content">
                            <h6 class="title"><a class="js-course-open" href="#course-1" data-course-id="1">Data Analitika</a></h6>

                            <span class="quantity">1 * <span class="price" data-usd="180">$180</span></span>
                        </div>
                        <div class="close-btn">
                            <button class="rbt-round-btn"><i class="feather-x"></i></button>
                        </div>
                    </li>

                    <li class="minicart-item">
                        <div class="thumbnail">
                            <a class="js-course-open" href="#course-2" data-course-id="2">
                                <img src="assets/images/course/datalab-sql-developer.svg" alt="SQL Developer">
                            </a>
                        </div>
                        <div class="product-content">
                            <h6 class="title"><a class="js-course-open" href="#course-2" data-course-id="2">SQL Developer</a></h6>

                            <span class="quantity">1 * <span class="price" data-usd="220">$220</span></span>
                        </div>
                        <div class="close-btn">
                            <button class="rbt-round-btn"><i class="feather-x"></i></button>
                        </div>
                    </li>

                    <li class="minicart-item">
                        <div class="thumbnail">
                            <a class="js-course-open" href="#course-3" data-course-id="3">
                                <img src="assets/images/course/datalab-excel.svg" alt="Excel">
                            </a>
                        </div>
                        <div class="product-content">
                            <h6 class="title"><a class="js-course-open" href="#course-3" data-course-id="3">Excel</a></h6>

                            <span class="quantity">1 * <span class="price" data-usd="40">$40</span></span>
                        </div>
                        <div class="close-btn">
                            <button class="rbt-round-btn"><i class="feather-x"></i></button>
                        </div>
                    </li>

                    <li class="minicart-item">
                        <div class="thumbnail">
                            <a class="js-course-open" href="#course-4" data-course-id="4">
                                <img src="assets/images/course/datalab-ai.svg" alt="AI ilə Effektiv İş">
                            </a>
                        </div>
                        <div class="product-content">
                            <h6 class="title"><a class="js-course-open" href="#course-4" data-course-id="4">AI ilə Effektiv İş</a></h6>

                            <span class="quantity">1 * <span class="price" data-usd="150">$150</span></span>
                        </div>
                        <div class="close-btn">
                            <button class="rbt-round-btn"><i class="feather-x"></i></button>
                        </div>
                    </li>
                </ul>
            </nav>

            <div class="rbt-minicart-footer">
                <hr class="mb--0">
                <div class="rbt-cart-subttotal">
                    <p class="subtotal"><strong>Yekun:</strong></p>
                    <p class="price" data-usd="590">$590</p>
                </div>
                <hr class="mb--0">
                <div class="rbt-minicart-bottom mt--20">
                    <div class="view-cart-btn">
                        <a class="rbt-btn btn-border icon-hover w-100 text-center" href="cart.html">
                            <span class="btn-text">Səbətə bax</span>
                            <span class="btn-icon"><i class="feather-arrow-right"></i></span>
                        </a>
                    </div>
                    <div class="checkout-btn mt--20">
                        <a class="rbt-btn btn-gradient icon-hover w-100 text-center" href="checkout.html">
                            <span class="btn-text">Ödəniş</span>
                            <span class="btn-icon"><i class="feather-arrow-right"></i></span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- End Side Vav -->
    <a class="close_side_menu" href="javascript:void(0);"></a>
        <main class="datalab-blog-area datalab-blog-detail-page">
        <section class="datalab-blog-detail-hero">
            <div class="container">
                <ul class="page-list">
                    <li class="rbt-breadcrumb-item"><a href="index.php" data-i18n="nav_home">Ana səhifə</a></li>
                    <li><div class="icon-right"><i class="feather-chevron-right"></i></div></li>
                    <li class="rbt-breadcrumb-item"><a href="blog-with-sidebar" data-blog-detail-i18n="detail_bc_blog">Bloq</a></li>
                    <li><div class="icon-right"><i class="feather-chevron-right"></i></div></li>
                    <li class="rbt-breadcrumb-item active"><?php echo dl_blog_e((string) ($dlBlogPost['category'] ?? 'Bloq')); ?></li>
                </ul>
                <span class="datalab-blog-chip mx-auto"><?php echo dl_blog_e((string) ($dlBlogPost['category'] ?? 'Bloq')); ?></span>
                <h1 class="title" data-dl-blog-lang data-az="<?php echo dl_blog_e($dlDetailTitleAz); ?>" data-en="<?php echo dl_blog_e($dlDetailTitleEn); ?>"><?php echo dl_blog_e($dlDetailTitleAz ?: $dlDetailTitleEn); ?></h1>
                <p class="description" data-dl-blog-lang data-az="<?php echo dl_blog_e($dlDetailExcerptAz); ?>" data-en="<?php echo dl_blog_e($dlDetailExcerptEn); ?>"><?php echo dl_blog_e($dlDetailExcerptAz ?: $dlDetailExcerptEn); ?></p>
                <ul class="datalab-detail-meta">
                    <li><i class="feather-user"></i> <?php echo dl_blog_e((string) ($dlBlogPost['author'] ?? 'DatalabAcademy')); ?></li>
                    <li><i class="feather-calendar"></i> <?php echo dl_blog_e(dl_blog_date((string) ($dlBlogPost['publishedDate'] ?? ''))); ?></li>
                    <li><i class="feather-watch"></i> <span data-dl-blog-lang data-az="<?php echo dl_blog_e($dlDetailReadAz); ?>" data-en="<?php echo dl_blog_e($dlDetailReadEn); ?>"><?php echo dl_blog_e($dlDetailReadAz ?: $dlDetailReadEn); ?></span></li>
                </ul>
            </div>
        </section>
        <section class="rbt-section-gapBottom rbt-section-overlayping-top">
            <div class="container">
                <div class="datalab-detail-cover mb--50">
                    <img src="<?php echo dl_blog_e(dl_blog_image($dlBlogPost)); ?>" alt="<?php echo dl_blog_e($dlDetailTitleAz ?: $dlDetailTitleEn); ?>" onerror="this.onerror=null;this.src='assets/images/blog/blog-grid-01.jpg';">
                </div>
                <div class="datalab-article-layout">
                    <article class="datalab-article-card">
                        <div data-dl-blog-rich data-az="<?php echo dl_blog_e($dlDetailContentAz); ?>" data-en="<?php echo dl_blog_e($dlDetailContentEn); ?>">
                            <?php echo dl_blog_render_rich_blocks($dlDetailContentAz, $dlDetailContentEn); ?>
                        </div>
                        <div class="datalab-article-note" data-blog-detail-i18n="detail_note">Əsas üstünlük AI-ın insanı əvəz etməsi deyil, analitikin təkrar işlərə daha az vaxt sərf edib qərar verməyə daha çox fokuslanmasıdır.</div>
                        <div class="rbt-button-group mt--35">
                            <a class="rbt-btn btn-gradient hover-icon-reverse" href="course-filter-two-toggle.php"><span class="icon-reverse-wrapper"><span class="btn-text" data-blog-detail-i18n="detail_course_cta">Online təlimə bax</span><span class="btn-icon"><i class="feather-arrow-right"></i></span><span class="btn-icon"><i class="feather-arrow-right"></i></span></span></a>
                            <a class="rbt-btn btn-border hover-icon-reverse" href="blog-with-sidebar"><span class="icon-reverse-wrapper"><span class="btn-text" data-blog-detail-i18n="detail_back_cta">Bloqa qayıt</span><span class="btn-icon"><i class="feather-arrow-left"></i></span><span class="btn-icon"><i class="feather-arrow-left"></i></span></span></a>
                        </div>
                    </article>
                    <aside class="datalab-detail-sidebar">
                        <div class="rbt-single-widget">
                            <h4 class="rbt-widget-title" data-blog-detail-i18n="detail_sidebar_title">Məqalə mövzuları</h4>
                            <div class="rbt-sidebar-list-wrapper rbt-tag-list"><a href="#">AI</a><a href="#">Data Analitika</a><a href="#">SQL</a><a href="#">Excel</a><a href="#">Dashboard</a></div>
                        </div>
                        <div class="rbt-single-widget">
                            <h4 class="rbt-widget-title" data-blog-detail-i18n="detail_related_title">Oxşar yazılar</h4>
                            <ul class="rbt-sidebar-list-wrapper recent-post-list">
                                <?php foreach (array_slice($dlRelatedPosts, 0, 2) as $related):
                                    $relatedHref = 'blog-details?slug=' . rawurlencode((string) ($related['slug'] ?? $related['id'] ?? ''));
                                    $relatedTitleAz = (string) ($related['titleAz'] ?? '');
                                    $relatedTitleEn = (string) (($related['titleEn'] ?? '') ?: $relatedTitleAz);
                                ?>
                                <li><div class="thumbnail"><a href="<?php echo dl_blog_e($relatedHref); ?>"><img src="<?php echo dl_blog_e(dl_blog_image($related)); ?>" alt="<?php echo dl_blog_e($relatedTitleAz ?: $relatedTitleEn); ?>" onerror="this.onerror=null;this.src='assets/images/blog/blog-grid-01.jpg';"></a></div><div class="content"><h6 class="title"><a href="<?php echo dl_blog_e($relatedHref); ?>" data-dl-blog-lang data-az="<?php echo dl_blog_e($relatedTitleAz); ?>" data-en="<?php echo dl_blog_e($relatedTitleEn); ?>"><?php echo dl_blog_e($relatedTitleAz ?: $relatedTitleEn); ?></a></h6><ul class="rbt-meta"><li><i class="feather-clock"></i><?php echo dl_blog_e(dl_blog_date((string) ($related['publishedDate'] ?? ''))); ?></li></ul></div></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    </main>    <!-- Start Footer aera -->
    <footer class="rbt-footer footer-style-1 bg-color-white overflow-hidden">
        <div class="footer-top">
            <div class="container">
                <div class="row g-5">
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="footer-widget">
                            <div class="logo logo-dark">
                                <a href="index.php">
                                    <img src="assets/images/logo/datalab-logo-transparent.png" alt="DatalabAcademy">
                                </a>
                            </div>
                            <div class="logo d-none logo-light">
                                <a href="index.php">
                                    <img src="assets/images/logo/datalab-logo-transparent.png" alt="DatalabAcademy">
                                </a>
                            </div>

                            <p class="description mt--20" data-i18n="footer_about_text">DatalabAcademy praktiki Data Analitika, SQL, Excel və AI təlimləri ilə karyera bacarıqlarınızı inkişaf etdirir.</p>

                            <ul class="social-icon social-default justify-content-start">
                                <li><a href="https://www.facebook.com/">
                                        <i class="feather-facebook"></i>
                                    </a>
                                </li>
                                <li><a href="https://www.twitter.com">
                                        <i class="feather-twitter"></i>
                                    </a>
                                </li>
                                <li><a href="https://www.instagram.com/">
                                        <i class="feather-instagram"></i>
                                    </a>
                                </li>
                                <li><a href="https://www.linkdin.com/">
                                        <i class="feather-linkedin"></i>
                                    </a>
                                </li>
                            </ul>

                            <div class="contact-btn mt--30">
                                <a class="rbt-btn hover-icon-reverse btn-border-gradient radius-round" href="#">
                                    <div class="icon-reverse-wrapper">
                                        <span class="btn-text" data-i18n="footer_contact_cta">Contact With Us</span>
                                        <span class="btn-icon"><i class="feather-arrow-right"></i></span>
                                        <span class="btn-icon"><i class="feather-arrow-right"></i></span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-6 col-sm-6 col-12">
                        <div class="footer-widget">
                            <h5 class="ft-title" data-i18n="footer_useful_links">Faydalı linklər</h5>
                            <ul class="ft-link">
                                <li>
                                    <a href="index.php" data-i18n="footer_link_home">Ana səhifə</a>
                                </li>
                                <li>
                                    <a href="course-filter-one-open.html" data-i18n="footer_link_courses">Kurslar</a>
                                </li>
                                <li>
                                    <a href="about" data-i18n="footer_link_about">Haqqımızda</a>
                                </li>
                                <li>
                                    <a href="blog-with-sidebar" data-i18n="footer_link_blog">Bloq</a>
                                </li>
                                <li>
                                    <a href="index.php#contact" data-i18n="footer_link_contact">Əlaqə</a>
                                </li>
                                <li>
                                    <a href="privacy-policy.html" data-i18n="privacy_policy">Məxfilik siyasəti</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-6 col-sm-6 col-12">
                        <div class="footer-widget">
                            <h5 class="ft-title" data-i18n="footer_our_company">Kurslarımız</h5>
                            <ul class="ft-link">
                                <li>
                                    <a class="js-course-open" href="#course-1" data-course-id="1" data-i18n="footer_course_1">Data Analitika</a>
                                </li>
                                <li>
                                    <a class="js-course-open" href="#course-2" data-course-id="2" data-i18n="footer_course_2">SQL Developer</a>
                                </li>
                                <li>
                                    <a class="js-course-open" href="#course-3" data-course-id="3" data-i18n="footer_course_3">Excel</a>
                                </li>
                                <li>
                                    <a class="js-course-open" href="#course-4" data-course-id="4" data-i18n="footer_course_4">AI ilə Effektiv İş</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="footer-widget">
                            <h5 class="ft-title" data-i18n="get_contact">Əlaqə məlumatı</h5>
                            <ul class="ft-link">
                                <li><span data-i18n="phone">Telefon:</span> <a href="tel:+994506549737">+994 50 654 97 37</a></li>
                                <li><span data-i18n="email">E-poçt:</span> <a href="mailto:info@datalabacademy.az">info@datalabacademy.az</a></li>
                                <li><span data-i18n="footer_address_label">Ünvan:</span> <span data-i18n="footer_address">Bakı, Azərbaycan</span></li>
                            </ul>

                            <form class="newsletter-form mt--20" action="#">
                                <h6 class="w-600" data-i18n="newsletter">Newsletter</h6>
                                <p class="description" data-i18n-html="newsletter_desc">Yeni qrup açılışları, faydalı materiallar və kampaniyalar üçün e-poçtunuzu qeyd edin.</p>

                                <div class="form-group right-icon icon-email mb--20">
                                    <label for="email" data-i18n="enter_email">Enter Your Email Here</label>
                                    <input id="email" type="email">
                                </div>

                                <div class="form-group mb--0">
                                    <button class="rbt-btn rbt-switch-btn btn-gradient radius-round btn-sm" type="submit">
                                        <span data-text="Submit Now" data-i18n="submit_now" data-i18n-data-text="submit_now">Submit Now</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="rbt-separator-mid">
            <div class="container">
                <hr class="rbt-separator m-0">
            </div>
        </div>
        <!-- Start Copyright Area  -->
        <div class="copyright-area copyright-style-1 ptb--20">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-12">
                        <p class="rbt-link-hover text-center text-lg-start" data-i18n-html="copyright_text">Copyright © 2026 DatalabAcademy. Bütün hüquqlar qorunur.</p>
                    </div>
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-12">
                        <ul class="copyright-link rbt-link-hover justify-content-center justify-content-lg-end mt_sm--10 mt_md--10">
                            <li><a href="#" data-i18n="terms_service">Terms of service</a></li>
                            <li><a href="privacy-policy.html" data-i18n="privacy_policy">Privacy policy</a></li>
                            <li><a href="subscription.html" data-i18n="subscription">Subscription</a></li>
                            <li><a href="login.html" data-i18n="login_register">Login & Register</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Copyright Area  -->
    </footer>
    <!-- End Footer aera -->
    <div class="rbt-progress-parent">
        <svg class="rbt-back-circle svg-inner" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>

    <!-- JS
============================================ -->
    <!-- Modernizer JS -->
    <script src="assets/js/vendor/modernizr.min.js"></script>
    <!-- jQuery JS -->
    <script src="assets/js/vendor/jquery.js"></script>
    <!-- Bootstrap JS -->
    <script src="assets/js/vendor/bootstrap.min.js"></script>
    <!-- sal.js -->
    <script src="assets/js/vendor/sal.js"></script>
    <!-- Dark Mode Switcher -->
    <script src="assets/js/vendor/js.cookie.js"></script>
    <script src="assets/js/vendor/jquery.style.switcher.js"></script>
    <script src="assets/js/vendor/swiper.js"></script>
    <script src="assets/js/vendor/jquery-appear.js"></script>
    <script src="assets/js/vendor/odometer.js"></script>
    <script src="assets/js/vendor/backtotop.js"></script>
    <script src="assets/js/vendor/isotop.js"></script>
    <script src="assets/js/vendor/imageloaded.js"></script>

    <script src="assets/js/vendor/wow.js"></script>
    <script src="assets/js/vendor/waypoint.min.js"></script>
    <script src="assets/js/vendor/easypie.js"></script>
    <script src="assets/js/vendor/text-type.js"></script>
    <script src="assets/js/vendor/jquery-one-page-nav.js"></script>
    <script src="assets/js/vendor/bootstrap-select.min.js"></script>
    <script src="assets/js/vendor/jquery-ui.js"></script>
    <script src="assets/js/vendor/magnify-popup.min.js"></script>
    <script src="assets/js/vendor/paralax-scroll.js"></script>
    <script src="assets/js/vendor/paralax.min.js"></script>
    <script src="assets/js/vendor/countdown.js"></script>
    <script src="assets/js/vendor/plyr.js"></script>
    <script src="assets/js/vendor/jodit.min.js"></script>
    <script src="assets/js/vendor/Sortable.min.js"></script>



    <!-- Main JS -->
    <div data-datalab-cart></div>
    <div data-datalab-footer></div>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/datalab-shared.js?v=20260627-hdr-2"></script>
    <script>
    (function () {
        const dict = {
            az: {
                lang_az: "Azərbaycan",
                lang_en: "English",
                category_label: "Kateqoriya",
                course_category: "Kurs Kateqoriyası",
                theme_light_label: "Açıq",
                theme_dark_label: "Tünd",
                theme_light_title: "Açıq Rejim",
                theme_dark_title: "Tünd Rejim",
                campaign_badge: "Məhdud Vaxt Təklifi",
                campaign_text: '<img src="assets/images/icons/hand-emojji.svg" alt="Hand Emojji Images"> Yeni qrup qeydiyyatı açıqdır. Data, SQL, Excel və AI kurslarına indi qoşulun.',
                campaign_cta: "İndi alın",
                search_placeholder: "Kurs axtarın",
                cart: "Səbət",
                view_profile: "Profilə bax",
                nav_home: "Ana səhifə",
                nav_courses: "Kurslar",
                nav_online_training: "Online təlimlər",
                nav_pages: "Səhifələr",
                nav_elements: "Elementlər",
                nav_blog: "Bloq",
                hero_title: 'Karyeranıza təkan verən ən böyük <span class="color-primary">Onlayn Təhsil</span> platforması.',
                hero_desc: 'Data Analitika, SQL, Excel və AI kursları ilə praktik bacarıqlarınızı artırın. Real tapşırıqlar, mentor dəstəyi və <strong>karyera yönümlü proqram</strong>.',
                hero_cta: "Kurslara bax",
                user_dashboard: "İdarə panelim",
                user_bookmark: "Əlfəcin",
                user_enrolled_courses: "Qeydiyyatlı kurslar",
                user_wishlist: "İstək siyahısı",
                user_reviews: "Rəylər",
                user_my_quiz_attempts: "Quiz cəhdlərim",
                user_order_history: "Sifariş tarixçəsi",
                user_qa: "Sual & Cavab",
                user_getting_started: "Başlanğıc",
                user_settings: "Parametrlər",
                user_logout: "Çıxış",
                testimonial_section_subtitle: "Hər kəs üçün təhsil",
                testimonial_section_title: 'Tələbələrin <span class="color-primary">rəyləri</span>',
                testimonial_1_role: "SQL Developer kursunun tələbəsi",
                testimonial_1_text: "SQL Developer kursu çox faydalı oldu. JOIN-lar, indekslər və real tapşırıqlar sayəsində sorğularım daha sürətli və daha düzgün işləməyə başladı. Dərslərin izahı aydın idi və praktika hissəsi xüsusilə xoşuma gəldi.",
                testimonial_2_role: 'Baş icraçı direktor <i>@ Google</i>',
                testimonial_2_text: "DatalabAcademy ilə öyrənmək həm sürətli, həm də effektlidir. Kurslar strukturlaşdırılıb və praktiki nümunələr kifayət qədərdir.",
                testimonial_3_role: 'İcraçı dizayner <i>@ Google</i>',
                testimonial_3_text: "Tədris materialları keyfiyyətlidir və dizayn baxımından çox rahatdır. Qısa zamanda nəticə görmək mümkündür.",
                news_section_subtitle: "Top xəbərlər",
                news_section_title: 'Xəbərlərimizə <span class="color-primary">nəzər salın</span>',
                news_view_all: "Bütün xəbərlər",
                learn_more: "Daha ətraflı",
                blog_card_1_title: "2026-da AI trendləri: nələr dəyişir?",
                blog_card_1_desc: "Generativ AI alətləri (chatbotlar, agentlər) biznesdə prosesləri necə sürətləndirir və hansı bacarıqlar ön plana çıxır?",
                blog_card_2_title: "SQL + AI: sorğuları necə daha tez yazmaq olar?",
                blog_card_2_desc: "AI köməkçiləri ilə JOIN-lar, indekslər və optimizasiya mövzularında real nümunələrlə daha sürətli nəticə əldə edin.",
                blog_card_3_title: "Data Analitika üçün AI alətləri (praktik siyahı)",
                blog_card_3_desc: "Vizualizasiya, proqnoz və avtomatlaşdırma üçün ən çox istifadə olunan AI alətlərinə qısa baxış.",
                news_modal_badge: "AI xəbəri",
                news_modal_cta: "Kurslara bax",
                news_modal_close: "Bağla",
                news_detail_1_title: "2026-da AI trendləri: nələr dəyişir?",
                news_detail_1_date: "Aprel 2026",
                news_detail_1_body: "<p>2026-da generativ AI artıq təkcə “chat” deyil — <strong>agentlər</strong>, <strong>avtomatlaşdırma</strong> və <strong>iş axınlarına inteqrasiya</strong> əsas trendə çevrilib. İstifadəçilər “mənə cavab ver”dən çox, “mənim yerimə işi gör” gözləntisindədir.</p><blockquote><strong>Qısa fikir:</strong> AI aləti yox, <em>AI proses</em> qurmaq qalib gəlir.</blockquote><hr><h4>1) Agentlər: tapşırıqdan nəticəyə</h4><p>Agent yanaşması “bir sorğu – bir cavab” modelini aşır: agent plan qurur, alt tapşırıqları bölür, yoxlayır və nəticəni təqdim edir. Bu, xüsusilə <strong>analitika</strong>, <strong>müştəri dəstəyi</strong> və <strong>kontent istehsalı</strong> kimi sahələrdə məhsuldarlığı artırır.</p><ul><li>İş axını: <code>tapşırıq → plan → icra → yoxlama → nəticə</code></li><li>Ən çox problem: “yoxlamasız” cavablar və səhv inam (hallucination)</li></ul><h4>2) RAG və “şirkət biliyi”</h4><p>Şirkətlər öz daxili qaydalarını, sənədlərini və dataset-lərini AI-yə “düzgün kontekstdə” vermək üçün RAG (Retrieval-Augmented Generation) yanaşmasına keçir. Məqsəd sadədir: model “ümumi internet” yox, <strong>sizin məlumat</strong> əsasında cavab versin.</p><ul><li>Fayda: daha az uydurma, daha çox dəqiqlik</li><li>Risk: məxfilik və səlahiyyət (access control)</li></ul><h4>3) Keyfiyyətə nəzarət: eval-lər və monitorinq</h4><p>2026-da “AI işləyir” demək kifayət deyil. Komandalar <strong>eval</strong> (qiymətləndirmə), <strong>telemetriya</strong> və <strong>monitorinq</strong> qurur: düzgün cavab faizi, gecikmə, xərclər, riskli mətnlər və s.</p><ul><li>Test dataset-i: real user sualları + gözlənilən cavablar</li><li>Ölçü: dəqiqlik, zərərli məzmun filtri, xərclər</li></ul><h4>4) Bacarıqlar: kimlər önə çıxır?</h4><p>Ən çox dəyər gətirən profil: <strong>problem həlli + data düşüncəsi + avtomatlaşdırma</strong>. Tək “prompt yazma” deyil, workflow dizayn etmək vacibdir.</p><ul><li>SQL + analitika əsasları</li><li>Avtomatlaşdırma düşüncəsi (flow, validation)</li><li>Data təhlükəsizliyi və etik yanaşma</li></ul><p>Biz DatalabAcademy-də bu trendləri real tapşırıqlarla, praktik ssenarilərlə izah edirik: məqsəd “trend bilmək” yox, <strong>işdə tətbiq etməkdir</strong>.</p>",
                news_detail_2_title: "SQL + AI: sorğuları necə daha tez yazmaq olar?",
                news_detail_2_date: "Aprel 2026",
                news_detail_2_body: "<p>AI köməkçiləri SQL yazmağı sürətləndirir, amma ən böyük qazanc <strong>düzgün struktur</strong> və <strong>performans</strong> düşüncəsi ilə gəlir. Yəni məqsəd “tez yazmaq” deyil — <strong>düzgün yazıb</strong> tez işlətməkdir.</p><hr><h4>1) AI ilə sorğu draft etmək (amma nəzarətlə)</h4><p>AI sizə sorğu skeleton-u verə bilər: CTE-lər, JOIN-lar, filtr məntiqi. Amma siz yoxlamalısınız:</p><ul><li>JOIN şərtləri doğrudurmu?</li><li>Null-lar və duplicate-lər nəticəni pozmur ki?</li><li>Tarix filtrləri və timezone doğru qurulub?</li></ul><blockquote><strong>Praktik tövsiyə:</strong> AI-yə tabel sxemlərini və nümunə satırları verin, sonra “expected output”u yazın.</blockquote><h4>2) JOIN strategiyası: LEFT vs INNER, grain</h4><p>Ən çox səhv “grain” (məlumatın dənəvərliyi) anlaşılmayanda olur. Məsələn, orders (order-level) ilə order_items (item-level) JOIN edəndə saylar şişə bilər.</p><ul><li>Öncə grain-i təyin edin</li><li>Sonra aggregation-u planlayın</li><li>Gərəkdirsə, əvvəlcə subquery/CTE ilə xülasə edin</li></ul><h4>3) İndekslər və performans</h4><p>AI indeks təklif edə bilər, amma həmişə “niyə?” sualını verin: filtr + join sütunları, kardinalıq, yazma (insert/update) xərcləri.</p><ul><li>Ən çox fayda: selektiv filtr sütunları</li><li>Join performansı: foreign key sütunları</li><li>Şərtli indekslər: bəzi hallarda çox effektiv</li></ul><h4>4) EXPLAIN/plan oxuma vərdişi</h4><p>Real dünyada qalib skill: sorğu planını oxumaq. Bu, SQL Developer rolu üçün “superpower” sayılır.</p><ul><li>Seq scan niyə oldu?</li><li>Index istifadə edildi mi?</li><li>Join order və cost nə deyir?</li></ul><p>DatalabAcademy-də SQL Developer kursunda bu mövzuları real tapşırıqlarla işləyirik: JOIN-lar, indekslər, optimizasiya və “debug thinking”.</p>",
                news_detail_3_title: "Data Analitika üçün AI alətləri (praktik siyahı)",
                news_detail_3_date: "Aprel 2026",
                news_detail_3_body: "<p>Data analitika dünyasında AI alətləri 2026-da “nice-to-have” deyil, <strong>gündəlik iş axınının</strong> bir hissəsidir. Amma çox adam eyni səhvi edir: alətin çox olmasına baxıb, problem və KPI-ları unudur.</p><blockquote><strong>Qayda:</strong> əvvəl <em>ölç</em>, sonra <em>şərh et</em>, ən sonda <em>avtomatlaşdır</em>.</blockquote><hr><h4>1) Analitika workflow-u: 5 addım</h4><ul><li><strong>Goal</strong>: biznes sualını yazın (məs: “Retention niyə düşür?”)</li><li><strong>Metric</strong>: KPI-ları təyin edin (DAU/MAU, conversion, churn)</li><li><strong>Data</strong>: mənbələri seçin (CRM, web/app events, satış)</li><li><strong>Model</strong>: segmentləşdirmə/proqnoz (sadə başlayın)</li><li><strong>Decision</strong>: nəticəni aksiyaya çevirin (experiment, kampaniya)</li></ul><h4>2) AI harada ən çox fayda verir?</h4><p>AI aləti “məlumatı əvəz etmir” — <strong>analitikin sürətini</strong> artırır. Ən çox fayda bu yerlərdə olur:</p><ul><li><strong>Data cleaning</strong>: sütun mapping, outlier izahı, dublikat aşkarı</li><li><strong>EDA</strong>: “nəyə baxım?” sualına ideya verir</li><li><strong>Vizualizasiya</strong>: qrafik seçimi, chart variantları, hekayələndirmə</li><li><strong>SQL drafting</strong>: CTE strukturu, join skeleton, sənədləşdirmə</li><li><strong>Report automation</strong>: həftəlik insight e-mail, dashboard xülasəsi</li></ul><h4>3) Praktik alət siyahısı (kateqoriya ilə)</h4><p>Bu siyahı “ən yaxşı” yox, <strong>ən çox iş görən</strong> kateqoriyalardır:</p><ul><li><strong>BI/Dashboard</strong>: Looker, Power BI, Tableau (AI assistant funksiyaları ilə)</li><li><strong>Notebook</strong>: Jupyter/Colab + AI code assistant</li><li><strong>Data transform</strong>: dbt + AI ilə test və dokumentasiya</li><li><strong>Tracking</strong>: event schema və naming convention yoxlaması</li><li><strong>Forecast</strong>: sadə baseline modellər + interpretasiya</li></ul><h4>4) Insight yazma (storytelling) – ən böyük fərq burada olur</h4><p>İnsight-lar “maraqlı fakt” deyil. Yaxşı insight 3 hissədən ibarətdir:</p><ul><li><strong>What?</strong> – nə baş verdi (rəqəm)</li><li><strong>Why?</strong> – səbəb hipotezi (segment, funnel step)</li><li><strong>So what?</strong> – qərar (eksperiment, dəyişiklik)</li></ul><blockquote><strong>Nümunə:</strong> “Retention 7% düşüb” yox, “Retention 7% düşüb, səbəb onboarding-də 2-ci addımda drop artıb; A/B test ilə formu qısaldırıq.”</blockquote><h4>5) Səhvlər və risklər</h4><ul><li>AI-dən gələn nəticəni “doğrudur” kimi qəbul etmək</li><li>Dataset-i görmədən “analiz” etmək</li><li>Privacy: şəxsi məlumatları təsadüfən paylaşmaq</li><li>Bias: segmentlər arasında yanlış müqayisələr</li></ul><p>DatalabAcademy-də Data Analitika kursunda bu mövzuları real case-lərlə işləyirik: KPI quruluşu, dashboard dizaynı, SQL, və nəticəni biznes qərarına çevirmə.</p>",
                course_modal_badge: "Kurs",
                course_modal_cta: "Kurslara bax",
                course_modal_register: "Səbətə əlavə et",
                course_modal_telegram: "Telegram",
                course_modal_close: "Bağla",
                course_detail_1_title: "Data Analitika",
                course_detail_1_meta: "16 dərs · 40 tələbə · Praktiki tapşırıqlar",
                course_detail_1_body: "<p><strong>Data Analitika</strong> kursu sizə data düşüncəsini sistemli şəkildə qurmağa kömək edir: biznes sualını KPI-a çevirmək, datanı oxumaq, insight çıxarmaq və nəticəni qərara çevirmək.</p><blockquote><strong>Model:</strong> Sual → KPI → Analiz → Insight → Aksiya</blockquote><hr><h4>Nələr öyrənəcəksiniz?</h4><ul><li>KPI və metrikanın düzgün seçimi (DAU/MAU, conversion, retention)</li><li>EDA: paylanma, outlier, cohort və segment analizi</li><li>Vizualizasiya: doğru qrafik seçimi və hekayələndirmə</li><li>Dashboard düşüncəsi: filtrlər, drill-down və oxunaqlılıq</li><li>Analitik hesabat: “What? Why? So what?” strukturu</li></ul><h4>Kimlər üçündür?</h4><ul><li>Yeni başlayanlar: analitikaya keçid etmək istəyənlər</li><li>Marketinq/satış/operasiya: rəqəmlə işləyən mütəxəssislər</li><li>Junior analyst: düşüncə və strukturunu gücləndirmək istəyənlər</li></ul><h4>Praktika necədir?</h4><p>Hər mövzu real case və mini tapşırıqla möhkəmlənir: KPI dizaynı, dashboard skeci, segment analizi və təqdimat.</p><h4>Yekun nəticə</h4><p>Kursun sonunda 1 mini layihə + təqdimat şablonu ilə portfolionuzu formalaşdırırsınız.</p>",
                course_detail_2_title: "SQL Developer",
                course_detail_2_meta: "18 dərs · 35 tələbə · Real SQL ssenariləri",
                course_detail_2_body: "<p><strong>SQL Developer</strong> kursu verilənlər bazası ilə işləmək üçün ən vacib skill-ləri praktik tapşırıqlarla öyrədir: JOIN-lar, CTE, window functions və performans optimizasiyası.</p><blockquote><strong>Hədəf:</strong> Düzgün sorğu + sürətli nəticə + oxunaqlı struktur</blockquote><hr><h4>Proqram (qısa)</h4><ul><li>SELECT, WHERE, GROUP BY, HAVING (fundamentals)</li><li>JOIN-lar: INNER/LEFT, many-to-many, edge-case-lər</li><li>Subquery və CTE: oxunaqlılıq və re-use</li><li>Window functions: ROW_NUMBER, RANK, LAG/LEAD</li><li>Index-lər və query performance: nə vaxt, niyə?</li><li>EXPLAIN/plan oxuma: bottleneck tapmaq</li></ul><h4>Praktik tapşırıqlar</h4><ul><li>Real dataset üzərində 30+ query ssenarisi</li><li>Səhv JOIN-ları debug etmək</li><li>Performans müqayisəsi: index vs no-index</li></ul><h4>Kimlər üçün?</h4><ul><li>Data Analyst, BI Analyst, Backend developer</li><li>Junior → Mid səviyyəyə çıxmaq istəyənlər</li></ul><p>Kurs boyunca diqqətimiz “copy-paste” yox, <strong>düşüncə modeli</strong> qurmaqdır.</p>",
                course_detail_3_title: "Excel",
                course_detail_3_meta: "14 dərs · 45 tələbə · Dashboard & Power Query",
                course_detail_3_body: "<p><strong>Excel</strong> kursu gündəlik iş üçün ən çox istifadə olunan alətləri real tapşırıqlarla öyrədir: formullar, Pivot Table, Power Query və dashboard quruluşu.</p><blockquote><strong>Prinsip:</strong> Səliqəli data → düzgün model → aydın dashboard</blockquote><hr><h4>Nələr öyrənəcəksiniz?</h4><ul><li>Əsas və qabaqcıl formullar: XLOOKUP, IF, SUMIFS, TEXT, DATE</li><li>Pivot Table: xülasə, drill-down, slicer-lər</li><li>Power Query: import, təmizləmə, transform, merge</li><li>Dashboard: KPI kartları, chart seçimi, filter UX</li><li>Best practices: fayl strukturu, naming, sürət optimizasiyası</li></ul><h4>Praktika</h4><p>Real satış/CRM datası ilə hesabat hazırlayır, dashboard-u sıfırdan qururuq.</p><h4>Yekun</h4><p>Kurs sonunda hazır dashboard template-i və 1 mini layihə ilə çıxırsınız.</p>",
                course_detail_4_title: "AI ilə Effektiv İş",
                course_detail_4_meta: "12 dərs · 60 tələbə · ChatGPT, Claude, Gemini",
                course_detail_4_body: "<p><strong>AI ilə Effektiv İş</strong> kursu süni intellekt alətlərini gündəlik iş axınlarınızda peşəkar şəkildə istifadə etməyi öyrədir: prompt engineering, doğru alət seçimi, etika və real ssenarilər.</p><blockquote><strong>Prinsip:</strong> AI əməyi əvəz etmir — <em>düşüncəni sürətləndirir</em>.</blockquote><hr><h4>Proqram</h4><ul><li>Prompt strukturu: rol + kontekst + tapşırıq + format</li><li>Few-shot, chain-of-thought və iterativ uğunlaşdırma</li><li>Alət müqayisəsi: ChatGPT vs Claude vs Gemini — hansını nə vaxt?</li><li>Halüsinasiyaları aşkar etmək və nəticəni yoxlamaq</li><li>Məxfilik: şəxsi/şirkət datası ilə təhlükəsiz iş</li><li>Etika və müəlliflik: AI-ın hüdudları</li></ul><h4>Real iş ssenariləri</h4><ul><li>Data analizi: CSV oxumaq, insight çıxarmaq, vizualizasiya</li><li>SQL drafting: CTE və JOIN skeleton yaratmaq</li><li>E-poçt və hesabat yazma: tonu və strukturu</li><li>Kod review: bug tutmaq, refactor təklifləri</li><li>Sənəd xülasəsi və meeting note-lar</li><li>Tərcümə və lokalizasiya keyfiyyəti</li></ul><h4>Kimlər üçün?</h4><ul><li>Bilik işçiləri, analitiklər, marketinq və satış komandaları</li><li>Developerlər (kod assistant-ı effektiv istifadə)</li><li>Menecerlər (qərar dəstəyi, hesabat avtomatlaşdırma)</li></ul><h4>Yekun nəticə</h4><p>Kursun sonunda öz iş axınınıza uyğun 5+ prompt template və avtomatlaşdırılmış 1 iş prosesi ilə çıxırsınız.</p>",
                course_4_card_title: "AI ilə Effektiv İş",
                course_4_card_desc: "Süni intellekt alətlərini (ChatGPT, Claude, Gemini) gündəlik iş axınlarınızda peşəkar və etik şəkildə istifadə etməyi öyrənin.",
                cat_panel_analytics: "Analitika",
                cat_panel_sql: "SQL",
                cat_panel_office: "Ofis",
                cat_panel_ai: "AI",
                cat_panel_analytics_title: "Analitika kursları",
                cat_panel_sql_title: "SQL kursları",
                cat_panel_office_title: "Ofis kursları",
                cat_panel_ai_title: "AI kursları",
                cat_panel_view_all: "Bütün kursları gör",
                portfolio_badge: "Portfolio",
                portfolio_title: 'Kurslarda <span class="color-primary">real layihələr</span> hazırlayın',
                portfolio_desc: "Dashboard, hesabat və analiz nümunələri ilə portfolionuzu gücləndirin.",
                portfolio_tag_powerbi: "Power BI",
                portfolio_tag_dashboard: "Dashboard",
                portfolio_tag_report: "Report",
                portfolio_amazon_title: "Amazon məhsul analitikası",
                portfolio_amazon_desc: "Məhsul siyahısı, axtarış və vizual məhsul kartları ilə interaktiv dashboard nümunəsi.",
                portfolio_vehicle_title: "Nəqliyyat KPI hesabatı",
                portfolio_vehicle_desc: "Gəlir, xərc, mənfəət və booking metrikalarını bir ekranda göstərən analiz paneli.",
                portfolio_vehicle_customers_title: "Müştəri mənfəət analizi",
                portfolio_vehicle_customers_desc: "Top və zəif müştəriləri gəlir, xərc və mənfəət üzrə müqayisə edən hesabat.",
                portfolio_sales_period_title: "Dövr üzrə satış analizi",
                portfolio_sales_period_desc: "Aylıq satış, mənfəət və xərc göstəricilərini izləmək üçün interaktiv dashboard.",
                portfolio_sales_segment_title: "Seqment üzrə satış analizi",
                portfolio_sales_segment_desc: "Seqmentlərin satış, mənfəət və marja performansını müqayisə edən layihə.",
                portfolio_sales_product_title: "Məhsul üzrə satış analizi",
                portfolio_sales_product_desc: "Məhsul performansı, COGS və profit margin nəticələrini göstərən portfolio işi.",
                certificate_badge: "Sertifikatlar",
                certificate_title: 'Kursu bitirənlər <span class="color-primary">sertifikat əldə edir</span>',
                certificate_desc: "Təlimi uğurla tamamlayan tələbələr portfolio və karyera profillərində paylaşa biləcəkləri sertifikatlar əldə edirlər.",
                certificate_tag_global: "Beynəlxalq sertifikat",
                certificate_tag_course: "Microsoft sertifikatı",
                certificate_ms_title: "Microsoft Certified: AI Business Professional",
                certificate_ms_desc: "DatalabAcademy-yə qoşulan və proqramı uğurla tamamlayan iştirakçılar beynəlxalq səviyyədə tanınan bu sertifikat nəticəsini CV və LinkedIn profilində paylaşa biləcəklər.",
                certificate_course_title: "Microsoft Office Specialist: Excel Expert (Microsoft 365 Apps)",
                certificate_course_desc: "Kursu tamamlayan iştirakçılar təlim nəticələrini təsdiqləyən bu sertifikatı əldə edib portfolio və karyera profillərində istifadə edə biləcəklər.",
                certificate_view: "Sertifikata bax",
                footer_about_text: "DatalabAcademy praktiki Data Analitika, SQL, Excel və AI təlimləri ilə karyera bacarıqlarınızı inkişaf etdirir.",
                footer_contact_cta: "Bizimlə əlaqə",
                footer_useful_links: "Faydalı linklər",
                footer_link_marketplace: "Kurs kataloqu",
                footer_link_excel: "Excel",
                footer_link_university: "Data Analitika",
                footer_link_gym_coaching: "AI ilə Effektiv İş",
                footer_link_faq: "FAQ",
                footer_link_about_us: "Haqqımızda",
                privacy_policy: "Məxfilik siyasəti",
                footer_our_company: "Şirkət",
                contact_us: "Əlaqə",
                become_teacher: "Müəllim olun",
                instructor: "Təlimçi",
                events: "Tədbirlər",
                course: "Kurs",
                contact: "Əlaqə",
                get_contact: "Əlaqə məlumatı",
                phone: "Telefon:",
                email: "E-poçt:",
                newsletter: "Xəbər bülleteni",
                newsletter_desc: "Yeni qrup açılışları, faydalı materiallar və kampaniyalar üçün e-poçtunuzu qeyd edin.",
                enter_email: "E-poçtunuzu daxil edin",
                submit_now: "Göndər",
                copyright_text: 'Copyright © 2026 DatalabAcademy. Bütün hüquqlar qorunur.',
                terms_service: "Xidmət şərtləri",
                subscription: "Abunəlik",
                login_register: "Giriş & Qeydiyyat"
                ,
                join_now: "Qoşul",
                nav_about: "Haqqımızda",
                nav_contact: "Əlaqə",
                cat_card_analytics_title: "Analitika",
                cat_card_analytics_sub: "1 kurs · Praktiki tapşırıqlar",
                cat_card_sql_title: "SQL",
                cat_card_sql_sub: "1 kurs · JOIN, indeks, performans",
                cat_card_office_title: "Ofis",
                cat_card_office_sub: "1 kurs · Excel, Pivot, Power Query",
                cat_card_ai_title: "AI",
                cat_card_ai_sub: "1 kurs · ChatGPT, Claude, Gemini",
                footer_link_home: "Ana səhifə",
                footer_link_courses: "Kurslar",
                footer_link_about: "Haqqımızda",
                footer_link_blog: "Bloq",
                footer_link_contact: "Əlaqə",
                footer_course_1: "Data Analitika",
                footer_course_2: "SQL Developer",
                footer_course_3: "Excel",
                footer_course_4: "AI ilə Effektiv İş",
                footer_address_label: "Ünvan:",
                footer_address: "Bakı, Azərbaycan"
            },
            en: {
                lang_az: "Azerbaijani",
                lang_en: "English",
                category_label: "Category",
                course_category: "Course Category",
                theme_light_label: "Light",
                theme_dark_label: "Dark",
                theme_light_title: "Light Mode",
                theme_dark_title: "Dark Mode",
                campaign_badge: "Limited Time Offer",
                campaign_text: '<img src="assets/images/icons/hand-emojji.svg" alt="Hand Emojji Images"> New cohort registration is open. Join Data, SQL, Excel and AI courses today.',
                campaign_cta: "Buy Now",
                search_placeholder: "Search courses",
                cart: "Cart",
                view_profile: "View Profile",
                nav_home: "Home",
                nav_courses: "Courses",
                nav_online_training: "Online training",
                nav_pages: "Pages",
                nav_elements: "Elements",
                nav_blog: "Blog",
                hero_title: 'The biggest <span class="color-primary">Online Education</span> platform to boost your career.',
                hero_desc: 'Build practical skills with Data Analytics, SQL, Excel and AI courses. Learn through real tasks, mentor support and a <strong>career-focused program</strong>.',
                hero_cta: "Browse Courses",
                user_dashboard: "My Dashboard",
                user_bookmark: "Bookmark",
                user_enrolled_courses: "Enrolled Courses",
                user_wishlist: "Seçilmiş kurslar",
                user_reviews: "Reviews",
                user_my_quiz_attempts: "My Quiz Attempts",
                user_order_history: "Order History",
                user_qa: "Question & Answer",
                user_getting_started: "Getting Started",
                user_settings: "Settings",
                user_logout: "Logout",
                testimonial_section_subtitle: "EDUCATION FOR EVERYONE",
                testimonial_section_title: 'Student&#39;s <span class="color-primary">Feedback</span>',
                testimonial_1_role: "SQL Developer student",
                testimonial_1_text: "The SQL Developer course was very helpful. With JOINs, indexes, and real-world exercises, my queries became faster and more accurate. The explanations were clear, and the hands-on practice was the best part.",
                testimonial_2_role: 'CEO <i>@ Google</i>',
                testimonial_2_text: "DatalabAcademy education, vulputate at sapien sit amet, auctor iaculis lorem. In vel hend rerit nisi. Vestibulum eget.",
                testimonial_3_role: 'Executive Designer <i>@ Google</i>',
                testimonial_3_text: "Our educational, vulputate at sapien sit amet, auctor iaculis lorem. In vel hend rerit nisi. Vestibulum eget.",
                news_section_subtitle: "Top News",
                news_section_title: 'Have a look on <span class="color-primary">our News</span>',
                news_view_all: "View All News",
                learn_more: "Kurslara bax",
                blog_card_1_title: "AI trends in 2026: what’s changing?",
                blog_card_1_desc: "A quick overview of how generative AI (chatbots, agents) is reshaping workflows and which skills are becoming essential.",
                blog_card_2_title: "SQL + AI: write queries faster (and better)",
                blog_card_2_desc: "Use AI assistants to improve JOINs, indexing decisions, and query performance with practical examples.",
                blog_card_3_title: "AI tools for Data Analytics (practical picks)",
                blog_card_3_desc: "A short list of popular AI tools for visualization, forecasting, and automation to level up analytics work.",
                news_modal_badge: "AI news",
                news_modal_cta: "Browse Courses",
                news_modal_close: "Close",
                news_detail_1_title: "AI trends in 2026: what’s changing?",
                news_detail_1_date: "April 2026",
                news_detail_1_body: "<p>In 2026, generative AI is no longer just “chat” — <strong>agents</strong>, <strong>automation</strong>, and <strong>workflow integration</strong> are the main trend. People don’t just want answers; they want outcomes.</p><blockquote><strong>Quick takeaway:</strong> The winners don’t “use AI”, they <em>design AI workflows</em>.</blockquote><hr><h4>1) Agents: from task to outcome</h4><p>Agents move beyond single prompts: they plan, execute subtasks, validate results, and present a final output — especially impactful in <strong>analytics</strong>, <strong>support</strong>, and <strong>content</strong>.</p><ul><li>Flow: <code>task → plan → execute → verify → deliver</code></li><li>Main risk: unverified answers and false confidence</li></ul><h4>2) RAG and “company knowledge”</h4><p>Teams are increasingly using RAG (Retrieval-Augmented Generation) to ground responses in <strong>their own docs and data</strong> — not generic internet text.</p><ul><li>Benefit: fewer hallucinations, better accuracy</li><li>Risk: privacy + access control</li></ul><h4>3) Quality: evals and monitoring</h4><p>“It works” isn’t enough. Modern AI systems ship with eval sets, metrics, and monitoring: accuracy, latency, cost, and safety.</p><ul><li>Eval set: real user questions + expected answers</li><li>Metrics: accuracy, safety filters, cost</li></ul><h4>4) Skills: what matters now</h4><p>The most valuable profile is <strong>problem solving + data thinking + automation</strong>. Prompting helps, but workflow design matters more.</p><ul><li>SQL + analytics fundamentals</li><li>Validation mindset (checks, guardrails)</li><li>Data safety and ethics</li></ul><p>At DatalabAcademy, we focus on applying these trends with practical tasks — not just talking about them.</p>",
                news_detail_2_title: "SQL + AI: write queries faster (and better)",
                news_detail_2_date: "April 2026",
                news_detail_2_body: "<p>AI assistants can speed up SQL, but the biggest gains come from strong <strong>structure</strong> and <strong>performance thinking</strong>. The goal isn’t just to write SQL faster — it’s to write it <strong>correctly</strong> and make it <strong>run fast</strong>.</p><hr><h4>1) Draft with AI — then validate</h4><p>AI can produce a query skeleton (CTEs, joins, filters), but you must validate:</p><ul><li>Are JOIN conditions correct?</li><li>Do nulls/duplicates distort results?</li><li>Are time filters/timezones correct?</li></ul><blockquote><strong>Tip:</strong> Provide schema + sample rows and define the expected output before asking for SQL.</blockquote><h4>2) JOIN strategy: grain matters</h4><p>Many bugs happen when you ignore data grain. Joining order-level to item-level data can inflate counts.</p><ul><li>Define grain first</li><li>Plan aggregations</li><li>Use pre-aggregation in CTEs when needed</li></ul><h4>3) Indexes and performance</h4><p>AI may suggest indexes, but always ask “why?”: selectivity, join keys, and write overhead.</p><ul><li>High-value: selective filter columns</li><li>Join performance: FK columns</li><li>Partial indexes: huge wins in some cases</li></ul><h4>4) Learn to read EXPLAIN</h4><p>In real jobs, the superpower is reading the plan: scans, joins, and costs.</p><ul><li>Why a seq scan?</li><li>Is an index used?</li><li>Join order and cost signals</li></ul><p>In our SQL Developer course, we practice these topics with real datasets and scenarios.</p>",
                news_detail_3_title: "AI tools for Data Analytics (practical picks)",
                news_detail_3_date: "April 2026",
                news_detail_3_body: "<p>In 2026, AI tools in analytics are not a “nice-to-have” — they’re part of the daily workflow. But many teams make the same mistake: they collect tools and forget the <strong>problem</strong>, the <strong>KPI</strong>, and the <strong>decision</strong>.</p><blockquote><strong>Rule:</strong> first <em>measure</em>, then <em>explain</em>, only then <em>automate</em>.</blockquote><hr><h4>1) A practical analytics workflow (5 steps)</h4><ul><li><strong>Goal</strong>: write the business question (e.g., “Why did retention drop?”)</li><li><strong>Metric</strong>: define KPIs (DAU/MAU, conversion, churn)</li><li><strong>Data</strong>: pick sources (CRM, events, sales)</li><li><strong>Model</strong>: segmentation/forecast (start simple)</li><li><strong>Decision</strong>: turn insights into action (experiment, campaign)</li></ul><h4>2) Where AI helps the most</h4><p>AI doesn’t replace data — it increases analyst speed. The highest ROI areas:</p><ul><li><strong>Cleaning</strong>: mapping, outliers, duplicates</li><li><strong>EDA</strong>: “what should I look at?” suggestions</li><li><strong>Visualization</strong>: chart choices and narrative</li><li><strong>SQL drafting</strong>: CTE structure, join skeletons, docs</li><li><strong>Reporting</strong>: weekly summaries and automation</li></ul><h4>3) Tool categories (practical picks)</h4><p>This isn’t a “best tools” list — it’s the categories that actually get work done:</p><ul><li><strong>BI/Dashboard</strong>: Looker, Power BI, Tableau (with AI assistants)</li><li><strong>Notebooks</strong>: Jupyter/Colab + code assistants</li><li><strong>Transform</strong>: dbt + AI for tests/docs</li><li><strong>Tracking</strong>: event schema and naming validation</li><li><strong>Forecast</strong>: baselines + interpretation</li></ul><h4>4) Insight writing (storytelling) is the real differentiator</h4><p>Good insights are not “interesting facts”. They follow a simple structure:</p><ul><li><strong>What?</strong> – what happened (numbers)</li><li><strong>Why?</strong> – hypothesis (segment, funnel step)</li><li><strong>So what?</strong> – decision (experiment/change)</li></ul><blockquote><strong>Example:</strong> Instead of “Retention is down 7%”, write “Retention is down 7% because drop-off increased at onboarding step 2; we’ll A/B test a shorter form.”</blockquote><h4>5) Common pitfalls</h4><ul><li>Trusting AI outputs without validation</li><li>“Analysis” without seeing the dataset</li><li>Privacy mistakes with sensitive data</li><li>Biased comparisons across segments</li></ul><p>At DatalabAcademy, our Data Analytics course focuses on real cases: KPI design, dashboards, SQL, and turning insights into decisions.</p>",
                course_modal_badge: "Course",
                course_modal_cta: "Browse Courses",
                course_modal_register: "Add to cart",
                course_modal_telegram: "Telegram",
                course_modal_close: "Close",
                course_detail_1_title: "Data Analytics",
                course_detail_1_meta: "16 lessons · 40 students · Practical tasks",
                course_detail_1_body: "<p>The <strong>Data Analytics</strong> course helps you build a structured analytics mindset: turning business questions into KPIs, exploring data, extracting insights, and translating them into decisions.</p><blockquote><strong>Framework:</strong> Question → KPI → Analysis → Insight → Action</blockquote><hr><h4>What you’ll learn</h4><ul><li>Choosing the right KPIs (DAU/MAU, conversion, retention)</li><li>EDA: distributions, outliers, cohorts, segmentation</li><li>Visualization: picking the right chart and telling the story</li><li>Dashboard thinking: filters, drill-down, clarity</li><li>Insight writing: “What? Why? So what?”</li></ul><h4>Who is it for?</h4><ul><li>Beginners switching into analytics</li><li>Marketing/sales/ops specialists working with numbers</li><li>Junior analysts who want better structure</li></ul><h4>Hands-on practice</h4><p>Every topic comes with real cases and mini tasks: KPI design, dashboard sketches, segmentation, and presentation.</p><h4>Outcome</h4><p>You finish with a mini project + a presentation template for your portfolio.</p>",
                course_detail_2_title: "SQL Developer",
                course_detail_2_meta: "18 lessons · 35 students · Real SQL scenarios",
                course_detail_2_body: "<p>The <strong>SQL Developer</strong> course teaches the most important database skills through practical tasks: JOINs, CTEs, window functions, and performance optimization.</p><blockquote><strong>Goal:</strong> Correct queries + fast results + clean structure</blockquote><hr><h4>Syllabus (short)</h4><ul><li>SELECT, WHERE, GROUP BY, HAVING fundamentals</li><li>JOINs: INNER/LEFT, many-to-many, edge cases</li><li>Subqueries & CTEs for readability and reuse</li><li>Window functions: ROW_NUMBER, RANK, LAG/LEAD</li><li>Indexes and query performance: when and why</li><li>Reading EXPLAIN plans to find bottlenecks</li></ul><h4>Practice</h4><ul><li>30+ scenarios on real datasets</li><li>Debugging incorrect JOINs</li><li>Performance comparisons: index vs no-index</li></ul><h4>Who is it for?</h4><ul><li>Data/BI analysts and backend developers</li><li>Anyone moving from junior to mid-level</li></ul><p>We focus on building a <strong>thinking model</strong>, not copy-pasting queries.</p>",
                course_detail_3_title: "Excel",
                course_detail_3_meta: "14 lessons · 45 students · Dashboards & Power Query",
                course_detail_3_body: "<p>The <strong>Excel</strong> course covers the most-used tools for day-to-day work: formulas, Pivot Tables, Power Query, and dashboard building with real tasks.</p><blockquote><strong>Principle:</strong> Clean data → solid model → clear dashboard</blockquote><hr><h4>What you’ll learn</h4><ul><li>Core & advanced formulas: XLOOKUP, IF, SUMIFS, TEXT, DATE</li><li>Pivot Tables: summaries, drill-down, slicers</li><li>Power Query: import, cleaning, transforms, merge</li><li>Dashboards: KPI cards, chart choices, filter UX</li><li>Best practices: structure, naming, performance</li></ul><h4>Practice</h4><p>Build a report and a dashboard from scratch using real sales/CRM-style data.</p><h4>Outcome</h4><p>You finish with a dashboard template and a mini project.</p>",
                course_detail_4_title: "Working effectively with AI",
                course_detail_4_meta: "12 lessons · 60 students · ChatGPT, Claude, Gemini",
                course_detail_4_body: "<p><strong>Working effectively with AI</strong> teaches you how to use AI tools professionally in everyday work: prompt engineering, picking the right tool, ethics, and real workflows.</p><blockquote><strong>Principle:</strong> AI doesn’t replace work — it <em>accelerates thinking</em>.</blockquote><hr><h4>Program</h4><ul><li>Prompt structure: role + context + task + format</li><li>Few-shot, chain-of-thought, iterative refinement</li><li>Tool comparison: ChatGPT vs Claude vs Gemini — when to use which?</li><li>Detecting hallucinations and verifying output</li><li>Privacy: safely working with personal/company data</li><li>Ethics and authorship: the limits of AI</li></ul><h4>Real-world workflows</h4><ul><li>Data analysis: reading CSVs, extracting insights, visualization</li><li>SQL drafting: scaffolding CTEs and JOINs</li><li>Email and report writing: tone and structure</li><li>Code review: catching bugs, refactor suggestions</li><li>Document summaries and meeting notes</li><li>Translation and localization quality</li></ul><h4>Who is it for?</h4><ul><li>Knowledge workers, analysts, marketing & sales teams</li><li>Developers (using code assistants effectively)</li><li>Managers (decision support, report automation)</li></ul><h4>Outcome</h4><p>You finish with 5+ prompt templates tailored to your workflow and one automated process.</p>",
                course_4_card_title: "Working effectively with AI",
                course_4_card_desc: "Learn to use AI tools (ChatGPT, Claude, Gemini) professionally and ethically in your daily workflows.",
                cat_panel_analytics: "Analytics",
                cat_panel_sql: "SQL",
                cat_panel_office: "Office",
                cat_panel_ai: "AI",
                cat_panel_analytics_title: "Analytics courses",
                cat_panel_sql_title: "SQL courses",
                cat_panel_office_title: "Office courses",
                cat_panel_ai_title: "AI courses",
                cat_panel_view_all: "View all courses",
                portfolio_badge: "Portfolio",
                portfolio_title: 'Build <span class="color-primary">real projects</span> during the courses',
                portfolio_desc: "Strengthen your portfolio with dashboard, reporting and analytics examples.",
                portfolio_tag_powerbi: "Power BI",
                portfolio_tag_dashboard: "Dashboard",
                portfolio_tag_report: "Report",
                portfolio_amazon_title: "Amazon product analytics",
                portfolio_amazon_desc: "An interactive dashboard sample with product lists, search and visual product cards.",
                portfolio_vehicle_title: "Vehicle KPI report",
                portfolio_vehicle_desc: "An analytics panel showing revenue, expense, profit and booking metrics in one view.",
                portfolio_vehicle_customers_title: "Customer profitability analysis",
                portfolio_vehicle_customers_desc: "A report comparing top and weak customers by revenue, expense and profit.",
                portfolio_sales_period_title: "Sales analysis by period",
                portfolio_sales_period_desc: "An interactive dashboard for tracking monthly sales, profit and cost metrics.",
                portfolio_sales_segment_title: "Sales analysis by segment",
                portfolio_sales_segment_desc: "A project comparing segment sales, profit and margin performance.",
                portfolio_sales_product_title: "Sales analysis by product",
                portfolio_sales_product_desc: "A portfolio work showing product performance, COGS and profit margin results.",
                certificate_badge: "Certificates",
                certificate_title: 'Course graduates <span class="color-primary">earn certificates</span>',
                certificate_desc: "Students who successfully complete the training receive certificates they can share in portfolios and career profiles.",
                certificate_tag_global: "International certificate",
                certificate_tag_course: "Microsoft certificate",
                certificate_ms_title: "Microsoft Certified: AI Business Professional",
                certificate_ms_desc: "Students who join DatalabAcademy and successfully complete the program can share this internationally recognized certificate result on their CV and LinkedIn profile.",
                certificate_course_title: "Microsoft Office Specialist: Excel Expert (Microsoft 365 Apps)",
                certificate_course_desc: "Participants who complete the course receive a certificate that validates their training outcome and can use it in portfolios and career profiles.",
                certificate_view: "View certificate",
                footer_about_text: "DatalabAcademy helps you build career-ready skills through practical Data Analytics, SQL, Excel and AI courses.",
                footer_contact_cta: "Contact With Us",
                footer_useful_links: "Useful Links",
                footer_link_marketplace: "Kurs kataloqu",
                footer_link_excel: "Excel",
                footer_link_university: "Data Analitika",
                footer_link_gym_coaching: "AI ilə Effektiv İş",
                footer_link_faq: "FAQ",
                footer_link_about_us: "About Us",
                privacy_policy: "Privacy policy",
                footer_our_company: "Our Company",
                contact_us: "Contact Us",
                become_teacher: "Become Teacher",
                instructor: "Instructor Support",
                events: "Events",
                course: "Course",
                contact: "Contact",
                get_contact: "Get Contact",
                phone: "Phone:",
                email: "E-mail:",
                newsletter: "Newsletter",
                newsletter_desc: "Add your email for new cohorts, useful learning materials and campaign updates.",
                enter_email: "Enter Your Email Here",
                submit_now: "Submit Now",
                copyright_text: 'Copyright © 2026 DatalabAcademy. All rights reserved.',
                terms_service: "Terms of service",
                subscription: "Subscription",
                login_register: "Login & Register"
                ,
                join_now: "Join Now",
                nav_about: "About",
                nav_contact: "Contact",
                cat_card_analytics_title: "Analytics",
                cat_card_analytics_sub: "1 course · Practical tasks",
                cat_card_sql_title: "SQL",
                cat_card_sql_sub: "1 course · JOINs, indexes, performance",
                cat_card_office_title: "Office",
                cat_card_office_sub: "1 course · Excel, Pivot, Power Query",
                cat_card_ai_title: "AI",
                cat_card_ai_sub: "1 course · ChatGPT, Claude, Gemini",
                footer_link_home: "Home",
                footer_link_courses: "Courses",
                footer_link_about: "About",
                footer_link_blog: "Blog",
                footer_link_contact: "Contact",
                footer_course_1: "Data Analytics",
                footer_course_2: "SQL Developer",
                footer_course_3: "Excel",
                footer_course_4: "Working effectively with AI",
                footer_address_label: "Address:",
                footer_address: "Baku, Azerbaijan"
            }
        };

        function setLang(lang) {
            const normalized = (lang && dict[lang]) ? lang : "az";
            document.documentElement.lang = normalized;

            document.querySelectorAll("[data-i18n]").forEach(el => {
                const key = el.getAttribute("data-i18n");
                if (dict[normalized] && dict[normalized][key]) el.textContent = dict[normalized][key];
            });

            document.querySelectorAll("[data-i18n-html]").forEach(el => {
                const key = el.getAttribute("data-i18n-html");
                if (dict[normalized] && dict[normalized][key]) el.innerHTML = dict[normalized][key];
            });

            document.querySelectorAll("[data-i18n-placeholder]").forEach(el => {
                const key = el.getAttribute("data-i18n-placeholder");
                if (dict[normalized] && dict[normalized][key]) el.setAttribute("placeholder", dict[normalized][key]);
            });

            document.querySelectorAll("[data-i18n-title]").forEach(el => {
                const key = el.getAttribute("data-i18n-title");
                if (dict[normalized] && dict[normalized][key]) el.setAttribute("title", dict[normalized][key]);
            });

            document.querySelectorAll("[data-i18n-data-text]").forEach(el => {
                const key = el.getAttribute("data-i18n-data-text");
                if (dict[normalized] && dict[normalized][key]) el.setAttribute("data-text", dict[normalized][key]);
            });

            const currentKey = `lang_${normalized}`;
            document.querySelectorAll(".js-current-lang").forEach(currentLangLabel => {
                currentLangLabel.textContent = (dict[normalized] && dict[normalized][currentKey]) ? dict[normalized][currentKey] : normalized;
            });

            localStorage.setItem("siteLang", normalized);
        }

        // --- DatalabAcademy News Modal (AI news details) ---
        const newsModal = document.getElementById("dlNewsModal");
        const newsImageEl = document.getElementById("dlNewsImage");
        const newsTitleEl = document.getElementById("dlNewsTitle");
        const newsDateEl = document.getElementById("dlNewsDate");
        const newsBodyEl = document.getElementById("dlNewsBody");

        // --- DatalabAcademy Course Modal (course details) ---
        const courseModal = document.getElementById("dlCourseModal");
        const courseImageEl = document.getElementById("dlCourseImage");
        const courseTitleEl = document.getElementById("dlCourseTitle");
        const courseMetaEl = document.getElementById("dlCourseMeta");
        const courseBodyEl = document.getElementById("dlCourseBody");

        let openNewsId = null;
        let lastFocusedEl = null;
        let openCourseId = null;
        let lastCourseFocusedEl = null;

        function renderNewsModal(id) {
            if (!newsModal || !id) return;
            const lang = document.documentElement.lang || "az";
            const bag = dict[lang] || dict.az;

            const title = bag[`news_detail_${id}_title`];
            const date = bag[`news_detail_${id}_date`];
            const body = bag[`news_detail_${id}_body`];

            if (newsTitleEl) newsTitleEl.textContent = title || "";
            if (newsDateEl) newsDateEl.textContent = date || "";
            if (newsBodyEl) newsBodyEl.innerHTML = body || "";
        }

        // ── News: image + tag + readTime per ID
        const newsMetaMap = {
            "1": {
                img:      "https://images.unsplash.com/photo-1744640326166-433469d102f2?auto=format&fit=crop&w=900&q=80",
                tag:      "AI Trendlər",
                icon:     "feather-cpu",
                readTime: "5 dəq oxu"
            },
            "2": {
                img:      "https://images.unsplash.com/photo-1677442135703-1787eea5ce01?auto=format&fit=crop&w=900&q=80",
                tag:      "SQL + AI",
                icon:     "feather-database",
                readTime: "4 dəq oxu"
            },
            "3": {
                img:      "https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=900&q=80",
                tag:      "Data AI",
                icon:     "feather-bar-chart-2",
                readTime: "6 dəq oxu"
            }
        };

        function openNewsModal(id, triggerEl) {
            if (!newsModal) return;
            openNewsId = String(id || "");
            if (!openNewsId) return;
            lastFocusedEl = triggerEl || document.activeElement;
            if (courseModal && courseModal.classList.contains("is-open")) closeCourseModal();

            const meta = newsMetaMap[openNewsId];

            // ── Set image directly from map (reliable, no fallback issues)
            if (newsImageEl && meta) {
                newsImageEl.src = meta.img;
                newsImageEl.alt = meta.tag;
            }

            // ── Update image overlay tag
            const tagEl = newsModal.querySelector("#dlNewsTag");
            if (tagEl && meta) {
                tagEl.innerHTML = `<i class="${meta.icon}" style="font-size:10px;"></i> ${meta.tag}`;
            }

            // ── Fill content
            renderNewsModal(openNewsId);

            // ── Sync image-panel date
            const dateImgEl  = newsModal.querySelector("#dlNewsDateImg");
            const dateMainEl = document.getElementById("dlNewsDate");
            if (dateImgEl && dateMainEl) dateImgEl.textContent = dateMainEl.textContent;

            newsModal.classList.add("is-open");
            newsModal.setAttribute("aria-hidden", "false");
            document.body.style.overflow = "hidden";
            const closeBtn = newsModal.querySelector(".dl-nm-close");
            if (closeBtn) setTimeout(() => closeBtn.focus(), 0);
        }

        function closeNewsModal() {
            if (!newsModal) return;
            newsModal.classList.remove("is-open");
            newsModal.setAttribute("aria-hidden", "true");
            document.body.style.overflow = "";

            const restore = lastFocusedEl;
            openNewsId = null;
            lastFocusedEl = null;
            if (restore && typeof restore.focus === "function") restore.focus();
        }

        function renderCourseModal(id) {
            if (!courseModal || !id) return;
            const lang = document.documentElement.lang || "az";
            const bag = dict[lang] || dict.az;

            const title = bag[`course_detail_${id}_title`];
            const meta = bag[`course_detail_${id}_meta`];
            const body = bag[`course_detail_${id}_body`];

            if (courseTitleEl) courseTitleEl.textContent = title || "";
            if (courseMetaEl) courseMetaEl.textContent = meta || "";
            if (courseBodyEl) courseBodyEl.innerHTML = body || "";
        }

        // ── Course: image + tag per ID
        const courseMetaMap = {
            "1": { img: "assets/images/course/datalab-data-analitika.svg", tag: "Data Analitika", lessons: "16 Dərs", students: "40 Tələbə" },
            "2": { img: "assets/images/course/datalab-sql-developer.svg",  tag: "SQL Developer",  lessons: "18 Dərs", students: "35 Tələbə" },
            "3": { img: "assets/images/course/datalab-excel.svg",           tag: "Excel",          lessons: "14 Dərs", students: "45 Tələbə" },
            "4": { img: "assets/images/course/datalab-ai.svg",              tag: "AI ilə Effektiv İş", lessons: "12 Dərs", students: "60 Tələbə" }
        };

        const CART_KEY = 'dlCart';
        const coursePrices = { "1": 180, "2": 220, "3": 40, "4": 150 };
        function defaultCartIds() { return []; }
        function getCartIds() {
            try {
                const parsed = JSON.parse(localStorage.getItem(CART_KEY) || 'null');
                return Array.isArray(parsed) ? parsed.map(String).filter(id => courseMetaMap[id]) : defaultCartIds();
            } catch (err) {
                return defaultCartIds();
            }
        }
        function setCartIds(ids) {
            localStorage.setItem(CART_KEY, JSON.stringify(Array.from(new Set(ids.map(String))).filter(id => courseMetaMap[id])));
        }
        function cartToast(message) {
            let t = document.getElementById('dl-cart-toast');
            if (!t) {
                t = document.createElement('div');
                t.id = 'dl-cart-toast';
                t.style.cssText = 'position:fixed;left:50%;bottom:30px;transform:translateX(-50%);background:#0d1428;color:#fff;padding:14px 22px;border-radius:10px;box-shadow:0 8px 30px rgba(0,0,0,.3);z-index:100000;font-weight:500;max-width:90%;text-align:center;opacity:0;transition:opacity .25s ease;';
                document.body.appendChild(t);
            }
            t.textContent = message;
            requestAnimationFrame(() => { t.style.opacity = '1'; });
            clearTimeout(t._h);
            t._h = setTimeout(() => { t.style.opacity = '0'; }, 2600);
        }
        function syncMiniCart() {
            const ids = getCartIds();
            document.querySelectorAll('.rbt-minicart-wrapper .minicart-item').forEach(item => {
                const id = item.querySelector('[data-course-id]')?.getAttribute('data-course-id');
                item.style.display = id && ids.indexOf(id) !== -1 ? '' : 'none';
            });
            document.querySelectorAll('.rbt-minicart-wrapper').forEach(list => {
                let empty = list.parentElement.querySelector('[data-mini-cart-empty]');
                if (!empty) {
                    empty = document.createElement('div');
                    empty.className = 'datalab-mini-cart-empty';
                    empty.setAttribute('data-mini-cart-empty', '');
                    empty.textContent = 'Səbət boşdur';
                    list.parentElement.appendChild(empty);
                }
                empty.style.display = ids.length ? 'none' : 'block';
            });
            const total = ids.reduce((sum, id) => sum + (coursePrices[id] || 0), 0);
            document.querySelectorAll('.rbt-cart-subttotal .price[data-usd]').forEach(el => {
                el.setAttribute('data-usd', String(total));
            });
            document.querySelectorAll('.rbt-minicart-footer').forEach(footer => { footer.style.display = ids.length ? '' : 'none'; });
            document.querySelectorAll('a[href="checkout.html"]').forEach(link => {
                link.classList.toggle('disabled', ids.length === 0);
                link.setAttribute('aria-disabled', ids.length === 0 ? 'true' : 'false');
            });
            document.querySelectorAll('[data-cart-count]').forEach(el => {
                el.textContent = String(ids.length);
                el.style.display = ids.length ? 'inline-flex' : 'none';
            });
            applyCurrency();
        }
        function addCourseToCart(id) {
            if (!id || !courseMetaMap[id]) return;
            const ids = getCartIds();
            if (ids.indexOf(id) === -1) {
                ids.push(id);
                setCartIds(ids);
                syncMiniCart();
            }
            cartToast('Kurs səbətə əlavə edildi.');
        }
        function addCurrentCourseToCart() {
            addCourseToCart(openCourseId);
        }

        function openCourseModal(id, triggerEl) {
            if (!courseModal) return;
            openCourseId = String(id || "");
            if (!openCourseId) return;
            lastCourseFocusedEl = triggerEl || document.activeElement;
            if (newsModal && newsModal.classList.contains("is-open")) closeNewsModal();

            const cMeta = courseMetaMap[openCourseId];

            // ── Set image from map
            if (courseImageEl && cMeta) {
                courseImageEl.src = cMeta.img;
                courseImageEl.alt = cMeta.tag;
            }

            // ── Update left panel tag & meta
            const courseTagEl     = courseModal.querySelector("#dlCourseTag");
            const courseImgMetaEl = courseModal.querySelector("#dlCourseImgMeta");
            if (courseTagEl && cMeta) {
                courseTagEl.innerHTML = `<i class="feather-book-open" style="font-size:10px;"></i> ${cMeta.tag}`;
            }
            if (courseImgMetaEl && cMeta) {
                courseImgMetaEl.innerHTML = `<i class="feather-book"></i> ${cMeta.lessons} &nbsp;·&nbsp; <i class="feather-users"></i> ${cMeta.students}`;
            }

            const registerBtn = courseModal.querySelector("#dlCourseRegister");
            if (registerBtn) registerBtn.setAttribute("data-course-id", openCourseId);
            renderCourseModal(openCourseId);
            courseModal.classList.add("is-open");
            courseModal.setAttribute("aria-hidden", "false");
            document.body.style.overflow = "hidden";

            const closeBtnC = courseModal.querySelector(".dl-nm-close");
            if (closeBtnC) setTimeout(() => closeBtnC.focus(), 0);
        }

        function closeCourseModal() {
            if (!courseModal) return;
            courseModal.classList.remove("is-open");
            courseModal.setAttribute("aria-hidden", "true");
            document.body.style.overflow = "";

            const restore = lastCourseFocusedEl;
            openCourseId = null;
            lastCourseFocusedEl = null;
            if (restore && typeof restore.focus === "function") restore.focus();
        }

        const saved = localStorage.getItem("siteLang") || "az";
        setLang(saved);

        // ===== Valyuta konvertasiyası (USD ↔ AZN) =====
        const USD_TO_AZN = 1.70;
        if (localStorage.getItem('siteCurrency') === 'USD' && !localStorage.getItem('siteCurrencyTouched')) {
            localStorage.setItem('siteCurrency', 'AZN');
        }
        function applyCurrency() {
            const cur = localStorage.getItem('siteCurrency') || 'AZN';
            document.querySelectorAll('[data-usd]').forEach(function (el) {
                const usd = parseFloat(el.getAttribute('data-usd')) || 0;
                el.textContent = (cur === 'AZN')
                    ? (usd * USD_TO_AZN).toFixed(0) + ' ₼'
                    : '$' + usd;
            });
        }
        function syncCurrencyLabels(cur) {
            document.querySelectorAll('.js-current-currency').forEach(label => { label.textContent = cur; });
        }
        const savedCur = localStorage.getItem('siteCurrency') || 'AZN';
        syncCurrencyLabels(savedCur);
        applyCurrency();
        syncMiniCart();
        document.querySelectorAll('img').forEach((img, index) => {
            if (!img.hasAttribute('decoding')) img.setAttribute('decoding', 'async');
            if (index > 2 && !img.hasAttribute('loading')) img.setAttribute('loading', 'lazy');
        });
        document.querySelectorAll('.currency-menu [data-currency]').forEach(function (a) {
            a.addEventListener('click', function (e) {
                e.preventDefault();
                const cur = this.getAttribute('data-currency');
                localStorage.setItem('siteCurrency', cur);
                localStorage.setItem('siteCurrencyTouched', '1');
                syncCurrencyLabels(cur);
                applyCurrency();
            });
        });

        // ===== Newsletter / Contact form (preventDefault + friendly toast) =====
        (function () {
            function showToast(msg) {
                let t = document.getElementById('dl-toast');
                if (!t) {
                    t = document.createElement('div');
                    t.id = 'dl-toast';
                    t.style.cssText = 'position:fixed;left:50%;bottom:30px;transform:translateX(-50%);background:#0d1428;color:#fff;padding:14px 22px;border-radius:10px;box-shadow:0 8px 30px rgba(0,0,0,.3);z-index:100000;font-weight:500;max-width:90%;text-align:center;opacity:0;transition:opacity .25s ease;';
                    document.body.appendChild(t);
                }
                t.textContent = msg;
                requestAnimationFrame(function () { t.style.opacity = '1'; });
                clearTimeout(t._h);
                t._h = setTimeout(function () { t.style.opacity = '0'; }, 3000);
            }
            document.querySelectorAll('form.newsletter-form, form.newsletter-form-1, form.js-contact-form').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    showToast('Təşəkkürlər! Tezliklə əlaqə saxlayacağıq.');
                    try { form.reset(); } catch (err) {}
                });
            });
        })();

        // ===== Bookmark (əlfəcin) – localStorage =====
        let dlBookmarks = JSON.parse(localStorage.getItem('dlBookmarks') || '[]');
        function isBookmarked(id) { return dlBookmarks.indexOf(String(id)) !== -1; }
        function syncBookmarkBtn(btn, id) {
            const icon = btn.querySelector('i');
            if (isBookmarked(id)) {
                btn.classList.add('is-bookmarked');
                btn.setAttribute('title', 'Əlfəcindən sil');
                if (icon) icon.style.color = '#f59e0b';
            } else {
                btn.classList.remove('is-bookmarked');
                btn.setAttribute('title', 'Əlfəcin');
                if (icon) icon.style.color = '';
            }
        }
        document.querySelectorAll('.rbt-card .rbt-bookmark-btn .rbt-round-btn').forEach(function (btn) {
            const card = btn.closest('.rbt-card');
            if (!card) return;
            const courseLink = card.querySelector('.js-course-open');
            if (!courseLink) return;
            const id = courseLink.getAttribute('data-course-id');
            if (!id) return;
            syncBookmarkBtn(btn, id);
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const idx = dlBookmarks.indexOf(String(id));
                if (idx === -1) dlBookmarks.push(String(id));
                else dlBookmarks.splice(idx, 1);
                localStorage.setItem('dlBookmarks', JSON.stringify(dlBookmarks));
                syncBookmarkBtn(btn, id);
            });
        });

        const headerSearchInput = document.querySelector('.rbt-search-field input[type="text"]');
        const headerSearchButton = document.querySelector('.rbt-search-field .serach-btn');
        function goToCourseSearch() {
            const q = (headerSearchInput?.value || '').trim();
            window.location.href = q ? `course-filter-one-open.html?search=${encodeURIComponent(q)}` : 'course-filter-one-open.html';
        }
        if (headerSearchInput) {
            headerSearchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    goToCourseSearch();
                }
            });
        }
        if (headerSearchButton) {
            headerSearchButton.addEventListener('click', function (e) {
                e.preventDefault();
                goToCourseSearch();
            });
        }

        document.querySelectorAll(".switcher-language [data-lang]").forEach(a => {
            a.addEventListener("click", function (e) {
                e.preventDefault();
                setLang(this.getAttribute("data-lang"));
                if (newsModal && newsModal.classList.contains("is-open") && openNewsId) {
                    renderNewsModal(openNewsId);
                }
                if (courseModal && courseModal.classList.contains("is-open") && openCourseId) {
                    renderCourseModal(openCourseId);
                }
            });
        });

        document.addEventListener("click", function (e) {
            const directAddEl = e.target.closest("[data-add-course-id]");
            if (directAddEl) {
                e.preventDefault();
                addCourseToCart(directAddEl.getAttribute("data-add-course-id"));
                return;
            }

            const addCourseEl = e.target.closest("[data-add-current-course]");
            if (addCourseEl) {
                e.preventDefault();
                addCurrentCourseToCart();
                return;
            }

            const miniRemoveEl = e.target.closest(".rbt-minicart-wrapper .close-btn button");
            if (miniRemoveEl) {
                e.preventDefault();
                const item = miniRemoveEl.closest(".minicart-item");
                const id = item?.querySelector("[data-course-id]")?.getAttribute("data-course-id");
                if (id) {
                    setCartIds(getCartIds().filter(cartId => cartId !== id));
                    syncMiniCart();
                }
                return;
            }

            const courseOpenEl = e.target.closest(".js-course-open");
            if (courseOpenEl) {
                e.preventDefault();
                openCourseModal(courseOpenEl.getAttribute("data-course-id"), courseOpenEl);
                return;
            }

            const openEl = e.target.closest(".js-news-open");
            if (openEl) {
                e.preventDefault();
                openNewsModal(openEl.getAttribute("data-news-id"), openEl);
                return;
            }

            const courseCloseEl = e.target.closest("[data-dl-course-close]");
            if (courseCloseEl && courseModal && courseModal.classList.contains("is-open")) {
                e.preventDefault();
                closeCourseModal();
                return;
            }

            const closeEl = e.target.closest("[data-dl-news-close]");
            if (closeEl && newsModal && newsModal.classList.contains("is-open")) {
                e.preventDefault();
                closeNewsModal();
            }
        });

        const initialCourseMatch = (window.location.hash || '').match(/^#course-(\d+)$/);
        if (initialCourseMatch) {
            setTimeout(() => openCourseModal(initialCourseMatch[1], null), 200);
        }

        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") {
                if (courseModal && courseModal.classList.contains("is-open")) {
                    e.preventDefault();
                    closeCourseModal();
                    return;
                }
                if (newsModal && newsModal.classList.contains("is-open")) {
                    e.preventDefault();
                    closeNewsModal();
                }
            }
        });
    })();
    </script>
    <script>
    (function () {
        const blogDict = {
            az: { blog_bc: "Bloq", blog_title: "DatalabAcademy bloqu", blog_badge: "Praktiki məqalələr", blog_desc: "Data Analitika, SQL, Excel və AI üzrə işdə tətbiq edə biləcəyiniz qısa və praktik yazılar.", blog_post_1_title: "2026-da AI trendləri: nələr dəyişir?", blog_post_1_desc: "Generativ AI, agentlər və avtomatlaşdırma alətlərinin analitika iş axınını necə dəyişdirdiyinə praktik baxış.", blog_post_2_title: "SQL + AI: sorğuları necə daha tez yazmaq olar?", blog_post_2_desc: "AI köməkçiləri ilə JOIN, indeks və performans mövzularında daha sürətli nəticə almağın yolları.", blog_post_3_title: "Data Analitika üçün AI alətləri", blog_post_3_desc: "Vizualizasiya, proqnoz və hesabat avtomatlaşdırması üçün praktik AI alətlərinin qısa siyahısı.", blog_post_4_title: "Excel dashboard hazırlayarkən 5 əsas prinsip", blog_post_4_desc: "Daha oxunaqlı, sürətli və idarəolunan hesabatlar qurmaq üçün sadə dizayn və model tövsiyələri.", blog_read_more: "Daha ətraflı", blog_read_4: "4 dəq oxu", blog_read_5: "5 dəq oxu", blog_read_6: "6 dəq oxu", blog_search: "Bloqda axtarın", blog_recent: "Son yazılar", blog_tags: "Mövzular" },
            en: { blog_bc: "Blog", blog_title: "DatalabAcademy blog", blog_badge: "Practical articles", blog_desc: "Short, practical posts on Data Analytics, SQL, Excel, and AI that you can use at work.", blog_post_1_title: "AI trends in 2026: what is changing?", blog_post_1_desc: "A practical look at how generative AI, agents, and automation tools are changing analytics workflows.", blog_post_2_title: "SQL + AI: how to write queries faster", blog_post_2_desc: "Ways to get faster results with AI assistants across JOINs, indexes, and performance topics.", blog_post_3_title: "AI tools for Data Analytics", blog_post_3_desc: "A short list of practical AI tools for visualization, forecasting, and report automation.", blog_post_4_title: "5 key principles for Excel dashboards", blog_post_4_desc: "Simple design and model tips for building clearer, faster, and easier-to-manage reports.", blog_read_more: "Read more", blog_read_4: "4 min read", blog_read_5: "5 min read", blog_read_6: "6 min read", blog_search: "Search blog", blog_recent: "Recent posts", blog_tags: "Topics" }
        };
        function applyBlogLang(lang) {
            const bag = blogDict[lang] || blogDict.az;
            document.documentElement.setAttribute("lang", lang === "en" ? "en" : "az");
            document.querySelectorAll("[data-blog-i18n]").forEach(function (el) { const key = el.getAttribute("data-blog-i18n"); if (bag[key]) el.textContent = bag[key]; });
            document.querySelectorAll("[data-blog-i18n-placeholder]").forEach(function (el) { const key = el.getAttribute("data-blog-i18n-placeholder"); if (bag[key]) el.setAttribute("placeholder", bag[key]); });
            document.querySelectorAll("[data-dl-blog-lang]").forEach(function (el) { const value = el.getAttribute(lang === "en" ? "data-en" : "data-az"); if (value) el.textContent = value; });
            document.querySelectorAll("[data-dl-blog-rich]").forEach(function (el) {
                const value = el.getAttribute(lang === "en" ? "data-en" : "data-az") || "";
                el.innerHTML = renderBlogRich(value);
            });
        }
        function escHtml(value) {
            return String(value || "").replace(/[&<>"']/g, function (ch) {
                return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[ch];
            });
        }
        function renderBlogRich(text) {
            return String(text || "").trim().split(/\n\s*\n/).map(function (block) {
                block = block.trim();
                if (!block) return "";
                const heading = block.match(/^#{2,3}\s+(.+)$/);
                if (heading) return "<h3>" + escHtml(heading[1].trim()) + "</h3>";
                if (block.charAt(0) === ">") {
                    return "<blockquote>" + escHtml(block.replace(/^>\s?/gm, "").trim()).replace(/\n/g, "<br>") + "</blockquote>";
                }
                const lines = block.split(/\n/).filter(Boolean);
                const items = lines.map(function (line) { const m = line.match(/^\s*[-*]\s+(.+)$/); return m ? m[1].trim() : null; });
                if (items.length && items.every(Boolean)) {
                    return "<ul>" + items.map(function (item) {
                        return '<li><i class="feather-check-circle"></i><span>' + escHtml(item) + "</span></li>";
                    }).join("") + "</ul>";
                }
                return "<p>" + escHtml(block).replace(/\n/g, "<br>") + "</p>";
            }).join("");
        }
        document.addEventListener("DOMContentLoaded", function () { applyBlogLang(localStorage.getItem("siteLang") || "az"); });
        document.addEventListener("click", function (event) { const langLink = event.target.closest("[data-lang]"); if (langLink) setTimeout(function () { applyBlogLang(langLink.getAttribute("data-lang")); }, 0); });
    })();
    </script>
    <script>
    (function () {
        const detailDict = {
            az: { detail_bc_blog: "Bloq", detail_bc_current: "AI trendləri", detail_badge: "AI trendləri", detail_title: "2026-da AI trendləri: nələr dəyişir?", detail_intro: "Generativ AI, agentlər və avtomatlaşdırma alətlərinin data analitika, SQL və hesabat iş axınını necə dəyişdirdiyinə praktik baxış.", detail_read_time: "5 dəq oxu", detail_p1: "AI artıq yalnız mətn yazan köməkçi deyil. 2026-da əsas dəyişiklik odur ki, alətlər məlumat toplama, təmizləmə, sorğu yazma və hesabat hazırlama mərhələlərində daha çox iş görür.", detail_h1: "Analitiklər üçün əsas dəyişikliklər", detail_p2: "Data analitika komandalarında AI alətləri Excel formulalarından SQL sorğularına, dashboard ideyalarından avtomatik izahlara qədər gündəlik işin içində istifadə olunur.", detail_l1: "SQL sorğularının ilkin variantını daha tez hazırlamaq mümkündür.", detail_l2: "Excel və Power BI hesabatlarında izah və summary hissələri daha sürətli yazılır.", detail_l3: "Data keyfiyyəti problemlərini tapmaq üçün AI-dan checklist kimi istifadə etmək olur.", detail_note: "Əsas üstünlük AI-ın insanı əvəz etməsi deyil, analitikin təkrar işlərə daha az vaxt sərf edib qərar verməyə daha çox fokuslanmasıdır.", detail_h2: "Nəyi öyrənmək vacibdir?", detail_p3: "AI-dan güclü istifadə etmək üçün data məntiqi yenə də vacibdir. SQL, Excel, data model və vizualizasiya biliyi olmadan AI nəticəsini yoxlamaq və düzgün istiqamətləndirmək çətinləşir.", detail_h3: "Praktiki yanaşma", detail_p4: "DatalabAcademy dərslərində mövzular real tapşırıqlar üzərində qurulur: əvvəl problemi başa düşürük, sonra datanı hazırlayırıq, daha sonra AI-dan sürətləndirici alət kimi istifadə edirik.", detail_course_cta: "Online təlimə bax", detail_back_cta: "Bloqa qayıt", detail_sidebar_title: "Məqalə mövzuları", detail_related_title: "Oxşar yazılar" },
            en: { detail_bc_blog: "Blog", detail_bc_current: "AI trends", detail_badge: "AI trends", detail_title: "AI trends in 2026: what is changing?", detail_intro: "A practical look at how generative AI, agents, and automation tools are changing data analytics, SQL, and reporting workflows.", detail_read_time: "5 min read", detail_p1: "AI is no longer only a writing assistant. In 2026, the key change is that tools help more across data collection, cleaning, query writing, and report preparation.", detail_h1: "Key changes for analysts", detail_p2: "In data analytics teams, AI tools are now part of everyday work, from Excel formulas and SQL queries to dashboard ideas and automated explanations.", detail_l1: "You can draft the first version of SQL queries faster.", detail_l2: "Explanations and summaries in Excel and Power BI reports become quicker to prepare.", detail_l3: "AI can be used as a checklist to spot data quality issues.", detail_note: "The main advantage is not replacing the analyst, but helping them spend less time on repetitive work and more time on decisions.", detail_h2: "What should you learn?", detail_p3: "To use AI well, data logic still matters. Without SQL, Excel, data modeling, and visualization skills, it becomes harder to verify and guide AI output.", detail_h3: "A practical approach", detail_p4: "At DatalabAcademy, lessons are built around real tasks: we understand the problem, prepare the data, and then use AI as an accelerator.", detail_course_cta: "View online training", detail_back_cta: "Back to blog", detail_sidebar_title: "Article topics", detail_related_title: "Related posts" }
        };
        function applyDetailLang(lang) {
            const bag = detailDict[lang] || detailDict.az;
            document.querySelectorAll("[data-blog-detail-i18n]").forEach(function (el) {
                const key = el.getAttribute("data-blog-detail-i18n");
                if (bag[key]) el.textContent = bag[key];
            });
        }
        document.addEventListener("DOMContentLoaded", function () { applyDetailLang(localStorage.getItem("siteLang") || "az"); });
        document.addEventListener("click", function (event) { const langLink = event.target.closest("[data-lang]"); if (langLink) setTimeout(function () { applyDetailLang(langLink.getAttribute("data-lang")); }, 0); });
    })();
    </script>
</body>

</html>





