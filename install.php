<?php
/**
 * PTI Installation Script
 * Run this once to set up the database and default admin user.
 * DELETE THIS FILE AFTER INSTALLATION for security.
 */

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step == 2) {
    $dbHost = $_POST['db_host'] ?? 'localhost';
    $dbName = $_POST['db_name'] ?? 'pti_db';
    $dbUser = $_POST['db_user'] ?? 'root';
    $dbPass = $_POST['db_pass'] ?? '';

    try {
        $pdo = new PDO("mysql:host=$dbHost;charset=utf8mb4", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$dbName`");

        $sql = file_get_contents(__DIR__ . '/sql/database.sql');
        $pdo->exec($sql);

        $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'")->execute([$adminPass]);

        $config = "<?php\n";
        $config .= "define('DB_HOST', '$dbHost');\n";
        $config .= "define('DB_NAME', '$dbName');\n";
        $config .= "define('DB_USER', '$dbUser');\n";
        $config .= "define('DB_PASS', '$dbPass');\n";

        file_put_contents(__DIR__ . '/config/database.php', "<?php\nsession_start();\n\n" . $config . "\ndefine('SITE_URL', 'http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . "');\ndefine('ADMIN_URL', SITE_URL . '/admin');\ndefine('UPLOAD_PATH', __DIR__ . '/../public/uploads');\ndefine('UPLOAD_URL', SITE_URL . '/public/uploads');\ndefine('ASSETS_URL', SITE_URL . '/assets');\n\ntry {\n    \$pdo = new PDO(\"mysql:host=\" . DB_HOST . \";dbname=\" . DB_NAME . \";charset=utf8mb4\", DB_USER, DB_PASS);\n    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);\n    \$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);\n} catch (PDOException \$e) {\n    die(\"Database connection failed: \" . \$e->getMessage());\n}\n\nfunction getSetting(\$key) {\n    global \$pdo;\n    \$stmt = \$pdo->prepare(\"SELECT setting_value FROM settings WHERE setting_key = ?\");\n    \$stmt->execute([\$key]);\n    \$row = \$stmt->fetch();\n    return \$row ? \$row['setting_value'] : null;\n}\n\nfunction isLoggedIn() {\n    return isset(\$_SESSION['user_id']);\n}\n\nfunction isAdmin() {\n    return isset(\$_SESSION['role']) && \$_SESSION['role'] === 'admin';\n}\n\nfunction requireLogin() {\n    if (!isLoggedIn()) {\n        header('Location: ' . ADMIN_URL . '/index.php');\n        exit;\n    }\n}\n\nfunction requireAdmin() {\n    requireLogin();\n    if (!isAdmin()) {\n        header('Location: ' . ADMIN_URL . '/dashboard.php');\n        exit;\n    }\n}\n\nfunction logActivity(\$action, \$description = '') {\n    global \$pdo;\n    \$userId = \$_SESSION['user_id'] ?? null;\n    \$ip = \$_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';\n    \$ua = \$_SERVER['HTTP_USER_AGENT'] ?? '';\n    \$stmt = \$pdo->prepare(\"INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)\");\n    \$stmt->execute([\$userId, \$action, \$description, \$ip, \$ua]);\n}\n\nfunction sanitize(\$input) {\n    return htmlspecialchars(strip_tags(trim(\$input)), ENT_QUOTES, 'UTF-8');\n}\n\nfunction slugify(\$text) {\n    \$text = preg_replace('~[^\\pL\\d]+~u', '-', \$text);\n    \$text = iconv('utf-8', 'us-ascii//TRANSLIT', \$text);\n    \$text = preg_replace('~[^-\\w]+~', '', \$text);\n    \$text = trim(\$text, '-');\n    \$text = preg_replace('~-+~', '-', \$text);\n    \$text = strtolower(\$text);\n    return \$text ?: 'n-a';\n}\n\nfunction timeAgo(\$datetime) {\n    \$time = strtotime(\$datetime);\n    \$diff = time() - \$time;\n    \$units = [\n        31536000 => 'year', 2592000 => 'month', 604800 => 'week',\n        86400 => 'day', 3600 => 'hour', 60 => 'minute', 1 => 'second'\n    ];\n    foreach (\$units as \$sec => \$unit) {\n        if (\$diff >= \$sec) {\n            \$count = floor(\$diff / \$sec);\n            return \$count . ' ' . \$unit . (\$count > 1 ? 's' : '') . ' ago';\n        }\n    }\n    return 'just now';\n}\n\nfunction trackVisit(\$page) {\n    global \$pdo;\n    \$ip = \$_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';\n    \$ua = \$_SERVER['HTTP_USER_AGENT'] ?? '';\n    \$ref = \$_SERVER['HTTP_REFERER'] ?? '';\n    \$stmt = \$pdo->prepare(\"INSERT INTO page_visits (page, ip_address, user_agent, referrer) VALUES (?, ?, ?, ?)\");\n    \$stmt->execute([\$page, \$ip, \$ua, \$ref]);\n}\n", FILE_USE_INCLUDE_PATH);

        $success = 'Installation completed successfully!';
        $success .= '<br><br>Default login: <strong>admin</strong> / <strong>admin123</strong>';
        $success .= '<br><br><strong>IMPORTANT: Delete the install.php file for security!</strong>';
        $success .= '<br><br><a href="admin/index.php" class="btn-primary" style="display:inline-block;margin-top:10px;padding:10px 24px;background:#006B3F;color:#fff;text-decoration:none;border-radius:4px;">Go to Admin Panel</a>';
        $step = 3;
    } catch (PDOException $e) {
        $error = 'Database Error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install PTI CMS</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        :root { --primary: #006B3F; --gold: #D4AF37; --dark: #0F1A2E; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg max-w-2xl w-full p-8">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-dark-primary">Parliamentary Training Institute</h1>
            <p class="text-gray-500">CMS Installation</p>
        </div>

        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg mb-6"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 p-6 rounded-lg mb-6"><?= $success ?></div>
        <?php endif; ?>

        <?php if ($step == 1): ?>
        <div class="text-center py-8">
            <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-database text-2xl text-primary"></i>
            </div>
            <h2 class="text-xl font-bold mb-2">Welcome to PTI CMS Installer</h2>
            <p class="text-gray-500 mb-6">This script will set up the database and create the default admin user.</p>
            <p class="text-sm text-gray-400 mb-6">Please ensure MySQL/MariaDB is running and you have database credentials ready.</p>
            <a href="?step=2" class="inline-block bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary-dark transition">Start Installation</a>
        </div>
        <?php elseif ($step == 2): ?>
        <form method="POST">
            <h2 class="text-lg font-bold mb-4">Database Configuration</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Database Host</label>
                    <input type="text" name="db_host" class="w-full border rounded-lg px-4 py-2.5" value="localhost" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Database Name</label>
                    <input type="text" name="db_name" class="w-full border rounded-lg px-4 py-2.5" value="pti_db" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Database Username</label>
                    <input type="text" name="db_user" class="w-full border rounded-lg px-4 py-2.5" value="root" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Database Password</label>
                    <input type="text" name="db_pass" class="w-full border rounded-lg px-4 py-2.5" placeholder="Leave blank if no password">
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="w-full bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary-dark transition">Install</button>
            </div>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
