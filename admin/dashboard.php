<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();
$title = 'Dashboard';

$totalPages = $pdo->query("SELECT COUNT(*) FROM pages WHERE status='published'")->fetchColumn();
$totalPrograms = $pdo->query("SELECT COUNT(*) FROM programs WHERE status='active'")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status='active'")->fetchColumn();
$totalMessages = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn();
$totalVisits = $pdo->query("SELECT COUNT(*) FROM page_visits WHERE visited_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
$totalNews = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();

$recentLogs = $pdo->query("SELECT al.*, u.username, u.fullname FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id ORDER BY al.created_at DESC LIMIT 10")->fetchAll();

require_once 'includes/header.php';
?>
<div class="grid-6 mb-8">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
        <div class="stat-num mt-3"><?= $totalPages ?></div>
        <div class="stat-label">Published Pages</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-graduation-cap"></i></div>
        <div class="stat-num mt-3"><?= $totalPrograms ?></div>
        <div class="stat-label">Active Programs</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-num mt-3"><?= $totalUsers ?></div>
        <div class="stat-label">Active Users</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-envelope"></i></div>
        <div class="stat-num mt-3"><?= $totalMessages ?></div>
        <div class="stat-label">Unread Messages</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
        <div class="stat-num mt-3"><?= $totalVisits ?></div>
        <div class="stat-label">Weekly Visits</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-newspaper"></i></div>
        <div class="stat-num mt-3"><?= $totalNews ?></div>
        <div class="stat-label">News Articles</div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">Recent Activity</div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>User</th><th>Action</th><th>Date</th></tr></thead>
                <tbody>
                    <?php if (empty($recentLogs)): ?>
                    <tr class="empty"><td colspan="3">No activity recorded yet</td></tr>
                    <?php endif; ?>
                    <?php foreach ($recentLogs as $log): ?>
                    <tr>
                        <td class="fw-500"><?= $log['fullname'] ?? $log['username'] ?? 'System' ?></td>
                        <td><span class="badge badge-info"><?= $log['action'] ?></span></td>
                        <td class="text-muted text-sm"><?= timeAgo($log['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Quick Actions</div>
        <div class="card-body">
            <div class="grid-3">
                <a href="pages.php?action=add" class="btn btn-ghost flex-col !items-center !gap-2 !p-5 !h-auto">
                    <i class="fas fa-plus-circle text-2xl text-primary"></i>
                    <span class="text-xs">New Page</span>
                </a>
                <a href="programs.php?action=add" class="btn btn-ghost flex-col !items-center !gap-2 !p-5 !h-auto">
                    <i class="fas fa-plus-circle text-2xl text-primary"></i>
                    <span class="text-xs">New Program</span>
                </a>
                <a href="news.php?action=add" class="btn btn-ghost flex-col !items-center !gap-2 !p-5 !h-auto">
                    <i class="fas fa-plus-circle text-2xl text-primary"></i>
                    <span class="text-xs">New News</span>
                </a>
                <a href="banners.php?action=add" class="btn btn-ghost flex-col !items-center !gap-2 !p-5 !h-auto">
                    <i class="fas fa-plus-circle text-2xl text-primary"></i>
                    <span class="text-xs">New Banner</span>
                </a>
                <a href="users.php?action=add" class="btn btn-ghost flex-col !items-center !gap-2 !p-5 !h-auto">
                    <i class="fas fa-plus-circle text-2xl text-primary"></i>
                    <span class="text-xs">New User</span>
                </a>
                <a href="messages.php" class="btn btn-ghost flex-col !items-center !gap-2 !p-5 !h-auto">
                    <i class="fas fa-inbox text-2xl text-primary"></i>
                    <span class="text-xs">Messages <?= $totalMessages ? "($totalMessages)" : '' ?></span>
                </a>
            </div>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
