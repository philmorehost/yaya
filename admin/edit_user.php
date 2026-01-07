<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';
require_once dirname(__DIR__) . '/includes/utils.php';

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
$stmt = $db->prepare("SELECT * FROM Users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

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

                <h5 class="mt-4">Additional Information (Read-Only)</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">BVN</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars(mask_string($user['bvn'] ?? '')); ?>" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NIN</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars(mask_string($user['nin'] ?? '')); ?>" readonly>
                    </div>
                </div>
                 <div class="mb-3">
                    <label class="form-label">Home Address</label>
                    <textarea class="form-control" readonly><?php echo htmlspecialchars($user['home_address'] ?? ''); ?></textarea>
                </div>
                 <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Marital Status</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['marital_status'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Gender</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['gender'] ?? ''); ?>" readonly>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update User</button>
            </form>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
