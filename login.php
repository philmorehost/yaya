<?php
session_start();
require_once 'config/db_connect.php';

if (isset($_SESSION['admin_loggedin'])) {
    header('Location: admin/dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_loggedin'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_role_id'] = $admin['role_id'];
        header('Location: admin/dashboard.php');
        exit;
    } else {
        $error = 'Invalid credentials!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #2c3e50; /* Subtle dark background */
            color: #ecf0f1;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            background: #34495e;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
        }
        .form-control {
            background-color: #2c3e50;
            border: 1px solid #7f8c8d;
            color: #ecf0f1;
        }
        .form-control:focus {
            background-color: #2c3e50;
            border-color: #3498db;
            color: #ecf0f1;
        }
        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="text-center mb-4">
            <?php
            require_once 'includes/helpers.php';
            $logo_url = get_setting('site_logo_url');
            if ($logo_url) {
                echo '<img src="' . htmlspecialchars($logo_url) . '" alt="Site Logo" class="img-fluid" style="max-height: 70px;">';
            } else {
                echo '<h2>YAYA Admin Login</h2>';
            }
            ?>
        </div>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
    </div>
</body>
</html>
