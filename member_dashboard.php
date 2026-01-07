<?php
require_once 'includes/member_init.php';
require_once 'includes/public_header.php';
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['member_name']); ?>!</h2>
        <p class="lead">This is your member dashboard. From here, you can access announcements, media, giving, and more.</p>
    </div>

    <?php
    if (isset($_SESSION['new_member_id'])) {
        echo '<div class="alert alert-success">Your registration was successful! Your Member ID is: <strong>' . $_SESSION['new_member_id'] . '</strong>. Please save this ID for future use.</div>';
        unset($_SESSION['new_member_id']);
    }
    ?>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-bullhorn fa-3x mb-3"></i>
                    <h5 class="card-title">Announcements</h5>
                    <p class="card-text">View the latest announcements.</p>
                    <a href="member_announcements.php?v=2" class="btn btn-primary">View Announcements</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                    <h5 class="card-title">Events</h5>
                    <p class="card-text">See what's happening.</p>
                    <a href="member_events.php?v=2" class="btn btn-primary">View Events</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-video fa-3x mb-3"></i>
                    <h5 class="card-title">Media</h5>
                    <p class="card-text">Watch the latest sermons and media.</p>
                    <a href="member_media.php?v=2" class="btn btn-primary">View Media</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-hand-holding-heart fa-3x mb-3"></i>
                    <h5 class="card-title">Give</h5>
                    <p class="card-text">Support our mission and ministries.</p>
                    <a href="member_give.php?v=2" class="btn btn-primary">Give Online</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <h5 class="card-title">Departments</h5>
                    <p class="card-text">Find a place to serve.</p>
                    <a href="member_departments.php?v=2" class="btn btn-primary">View Departments</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-user-edit fa-3x mb-3"></i>
                    <h5 class="card-title">Profile</h5>
                    <p class="card-text">Update your personal information.</p>
                    <a href="member_profile.php?v=2" class="btn btn-primary">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="logout.php" class="btn btn-secondary">Logout</a>
        <?php if (isset($_SESSION['admin_original_session'])): ?>
            <a href="admin/switch_back.php" class="btn btn-warning">Switch Back to Admin</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/public_footer.php'; ?>
