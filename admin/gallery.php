<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();
$title = 'Gallery';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    if ($action === 'save') {
        $id = (int)$_POST['id'];
        $title = sanitize($_POST['title'] ?? '');
        $category = sanitize($_POST['category'] ?? '');
        $order = (int)$_POST['sort_order'];
        $image_url = sanitize($_POST['image_url'] ?? '');
        if (!empty($_FILES['image_file']['name'])) {
            $uploaded = uploadFile($_FILES['image_file'], 'gallery');
            if ($uploaded) $image_url = $uploaded;
        }

        if ($id) {
            $pdo->prepare("UPDATE gallery SET title=?, image_url=?, category=?, sort_order=? WHERE id=?")
                ->execute([$title, $image_url, $category, $order, $id]);
            $msg = 'Image updated.';
        } else {
            $pdo->prepare("INSERT INTO gallery (title, image_url, category, sort_order) VALUES (?,?,?,?)")
                ->execute([$title, $image_url, $category, $order]);
            $msg = 'Image added.';
        }
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM gallery WHERE id=?")->execute([$_POST['id']]);
        $msg = 'Image removed.';
    }
}

require_once 'includes/header.php';
?>
<?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?></div><?php endif; ?>

<div class="page-header">
    <h2>Gallery</h2>
    <div class="actions">
        <button onclick="openGalleryModal()" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Image</button>
    </div>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Preview</th><th>Title</th><th>Category</th><th>Order</th><th>Actions</th></tr></thead>
            <tbody>
                <?php
                $images = $pdo->query("SELECT * FROM gallery ORDER BY sort_order ASC, created_at DESC");
                if ($images->rowCount() == 0): ?>
                <tr class="empty"><td colspan="5">No images</td></tr>
                <?php endif; ?>
                <?php while ($img = $images->fetch()): ?>
                <tr>
                    <td><div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div></td>
                    <td class="fw-600"><?= $img['title'] ?: 'Untitled' ?></td>
                    <td><span class="badge badge-info"><?= $img['category'] ?: 'General' ?></span></td>
                    <td><?= $img['sort_order'] ?></td>
                    <td>
                        <div class="btn-group">
                            <button onclick="openGalleryModal(<?= $img['id'] ?>)" class="btn btn-ghost btn-sm"><i class="fas fa-edit"></i></button>
                            <form method="POST" class="inline" onsubmit="event.preventDefault();showConfirm('Remove this image?',function(){this.submit()}.bind(this))">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $img['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="galleryModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 id="galleryModalTitle">Add Image</h3>
            <button onclick="closeModal('galleryModal')" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" id="galleryId" value="0">
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" id="galleryTitle" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Image URL <span class="form-hint">(URL or upload below)</span></label>
                    <input type="text" name="image_url" id="galleryImage" class="form-input" placeholder="public/uploads/image.jpg">
                    <div class="mt-2">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Or upload a file:</label>
                        <input type="file" name="image_file" accept="image/*" class="form-input !py-1.5 text-sm">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" id="galleryCategory" class="form-input" placeholder="e.g. Training">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" id="galleryOrder" class="form-input" value="0">
                    </div>
                </div>
                <div class="divider"></div>
                <div class="flex gap-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" onclick="closeModal('galleryModal')" class="btn btn-ghost">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var galleryData = <?= json_encode($pdo->query("SELECT * FROM gallery ORDER BY sort_order ASC, created_at DESC")->fetchAll()) ?>;

function openGalleryModal(id) {
    var form = document.querySelector('#galleryModal form');
    form.reset();
    document.getElementById('galleryId').value = 0;
    document.getElementById('galleryTitle').value = '';
    document.getElementById('galleryImage').value = '';
    document.getElementById('galleryCategory').value = '';
    document.getElementById('galleryOrder').value = '0';

    if (id) {
        var img = galleryData.find(function(x) { return x.id == id; });
        if (img) {
            document.getElementById('galleryModalTitle').textContent = 'Edit Image';
            document.getElementById('galleryId').value = img.id;
            document.getElementById('galleryTitle').value = img.title || '';
            document.getElementById('galleryImage').value = img.image_url || '';
            document.getElementById('galleryCategory').value = img.category || '';
            document.getElementById('galleryOrder').value = img.sort_order;
        }
    } else {
        document.getElementById('galleryModalTitle').textContent = 'Add Image';
    }
    openModal('galleryModal');
}
</script>
<?php require_once 'includes/footer.php'; ?>
