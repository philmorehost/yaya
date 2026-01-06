<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/public_header.php';

$error_message = '';
$success_message = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    $stmt->execute([$token]);
    $reset_request = $stmt->fetch();

    if ($reset_request) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            if ($password !== $password_confirm) {
                $error_message = '<div class="alert alert-danger mt-4">Passwords do not match.</div>';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE members SET password = ? WHERE email = ?");
                $stmt->execute([$hashed_password, $reset_request['email']]);

                $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
                $stmt->execute([$token]);

                $success_message = '<div class="alert alert-success mt-4">Your password has been reset successfully! You can now log in with your new password.</div>';
            }
        }
    } else {
        $error_message = '<div class="alert alert-danger mt-4">Invalid or expired token.</div>';
    }
} else {
    header('Location: member_login.php');
    exit;
}
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h2>Reset Password</h2>
        <p class="lead">Enter a new password for your account.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form method="post">
                        <?php echo $error_message; ?>
                        <?php echo $success_message; ?>
                        <?php if (!$success_message): ?>
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary" style="background-color: #007bff; border-color: #007bff;">Reset Password</button>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/public_footer.php'; ?>
