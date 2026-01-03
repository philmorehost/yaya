<?php
require_once 'init.php';
check_permission('manage_settings');
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Simplified versioning: check for the existence of the author_id column
$update_needed = false;
try {
    $pdo->query("SELECT author_id FROM announcements LIMIT 1");
} catch (PDOException $e) {
    // If the query fails, the column likely doesn't exist.
    $update_needed = true;
}

$error_message = '';
$success_message = '';

// Handle update request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_update'])) {
    require_once '../includes/csrf_check.php';

    $update_file = '../updates/update.sql';
    if (is_readable($update_file)) {
        try {
            $sql = file_get_contents($update_file);
            $statements = array_filter(array_map('trim', explode(';', $sql)));

            $pdo->beginTransaction();
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    $pdo->exec($statement);
                }
            }
            $pdo->commit();

            // Mark update as complete by adding a setting
            update_setting('schema_version', '1.3'); // You can still use versions
            $success_message = "System updated successfully! The database is now up to date.";
            $update_needed = false; // Refresh the state
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = "An error occurred during the update: " . $e->getMessage();
        }
    } else {
        $error_message = "Update file not found or is not readable.";
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
                <?php if ($update_needed): ?>
                    <div class="alert alert-warning">
                        <h4 class="alert-heading">Database Update Required</h4>
                        <p>Your database schema is out of date. Applying the update will add new tables and columns required for the latest features to function correctly.</p>
                        <hr>
                        <p class="mb-0">It is strongly recommended to back up your database before proceeding.</p>
                    </div>
                    <form method="post" onsubmit="return confirm('Are you sure you want to apply the database update?');">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button type="submit" name="apply_update" class="btn btn-primary">Apply Database Update</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-success mb-0">
                        Your system is up to date. The database schema is correct.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
