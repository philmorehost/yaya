<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RCCG YAYA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/public_style.css">
    <script type='text/javascript' src='https://platform-api.sharethis.com/js/sharethis.js#property=66a4d81232b8570019521360&product=sticky-share-buttons' async='async'></script>
</head>
<body>
    <?php require_once 'includes/helpers.php'; ?>

    <!-- ShareThis Sticky Share Buttons container -->
    <div class="sharethis-sticky-share-buttons"></div>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <?php $logo_url = get_setting('site_logo_url'); ?>
                <?php if ($logo_url): ?>
                    <img src="<?php echo htmlspecialchars($logo_url); ?>" alt="Site Logo" height="40">
                <?php else: ?>
                    RCCG YAYA
                <?php endif; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="events.php">Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="media.php">Media</a></li>
                    <li class="nav-item"><a class="nav-link" href="give.php">Give</a></li>
                    <li class="nav-item"><a class="nav-link" href="connect.php">Connect</a></li>
                    <li class="nav-item"><a class="nav-link" href="member_login.php">Member Login</a></li>
                </ul>
            </div>
        </div>
    </nav>
