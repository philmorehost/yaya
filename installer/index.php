<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to YAYA CMS Installer</title>
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
        <h2 class="text-center mb-4">Welcome to YAYA CMS Installer</h2>
        <p>This wizard will guide you through the installation of the YAYA Church Management System. Please ensure the following requirements are met before proceeding.</p>

        <?php
        $requirements_met = true;
        ?>

        <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                PHP Version >= 8.0
                <?php
                if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
                    echo '<span class="badge bg-success">OK</span>';
                } else {
                    echo '<span class="badge bg-danger">Failed</span>';
                    $requirements_met = false;
                }
                ?>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                PDO Extension
                <?php
                if (extension_loaded('pdo')) {
                    echo '<span class="badge bg-success">OK</span>';
                } else {
                    echo '<span class="badge bg-danger">Failed</span>';
                    $requirements_met = false;
                }
                ?>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                PDO MySQL Driver
                <?php
                if (extension_loaded('pdo_mysql')) {
                    echo '<span class="badge bg-success">OK</span>';
                } else {
                    echo '<span class="badge bg-danger">Failed</span>';
                    $requirements_met = false;
                }
                ?>
            </li>
        </ul>

        <div class="d-grid mt-4">
            <a href="step2.php" class="btn btn-primary <?php if (!$requirements_met) echo 'disabled'; ?>">
                Next
            </a>
        </div>
    </div>
</body>
</html>
