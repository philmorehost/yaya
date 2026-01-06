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

// Recent Members
$recent_members = $pdo->query("SELECT * FROM members ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Recent Media
$recent_media = $pdo->query("SELECT * FROM media ORDER BY publication_date DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Recent Announcements
$recent_announcements = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
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
            <div class="col-md-6">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Total Members</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo number_format($total_members); ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Sunday Attendance</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo number_format($latest_attendance); ?></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card dashboard-card mb-3">
                    <div class="card-header">Recent Members</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_members as $member): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($member['name']); ?></td>
                                        <td><?php echo htmlspecialchars($member['email']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card mb-3">
                    <div class="card-header">Recent Media</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_media as $media): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($media['title']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($media['publication_date'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card mb-3">
                    <div class="card-header">Recent Announcements</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_announcements as $announcement): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($announcement['title']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($announcement['created_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>
