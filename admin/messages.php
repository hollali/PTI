<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();
$title = 'Messages';
$msg = $_GET['msg'] ?? '';

if (isset($_GET['read']) && isset($_GET['id'])) {
    $pdo->prepare("UPDATE contact_messages SET is_read=1 WHERE id=?")->execute([$_GET['id']]);
    $msg = 'Message marked as read.';
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM contact_messages WHERE id=?")->execute([$_GET['delete']]);
    $msg = 'Message deleted.';
}

require_once 'includes/header.php';
?>
<?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?></div><?php endif; ?>

<div class="page-header">
    <h2>Contact Messages</h2>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Subject</th><th>Message</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php
                $messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
                if ($messages->rowCount() == 0): ?>
                <tr class="empty"><td colspan="7">No messages received</td></tr>
                <?php endif; ?>
                <?php while ($m = $messages->fetch()): ?>
                <tr class="<?= !$m['is_read'] ? 'unread' : '' ?>">
                    <td class="fw-600"><?= $m['name'] ?></td>
                    <td><a href="mailto:<?= $m['email'] ?>" class="link"><?= $m['email'] ?></a></td>
                    <td class="text-sm text-muted"><?= $m['subject'] ?: '-' ?></td>
                    <td class="text-sm text-muted max-w-xs truncate"><?= substr($m['message'], 0, 60) ?>...</td>
                    <td class="text-muted text-sm"><?= date('M d, Y', strtotime($m['created_at'])) ?></td>
                    <td>
                        <?php if (!$m['is_read']): ?>
                        <span class="badge badge-warning">New</span>
                        <?php else: ?>
                        <span class="badge badge-success">Read</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-group">
                            <?php if (!$m['is_read']): ?>
                            <a href="?read=1&id=<?= $m['id'] ?>" class="btn btn-ghost btn-sm" title="Mark as read"><i class="fas fa-check"></i></a>
                            <?php endif; ?>
                            <a href="?delete=<?= $m['id'] ?>" class="btn btn-danger btn-sm" onclick="event.preventDefault();showConfirm('Delete this message?',function(){window.location.href='?delete=<?= $m['id'] ?>'})"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
