<?php
$_SESSION['install_stage'] = 2;
// In a real installer, you might pass the admin details via the session
$admin_email = 'admin@example.com';
$admin_password = 'password123';
?>

<div class="text-center">
    <h4 class="text-success">Installation Complete!</h4>
    <p>Congratulations, Watchmen Finance Hub has been installed successfully.</p>
</div>

<div class="alert alert-info mt-4">
    <h5 class="alert-heading">Admin Login Details</h5>
    <p>Please use the following credentials to log into the admin area. It is strongly recommended that you change the default password after your first login.</p>
    <hr>
    <p class="mb-0"><strong>Email:</strong> <?php echo $admin_email; ?></p>
    <p class="mb-0"><strong>Password:</strong> <?php echo $admin_password; ?></p>
</div>

<div class="alert alert-danger mt-4">
    <h5 class="alert-heading">IMPORTANT: Security Warning</h5>
    <p>For the security of your application, please delete the entire <strong>/install/</strong> directory from your server immediately.</p>
</div>

<div class="text-center mt-4">
    <a href="../index.php" class="btn btn-primary">Go to Homepage</a>
    <a href="../admin/login.php" class="btn btn-secondary">Go to Admin Login</a>
</div>
