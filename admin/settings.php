<?php require_once dirname(__DIR__) . '/config.php'; ?>
<?php require_once dirname(__DIR__) . '/database.php'; ?>
<?php require_once dirname(__DIR__) . '/includes/utils.php'; ?>
<?php
if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

function get_setting($key, $db) {
    try {
        $stmt = $db->prepare("SELECT setting_value FROM AdminSettings WHERE setting_key = :key");
        $stmt->execute([':key' => $key]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        return null;
    }
}

function update_setting($key, $value, $db) {
    try {
        $sql = "INSERT INTO AdminSettings (setting_key, setting_value) VALUES (:key, :value)
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
        $stmt = $db->prepare($sql);
        $stmt->execute([':key' => $key, ':value' => $value]);
    } catch (PDOException $e) {
        $_SESSION['errors'][] = 'There was an error updating the settings. The database may not be up to date.';
        error_log("Error updating setting {$key}: " . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("CSRF token validation failed on settings.php.", 3, dirname(__DIR__) . '/logs/errors.log');
        header('Location: ' . BASE_URL . 'pages/error.php');
        exit;
    }

    $fields = ['support_phone', 'account_details'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_setting($field, $_POST[$field], $db);
        }
    }

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $logo_path = upload_file($_FILES['logo'], ['image/jpeg', 'image/png', 'image/gif'], 5 * 1024 * 1024);
        if ($logo_path) {
            update_setting('logo', $logo_path, $db);
        }
    }

    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
        $hero_image_path = upload_file($_FILES['hero_image'], ['image/jpeg', 'image/png', 'image/gif'], 5 * 1024 * 1024);
        if ($hero_image_path) {
            update_setting('hero_image', $hero_image_path, $db);
        }
    }

    if (empty($_SESSION['errors'])) {
        $_SESSION['success_message'] = 'Settings updated successfully.';
    }
    header('Location: ' . BASE_URL . 'admin/settings.php');
    exit;
}

$settings['support_phone'] = get_setting('support_phone', $db);
$settings['account_details'] = get_setting('account_details', $db);
$settings['logo'] = get_setting('logo', $db);
$settings['hero_image'] = get_setting('hero_image', $db);
?>
<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <h1>Site Settings</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
        <div class="alert alert-danger">
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; unset($_SESSION['errors']); ?>
        </div>
    <?php endif; ?>

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
                    <?php if (!empty($settings['logo'])): ?>
                        <img src="<?php echo BASE_URL . htmlspecialchars($settings['logo']); ?>" alt="Logo" class="img-thumbnail mt-2" style="max-height: 100px;">
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="hero_image" class="form-label">Hero Image</label>
                    <input type="file" class="form-control" id="hero_image" name="hero_image">
                    <?php if (!empty($settings['hero_image'])): ?>
                        <img src="<?php echo BASE_URL . htmlspecialchars($settings['hero_image']); ?>" alt="Hero Image" class="img-thumbnail mt-2" style="max-height: 200px;">
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
