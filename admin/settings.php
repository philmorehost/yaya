<?php require_once dirname(__DIR__) . '/config.php'; ?>
<?php require_once dirname(__DIR__) . '/database.php'; ?>
<?php require_once dirname(__DIR__) . '/includes/utils.php'; ?>
<?php
if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

$settings = [];
try {
    $stmt = $db->query("SELECT * FROM AdminSettings");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    // Log the error or handle it gracefully
    // For now, we'll just suppress the error
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("CSRF token validation failed on settings.php.", 3, dirname(__DIR__) . '/logs/errors.log');
        header('Location: ' . BASE_URL . 'pages/error.php');
        exit;
    }

    try {
        $fields = ['support_phone', 'account_details'];

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $stmt = $db->prepare("INSERT INTO AdminSettings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value");
                $stmt->execute([':key' => $field, ':value' => $_POST[$field]]);
            }
        }

        if (isset($_FILES['logo'])) {
            $logo_path = upload_file($_FILES['logo'], ['image/jpeg', 'image/png', 'image/gif'], 5 * 1024 * 1024);
            if ($logo_path) {
                $stmt = $db->prepare("INSERT INTO AdminSettings (setting_key, setting_value) VALUES ('logo', :value) ON DUPLICATE KEY UPDATE setting_value = :value");
                $stmt->execute([':value' => $logo_path]);
            }
        }

        if (isset($_FILES['hero_image'])) {
            $hero_image_path = upload_file($_FILES['hero_image'], ['image/jpeg', 'image/png', 'image/gif'], 5 * 1024 * 1024);
            if ($hero_image_path) {
                $stmt = $db->prepare("INSERT INTO AdminSettings (setting_key, setting_value) VALUES ('hero_image', :value) ON DUPLICATE KEY UPDATE setting_value = :value");
                $stmt->execute([':value' => $hero_image_path]);
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'There was an error updating the settings. The database may not be up to date.';
        header('Location: ' . BASE_URL . 'admin/settings.php');
        exit;
    }

    $_SESSION['success_message'] = 'Settings updated successfully.';
    header('Location: ' . BASE_URL . 'admin/settings.php');
    exit;
}
?>
<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <h1>Site Settings</h1>
    <div class="card">
        <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="mb-3">
                    <label for="support_phone" class="form-label">Support Phone Number</label>
                    <input type="text" class="form-control" id="support_phone" name="support_phone" value="<?php echo htmlspecialchars($settings['support_phone'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label for="account_details" class="form-label">Loan Repayment Account Details</label>
                    <textarea class="form-control" id="account_details" name="account_details" rows="3"><?php echo htmlspecialchars($settings['account_details'] ?? ''); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="logo" class="form-label">Logo</label>
                    <input type="file" class="form-control" id="logo" name="logo">
                    <?php if (isset($settings['logo'])): ?>
                        <img src="<?php echo BASE_URL . htmlspecialchars($settings['logo']); ?>" alt="Logo" class="img-thumbnail mt-2" style="max-height: 100px;">
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="hero_image" class="form-label">Hero Image</label>
                    <input type="file" class="form-control" id="hero_image" name="hero_image">
                    <?php if (isset($settings['hero_image'])): ?>
                        <img src="<?php echo BASE_URL . htmlspecialchars($settings['hero_image']); ?>" alt="Hero Image" class="img-thumbnail mt-2" style="max-height: 200px;">
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
