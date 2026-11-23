<?php
require_once __DIR__ . '/config/database.php';

$path = $_GET['path'] ?? '';
if (!$path || strpos($path, '..') !== false) {
    http_response_code(404);
    exit;
}

$diskPath = __DIR__ . '/' . $path;
if (file_exists($diskPath)) {
    $mime = mime_content_type($diskPath) ?: 'application/octet-stream';
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($diskPath));
    header('X-Served-From: disk');
    readfile($diskPath);
    exit;
}

$stmt = $pdo->prepare("SELECT file_data, file_mime FROM file_store WHERE file_path = ?");
$stmt->execute([$path]);
$row = $stmt->fetch();
if ($row) {
    header('Content-Type: ' . $row['file_mime']);
    header('Content-Length: ' . strlen($row['file_data']));
    header('X-Served-From: database');
    echo $row['file_data'];
    exit;
}

http_response_code(404);
