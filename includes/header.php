
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Finance Hub' : 'Finance Hub'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
</head>
<body>
    <?php
    if (isset($_SESSION['admin_return_session'])) {
        echo '<div class="alert alert-warning text-center sticky-top mb-0 rounded-0">You are currently viewing the site as a user. <a href="' . BASE_URL . 'admin/return_to_admin.php" class="alert-link fw-bold">Return to your Admin Session</a>.</div>';
    }
    ?>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <?php
                $logo = null;
                $logo = null;
                if (!isset($_GET['nodb']) && isset($db) && $db instanceof PDO) {
                    try {
                        $stmt = $db->query("SELECT setting_value FROM AdminSettings WHERE setting_key = 'logo'");
                        $logo = $stmt->fetch();
                    } catch (PDOException $e) {
                        // Suppress the error if the table doesn't exist yet, for example.
                    }
                }
                ?>
                <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
                    <?php if ($logo && !empty($logo['setting_value'])): ?>
                        <img src="<?php echo BASE_URL . htmlspecialchars($logo['setting_value']); ?>" alt="Finance Hub" style="max-height: 40px;">
                    <?php else: ?>
                        Finance Hub
                    <?php endif; ?>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="<?php echo BASE_URL; ?>">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>pages/apply.php">Apply for a Loan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>pages/news.php">News</a>
                        </li>
                        <?php if (isset($_SESSION['is_loggedin']) && $_SESSION['is_loggedin'] === true): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>pages/dashboard.php">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>pages/payment_history.php">Payment History</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>actions/logout_user.php">Logout</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>pages/login.php">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>pages/register.php">Register</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main>
