<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> | PTI CMS</title>
    <meta name="theme-color" content="#006B3F">
    <link rel="icon" type="image/png" href="<?= getSetting('site_favicon') ?: '../assets/logos/logo.png' ?>">
    <link rel="shortcut icon" href="<?= getSetting('site_favicon') ?: '../assets/logos/logo.png' ?>">
    <link rel="apple-touch-icon" href="<?= getSetting('site_favicon') ?: '../assets/logos/logo.png' ?>">
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: { DEFAULT: '#006B3F', dark: '#004F2E', light: '#008C52' },
                    secondary: '#D4AF37',
                    appdark: '#0F1A2E',
                    applight: '#F0FFF4',
                    surface: '#F8FAFB',
                }
            }
        }
    }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #006B3F;
            --primary-dark: #004F2E;
            --primary-light: #008C52;
            --secondary: #D4AF37;
            --dark: #0F1A2E;
            --light: #F0FFF4;
            --sidebar: #0F1A2E;
            --sidebar-hover: #1a2a3a;
            --border: #E8ECF0;
            --surface: #F8FAFB;
        }
        * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
        body {
            background: var(--surface);
            display: flex;
            -webkit-font-smoothing: antialiased;
        }

        /* Sidebar */
        .sidebar { width: 260px; min-height: 100vh; background: var(--sidebar); color: #fff; position: fixed; left: 0; top: 0; z-index: 100; transition: width 0.25s ease; display: flex; flex-direction: column; }
        .sidebar .brand { padding: 22px 24px; border-bottom: 1px solid rgba(255,255,255,0.04); flex-shrink: 0; white-space: nowrap; overflow: hidden; }
        .sidebar .brand h2 { font-size: 18px; font-weight: 700; color: #fff; letter-spacing: -0.3px; }
        .sidebar .brand span { color: var(--secondary); }
        .sidebar .brand small { font-size: 9px; opacity: 0.4; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.4); }
        .sidebar .nav-wrap { flex: 1; overflow-y: auto; padding: 12px 0; }
        .sidebar .nav-section { font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.2); padding: 16px 24px 6px; font-weight: 500; white-space: nowrap; overflow: hidden; }
        .sidebar .nav-item { display: flex; align-items: center; padding: 10px 24px; color: #9aa4b8; transition: all 0.15s; font-size: 13px; border-left: 3px solid transparent; margin: 1px 0; white-space: nowrap; overflow: hidden; }
        .sidebar .nav-item:hover, .sidebar .nav-item.active { background: var(--sidebar-hover); color: #fff; border-left-color: var(--secondary); }
        .sidebar .nav-item i { width: 20px; text-align: center; margin-right: 12px; font-size: 14px; flex-shrink: 0; }
        .sidebar .nav-item span { font-weight: 450; }
        .sidebar.collapsed { width: 60px; }
        .sidebar.collapsed .brand h2, .sidebar.collapsed .brand small, .sidebar.collapsed .nav-item span, .sidebar.collapsed .nav-section { display: none; }
        .sidebar.collapsed .nav-item { justify-content: center; padding: 14px 0; }
        .sidebar.collapsed .nav-item i { margin: 0; }
        .sidebar.collapsed .nav-wrap { padding: 8px 0; }

        .main-content { margin-left: 260px; flex: 1; min-height: 100vh; display: flex; flex-direction: column; transition: margin-left 0.25s ease; }
        .sidebar.collapsed ~ .main-content { margin-left: 60px; }

        /* Top Bar */
        .topbar {
            background: #fff;
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border);
            height: 60px;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .topbar h1 { font-size: 18px; font-weight: 700; color: var(--dark); letter-spacing: -0.3px; }
        .topbar .user-info { display: flex; align-items: center; gap: 14px; }
        .topbar .user-info .avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 600; font-size: 13px;
            flex-shrink: 0;
        }

        .content-area { padding: 28px 32px; flex: 1; }

        /* Card System */
        .card { background: #fff; border-radius: 12px; border: 1px solid var(--border); }
        .card-header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            color: var(--dark);
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-body { padding: 24px; }
        .card-sm .card-body { padding: 20px; }
        .card-hover { transition: box-shadow 0.2s; }
        .card-hover:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.04); }

        /* Stat Cards */
        .stat-card { background: #fff; border-radius: 12px; padding: 22px 24px; border: 1px solid var(--border); transition: all 0.2s; }
        .stat-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.04); border-color: #d0d4d8; }
        .stat-card .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; background: var(--light); color: var(--primary); }
        .stat-card .stat-num { font-size: 28px; font-weight: 800; color: var(--dark); letter-spacing: -0.5px; line-height: 1; }
        .stat-card .stat-label { font-size: 12px; color: #888; font-weight: 500; margin-top: 4px; }

        /* Tables */
        .table-wrap { overflow-x: auto; }
        .table-wrap table { width: 100%; border-collapse: collapse; }
        .table-wrap th {
            text-align: left;
            padding: 10px 20px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #999;
            font-weight: 600;
            background: #FAFBFC;
            border-bottom: 1px solid var(--border);
        }
        .table-wrap td { padding: 13px 20px; font-size: 13px; border-bottom: 1px solid #F0F2F4; color: #444; }
        .table-wrap tbody tr { transition: background 0.15s; }
        .table-wrap tbody tr:hover td { background: #FAFBFC; }
        .table-wrap tbody tr:last-child td { border-bottom: none; }
        .table-wrap .empty td { text-align: center; color: #bbb; padding: 48px 20px; font-size: 13px; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 7px; font-size: 12px; font-weight: 500; cursor: pointer; border: none; transition: all 0.15s; text-decoration: none; line-height: 1.4; }
        .btn-sm { padding: 5px 10px; font-size: 11px; border-radius: 6px; }
        .btn-lg { padding: 10px 22px; font-size: 13px; border-radius: 8px; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-secondary { background: var(--secondary); color: #0F1A2E; }
        .btn-secondary:hover { background: #B8942E; color: #0F1A2E; }
        .btn-ghost { background: transparent; color: #666; border: 1px solid #e0e2e6; }
        .btn-ghost:hover { background: #f5f5f5; border-color: #d0d2d6; }
        .btn-danger { background: #DC2626; color: #fff; }
        .btn-danger:hover { background: #B91C1C; }
        .btn-success { background: #16A34A; color: #fff; }
        .btn-success:hover { background: #15803D; }
        .btn-warning { background: #F59E0B; color: #fff; }
        .btn-warning:hover { background: #D97706; }
        .btn-outline-primary { background: transparent; color: var(--primary); border: 1px solid var(--primary); }
        .btn-outline-primary:hover { background: var(--light); }

        .btn-group { display: flex; gap: 5px; flex-wrap: wrap; }

        /* Forms */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 12px; font-weight: 600; color: #555; margin-bottom: 5px; }
        .form-label .required { color: #DC2626; margin-left: 2px; }
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 9px 13px;
            border: 1px solid #e2e4e8;
            border-radius: 8px;
            font-size: 13px;
            outline: none;
            transition: all 0.15s;
            background: #fff;
            color: #333;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0,107,63,0.06);
        }
        .form-input::placeholder { color: #bbb; }
        .form-textarea { resize: vertical; min-height: 100px; font-family: inherit; }
        .form-hint { font-size: 11px; color: #999; margin-top: 4px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-section { padding: 20px 0; }
        .form-section-title { font-size: 13px; font-weight: 700; color: var(--dark); margin-bottom: 14px; padding-bottom: 8px; border-bottom: 1px solid var(--border); }
        @media (max-width: 640px) { .form-row { grid-template-columns: 1fr; } }

        .checkbox-wrap { display: flex; align-items: center; gap: 8px; }
        .checkbox-wrap input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--primary); }

        /* Badges */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 500; line-height: 1.3; }
        .badge-success { background: #DCFCE7; color: #166534; }
        .badge-warning { background: #FEF3C7; color: #92400E; }
        .badge-danger { background: #FEE2E2; color: #991B1B; }
        .badge-info { background: #DBEAFE; color: #1E40AF; }
        .badge-neutral { background: #F3F4F6; color: #6B7280; }

        /* Alerts */
        .alert { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; line-height: 1.5; }
        .alert-success { background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; }
        .alert-danger { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
        .alert-info { background: #DBEAFE; color: #1E40AF; border: 1px solid #BFDBFE; }
        .alert i { font-size: 16px; flex-shrink: 0; }
        .alert .close { margin-left: auto; cursor: pointer; opacity: 0.5; }
        .alert .close:hover { opacity: 1; }

        /* Page Header */
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
        .page-header h2 { font-size: 20px; font-weight: 700; color: var(--dark); letter-spacing: -0.3px; }
        .page-header .actions { display: flex; gap: 8px; flex-wrap: wrap; }

        /* Empty state */
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 48px; color: #ddd; margin-bottom: 16px; }
        .empty-state h3 { font-size: 16px; font-weight: 600; color: #888; margin-bottom: 6px; }
        .empty-state p { font-size: 13px; color: #bbb; }

        /* Pagination */
        .pagination { display: flex; gap: 4px; }
        .pagination a, .pagination span { padding: 6px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 12px; color: #666; transition: all 0.15s; text-decoration: none; }
        .pagination a:hover { border-color: var(--primary); color: var(--primary); }
        .pagination .active { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* Detail grid */
        .detail-grid { display: grid; grid-template-columns: 120px 1fr; gap: 2px 16px; font-size: 13px; }
        .detail-grid .dt { color: #888; font-weight: 500; padding: 6px 0; }
        .detail-grid .dd { color: #444; padding: 6px 0; }

        /* Tabs */
        .tabs { display: flex; gap: 0; border-bottom: 1px solid var(--border); margin-bottom: 20px; }
        .tabs a { padding: 10px 20px; font-size: 13px; font-weight: 500; color: #888; border-bottom: 2px solid transparent; transition: all 0.15s; text-decoration: none; margin-bottom: -1px; }
        .tabs a:hover { color: var(--primary); }
        .tabs a.active { color: var(--primary); border-bottom-color: var(--primary); }

        /* Grid helpers */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
        .grid-4 { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px; }
        .grid-6 { display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px; }
        @media (max-width: 1200px) { .grid-4, .grid-6 { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 900px) { .grid-3, .grid-4, .grid-6 { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 640px) { .grid-2, .grid-3, .grid-4, .grid-6 { grid-template-columns: 1fr; } }

        /* Search */
        .search-wrap { display: flex; gap: 8px; }
        .search-wrap input { padding: 7px 13px; border: 1px solid var(--border); border-radius: 7px; font-size: 13px; outline: none; width: 200px; transition: all 0.15s; }
        .search-wrap input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,107,63,0.06); width: 260px; }

        /* Scroll to top */
        #scrollTop {
            width: 38px; height: 38px;
            background: var(--primary); color: #fff;
            border: none; border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0,107,63,0.2);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
            position: fixed; bottom: 20px; right: 20px; z-index: 50;
        }
        #scrollTop:hover { background: var(--primary-dark); transform: translateY(-2px); }

        /* Utilities */
        .text-muted { color: #888; }
        .text-sm { font-size: 12px; }
        .text-xs { font-size: 11px; }
        .fw-500 { font-weight: 500; }
        .fw-600 { font-weight: 600; }
        .fw-700 { font-weight: 700; }

        .divider { height: 1px; background: var(--border); margin: 20px 0; }

        /* Inline form helper */
        form.inline { display: inline; }
        .btn-group form.inline { display: inline-flex; }

        /* Modal */
        .modal-overlay {
            position: fixed; inset: 0; z-index: 1000;
            background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);
            display: none; align-items: flex-start; justify-content: center;
            padding: 60px 20px; overflow-y: auto;
        }
        .modal-overlay.active { display: flex; }
        .modal-dialog {
            background: #fff; border-radius: 14px;
            box-shadow: 0 25px 80px rgba(0,0,0,0.25);
            width: 100%; max-width: 520px;
            animation: modalIn 0.2s ease;
            margin: auto;
        }
        .modal-dialog.modal-lg { max-width: 740px; }
        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.96) translateY(12px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .modal-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 22px 26px 0;
        }
        .modal-header h3 { font-size: 17px; font-weight: 700; color: var(--dark); }
        .modal-close {
            width: 32px; height: 32px; border-radius: 8px;
            background: #f2f2f2; border: none; font-size: 18px;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            color: #999; transition: all 0.15s;
        }
        .modal-close:hover { background: #e6e6e6; color: #333; }
        .modal-body { padding: 16px 26px 24px; }

        /* Confirm modal */
        .confirm-icon { font-size: 44px; color: var(--danger, #e74c3c); margin-bottom: 12px; }
        .confirm-title { font-size: 18px; font-weight: 700; color: var(--dark); margin-bottom: 6px; }
        .confirm-msg { color: #888; font-size: 14px; margin-bottom: 24px; line-height: 1.5; }
        .justify-center { justify-content: center; }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { width: 60px; }
            .sidebar .brand h2, .sidebar .brand small, .sidebar .nav-item span, .sidebar .nav-section { display: none; }
            .main-content { margin-left: 60px; }
            .sidebar .nav-item { justify-content: center; padding: 14px 0; }
            .sidebar .nav-item i { margin: 0; }
            .sidebar .nav-wrap { padding: 8px 0; }
            .content-area { padding: 20px 16px; }
            .topbar { padding: 0 16px; height: 52px; }
            .topbar h1 { font-size: 15px; }
            .search-wrap input { width: 120px; }
            .search-wrap input:focus { width: 150px; }
            .page-header { flex-direction: column; align-items: stretch; }
        }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="brand">
        <h2>PTI <span>CMS</span></h2>
        <small>Content Management System</small>
    </div>
    <div class="nav-wrap">
        <div class="nav-section">Main</div>
        <a href="dashboard.php" class="nav-item <?= basename($_SERVER['SCRIPT_NAME']) == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
        </a>
        <a href="pages.php" class="nav-item <?= strpos($_SERVER['SCRIPT_NAME'], 'pages.php') !== false ? 'active' : '' ?>">
            <i class="fas fa-file-alt"></i> <span>Pages</span>
        </a>
        <a href="menu-control.php" class="nav-item <?= strpos($_SERVER['SCRIPT_NAME'], 'menu-control.php') !== false ? 'active' : '' ?>">
            <i class="fas fa-bars"></i> <span>Menu Control</span>
        </a>
        <a href="banners.php" class="nav-item <?= strpos($_SERVER['SCRIPT_NAME'], 'banners.php') !== false ? 'active' : '' ?>">
            <i class="fas fa-images"></i> <span>Banners</span>
        </a>
        <a href="programs.php" class="nav-item <?= strpos($_SERVER['SCRIPT_NAME'], 'programs.php') !== false ? 'active' : '' ?>">
            <i class="fas fa-graduation-cap"></i> <span>Programs</span>
        </a>
        <a href="news.php" class="nav-item <?= strpos($_SERVER['SCRIPT_NAME'], 'news.php') !== false ? 'active' : '' ?>">
            <i class="fas fa-newspaper"></i> <span>News</span>
        </a>
        <a href="gallery.php" class="nav-item <?= strpos($_SERVER['SCRIPT_NAME'], 'gallery.php') !== false ? 'active' : '' ?>">
            <i class="fas fa-camera"></i> <span>Gallery</span>
        </a>
        <div class="nav-section">Management</div>
        <a href="messages.php" class="nav-item <?= strpos($_SERVER['SCRIPT_NAME'], 'messages.php') !== false ? 'active' : '' ?>">
            <i class="fas fa-envelope"></i> <span>Messages</span>
        </a>
        <a href="users.php" class="nav-item <?= strpos($_SERVER['SCRIPT_NAME'], 'users.php') !== false ? 'active' : '' ?>">
            <i class="fas fa-users-cog"></i> <span>Users</span>
        </a>
        <a href="activity-logs.php" class="nav-item <?= strpos($_SERVER['SCRIPT_NAME'], 'activity-logs.php') !== false ? 'active' : '' ?>">
            <i class="fas fa-history"></i> <span>Activity Logs</span>
        </a>
        <a href="analytics.php" class="nav-item <?= strpos($_SERVER['SCRIPT_NAME'], 'analytics.php') !== false ? 'active' : '' ?>">
            <i class="fas fa-chart-bar"></i> <span>Analytics</span>
        </a>
        <a href="settings.php" class="nav-item <?= strpos($_SERVER['SCRIPT_NAME'], 'settings.php') !== false ? 'active' : '' ?>">
            <i class="fas fa-cog"></i> <span>Settings</span>
        </a>
        <div class="nav-section">Account</div>
        <a href="logout.php" class="nav-item">
            <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
        </a>
    </div>
</div>
<div class="main-content">
    <div class="topbar">
        <div class="flex items-center gap-3">
            <button id="sidebarToggle" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-500 transition" title="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <h1><?= $title ?? 'Dashboard' ?></h1>
        </div>
        <div class="user-info">
            <span class="text-xs text-gray-400 hidden md:block"><?= $_SESSION['fullname'] ?? 'Admin' ?></span>
            <span class="badge badge-success"><?= ucfirst($_SESSION['role'] ?? 'user') ?></span>
            <div class="avatar"><?= substr($_SESSION['fullname'] ?? 'A', 0, 1) ?></div>
        </div>
    </div>
    <div class="content-area">
