<?php
require_once 'init.php';
check_permission('manage_settings');
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';

    // An array of settings to update
    $smtp_settings = [
        'smtp_host',
        'smtp_port',
        'smtp_user',
        'smtp_pass',
        'smtp_encryption',
        'smtp_sender_email'
    ];

    foreach ($smtp_settings as $setting_name) {
        if (isset($_POST[$setting_name])) {
            update_setting($setting_name, $_POST[$setting_name]);
        }
    }

    $_SESSION['success_message'] = "SMTP settings saved successfully!";
    header("Location: smtp_settings.php");
    exit();
}
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">SMTP Settings</h2>

        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . $_SESSION['success_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            unset($_SESSION['success_message']);
        }
        ?>

        <div class="card">
            <div class="card-header">
                Configure SMTP for Sending Emails
            </div>
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
                        <label for="smtp_encryption" class="form-label">Encryption</label>
                        <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                            <option value="none" <?php if(get_setting('smtp_encryption') == 'none') echo 'selected'; ?>>None</option>
                            <option value="ssl" <?php if(get_setting('smtp_encryption') == 'ssl') echo 'selected'; ?>>SSL</option>
                            <option value="tls" <?php if(get_setting('smtp_encryption') == 'tls') echo 'selected'; ?>>TLS</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="smtp_sender_email" class="form-label">Sender Email</label>
                        <input type="email" class="form-control" id="smtp_sender_email" name="smtp_sender_email" value="<?php echo htmlspecialchars(get_setting('smtp_sender_email')); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
