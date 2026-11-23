<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();
$title = 'Analytics';

$totalVisits = $pdo->query("SELECT COUNT(*) FROM page_visits")->fetchColumn();
$todayVisits = $pdo->query("SELECT COUNT(*) FROM page_visits WHERE DATE(visited_at) = CURDATE()")->fetchColumn();
$uniqueVisitors = $pdo->query("SELECT COUNT(DISTINCT ip_address) FROM page_visits")->fetchColumn();
$avgDaily = $pdo->query("SELECT ROUND(COUNT(*) / DATEDIFF(MAX(DATE(visited_at)), MIN(DATE(visited_at)))) FROM page_visits")->fetchColumn();

$popularPages = $pdo->query("SELECT page, COUNT(*) as visits FROM page_visits GROUP BY page ORDER BY visits DESC LIMIT 10")->fetchAll();

$dailyVisits = $pdo->query("SELECT DATE(visited_at) as date, COUNT(*) as count FROM page_visits WHERE visited_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY DATE(visited_at) ORDER BY date ASC")->fetchAll();

require_once 'includes/header.php';
?>
<div class="grid-4">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
        <div class="stat-num"><?= number_format($totalVisits) ?></div>
        <div class="stat-label">Total Visits</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
        <div class="stat-num"><?= number_format($todayVisits) ?></div>
        <div class="stat-label">Today</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-num"><?= number_format($uniqueVisitors) ?></div>
        <div class="stat-label">Unique Visitors</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-chart-bar"></i></div>
        <div class="stat-num"><?= $avgDaily ? number_format($avgDaily, 0) : 0 ?></div>
        <div class="stat-label">Avg Daily</div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">Most Visited Pages</div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Page</th><th>Visits</th></tr></thead>
                <tbody>
                    <?php if (empty($popularPages)): ?>
                    <tr class="empty"><td colspan="2">No data yet</td></tr>
                    <?php endif; ?>
                    <?php foreach ($popularPages as $p): ?>
                    <tr>
                        <td class="fw-600"><?= $p['page'] ?></td>
                        <td><strong><?= $p['visits'] ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Last 30 Days Traffic</div>
        <div class="card-body">
            <?php if (empty($dailyVisits)): ?>
            <div class="empty-state">No data available yet</div>
            <?php else: ?>
            <div class="space-y-2">
                <?php foreach (array_slice($dailyVisits, -14) as $d): ?>
                <div class="flex items-center gap-3 text-sm">
                    <span class="w-24 text-muted"><?= date('M d', strtotime($d['date'])) ?></span>
                    <div class="flex-1 bg-gray-100 rounded-full h-4 overflow-hidden">
                        <div class="bg-primary h-full rounded-full" style="width: <?= min(100, ($d['count'] / max(array_column($dailyVisits, 'count'))) * 100) ?>%"></div>
                    </div>
                    <span class="fw-600 w-8 text-right"><?= $d['count'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
