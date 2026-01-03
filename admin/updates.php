<?php
require_once 'init.php';
check_permission('manage_settings');
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$current_version = get_setting('schema_version') ?: '1.0';
$update_files = glob('../updates/update-to-*.sql');
$available_updates = [];
$error_message = '';
$success_message = '';

foreach ($update_files as $file) {
    preg_match('/update-to-([\d\.]+)\.sql/', $file, $matches);
    if (isset($matches[1])) {
        $version = $matches[1];
        if (version_compare($version, $current_version, '>')) {
            $available_updates[$version] = $file;
        }
    }
}
uksort($available_updates, 'version_compare');

// Handle update request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_updates'])) {
    require_once '../includes/csrf_check.php';

    try {
        $pdo->beginTransaction();

        foreach ($available_updates as $version => $file) {
            $sql = file_get_contents($file);
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    $pdo->exec($statement);
                }
            }
            update_setting('schema_version', $version);
            $current_version = $version;
        }

        $pdo->commit();
        $success_message = "System updated successfully to version " . htmlspecialchars($current_version) . "!";
        $available_updates = []; // Clear updates list
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = "An error occurred during the update: " . $e->getMessage();
    }
}
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">System Updates</h2>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                Database Schema Updater
            </div>
            <div class="card-body">
                <p><strong>Current Schema Version:</strong> <?php echo htmlspecialchars($current_version); ?></p>

                <?php if (!empty($available_updates)): ?>
                    <p>The following updates are available:</p>
                    <ul>
                        <?php foreach ($available_updates as $version => $file): ?>
                            <li>Update to version <?php echo htmlspecialchars($version); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <form method="post" onsubmit="return confirm('Are you sure you want to apply these database updates? It is recommended to back up your database first.');">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button type="submit" name="apply_updates" class="btn btn-primary">Apply Updates</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-success mb-0">
                        Your system is up to date. No new updates are available.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
