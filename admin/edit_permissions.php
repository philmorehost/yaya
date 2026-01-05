<?php
require_once 'init.php';
check_permission('manage_roles');
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$role_id = isset($_GET['role_id']) ? (int)$_GET['role_id'] : 0;
if (!$role_id) {
    header("Location: roles.php");
    exit();
}

// Fetch role details
$stmt = $pdo->prepare("SELECT * FROM roles WHERE id = ?");
$stmt->execute([$role_id]);
$role = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$role) {
    header("Location: roles.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';

    // Get all selected permissions
    $permission_ids = isset($_POST['permissions']) ? $_POST['permissions'] : [];

    // Clear existing permissions for this role
    $stmt = $pdo->prepare("DELETE FROM role_permissions WHERE role_id = ?");
    $stmt->execute([$role_id]);

    // Insert new permissions
    if (!empty($permission_ids)) {
        $stmt = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
        foreach ($permission_ids as $permission_id) {
            $stmt->execute([$role_id, $permission_id]);
        }
    }

    $_SESSION['success_message'] = "Permissions updated successfully for role: " . htmlspecialchars($role['name']);
    header("Location: roles.php");
    exit();
}

// Fetch all permissions
$all_permissions = $pdo->query("SELECT * FROM permissions ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Fetch current permissions for the role
$stmt = $pdo->prepare("SELECT permission_id FROM role_permissions WHERE role_id = ?");
$stmt->execute([$role_id]);
$current_permissions = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Edit Permissions for <?php echo htmlspecialchars($role['name']); ?></h2>

        <div class="card">
            <div class="card-header">
                Assign Permissions
            </div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="row">
                        <?php foreach ($all_permissions as $permission): ?>
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="<?php echo $permission['id']; ?>" id="perm_<?php echo $permission['id']; ?>"
                                        <?php if (in_array($permission['id'], $current_permissions)) echo 'checked'; ?>>
                                    <label class="form-check-label" for="perm_<?php echo $permission['id']; ?>">
                                        <strong><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $permission['name']))); ?></strong>
                                    </label>
                                    <small class="d-block text-muted"><?php echo htmlspecialchars($permission['description']); ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">Save Permissions</button>
                    <a href="roles.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
