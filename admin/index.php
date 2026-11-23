<?php
require_once __DIR__ . '/../config/database.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND status='active'");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

            $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
            logActivity('login', "User {$user['username']} logged in");
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username/email or password.';
        }
    } else {
        $error = 'Please enter username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | PTI CMS</title>
    <meta name="theme-color" content="#006B3F">
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: { DEFAULT: '#006B3F', dark: '#004F2E' },
                    appdark: '#0F1A2E',
                }
            }
        }
    }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --primary: #006B3F; --primary-dark: #004F2E; --dark: #0F1A2E; }
        * { font-family: 'Inter', sans-serif; }
        body {
            background: linear-gradient(135deg, #0F1A2E 0%, #006B3F 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        }
        .login-card h1 { font-size: 22px; font-weight: 700; color: var(--dark); }
        .login-card h1 span { color: var(--primary); }
        .login-card p { font-size: 12px; color: #999; }
        .form-input {
            width: 100%;
            padding: 11px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
        }
        .form-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,107,63,0.08); }
        .btn-login {
            width: 100%;
            padding: 11px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s;
        }
        .btn-login:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 5px 20px rgba(0,107,63,0.25); }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-applight rounded-xl flex items-center justify-center mx-auto mb-4" style="background:#F0FFF4">
                <i class="fas fa-landmark text-2xl" style="color:#006B3F"></i>
            </div>
            <h1>PTI <span>CMS</span></h1>
            <p>Parliamentary Training Institute - Admin Panel</p>
        </div>

        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-6 flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <?= $error ?>
        </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-600 mb-1">Username or Email</label>
                <input type="text" name="username" class="form-input" placeholder="Enter your username" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-600 mb-1">Password</label>
                <input type="password" name="password" class="form-input" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn-login">Sign In <i class="fas fa-arrow-right ml-2"></i></button>
        </form>

        <div class="text-center mt-6">
            <a href="<?= SITE_URL ?>/index.php" class="text-sm text-gray-400 hover:text-primary transition">
                <i class="fas fa-arrow-left mr-1"></i> Back to Website
            </a>
        </div>
    </div>
</body>
</html>
