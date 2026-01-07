<?php require_once '../includes/helpers.php'; ?>
<div id="sidebar-wrapper">
    <div class="sidebar-heading text-center">
        <?php
        $logo_url = get_setting('site_logo_url');
        if ($logo_url) {
            echo '<img src="../' . htmlspecialchars($logo_url) . '" alt="Site Logo" class="img-fluid" style="max-height: 50px;">';
        } else {
            echo 'YAYA CMS';
        }
        ?>
    </div>
    <div class="list-group list-group-flush">
        <a href="dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
        <?php if (check_permission('manage_announcements')): ?>
            <a href="announcements.php" class="list-group-item list-group-item-action">Announcements</a>
        <?php endif; ?>
        <?php if (check_permission('manage_members')): ?>
            <a href="members.php" class="list-group-item list-group-item-action">Members</a>
        <?php endif; ?>
        <?php if (check_permission('manage_attendance')): ?>
            <a href="attendance.php" class="list-group-item list-group-item-action">Attendance</a>
        <?php endif; ?>
        <?php if (check_permission('manage_finance')): ?>
            <a href="finance.php" class="list-group-item list-group-item-action">Finance</a>
            <a href="giving_accounts.php" class="list-group-item list-group-item-action">Giving Accounts</a>
        <?php endif; ?>
        <?php if (check_permission('manage_events')): ?>
            <a href="events.php" class="list-group-item list-group-item-action">Events</a>
        <?php endif; ?>
        <?php if (check_permission('manage_media')): ?>
            <a href="media.php" class="list-group-item list-group-item-action">Media</a>
        <?php endif; ?>
        <?php if (check_permission('manage_departments')): ?>
            <a href="departments.php" class="list-group-item list-group-item-action">Departments</a>
            <a href="department_applications.php" class="list-group-item list-group-item-action">Department Applications</a>
        <?php endif; ?>
        <?php if (check_permission('manage_roles')): ?>
            <a href="roles.php" class="list-group-item list-group-item-action">Roles</a>
        <?php endif; ?>

        <?php if (check_permission('manage_settings')): ?>
            <div class="list-group-item list-group-item-action list-group-item-heading">Settings</div>
            <a href="homepage_settings.php" class="list-group-item list-group-item-action">Homepage Settings</a>
            <a href="smtp_settings.php" class="list-group-item list-group-item-action">SMTP Settings</a>
        <?php endif; ?>
        <?php if (check_permission('manage_updates')): ?>
            <a href="updates.php" class="list-group-item list-group-item-action">System Updates</a>
        <?php endif; ?>

        <a href="../logout.php" class="list-group-item list-group-item-action">Logout</a>
    </div>
</div>
<div id="page-content-wrapper">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom">
        <div class="container-fluid">
            <button class="btn btn-primary" id="menu-toggle"><i class="fa fa-bars"></i></button>
        </div>
    </nav>
