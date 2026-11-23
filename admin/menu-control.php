<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();
$title = 'Menu Control';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    if ($action === 'save') {
        $id = (int)$_POST['id'];
        $label = sanitize($_POST['label']);
        $link = sanitize($_POST['link']);
        $parent = $_POST['parent_id'] ?: null;
        $order = (int)$_POST['sort_order'];
        $status = sanitize($_POST['status'] ?? 'active');
        $target = sanitize($_POST['target'] ?? '_self');

        if ($id) {
            $icon = sanitize($_POST['icon'] ?? '');
            $pdo->prepare("UPDATE menu_items SET label=?, link=?, parent_id=?, icon=?, sort_order=?, status=?, target=? WHERE id=?")
                ->execute([$label, $link, $parent, $icon, $order, $status, $target, $id]);
            logActivity('update_menu', "Updated menu item: $label");
            $msg = 'Menu item updated.';
        } else {
            $icon = sanitize($_POST['icon'] ?? '');
            $pdo->prepare("INSERT INTO menu_items (label, link, parent_id, icon, sort_order, status, target) VALUES (?,?,?,?,?,?,?)")
                ->execute([$label, $link, $parent, $icon, $order, $status, $target]);
            logActivity('create_menu', "Created menu item: $label");
            $msg = 'Menu item added.';
        }
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM menu_items WHERE id=?")->execute([$_POST['id']]);
        $msg = 'Menu item deleted.';
    }
}

require_once 'includes/header.php';
?>
<?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?></div><?php endif; ?>

<div class="page-header">
    <h2>Menu Items</h2>
    <div class="actions">
        <button onclick="openMenuModal()" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Menu Item</button>
    </div>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Order</th><th>Label</th><th>Link</th><th>Parent</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php
                $items = $pdo->query("SELECT m1.*, m2.label as parent_label FROM menu_items m1 LEFT JOIN menu_items m2 ON m1.parent_id = m2.id ORDER BY m1.sort_order");
                if ($items->rowCount() == 0): ?>
                <tr class="empty"><td colspan="6">No menu items</td></tr>
                <?php endif; ?>
                <?php while ($item = $items->fetch()): ?>
                <tr>
                    <td class="text-center"><?= $item['sort_order'] ?></td>
                    <td class="fw-600"><?= $item['label'] ?></td>
                    <td class="text-muted text-sm"><?= $item['link'] ?></td>
                    <td class="text-muted text-sm"><?= $item['parent_label'] ?: '-' ?></td>
                    <td><span class="badge <?= $item['status']=='active'?'badge-success':'badge-danger' ?>"><?= $item['status'] ?></span></td>
                    <td>
                        <div class="btn-group">
                            <button onclick="openMenuModal(<?= $item['id'] ?>)" class="btn btn-ghost btn-sm"><i class="fas fa-edit"></i></button>
                            <form method="POST" class="inline" onsubmit="event.preventDefault();showConfirm('Delete this menu item?',function(){this.submit()}.bind(this))">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
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

<div class="modal-overlay" id="menuModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 id="menuModalTitle">New Menu Item</h3>
            <button onclick="closeModal('menuModal')" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" id="menuId" value="0">
                <div class="form-group">
                    <label class="form-label">Label <span class="required">*</span></label>
                    <input type="text" name="label" id="menuLabel" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Link <span class="required">*</span></label>
                    <input type="text" name="link" id="menuLink" class="form-input" value="#" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Icon <span class="form-hint">(Font Awesome class, e.g. fa-home)</span></label>
                    <input type="text" name="icon" id="menuIcon" class="form-input" placeholder="fa-home">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Parent (optional)</label>
                        <select name="parent_id" id="menuParent" class="form-select">
                            <option value="">None (Top Level)</option>
                            <?php
                            $parents = $pdo->query("SELECT * FROM menu_items WHERE parent_id IS NULL ORDER BY sort_order");
                            while ($p = $parents->fetch()):
                            ?>
                            <option value="<?= $p['id'] ?>"><?= $p['label'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" id="menuOrder" class="form-input" value="0" min="0">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" id="menuStatus" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Target</label>
                        <select name="target" id="menuTarget" class="form-select">
                            <option value="_self">Same Window</option>
                            <option value="_blank">New Window</option>
                        </select>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="flex gap-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" onclick="closeModal('menuModal')" class="btn btn-ghost">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var menuData = <?= json_encode($pdo->query("SELECT m1.*, m2.label as parent_label FROM menu_items m1 LEFT JOIN menu_items m2 ON m1.parent_id = m2.id ORDER BY m1.sort_order")->fetchAll()) ?>;
var parentOptions = '';

document.addEventListener('DOMContentLoaded', function() {
    var sel = document.getElementById('menuParent');
    parentOptions = sel.innerHTML;
});

function openMenuModal(id) {
    var form = document.querySelector('#menuModal form');
    form.reset();
    document.getElementById('menuId').value = 0;
    document.getElementById('menuLabel').value = '';
    document.getElementById('menuLink').value = '#';
    document.getElementById('menuIcon').value = '';
    document.getElementById('menuParent').innerHTML = parentOptions;
    document.getElementById('menuOrder').value = '0';
    document.getElementById('menuStatus').value = 'active';
    document.getElementById('menuTarget').value = '_self';

    if (id) {
        var item = menuData.find(function(x) { return x.id == id; });
        if (item) {
            document.getElementById('menuModalTitle').textContent = 'Edit Menu Item';
            document.getElementById('menuId').value = item.id;
            document.getElementById('menuLabel').value = item.label;
            document.getElementById('menuLink').value = item.link;
            document.getElementById('menuIcon').value = item.icon || '';

            var parentSel = document.getElementById('menuParent');
            parentSel.innerHTML = parentOptions;
            if (item.parent_id) {
                parentSel.value = item.parent_id;
            }

            document.getElementById('menuOrder').value = item.sort_order;
            document.getElementById('menuStatus').value = item.status;
            document.getElementById('menuTarget').value = item.target;
        }
    } else {
        document.getElementById('menuModalTitle').textContent = 'New Menu Item';
    }
    openModal('menuModal');
}
</script>
<?php require_once 'includes/footer.php'; ?>
