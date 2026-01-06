<?php
require_once 'init.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Total Members
$total_members = $pdo->query("SELECT COUNT(*) FROM members")->fetchColumn();

// Sunday Attendance (most recent entry)
$latest_attendance = $pdo->query("SELECT (men + women + children) as total FROM attendance_headcount ORDER BY service_date DESC LIMIT 1")->fetchColumn();
if (!$latest_attendance) {
    $latest_attendance = 0;
}

// Month's Offering & Tithe
$giving_count = $pdo->query("SELECT COUNT(*) FROM giving")->fetchColumn();
if ($giving_count == 0) {
    $current_month_offering = 0;
    $current_month_tithe = 0;
} else {
    $offering_query = "SELECT COALESCE(SUM(amount), 0) FROM giving WHERE type = 'Offering' AND MONTH(giving_date) = MONTH(CURRENT_DATE()) AND YEAR(giving_date) = YEAR(CURRENT_DATE())";
    $current_month_offering = $pdo->query($offering_query)->fetchColumn();

    $tithe_query = "SELECT COALESCE(SUM(amount), 0) FROM giving WHERE type = 'Tithe' AND MONTH(giving_date) = MONTH(CURRENT_DATE()) AND YEAR(giving_date) = YEAR(CURRENT_DATE())";
    $current_month_tithe = $pdo->query($tithe_query)->fetchColumn();
}
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Dashboard</h2>

        <?php
        // Standardized version check
        $current_version = get_setting('schema_version') ?: '1.0';
        if (version_compare($current_version, LATEST_SCHEMA_VERSION, '<')):
        ?>
        <div class="alert alert-danger">
            <h4 class="alert-heading">Database Update Required!</h4>
            <p>Your database schema is out of date and needs to be updated for all features to work correctly.</p>
            <hr>
            <a href="updates.php" class="btn btn-danger mb-0">Go to System Updates Page</a>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Total Members</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo number_format($total_members); ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Sunday Attendance</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo number_format($latest_attendance); ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Month's Offering</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($current_month_offering, 2); ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Month's Tithe</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($current_month_tithe, 2); ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>
