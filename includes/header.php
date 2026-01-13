
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Watchmen Finance Hub' : 'Watchmen Finance Hub'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    if (in_array($current_page, ['manage_articles.php', 'edit_article.php'])):
    ?>
    <!--
        NOTE: The rich text editor below (TinyMCE) is using a free, limited version.
        For production use, it is recommended to sign up for a free API key at https://www.tiny.cloud/
        to remove developer warnings and access more features.
    -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>tinymce.init({selector:'textarea'});</script>
    <?php endif; ?>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?php echo BASE_URL; ?>">Watchmen Finance Hub</a>
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
                            <a class="nav-link" href="<?php echo BASE_URL; ?>pages/news.php">The Watchmen News</a>
                        </li>
                        <?php if (isset($_SESSION['is_loggedin']) && $_SESSION['is_loggedin'] === true): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>pages/dashboard.php">Dashboard</a>
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
