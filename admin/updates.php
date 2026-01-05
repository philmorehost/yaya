<?php
require_once 'init.php';
check_permission('manage_settings');
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$current_version = get_setting('schema_version') ?: '1.0';
$update_needed = version_compare($current_version, LATEST_SCHEMA_VERSION, '<');
$error_message = '';
$success_message = '';

// Handle update request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_update'])) {
    require_once '../includes/csrf_check.php';

    $update_file = '../updates/update.sql';
    if ($update_needed && is_readable($update_file)) {
        try {
            $sql = file_get_contents($update_file);
            $pdo->exec($sql); // Execute the entire script as a single transaction

            // After execution, the script itself will have updated the version.
            // We refresh the page to reflect the new state.
            header("Location: updates.php?updated=true");
            exit();
        } catch (Exception $e) {
            $error_message = "An error occurred during the update: " . $e->getMessage();
        }
    } else {
        $error_message = "No update is required or the update file is missing.";
    }
}

// Check for success message
if (isset($_GET['updated'])) {
    $success_message = "System updated successfully! Your database is now up to date.";
}

// Re-check version after potential update
$current_version = get_setting('schema_version') ?: '1.0';
$update_needed = version_compare($current_version, LATEST_SCHEMA_VERSION, '<');
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
                <p><strong>Latest Schema Version:</strong> <?php echo LATEST_SCHEMA_VERSION; ?></p>
                <hr>
                <?php if ($update_needed): ?>
                    <div class="alert alert-warning">
                        <h4 class="alert-heading">Database Update Required</h4>
                        <p>Your database schema is out of date. Applying the update will add new tables and columns required for the latest features to function correctly.</p>
                        <p class="mb-0">It is strongly recommended to back up your database before proceeding.</p>
                    </div>
                    <form method="post" onsubmit="return confirm('Are you sure you want to apply the database update?');">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button type="submit" name="apply_update" class="btn btn-primary">Apply Database Update</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-success mb-0">
                        Your system is up to date.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
