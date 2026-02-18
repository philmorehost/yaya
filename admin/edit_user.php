<?php
require_once 'init.php';
check_permission('manage_users');
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user = ['email' => '', 'role_id' => ''];
$is_editing = $user_id > 0;

if ($is_editing) {
    $stmt = $pdo->prepare("SELECT email, role_id FROM admin_users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        header("Location: users.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';
    $email = $_POST['email'];
    $role_id = $_POST['role_id'];
    $password = $_POST['password'];

    if ($is_editing) {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admin_users SET email = ?, role_id = ?, password = ? WHERE id = ?");
            $stmt->execute([$email, $role_id, $hashed_password, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE admin_users SET email = ?, role_id = ? WHERE id = ?");
            $stmt->execute([$email, $role_id, $user_id]);
        }
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admin_users (email, password, role_id) VALUES (?, ?, ?)");
        $stmt->execute([$email, $hashed_password, $role_id]);
    }

    $_SESSION['success_message'] = "User saved successfully.";
    header("Location: users.php");
    exit();
}

// Fetch all roles for the dropdown
$roles = $pdo->query("SELECT id, name FROM roles ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4"><?php echo $is_editing ? 'Edit User' : 'Add New User'; ?></h2>

        <div class="card">
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" <?php if (!$is_editing) echo 'required'; ?>>
                        <?php if ($is_editing): ?>
                            <small class="form-text text-muted">Leave blank to keep the current password.</small>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role</label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            <option value="">Select Role</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['id']; ?>" <?php if ($user['role_id'] == $role['id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($role['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save User</button>
                    <a href="users.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
