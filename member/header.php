<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Area</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Member Area</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">My Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="../media.php">Media</a></li>
                    <li class="nav-item"><a class="nav-link" href="../announcements.php">Announcements</a></li>
                    <li class="nav-item"><a class="nav-link" href="../give.php">Give</a></li>
                    <li class="nav-item"><a class="nav-link" href="departments.php">Departments</a></li>
                    <?php if (isset($_SESSION['admin_relogin'])): ?>
                        <li class="nav-item"><a class="nav-link" href="switch_back.php">Switch Back to Admin</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="../member_logout.php">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
