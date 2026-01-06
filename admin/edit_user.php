<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';

if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    header('Location: ' . BASE_URL . 'admin/manage_users.php');
    exit;
}

// Fetch user data
$stmt = $db->prepare("SELECT id, fullName, email FROM Users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Update user data
    $sql = "UPDATE Users SET fullName = :fullName, email = :email";
    $params = [':fullName' => $fullName, ':email' => $email, ':id' => $user_id];
    if (!empty($password)) {
        $sql .= ", password = :password";
        $params[':password'] = password_hash($password, PASSWORD_DEFAULT);
    }
    $sql .= " WHERE id = :id";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    $_SESSION['success_message'] = 'User updated successfully.';
    header('Location: ' . BASE_URL . 'admin/manage_users.php');
    exit;
}

include 'includes/header.php';
?>

<div class="container mt-5">
    <h1>Edit User</h1>
    <div class="card">
        <div class="card-body">
            <form action="" method="post">
                <div class="mb-3">
                    <label for="fullName" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="fullName" name="fullName" value="<?php echo htmlspecialchars($user['fullName']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <small class="form-text text-muted">Leave blank to keep the current password.</small>
                </div>
                <button type="submit" class="btn btn-primary">Update User</button>
            </form>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
