<?php
require_once __DIR__ . '/../config/database.php';
requireAdmin();
$title = 'Users';

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'save') {
        $id = (int)$_POST['id'];
        $fullname = sanitize($_POST['fullname']);
        $email = sanitize($_POST['email']);
        $username = sanitize($_POST['username']);
        $password = $_POST['password'];
        $role = sanitize($_POST['role']);
        $status = sanitize($_POST['status']);

        if ($id) {
            $pdo->prepare("UPDATE users SET fullname=?, email=?, role=?, status=? WHERE id=?")
                ->execute([$fullname, $email, $role, $status, $id]);
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([$hash, $id]);
            }
            logActivity('update_user', "Updated user ID: $id");
            $msg = 'User updated.';
        } else {
            if ($fullname && $email && $username && $password) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                try {
                    $pdo->prepare("INSERT INTO users (fullname, email, username, password, role, status) VALUES (?,?,?,?,?,?)")
                        ->execute([$fullname, $email, $username, $hash, $role, $status]);
                    logActivity('create_user', "Created user: $username");
                    $msg = 'User created successfully.';
                } catch (PDOException $e) {
                    $error = 'Username or email already exists.';
                }
            } else {
                $error = 'All fields are required for new users.';
            }
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        if ($id == 1) {
            $error = 'Cannot delete the primary admin account.';
        } else {
            $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
            $msg = 'User deleted.';
        }
    }
}

require_once 'includes/header.php';
?>
<?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>

<div class="page-header">
    <h2>System Users</h2>
    <div class="actions">
        <button onclick="openUserModal()" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New User</button>
    </div>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th><th>Actions</th></tr></thead>
            <tbody>
                <?php
                $users = $pdo->query("SELECT * FROM users ORDER BY id");
                foreach ($users as $u):
                ?>
                <tr>
                    <td class="fw-600"><?= $u['fullname'] ?></td>
                    <td><?= $u['username'] ?></td>
                    <td class="text-sm text-muted"><?= $u['email'] ?></td>
                    <td><span class="badge <?= $u['role']=='admin'?'badge-danger':'badge-info' ?>"><?= $u['role'] ?></span></td>
                    <td><span class="badge <?= $u['status']=='active'?'badge-success':'badge-warning' ?>"><?= $u['status'] ?></span></td>
                    <td class="text-muted text-sm"><?= $u['last_login'] ? timeAgo($u['last_login']) : 'Never' ?></td>
                    <td>
                        <div class="btn-group">
                            <button onclick="openUserModal(<?= $u['id'] ?>)" class="btn btn-ghost btn-sm"><i class="fas fa-edit"></i></button>
                            <?php if ($u['id'] != 1): ?>
                            <form method="POST" class="inline" onsubmit="event.preventDefault();showConfirm('Delete this user?',function(){this.submit()}.bind(this))">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="userModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 id="userModalTitle">New User</h3>
            <button onclick="closeModal('userModal')" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" id="userId" value="0">
                <div class="form-group">
                    <label class="form-label">Full Name <span class="required">*</span></label>
                    <input type="text" name="fullname" id="userFullname" class="form-input" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Username <span class="required">*</span></label>
                        <input type="text" name="username" id="userUsername" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" name="email" id="userEmail" class="form-input" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Password <span class="form-hint" id="pwHint">(required for new users)</span></label>
                    <input type="password" name="password" id="userPassword" class="form-input">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Role</label>
                        <select name="role" id="userRole" class="form-select">
                            <option value="admin">Admin</option>
                            <option value="editor">Editor</option>
                            <option value="viewer">Viewer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" id="userStatus" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="flex gap-3">
                    <button type="submit" class="btn btn-primary">Save User</button>
                    <button type="button" onclick="closeModal('userModal')" class="btn btn-ghost">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var userData = <?= json_encode($pdo->query("SELECT * FROM users ORDER BY id")->fetchAll()) ?>;

function openUserModal(id) {
    var form = document.querySelector('#userModal form');
    form.reset();
    document.getElementById('userId').value = 0;
    document.getElementById('userFullname').value = '';
    document.getElementById('userUsername').value = '';
    document.getElementById('userUsername').disabled = false;
    document.getElementById('userEmail').value = '';
    document.getElementById('userPassword').value = '';
    document.getElementById('userPassword').required = true;
    document.getElementById('pwHint').textContent = '(required)';
    document.getElementById('userRole').value = 'editor';
    document.getElementById('userStatus').value = 'active';

    if (id) {
        var u = userData.find(function(x) { return x.id == id; });
        if (u) {
            document.getElementById('userModalTitle').textContent = 'Edit User';
            document.getElementById('userId').value = u.id;
            document.getElementById('userFullname').value = u.fullname;
            document.getElementById('userUsername').value = u.username;
            document.getElementById('userUsername').disabled = true;
            document.getElementById('userEmail').value = u.email;
            document.getElementById('userPassword').value = '';
            document.getElementById('userPassword').required = false;
            document.getElementById('pwHint').textContent = '(leave blank to keep current)';
            document.getElementById('userRole').value = u.role;
            document.getElementById('userStatus').value = u.status;
        }
    } else {
        document.getElementById('userModalTitle').textContent = 'New User';
    }
    openModal('userModal');
}
</script>
<?php require_once 'includes/footer.php'; ?>
