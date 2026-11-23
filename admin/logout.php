<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();
$user = $_SESSION['username'] ?? 'Unknown';
logActivity('logout', "User $user logged out");
session_destroy();
header('Location: index.php');
exit;
