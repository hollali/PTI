<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? getSetting('site_title') ?> | <?= getSetting('site_title') ?></title>
    <meta name="description" content="<?= $metaDesc ?? getSetting('site_tagline') ?>">
    <meta name="theme-color" content="#006B3F">
    <link rel="icon" type="image/png" href="<?= getSetting('site_favicon') ?: 'assets/logos/logo.png' ?>">
    <link rel="shortcut icon" href="<?= getSetting('site_favicon') ?: 'assets/logos/logo.png' ?>">
    <link rel="apple-touch-icon" href="<?= getSetting('site_favicon') ?: 'assets/logos/logo.png' ?>">
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: { DEFAULT: '#006B3F', dark: '#004F2E', light: '#008C52' },
                    secondary: { DEFAULT: '#D4AF37', light: '#F5D76E' },
                    appdark: '#0F1A2E',
                    applight: '#F0FFF4',
                }
            }
        }
    }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #006B3F;
            --primary-dark: #004F2E;
            --primary-light: #008C52;
            --secondary: #D4AF37;
            --secondary-light: #F5D76E;
            --dark: #0F1A2E;
            --light: #F0FFF4;
            --gold: #D4AF37;
            --gold-light: #F5D76E;
            --red-accent: #CE1126;
            --gray-50: #F9FAFB;
            --gray-100: #F3F4F6;
            --gray-200: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-400: #9CA3AF;
            --gray-500: #6B7280;
            --gray-600: #4B5563;
            --gray-700: #374151;
            --gray-800: #1F2937;
        }
        * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
        html { scroll-behavior: smooth; }
        body {
            background: var(--gray-50);
            color: var(--gray-800);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .text-primary { color: var(--primary); }
        .text-secondary { color: var(--secondary); }
        .text-gold { color: var(--gold); }
        .text-dark, .text-dark-primary { color: var(--dark); }
        .text-green-primary { color: var(--primary); }
        .text-red-primary { color: var(--red-accent); }
        .bg-primary { background-color: var(--primary); }
        .bg-secondary { background-color: var(--secondary); }
        .bg-gold { background-color: var(--gold); }
        .bg-dark-primary { background-color: var(--dark); }
        .bg-green-primary { background-color: var(--primary); }
        .bg-red-primary { background-color: var(--red-accent); }
        .bg-appdark { background-color: var(--dark); }
        .bg-applight { background-color: var(--light); }
        .border-primary { border-color: var(--primary); }

        /* Top Bar */
        .top-bar {
            background: var(--dark);
            color: rgba(255,255,255,0.65);
            font-size: 12px;
            padding: 6px 0;
            letter-spacing: 0.2px;
        }
        .top-bar a { color: rgba(255,255,255,0.5); transition: color 0.2s; }
        .top-bar a:hover { color: #fff; }
        .top-bar .contact-text { display: inline; }
        @media (max-width: 639px) {
            .top-bar .contact-text { display: none; }
            .top-bar .gap-4 { gap: 8px; }
        }

        /* Header */
        .main-header {
            background: #fff;
            border-bottom: 1px solid var(--gray-200);
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.3s;
        }
        .main-header.scrolled { box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
        .main-header .logo img { height: 44px; }
        .main-header .logo-text {
            font-size: 16px;
            font-weight: 800;
            color: var(--dark);
            line-height: 1.2;
            letter-spacing: -0.3px;
        }
        .main-header .logo-text span { color: var(--primary); }
        .main-header .logo-sub {
            font-size: 8px;
            color: var(--gray-400);
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .main-nav a {
            color: var(--gray-600);
            font-size: 13px;
            font-weight: 500;
            padding: 26px 16px;
            display: block;
            transition: all 0.2s;
            position: relative;
        }
        .main-nav a:hover, .main-nav a.active { color: var(--primary); }
        .main-nav a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2.5px;
            background: var(--primary);
            border-radius: 2px;
            transition: width 0.25s;
        }
        .main-nav a:hover::after, .main-nav a.active::after { width: 70%; }

        .nav-item { position: relative; }
        .nav-has-child > a::after {
            content: '\f107';
            font-family: 'Font Awesome 6 Free'; font-weight: 900;
            margin-left: 5px; font-size: 11px; transition: transform 0.2s;
        }
        .nav-item:hover > a::after { transform: rotate(180deg); }

        .dropdown-menu-custom {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: #fff;
            min-width: 220px;
            box-shadow: 0 16px 48px rgba(0,0,0,0.1);
            border: 1px solid #e5e7eb;
            border-top: 2.5px solid var(--primary);
            border-radius: 0 0 12px 12px;
            z-index: 999;
            padding: 6px 0;
        }
        .nav-item:hover > .dropdown-menu-custom { display: block; animation: fadeDown 0.2s ease; }
        .dropdown-menu-custom a {
            padding: 10px 20px !important;
            text-transform: none !important;
            font-size: 13px !important;
            color: #4b5563 !important;
        }
        .dropdown-menu-custom a:hover { background: var(--light); color: var(--primary) !important; }
        .dropdown-menu-custom a::after { display: none; }

        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Mobile submenu */
        .mobile-sub {
            display: none; padding-left: 16px;
        }
        .mobile-sub.open { display: block; }
        .mobile-sub a {
            display: block; padding: 10px 12px; font-size: 13px;
            color: #6b7280; border-radius: 8px; transition: all 0.15s;
        }
        .mobile-sub a:hover { background: var(--light); color: var(--primary); }
        .mobile-toggle {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 12px; font-size: 14px; font-weight: 500;
            color: #4b5563; border-radius: 8px; cursor: pointer;
            transition: all 0.15s;
        }
        .mobile-toggle:hover { background: var(--light); color: var(--primary); }
        .mobile-toggle .arrow { transition: transform 0.2s; font-size: 12px; }
        .mobile-toggle.open .arrow { transform: rotate(180deg); }

        /* Hero */
        .hero-section {
            position: relative;
            overflow: hidden;
            min-height: 85vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #0F1A2E 0%, #1a2a3a 40%, var(--primary) 100%);
            width: 100%;
        }
        .hero-section .grid-bg {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            z-index: 0;
        }
        .hero-section .content { position: relative; z-index: 1; width: 100%; }
        .hero-section .hero-wave {
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            z-index: 1;
        }

        /* Hero Carousel */
        .hero-carousel {
            position: relative;
            align-self: stretch;
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        .hero-slide {
            position: absolute; inset: 0;
            display: flex; align-items: center;
            opacity: 0; transition: opacity 0.8s ease;
            background: linear-gradient(135deg, #0F1A2E 0%, #1a2a3a 40%, var(--primary) 100%);
            background-size: cover; background-position: center;
            z-index: 0;
        }
        .hero-slide.active { opacity: 1; z-index: 1; position: relative; flex: 1; width: 100%; }
        .hero-slide::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(15,26,46,0.85) 0%, rgba(26,42,58,0.7) 50%, rgba(0,107,63,0.6) 100%);
            z-index: 0;
        }
        .hero-slide .content { position: relative; z-index: 1; }
        .hero-dots {
            position: absolute; bottom: 80px; left: 50%; transform: translateX(-50%);
            display: flex; gap: 10px; z-index: 2;
        }
        .hero-dots .dot {
            width: 10px; height: 10px; border-radius: 50%;
            background: rgba(255,255,255,0.25); cursor: pointer;
            transition: all 0.3s; border: none;
        }
        .hero-dots .dot.active { background: #fff; transform: scale(1.3); }

        /* Buttons */
        .btn-primary {
            background: var(--primary);
            color: #fff;
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s;
            border: none;
            cursor: pointer;
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(0,107,63,0.25);
        }

        .btn-outline-light {
            border: 2px solid rgba(255,255,255,0.25);
            color: #fff;
            padding: 10px 26px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s;
            background: transparent;
        }
        .btn-outline-light:hover {
            border-color: #fff;
            background: rgba(255,255,255,0.08);
            transform: translateY(-1px);
        }

        .btn-ghost {
            color: var(--primary);
            font-weight: 600;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: gap 0.25s;
        }
        .btn-ghost:hover { gap: 10px; }

        /* Section */
        .section-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--primary);
            margin-bottom: 10px;
        }
        .section-tag::before {
            content: '';
            width: 20px;
            height: 2px;
            background: var(--primary);
            border-radius: 1px;
        }
        .section-title {
            font-size: 32px;
            font-weight: 800;
            color: var(--dark);
            line-height: 1.2;
            letter-spacing: -0.5px;
        }
        .section-title .highlight { color: var(--primary); }

        /* Cards */
        .card-app {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--gray-200);
            transition: all 0.3s;
        }
        .card-app:hover {
            box-shadow: 0 8px 32px rgba(0,0,0,0.06);
            border-color: var(--gray-300);
        }

        /* Stat Card */
        .stat-card-app {
            text-align: center;
            padding: 32px 20px;
        }
        .stat-card-app .number {
            font-size: 42px;
            font-weight: 900;
            color: var(--primary);
            line-height: 1;
        }
        .stat-card-app .label {
            font-size: 12px;
            color: var(--gray-400);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 6px;
        }

        /* Program Card */
        .program-card-app {
            padding: 28px 24px;
        }
        .program-card-app .icon-wrap {
            width: 56px;
            height: 56px;
            background: var(--light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: var(--primary);
            margin-bottom: 16px;
            transition: all 0.3s;
        }
        .program-card-app:hover .icon-wrap {
            background: var(--primary);
            color: #fff;
        }
        .program-card-app h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
        }
        .program-card-app .duration {
            font-size: 11px;
            color: var(--gray-400);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        .program-card-app p { font-size: 13px; color: var(--gray-500); line-height: 1.7; }

        /* News Card */
        .news-card-app .img-wrap {
            height: 200px;
            background: linear-gradient(135deg, var(--dark), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .news-card-app .img-wrap i { font-size: 40px; opacity: 0.12; color: #fff; transition: all 0.3s; }
        .news-card-app:hover .img-wrap i { opacity: 0.25; transform: scale(1.1); }
        .news-card-app .img-wrap .date-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(0,0,0,0.3);
            backdrop-filter: blur(8px);
            color: #fff;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 500;
        }
        .news-card-app .body { padding: 20px; }
        .news-card-app .body h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--dark);
            line-height: 1.4;
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .news-card-app .body p {
            font-size: 13px;
            color: var(--gray-500);
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* CTA */
        .cta-section {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        }
        .cta-section .grid-bg {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 60px 60px;
            z-index: 0;
        }
        .cta-section .content { position: relative; z-index: 1; }

        /* Footer */
        .footer {
            background: var(--dark);
            color: rgba(255,255,255,0.5);
            font-size: 13px;
        }
        .footer h5 {
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 16px;
        }
        .footer a { color: rgba(255,255,255,0.45); transition: all 0.2s; }
        .footer a:hover { color: #fff; }
        .footer .social-link {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.06);
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: rgba(255,255,255,0.35);
            transition: all 0.2s;
        }
        .footer .social-link:hover {
            background: var(--primary);
            color: #fff;
            transform: translateY(-2px);
        }
        .footer .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 10px;
        }
        .footer .contact-item i { color: var(--primary); font-size: 13px; margin-top: 2px; width: 16px; }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.06);
            padding: 16px 0;
            font-size: 12px;
        }

        /* Animations */
        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .reveal-d1 { transition-delay: 0.1s; }
        .reveal-d2 { transition-delay: 0.2s; }
        .reveal-d3 { transition-delay: 0.3s; }
        .reveal-d4 { transition-delay: 0.4s; }

        /* Scroll to top */
        #scrollTop {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0,107,63,0.25);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 50;
        }
        #scrollTop:hover { background: var(--primary-dark); transform: translateY(-2px); }

        /* Page Banner */
        .page-banner {
            background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
            padding: 80px 0 60px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .page-banner .grid-bg {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            z-index: 0;
        }
        .page-banner .content { position: relative; z-index: 1; }

        /* Mobile */
        .mobile-menu { display: none; }
        @media (max-width: 1023px) {
            .desktop-menu { display: none; }
            .mobile-menu { display: block; }
            .main-header .logo img { height: 36px; }
            .main-header .logo-text { font-size: 13px; }
            .section-title { font-size: 24px; }
            .stat-card-app .number { font-size: 32px; }
            .stat-card-app { padding: 24px 16px; }
            .hero-section { min-height: 70vh; }
            .hero-section .hero-wave svg { height: 40px; }
            .page-banner { padding: 60px 0 40px; }
            .page-banner h1 { font-size: 28px !important; }
            .page-banner p { font-size: 14px !important; }
            section.py-16 { padding-top: 40px; padding-bottom: 40px; }
            section.py-20 { padding-top: 48px; padding-bottom: 48px; }
            .card.p-8, .card-p-8 { padding: 20px !important; }
            .stat-card-app .label { font-size: 10px; }
            .program-card-app { padding: 20px; }
            .news-card-app .body { padding: 16px; }
            .footer .grid { gap: 24px; }
        }
        @media (max-width: 639px) {
            .page-banner { padding: 48px 0 32px; }
            section.py-16 { padding-top: 32px; padding-bottom: 32px; }
            section.py-20 { padding-top: 40px; padding-bottom: 40px; }
            .hero-section h1 { font-size: 28px !important; }
            .hero-section p { font-size: 14px !important; }
            .hero-section .btn-primary, .hero-section .btn-outline-light { font-size: 13px; padding: 10px 20px; width: 100%; justify-content: center; }
            .hero-section .flex-wrap { flex-direction: column; }
            .hero-section .flex-wrap > * { width: 100%; }
            @media (min-width: 400px) {
                .hero-section .flex-wrap { flex-direction: row; }
                .hero-section .flex-wrap > * { width: auto; }
                .hero-section .btn-primary, .hero-section .btn-outline-light { width: auto; }
            }
            .stat-card-app { padding: 20px 12px; }
            .card.p-8, .card-p-8 { padding: 16px !important; }
            #scrollTop { width: 36px; height: 36px; bottom: 16px; right: 16px; }
        }

        /* Prose */
        .prose { max-width: none; line-height: 1.8; color: var(--gray-600); font-size: 15px; }
        .prose h2 { color: var(--dark); font-size: 22px; font-weight: 700; margin-top: 28px; margin-bottom: 14px; }
        .prose h3 { color: var(--dark); font-size: 18px; font-weight: 600; margin-top: 20px; margin-bottom: 10px; }
        .prose p { margin-bottom: 14px; }
        .prose ul, .prose ol { margin-bottom: 14px; padding-left: 20px; }

        /* Utility */
        .divider { height: 1px; background: var(--gray-200); }
    </style>
</head>
<body>
<?php $currentPage = basename($_SERVER['SCRIPT_NAME']); ?>

<div class="top-bar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4 text-xs">
                <a href="mailto:<?= getSetting('site_email') ?>" class="flex items-center gap-1.5"><i class="fas fa-envelope" style="color:var(--primary)"></i> <span class="contact-text"><?= getSetting('site_email') ?></span></a>
                <a href="tel:<?= getSetting('site_phone') ?>" class="flex items-center gap-1.5"><i class="fas fa-phone" style="color:var(--primary)"></i> <span class="contact-text"><?= getSetting('site_phone') ?></span></a>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <a href="#" class="hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="hover:text-white transition"><i class="fab fa-twitter"></i></a>
                <a href="#" class="hover:text-white transition"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="hover:text-white transition"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>
</div>

<header class="main-header" id="mainHeader">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-3 py-2.5">
                <div class="logo"><img src="assets/logos/logo.png" alt="PTI" onerror="this.style.display='none'"></div>
                <div>
                    <div class="logo-text">Parliamentary <span>Training</span> Institute</div>
                    <div class="logo-sub">Parliament of Ghana</div>
                </div>
            </a>

            <nav class="desktop-menu">
                <div class="main-nav flex items-center">
                    <?php
                    $stmt = $pdo->query("SELECT * FROM menu_items WHERE parent_id IS NULL AND status='active' ORDER BY sort_order");
                    while ($item = $stmt->fetch()):
                        $active = ($currentPage == $item['link']) ? 'active' : '';
                    ?>
                    <?php
                    $subs = $pdo->prepare("SELECT * FROM menu_items WHERE parent_id = ? AND status='active' ORDER BY sort_order");
                    $subs->execute([$item['id']]);
                    $hasChild = $subs->rowCount() > 0;
                    ?>
                    <div class="nav-item<?= $hasChild ? ' nav-has-child' : '' ?>">
                        <a href="<?= $item['link'] ?>" class="<?= $active ?>" target="<?= $item['target'] ?>"><?= $item['label'] ?></a>
                        <?php if ($hasChild): ?>
                        <div class="dropdown-menu-custom">
                            <?php while ($sub = $subs->fetch()): ?>
                            <a href="<?= $sub['link'] ?>"><?= $sub['label'] ?></a>
                            <?php endwhile; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                </div>
            </nav>

            <div class="mobile-menu">
                <button id="menuToggle" class="text-2xl p-2 text-gray-600 hover:text-primary transition">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>
    <div id="mobileNav" class="hidden bg-white border-t border-gray-200">
        <div class="px-4 sm:px-6 py-3 space-y-1">
            <?php
            $stmt = $pdo->query("SELECT * FROM menu_items WHERE parent_id IS NULL AND status='active' ORDER BY sort_order");
            while ($item = $stmt->fetch()):
                $subs = $pdo->prepare("SELECT * FROM menu_items WHERE parent_id = ? AND status='active' ORDER BY sort_order");
                $subs->execute([$item['id']]);
                $hasChild = $subs->rowCount() > 0;
            ?>
            <?php if ($hasChild): ?>
            <div>
                <div class="mobile-toggle" onclick="toggleMobileSub(this)">
                    <span><?= $item['label'] ?></span>
                    <span class="arrow"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="mobile-sub">
                    <?php while ($sub = $subs->fetch()): ?>
                    <a href="<?= $sub['link'] ?>"><?= $sub['label'] ?></a>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php else: ?>
            <a href="<?= $item['link'] ?>" class="block py-2.5 px-3 text-gray-600 hover:text-primary hover:bg-applight rounded-lg transition font-medium text-sm"><?= $item['label'] ?></a>
            <?php endif; ?>
            <?php endwhile; ?>
        </div>
    </div>
</header>
<script>
function toggleMobileSub(el) {
    el.classList.toggle('open');
    var sub = el.nextElementSibling;
    if (sub) sub.classList.toggle('open');
}
</script>
