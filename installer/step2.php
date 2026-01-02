<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YAYA CMS Installer - Step 2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .installer-container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="installer-container">
        <h2 class="text-center mb-4">Database Details</h2>
        <p>Please provide your database connection details. The installer will also create the necessary tables.</p>

        <?php
        if (isset($_POST['submit'])) {
            $db_host = $_POST['db_host'];
            $db_name = $_POST['db_name'];
            $db_user = $_POST['db_user'];
            $db_pass = $_POST['db_pass'];

            try {
                $dbh = new PDO("mysql:host=$db_host", $db_user, $db_pass);
                $dbh->exec("CREATE DATABASE IF NOT EXISTS `$db_name`;");
                $dbh->exec("USE `$db_name`;");

                $sql = file_get_contents('schema.sql');
                $dbh->exec($sql);

                // Create config file
                $config_content = "<?php
                define('DB_HOST', '$db_host');
                define('DB_NAME', '$db_name');
                define('DB_USER', '$db_user');
                define('DB_PASS', '$db_pass');

                try {
                    \$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
                    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException \$e) {
                    die('Could not connect to the database ' . DB_NAME . ' :' . \$e->getMessage());
                }
                ?>";
                file_put_contents('../config/db_connect.php', $config_content);

                header('Location: step3.php');
                exit();

            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Database connection failed: ' . $e->getMessage() . '</div>';
            }
        }
        ?>

        <form method="post">
            <div class="mb-3">
                <label for="db_host" class="form-label">Database Host</label>
                <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
            </div>
            <div class="mb-3">
                <label for="db_name" class="form-label">Database Name</label>
                <input type="text" class="form-control" id="db_name" name="db_name" required>
            </div>
            <div class="mb-3">
                <label for="db_user" class="form-label">Database User</label>
                <input type="text" class="form-control" id="db_user" name="db_user" required>
            </div>
            <div class="mb-3">
                <label for="db_pass" class="form-label">Database Password</label>
                <input type="password" class="form-control" id="db_pass" name="db_pass">
            </div>
            <div class="d-grid">
                <button type="submit" name="submit" class="btn btn-primary">Install Schema & Proceed</button>
            </div>
        </form>
    </div>
</body>
</html>
