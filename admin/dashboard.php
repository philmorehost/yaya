<?php
require_once '../includes/auth_check.php';
require_once '../config/db_connect.php';
require_once '../config/app.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Total Members
$total_members = $pdo->query("SELECT COUNT(*) FROM members")->fetchColumn();

// Sunday Attendance (most recent entry)
$latest_attendance = $pdo->query("SELECT (men + women + children) as total FROM attendance_headcount ORDER BY service_date DESC LIMIT 1")->fetchColumn();
if (!$latest_attendance) {
    $latest_attendance = 0;
}

// Month's Offering
$start_date = date('Y-m-01');
$end_date = date('Y-m-01', strtotime('+1 month'));

$stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM giving WHERE type = 'Offering' AND giving_date >= ? AND giving_date < ?");
$stmt->execute([$start_date, $end_date]);
$current_month_offering = $stmt->fetchColumn();
if (!is_numeric($current_month_offering)) {
    $current_month_offering = 0;
}

// Month's Tithe
$stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM giving WHERE type = 'Tithe' AND giving_date >= ? AND giving_date < ?");
$stmt->execute([$start_date, $end_date]);
$current_month_tithe = $stmt->fetchColumn();
if (!is_numeric($current_month_tithe)) {
    $current_month_tithe = 0;
}
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Dashboard</h2>
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
