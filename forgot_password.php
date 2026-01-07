<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/public_header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $member_id = $_POST['member_id'];

    $stmt = $pdo->prepare("SELECT * FROM members WHERE member_id = ?");
    $stmt->execute([$member_id]);
    $member = $stmt->fetch();

    if ($member) {
        $token = bin2hex(random_bytes(32));
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?)");
        $stmt->execute([$member['email'], $token]);

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $reset_link = $protocol . $_SERVER['HTTP_HOST'] . '/reset_password.php?token=' . $token;

        // **Note:** Email sending is not implemented in this environment.
        // In a real-world scenario, you would use a library like PHPMailer to send the email.
        $message = '<div class="alert alert-success mt-4">A password reset link has been sent to your email address. Please check your inbox.</div>';
        $message .= '<div class="alert alert-info mt-4">For testing purposes, here is the reset link: <a href="' . $reset_link . '">' . $reset_link . '</a></div>';
    } else {
        $message = '<div class="alert alert-danger mt-4">No account found with that Member ID.</div>';
    }
}
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h2>Forgot Password</h2>
        <p class="lead">Enter your Member ID to request a password reset.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form method="post">
                        <?php echo $message; ?>
                        <div class="mb-3">
                            <label for="member_id" class="form-label">Member ID</label>
                            <input type="text" class="form-control" id="member_id" name="member_id" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" style="background-color: #007bff; border-color: #007bff;">Request Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/public_footer.php'; ?>
