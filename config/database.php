<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'pti_db');
define('DB_USER', 'root');
define('DB_PASS', 'Vendetta7080');

define('SITE_URL', 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/PTI');
define('ADMIN_URL', SITE_URL . '/admin');
define('UPLOAD_PATH', __DIR__ . '/../public/uploads');
define('UPLOAD_URL', SITE_URL . '/public/uploads');
define('ASSETS_URL', SITE_URL . '/assets');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function getSetting($key) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? $row['setting_value'] : null;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . ADMIN_URL . '/index.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . ADMIN_URL . '/dashboard.php');
        exit;
    }
}

function logActivity($action, $description = '') {
    global $pdo;
    $userId = $_SESSION['user_id'] ?? null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $action, $description, $ip, $ua]);
}

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text ?: 'n-a';
}

function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    $units = [
        31536000 => 'year', 2592000 => 'month', 604800 => 'week',
        86400 => 'day', 3600 => 'hour', 60 => 'minute', 1 => 'second'
    ];
    foreach ($units as $sec => $unit) {
        if ($diff >= $sec) {
            $count = floor($diff / $sec);
            return $count . ' ' . $unit . ($count > 1 ? 's' : '') . ' ago';
        }
    }
    return 'just now';
}

function uploadFile($file, $subdir = '') {
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) return false;
    $targetDir = UPLOAD_PATH . ($subdir ? '/' . $subdir : '');
    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    if (!in_array($ext, $allowed)) return false;
    $filename = uniqid() . '.' . $ext;
    $relativePath = 'public/uploads' . ($subdir ? '/' . $subdir : '') . '/' . $filename;
    if (move_uploaded_file($file['tmp_name'], $targetDir . '/' . $filename)) {
        global $pdo;
        $data = file_get_contents($targetDir . '/' . $filename);
        $mime = $file['type'] ?: 'application/octet-stream';
        try {
            $pdo->prepare("INSERT INTO file_store (file_path, file_data, file_mime, file_size) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE file_data=VALUES(file_data), file_mime=VALUES(file_mime), file_size=VALUES(file_size)")
                ->execute([$relativePath, $data, $mime, $file['size']]);
        } catch (Exception $e) {
        }
        return $relativePath;
    }
    return false;
}

function trackVisit($page) {
    global $pdo;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    $stmt = $pdo->prepare("INSERT INTO page_visits (page, ip_address, user_agent, referrer) VALUES (?, ?, ?, ?)");
    $stmt->execute([$page, $ip, $ua, $ref]);
}
