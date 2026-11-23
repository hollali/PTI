<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();
$title = 'Banners';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    if ($action === 'save') {
        $id = (int)$_POST['id'];
        $title = sanitize($_POST['title']);
        $subtitle = sanitize($_POST['subtitle'] ?? '');
        $link = sanitize($_POST['link'] ?? '');
        $btn_text = sanitize($_POST['btn_text'] ?? 'Learn More');
        $order = (int)$_POST['sort_order'];
        $status = sanitize($_POST['status'] ?? 'active');
        $image_url = $_POST['image_url'] ?? '';
        if (!empty($_FILES['image_file']['name'])) {
            $uploaded = uploadFile($_FILES['image_file'], 'banners');
            if ($uploaded) $image_url = $uploaded;
        }

        if ($id) {
            $pdo->prepare("UPDATE banners SET title=?, subtitle=?, image_url=?, link=?, btn_text=?, sort_order=?, status=? WHERE id=?")
                ->execute([$title, $subtitle, $image_url, $link, $btn_text, $order, $status, $id]);
            logActivity('update_banner', "Updated banner: $title");
            $msg = 'Banner updated.';
        } else {
            $pdo->prepare("INSERT INTO banners (title, subtitle, image_url, link, btn_text, sort_order, status) VALUES (?,?,?,?,?,?,?)")
                ->execute([$title, $subtitle, $image_url, $link, $btn_text, $order, $status]);
            logActivity('create_banner', "Created banner: $title");
            $msg = 'Banner created.';
        }
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM banners WHERE id=?")->execute([$_POST['id']]);
        $msg = 'Banner deleted.';
    }
}

require_once 'includes/header.php';
?>
<?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?></div><?php endif; ?>

<div class="page-header">
    <h2>Banners</h2>
    <div class="actions">
        <button onclick="openBannerModal()" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Banner</button>
    </div>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Order</th><th>Title</th><th>Subtitle</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php
                $banners = $pdo->query("SELECT * FROM banners ORDER BY sort_order");
                if ($banners->rowCount() == 0): ?>
                <tr class="empty"><td colspan="5">No banners</td></tr>
                <?php endif; ?>
                <?php while ($b = $banners->fetch()): ?>
                <tr>
                    <td class="text-center"><?= $b['sort_order'] ?></td>
                    <td class="fw-600"><?= $b['title'] ?></td>
                    <td class="text-muted text-sm"><?= substr($b['subtitle'], 0, 50) ?></td>
                    <td><span class="badge <?= $b['status']=='active'?'badge-success':'badge-danger' ?>"><?= $b['status'] ?></span></td>
                    <td>
                        <div class="btn-group">
                            <button onclick="openBannerModal(<?= $b['id'] ?>)" class="btn btn-ghost btn-sm"><i class="fas fa-edit"></i></button>
                            <form method="POST" class="inline" onsubmit="event.preventDefault();showConfirm('Delete this banner?',function(){this.submit()}.bind(this))">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $b['id'] ?>">
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

<div class="modal-overlay" id="bannerModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 id="bannerModalTitle">New Banner</h3>
            <button onclick="closeModal('bannerModal')" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" id="bannerId" value="0">
                <div class="form-group">
                    <label class="form-label">Title <span class="required">*</span></label>
                    <input type="text" name="title" id="bannerTitle" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Subtitle</label>
                    <textarea name="subtitle" id="bannerSubtitle" class="form-textarea" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Image URL <span class="form-hint">(URL or upload below)</span></label>
                    <input type="text" name="image_url" id="bannerImage" class="form-input" placeholder="assets/images/banner.jpg">
                    <div class="mt-2">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Or upload a file:</label>
                        <input type="file" name="image_file" accept="image/*" class="form-input !py-1.5 text-sm">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Button Text</label>
                        <input type="text" name="btn_text" id="bannerBtn" class="form-input" value="Learn More">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Button Link</label>
                        <input type="text" name="link" id="bannerLink" class="form-input" value="#">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" id="bannerOrder" class="form-input" value="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" id="bannerStatus" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="flex gap-3">
                    <button type="submit" class="btn btn-primary">Save Banner</button>
                    <button type="button" onclick="closeModal('bannerModal')" class="btn btn-ghost">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var bannerData = <?= json_encode($pdo->query("SELECT * FROM banners ORDER BY sort_order")->fetchAll()) ?>;

function openBannerModal(id) {
    var form = document.querySelector('#bannerModal form');
    form.reset();
    document.getElementById('bannerId').value = 0;
    document.getElementById('bannerTitle').value = '';
    document.getElementById('bannerSubtitle').value = '';
    document.getElementById('bannerImage').value = '';
    document.getElementById('bannerBtn').value = 'Learn More';
    document.getElementById('bannerLink').value = '#';
    document.getElementById('bannerOrder').value = '0';
    document.getElementById('bannerStatus').value = 'active';

    if (id) {
        var b = bannerData.find(function(x) { return x.id == id; });
        if (b) {
            document.getElementById('bannerModalTitle').textContent = 'Edit Banner';
            document.getElementById('bannerId').value = b.id;
            document.getElementById('bannerTitle').value = b.title;
            document.getElementById('bannerSubtitle').value = b.subtitle || '';
            document.getElementById('bannerImage').value = b.image_url || '';
            document.getElementById('bannerBtn').value = b.btn_text;
            document.getElementById('bannerLink').value = b.link || '#';
            document.getElementById('bannerOrder').value = b.sort_order;
            document.getElementById('bannerStatus').value = b.status;
        }
    } else {
        document.getElementById('bannerModalTitle').textContent = 'New Banner';
    }
    openModal('bannerModal');
}
</script>
<?php require_once 'includes/footer.php'; ?>
