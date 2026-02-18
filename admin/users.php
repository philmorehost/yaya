<?php
require_once 'init.php';
check_permission('manage_users');
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];
    // Prevent deleting the super admin or oneself
    if ($user_id !== 1 && $user_id !== $_SESSION['admin_id']) {
        $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
        $stmt->execute([$user_id]);
        $_SESSION['success_message'] = "User deleted successfully.";
    } else {
        $_SESSION['error_message'] = "You cannot delete this user.";
    }
    header("Location: users.php");
    exit();
}


$stmt = $pdo->query("SELECT u.id, u.email, r.name as role_name FROM admin_users u LEFT JOIN roles r ON u.role_id = r.id ORDER BY u.id");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Manage Admin Users</h2>

        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <a href="edit_user.php" class="btn btn-primary mb-3">Add New User</a>

        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($user['role_name'] ?: 'N/A'); ?></span></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-info">Edit</a>
                                <?php if ($user['id'] !== 1 && $user['id'] !== $_SESSION['admin_id']): ?>
                                    <a href="users.php?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
