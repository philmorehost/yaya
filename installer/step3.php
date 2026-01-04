<?php
session_start();
if (!file_exists('../config/db_connect.php')) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YAYA CMS Installer - Step 3</title>
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
        <h2 class="text-center mb-4">Admin Account Setup</h2>
        <p>Create your administrator account.</p>

        <?php
        if (isset($_POST['submit'])) {
            require_once '../config/db_connect.php';

            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            try {
                // Create Super Admin role
                $stmt = $pdo->prepare("INSERT INTO roles (name) VALUES (?)");
                $stmt->execute(['Super Admin']);
                $role_id = $pdo->lastInsertId();

                // Create admin user and assign to Super Admin role
                $stmt = $pdo->prepare("INSERT INTO admin_users (email, password, role_id) VALUES (?, ?, ?)");
                $stmt->execute([$email, $password, $role_id]);
                $user_id = $pdo->lastInsertId();

                // Grant all permissions to Super Admin role
                $permissions = [
                    'manage_members', 'manage_attendance', 'manage_finance',
                    'manage_events', 'manage_media', 'manage_settings',
                    'manage_roles', 'manage_announcements', 'view_dashboard'
                ];
                $stmt = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_name) VALUES (?, ?)");
                foreach ($permissions as $permission) {
                    $stmt->execute([$role_id, $permission]);
                }

                header('Location: step4.php');
                exit();

            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">An error occurred: ' . $e->getMessage() . '</div>';
            }
        }
        ?>

        <form method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Admin Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid">
                <button type="submit" name="submit" class="btn btn-primary">Create Admin User</button>
            </div>
        </form>
    </div>
</body>
</html>
