<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();
$title = 'News';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save') {
        $id = (int)$_POST['id'];
        $title = sanitize($_POST['title']);
        $slug = sanitize($_POST['slug']) ?: slugify($title);
        $content = $_POST['content'] ?? '';
        $status = sanitize($_POST['status'] ?? 'draft');

        if ($id) {
            $image = sanitize($_POST['image'] ?? '');
            if (!empty($_FILES['image_file']['name'])) {
                $uploaded = uploadFile($_FILES['image_file'], 'news');
                if ($uploaded) $image = $uploaded;
            }
            $pdo->prepare("UPDATE news SET title=?, slug=?, content=?, image=?, status=? WHERE id=?")
                ->execute([$title, $slug, $content, $image, $status, $id]);
            logActivity('update_news', "Updated news: $title");
            $msg = 'News updated.';
        } else {
            $image = sanitize($_POST['image'] ?? '');
            if (!empty($_FILES['image_file']['name'])) {
                $uploaded = uploadFile($_FILES['image_file'], 'news');
                if ($uploaded) $image = $uploaded;
            }
            $pdo->prepare("INSERT INTO news (title, slug, content, image, status, created_by) VALUES (?,?,?,?,?,?)")
                ->execute([$title, $slug, $content, $image, $status, $_SESSION['user_id']]);
            logActivity('create_news', "Created news: $title");
            $msg = 'News created.';
        }
    }
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM news WHERE id=?")->execute([$_GET['delete']]);
    $msg = 'News deleted.';
}

require_once 'includes/header.php';
?>
<?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?></div><?php endif; ?>

<div class="page-header">
    <h2>News Articles</h2>
    <div class="actions">
        <button onclick="openNewsModal()" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Article</button>
    </div>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Title</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
                <?php
                $news = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
                if ($news->rowCount() == 0): ?>
                <tr class="empty"><td colspan="4">No news articles</td></tr>
                <?php endif; ?>
                <?php while ($n = $news->fetch()): ?>
                <tr>
                    <td class="fw-600"><?= $n['title'] ?></td>
                    <td><span class="badge <?= $n['status']=='published'?'badge-success':'badge-warning' ?>"><?= $n['status'] ?></span></td>
                    <td class="text-muted text-sm"><?= date('M d, Y', strtotime($n['created_at'])) ?></td>
                    <td>
                        <div class="btn-group">
                            <button onclick="openNewsModal(<?= $n['id'] ?>)" class="btn btn-ghost btn-sm"><i class="fas fa-edit"></i></button>
                            <a href="?delete=<?= $n['id'] ?>" class="btn btn-danger btn-sm" onclick="event.preventDefault();showConfirm('Delete this article?',function(){window.location.href='?delete=<?= $n['id'] ?>'})"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="newsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-header">
            <h3 id="newsModalTitle">New Article</h3>
            <button onclick="closeModal('newsModal')" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" id="newsId" value="0">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Title <span class="required">*</span></label>
                        <input type="text" name="title" id="newsTitle" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" id="newsSlug" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Content (HTML)</label>
                    <textarea name="content" id="newsContent" class="form-textarea font-mono" rows="12"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Image URL <span class="form-hint">(URL or upload below)</span></label>
                    <input type="text" name="image" id="newsImage" class="form-input" placeholder="public/uploads/news-image.jpg">
                    <div class="mt-2">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Or upload a file:</label>
                        <input type="file" name="image_file" accept="image/*" class="form-input !py-1.5 text-sm">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" id="newsStatus" class="form-select">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="flex gap-3">
                    <button type="submit" class="btn btn-primary">Save Article</button>
                    <button type="button" onclick="closeModal('newsModal')" class="btn btn-ghost">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var newsData = <?= json_encode($pdo->query("SELECT * FROM news ORDER BY created_at DESC")->fetchAll()) ?>;

function openNewsModal(id) {
    var form = document.querySelector('#newsModal form');
    form.reset();
    document.getElementById('newsId').value = 0;
    document.getElementById('newsTitle').value = '';
    document.getElementById('newsSlug').value = '';
    document.getElementById('newsContent').value = '';
    document.getElementById('newsImage').value = '';
    document.getElementById('newsStatus').value = 'draft';

    if (id) {
        var n = newsData.find(function(x) { return x.id == id; });
        if (n) {
            document.getElementById('newsModalTitle').textContent = 'Edit Article';
            document.getElementById('newsId').value = n.id;
            document.getElementById('newsTitle').value = n.title;
            document.getElementById('newsSlug').value = n.slug;
            document.getElementById('newsContent').value = n.content;
            document.getElementById('newsImage').value = n.image || '';
            document.getElementById('newsStatus').value = n.status;
        }
    } else {
        document.getElementById('newsModalTitle').textContent = 'New Article';
    }
    openModal('newsModal');
}
</script>
<?php require_once 'includes/footer.php'; ?>
