<?php
require_once 'config/db_connect.php';
require_once 'includes/public_header.php';

$error_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if ($password !== $password_confirm) {
        $error_message = '<div class="alert alert-danger mt-4">Passwords do not match.</div>';
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM members WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $error_message = '<div class="alert alert-danger mt-4">An account with this email already exists.</div>';
        } else {
            $member_id = 'MEM' . uniqid();
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO members (member_id, name, email, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$member_id, $name, $email, $hashed_password]);

            $_SESSION['success_message'] = 'Registration successful! You can now log in.';
            header('Location: member_login.php');
            exit;
        }
    }
}
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h2>Member Registration</h2>
        <p class="lead">Create an account to access the member portal.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form method="post">
                        <?php echo $error_message; ?>
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" style="background-color: #007bff; border-color: #007bff;">Register</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/public_footer.php'; ?>
