<?php
require_once 'init.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Dashboard Analytics
$total_members = 0;
$latest_attendance = 0;
$current_month_offering = 0;
$current_month_tithe = 0;

try {
    $total_members = $pdo->query("SELECT COUNT(*) FROM members")->fetchColumn();
    $latest_attendance = $pdo->query("SELECT (men + women + children) as total FROM attendance_headcount ORDER BY service_date DESC LIMIT 1")->fetchColumn() ?: 0;

    $start_date = date('Y-m-01');
    $end_date = date('Y-m-t');

    $offering_stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM giving WHERE type = 'Offering' AND giving_date BETWEEN ? AND ?");
    $offering_stmt->execute([$start_date, $end_date]);
    $current_month_offering = $offering_stmt->fetchColumn();

    $tithe_stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM giving WHERE type = 'Tithe' AND giving_date BETWEEN ? AND ?");
    $tithe_stmt->execute([$start_date, $end_date]);
    $current_month_tithe = $tithe_stmt->fetchColumn();

} catch (PDOException $e) {
    // If a query fails (e.g., table doesn't exist yet), the values will remain 0.
    // This prevents fatal errors during initial setup.
}


// Recent Data
$recent_sermons = $pdo->query("SELECT * FROM media ORDER BY publication_date DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$recent_announcements = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$recent_members = $pdo->query("SELECT * FROM members ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Admin Dashboard</h2>

        <!-- Quick Actions -->
        <div class="mb-4">
            <a href="members.php" class="btn btn-primary">Manage Members</a>
            <a href="announcements.php" class="btn btn-info">Post Announcement</a>
            <a href="media.php" class="btn btn-success">Upload Media</a>
            <a href="finance.php" class="btn btn-warning">Track Finances</a>
        </div>

        <!-- Analytics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Total Members</div>
                    <div class="card-body"><h5 class="card-title"><?php echo number_format($total_members); ?></h5></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Recent Attendance</div>
                    <div class="card-body"><h5 class="card-title"><?php echo number_format($latest_attendance); ?></h5></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">This Month's Offering</div>
                    <div class="card-body"><h5 class="card-title"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($current_month_offering, 2); ?></h5></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">This Month's Tithe</div>
                    <div class="card-body"><h5 class="card-title"><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($current_month_tithe, 2); ?></h5></div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">Recent Sermons</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($recent_sermons as $sermon): ?>
                                <li class="list-group-item"><?php echo htmlspecialchars($sermon['title']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">Recent Announcements</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($recent_announcements as $announcement): ?>
                                <li class="list-group-item"><?php echo htmlspecialchars($announcement['title']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">Newest Members</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($recent_members as $member): ?>
                                <li class="list-group-item"><?php echo htmlspecialchars($member['name']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
