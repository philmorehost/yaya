<?php
require_once 'init.php';
if (!check_permission('manage_roles')) {
    $_SESSION['error_message'] = 'You do not have permission to access this page.';
    header('Location: dashboard.php');
    exit;
}
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';
    if (isset($_POST['add_role'])) {
        $name = $_POST['name'];
        $stmt = $pdo->prepare("INSERT INTO roles (name) VALUES (?)");
        $stmt->execute([$name]);
    } elseif (isset($_POST['edit_role'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $stmt = $pdo->prepare("UPDATE roles SET name = ? WHERE id = ?");
        $stmt->execute([$name, $id]);
    } elseif (isset($_POST['delete_role'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM roles WHERE id = ?");
        $stmt->execute([$id]);
    }
    header('Location: roles.php');
}

// Fetch Data with Permissions
$roles_stmt = $pdo->query("
    SELECT
        r.id,
        r.name,
        GROUP_CONCAT(p.name ORDER BY p.name SEPARATOR ', ') as permissions
    FROM roles r
    LEFT JOIN role_permissions rp ON r.id = rp.role_id
    LEFT JOIN permissions p ON rp.permission_id = p.id
    GROUP BY r.id, r.name
    ORDER BY r.name
");
$roles = $roles_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Roles</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addRoleModal">Add Role</button>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-dark">
                        <thead>
                            <tr>
                                <th>Role Name</th>
                                <th>Permissions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($roles as $role): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($role['name']); ?></td>
                                    <td>
                                        <?php if (!empty($role['permissions'])): ?>
                                            <?php foreach (explode(', ', $role['permissions']) as $permission): ?>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($permission); ?></span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted">No permissions assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="edit_permissions.php?role_id=<?php echo $role['id']; ?>" class="btn btn-sm btn-warning">Permissions</a>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editRoleModal-<?php echo $role['id']; ?>">Edit</button>
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="id" value="<?php echo $role['id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <button type="submit" name="delete_role" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">Add New Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <button type="submit" name="add_role" class="btn btn-primary">Save Role</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Role Modals -->
<?php foreach ($roles as $role): ?>
<div class="modal fade" id="editRoleModal-<?php echo $role['id']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">Edit Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="id" value="<?php echo $role['id']; ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($role['name']); ?>" required>
                    </div>
                    <button type="submit" name="edit_role" class="btn btn-primary">Update Role</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php require_once '../includes/footer.php'; ?>
