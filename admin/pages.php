<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();
$title = 'Pages';

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save') {
        $id = (int)$_POST['id'];
        $title = sanitize($_POST['title']);
        $slug = sanitize($_POST['slug']) ?: slugify($title);
        $content = $_POST['content'];
        $meta = sanitize($_POST['meta_description'] ?? '');
        $template = sanitize($_POST['template'] ?? 'default');
        $status = sanitize($_POST['status'] ?? 'draft');

        if ($id) {
            $pdo->prepare("UPDATE pages SET title=?, slug=?, content=?, meta_description=?, template=?, status=? WHERE id=?")
                ->execute([$title, $slug, $content, $meta, $template, $status, $id]);
            logActivity('update_page', "Updated page: $title");
            $msg = 'Page updated successfully.';
        } else {
            $pdo->prepare("INSERT INTO pages (title, slug, content, meta_description, template, status, created_by) VALUES (?,?,?,?,?,?,?)")
                ->execute([$title, $slug, $content, $meta, $template, $status, $_SESSION['user_id']]);
            logActivity('create_page', "Created page: $title");
            $msg = 'Page created successfully.';
        }
    }
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM pages WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    logActivity('delete_page', "Deleted page ID: {$_GET['delete']}");
    $msg = 'Page deleted successfully.';
}

require_once 'includes/header.php';
?>
<?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>

<div class="page-header">
    <h2>All Pages</h2>
    <div class="actions">
        <button onclick="openPageModal()" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Page</button>
    </div>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Title</th><th>Slug</th><th>Template</th><th>Status</th><th>Updated</th><th>Actions</th></tr></thead>
            <tbody>
                <?php
                $pages = $pdo->query("SELECT * FROM pages ORDER BY updated_at DESC");
                if ($pages->rowCount() == 0): ?>
                <tr class="empty"><td colspan="6">No pages found</td></tr>
                <?php endif; ?>
                <?php while ($p = $pages->fetch()): ?>
                <tr>
                    <td class="fw-600"><?= $p['title'] ?></td>
                    <td class="text-muted text-sm">/<?= $p['slug'] ?></td>
                    <td><span class="badge badge-neutral"><?= $p['template'] ?></span></td>
                    <td><span class="badge <?= $p['status']=='published'?'badge-success':'badge-warning' ?>"><?= $p['status'] ?></span></td>
                    <td class="text-muted text-sm"><?= timeAgo($p['updated_at']) ?></td>
                    <td>
                        <div class="btn-group">
                            <button onclick="openPageModal(<?= $p['id'] ?>)" class="btn btn-ghost btn-sm"><i class="fas fa-edit"></i></button>
                            <a href="?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="event.preventDefault();showConfirm('Delete this page?',function(){window.location.href='?delete=<?= $p['id'] ?>'})"><i class="fas fa-trash"></i></a>
                            <a href="<?= SITE_URL ?>/<?= $p['slug'] == 'home' ? 'index' : $p['slug'] ?>.php" target="_blank" class="btn btn-ghost btn-sm"><i class="fas fa-external-link-alt"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="pageModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-header">
            <h3 id="pageModalTitle">New Page</h3>
            <button onclick="closeModal('pageModal')" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" id="pageId" value="0">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Page Title <span class="required">*</span></label>
                        <input type="text" name="title" id="pageTitle" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">URL Slug</label>
                        <input type="text" name="slug" id="pageSlug" class="form-input" placeholder="leave-empty-to-auto-generate">
                        <div class="form-hint">Auto-generated from title if left blank</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Content</label>
                    <textarea name="content" id="pageContent" rows="12" class="form-textarea font-mono"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Meta Description</label>
                        <input type="text" name="meta_description" id="pageMeta" class="form-input">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Template</label>
                        <select name="template" id="pageTemplate" class="form-select">
                            <option value="default">Default</option>
                            <option value="fullwidth">Full Width</option>
                            <option value="sidebar">With Sidebar</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" id="pageStatus" class="form-select">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="flex gap-3">
                    <button type="submit" class="btn btn-primary btn-lg">Save Page</button>
                    <button type="button" onclick="closeModal('pageModal')" class="btn btn-ghost btn-lg">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var pageData = <?= json_encode($pdo->query("SELECT * FROM pages ORDER BY updated_at DESC")->fetchAll()) ?>;

function openPageModal(id) {
    var titleEl = document.getElementById('pageModalTitle');
    var form = document.querySelector('#pageModal form');
    form.reset();
    document.getElementById('pageId').value = 0;
    document.getElementById('pageTitle').value = '';
    document.getElementById('pageSlug').value = '';
    document.getElementById('pageContent').value = '';
    document.getElementById('pageMeta').value = '';
    document.getElementById('pageTemplate').value = 'default';
    document.getElementById('pageStatus').value = 'draft';

    if (id) {
        var p = pageData.find(function(x) { return x.id == id; });
        if (p) {
            titleEl.textContent = 'Edit Page';
            document.getElementById('pageId').value = p.id;
            document.getElementById('pageTitle').value = p.title;
            document.getElementById('pageSlug').value = p.slug;
            document.getElementById('pageContent').value = p.content;
            document.getElementById('pageMeta').value = p.meta_description || '';
            document.getElementById('pageTemplate').value = p.template;
            document.getElementById('pageStatus').value = p.status;
        }
    } else {
        titleEl.textContent = 'New Page';
    }
    openModal('pageModal');
}
</script>
<?php require_once 'includes/footer.php'; ?>
