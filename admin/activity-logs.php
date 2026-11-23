<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();
$title = 'Activity Logs';

require_once 'includes/header.php';
?>

<div class="page-header">
    <h2>Activity Logs</h2>
    <form method="GET" class="search-wrap">
        <input type="text" name="search" class="form-input" placeholder="Search actions..." value="<?= $_GET['search'] ?? '' ?>">
        <button type="submit" class="btn btn-ghost btn-sm"><i class="fas fa-search"></i></button>
    </form>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>User</th><th>Action</th><th>Description</th><th>IP Address</th><th>Date</th></tr></thead>
            <tbody>
                <?php
                $search = $_GET['search'] ?? '';
                $query = "SELECT al.*, u.username, u.fullname FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id";
                $params = [];
                if ($search) {
                    $query .= " WHERE al.action LIKE ? OR al.description LIKE ? OR u.username LIKE ?";
                    $params = ["%$search%", "%$search%", "%$search%"];
                }
                $query .= " ORDER BY al.created_at DESC LIMIT 100";
                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
                $logs = $stmt->fetchAll();

                if (empty($logs)): ?>
                <tr class="empty"><td colspan="5">No activity logs found</td></tr>
                <?php endif; ?>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td class="fw-600 text-sm"><?= $log['fullname'] ?? $log['username'] ?? 'System' ?></td>
                    <td><span class="badge badge-info"><?= $log['action'] ?></span></td>
                    <td class="text-sm text-muted max-w-xs truncate"><?= $log['description'] ?></td>
                    <td class="text-xs font-mono text-muted"><?= $log['ip_address'] ?></td>
                    <td class="text-muted text-sm"><?= date('M d, Y H:i', strtotime($log['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
