<?php
require_once 'init.php';
if (!check_permission('manage_settings')) {
    $_SESSION['error_message'] = 'You do not have permission to access this page.';
    header('Location: dashboard.php');
    exit;
}
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';
    update_setting('smtp_host', $_POST['smtp_host']);
    update_setting('smtp_port', $_POST['smtp_port']);
    update_setting('smtp_user', $_POST['smtp_user']);
    update_setting('smtp_pass', $_POST['smtp_pass']);
    update_setting('smtp_from_email', $_POST['smtp_from_email']);
    update_setting('smtp_from_name', $_POST['smtp_from_name']);

    $_SESSION['success_message'] = "SMTP settings updated successfully!";
    header('Location: smtp_settings.php');
    exit;
}
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">SMTP Settings</h2>

        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="smtp_host" class="form-label">SMTP Host</label>
                        <input type="text" class="form-control" id="smtp_host" name="smtp_host" value="<?php echo htmlspecialchars(get_setting('smtp_host')); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="smtp_port" class="form-label">SMTP Port</label>
                        <input type="number" class="form-control" id="smtp_port" name="smtp_port" value="<?php echo htmlspecialchars(get_setting('smtp_port')); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="smtp_user" class="form-label">SMTP Username</label>
                        <input type="text" class="form-control" id="smtp_user" name="smtp_user" value="<?php echo htmlspecialchars(get_setting('smtp_user')); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="smtp_pass" class="form-label">SMTP Password</label>
                        <input type="password" class="form-control" id="smtp_pass" name="smtp_pass" value="<?php echo htmlspecialchars(get_setting('smtp_pass')); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="smtp_from_email" class="form-label">From Email</label>
                        <input type="email" class="form-control" id="smtp_from_email" name="smtp_from_email" value="<?php echo htmlspecialchars(get_setting('smtp_from_email')); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="smtp_from_name" class="form-label">From Name</label>
                        <input type="text" class="form-control" id="smtp_from_name" name="smtp_from_name" value="<?php echo htmlspecialchars(get_setting('smtp_from_name')); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
