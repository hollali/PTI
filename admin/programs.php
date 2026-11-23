<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();
$title = 'Programs';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save') {
        $id = (int)$_POST['id'];
        $title = sanitize($_POST['title']);
        $slug = sanitize($_POST['slug']) ?: slugify($title);
        $desc = sanitize($_POST['description']);
        $content = $_POST['content'] ?? '';
        $duration = sanitize($_POST['duration']);
        $fee = (float)$_POST['fee'];
        $category = sanitize($_POST['category']);
        $status = sanitize($_POST['status']);
        $featured = isset($_POST['featured']) ? 1 : 0;

        if ($id) {
            $image = sanitize($_POST['image'] ?? '');
            if (!empty($_FILES['image_file']['name'])) {
                $uploaded = uploadFile($_FILES['image_file'], 'programs');
                if ($uploaded) $image = $uploaded;
            }
            $pdo->prepare("UPDATE programs SET title=?, slug=?, description=?, content=?, image=?, duration=?, fee=?, category=?, status=?, featured=? WHERE id=?")
                ->execute([$title, $slug, $desc, $content, $image, $duration, $fee, $category, $status, $featured, $id]);
            logActivity('update_program', "Updated program: $title");
            $msg = 'Program updated.';
        } else {
            $image = sanitize($_POST['image'] ?? '');
            if (!empty($_FILES['image_file']['name'])) {
                $uploaded = uploadFile($_FILES['image_file'], 'programs');
                if ($uploaded) $image = $uploaded;
            }
            $pdo->prepare("INSERT INTO programs (title, slug, description, content, image, duration, fee, category, status, featured, created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?)")
                ->execute([$title, $slug, $desc, $content, $image, $duration, $fee, $category, $status, $featured, $_SESSION['user_id']]);
            logActivity('create_program', "Created program: $title");
            $msg = 'Program created.';
        }
    }
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM programs WHERE id=?")->execute([$_GET['delete']]);
    $msg = 'Program deleted.';
}

require_once 'includes/header.php';
?>
<?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?></div><?php endif; ?>

<div class="page-header">
    <h2>All Programs</h2>
    <div class="actions">
        <button onclick="openProgramModal()" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Program</button>
    </div>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Title</th><th>Duration</th><th>Category</th><th>Featured</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php
                $programs = $pdo->query("SELECT * FROM programs ORDER BY featured DESC, id ASC");
                if ($programs->rowCount() == 0): ?>
                <tr class="empty"><td colspan="6">No programs found</td></tr>
                <?php endif; ?>
                <?php while ($p = $programs->fetch()): ?>
                <tr>
                    <td class="fw-600"><?= $p['title'] ?></td>
                    <td class="text-sm"><?= $p['duration'] ?></td>
                    <td><span class="badge badge-neutral"><?= $p['category'] ?></span></td>
                    <td><?= $p['featured'] ? '<span class="badge badge-success">Featured</span>' : '<span class="text-muted text-sm">—</span>' ?></td>
                    <td><span class="badge <?= $p['status']=='active'?'badge-success':'badge-danger' ?>"><?= $p['status'] ?></span></td>
                    <td>
                        <div class="btn-group">
                            <button onclick="openProgramModal(<?= $p['id'] ?>)" class="btn btn-ghost btn-sm"><i class="fas fa-edit"></i></button>
                            <a href="?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="event.preventDefault();showConfirm('Delete this program?',function(){window.location.href='?delete=<?= $p['id'] ?>'})"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="programModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-header">
            <h3 id="programModalTitle">New Program</h3>
            <button onclick="closeModal('programModal')" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" id="programId" value="0">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Program Title <span class="required">*</span></label>
                        <input type="text" name="title" id="programTitle" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" id="programSlug" class="form-input">
                        <div class="form-hint">Auto-generated from title if left blank</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Short Description</label>
                    <textarea name="description" id="programDesc" class="form-textarea" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Full Content (HTML)</label>
                    <textarea name="content" id="programContent" class="form-textarea font-mono" rows="8"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Image URL <span class="form-hint">(URL or upload below)</span></label>
                    <input type="text" name="image" id="programImage" class="form-input" placeholder="public/uploads/program.jpg">
                    <div class="mt-2">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Or upload a file:</label>
                        <input type="file" name="image_file" accept="image/*" class="form-input !py-1.5 text-sm">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Duration</label>
                        <input type="text" name="duration" id="programDuration" class="form-input" placeholder="e.g. 2 Weeks">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fee (GHS)</label>
                        <input type="number" step="0.01" name="fee" id="programFee" class="form-input" value="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" id="programCategory" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" id="programStatus" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="checkbox-wrap">
                        <input type="checkbox" name="featured" id="programFeatured" value="1">
                        <span class="text-sm fw-500">Featured program (shown on homepage)</span>
                    </label>
                </div>
                <div class="divider"></div>
                <div class="flex gap-3">
                    <button type="submit" class="btn btn-primary btn-lg">Save Program</button>
                    <button type="button" onclick="closeModal('programModal')" class="btn btn-ghost btn-lg">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var programData = <?= json_encode($pdo->query("SELECT * FROM programs ORDER BY featured DESC, id ASC")->fetchAll()) ?>;

function openProgramModal(id) {
    var form = document.querySelector('#programModal form');
    form.reset();
    document.getElementById('programId').value = 0;
    document.getElementById('programTitle').value = '';
            document.getElementById('programSlug').value = '';
    document.getElementById('programDesc').value = '';
    document.getElementById('programContent').value = '';
    document.getElementById('programImage').value = '';
    document.getElementById('programDuration').value = '';
    document.getElementById('programFee').value = '0';
    document.getElementById('programCategory').value = '';
    document.getElementById('programStatus').value = 'active';
    document.getElementById('programFeatured').checked = false;

    if (id) {
        var p = programData.find(function(x) { return x.id == id; });
        if (p) {
            document.getElementById('programModalTitle').textContent = 'Edit Program';
            document.getElementById('programId').value = p.id;
            document.getElementById('programTitle').value = p.title;
            document.getElementById('programSlug').value = p.slug;
            document.getElementById('programDesc').value = p.description;
            document.getElementById('programContent').value = p.content;
            document.getElementById('programImage').value = p.image || '';
            document.getElementById('programDuration').value = p.duration;
            document.getElementById('programFee').value = p.fee;
            document.getElementById('programCategory').value = p.category;
            document.getElementById('programStatus').value = p.status;
            document.getElementById('programFeatured').checked = p.featured == 1;
        }
    } else {
        document.getElementById('programModalTitle').textContent = 'New Program';
    }
    openModal('programModal');
}
</script>
<?php require_once 'includes/footer.php'; ?>
