<?php
session_start();
require_once 'config/db_connect.php';

if (isset($_SESSION['member_loggedin'])) {
    header('Location: member_dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $member_id = $_POST['member_id'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM members WHERE member_id = ?");
    $stmt->execute([$member_id]);
    $member = $stmt->fetch();

    if ($member && password_verify($password, $member['password'])) {
        $_SESSION['member_loggedin'] = true;
        $_SESSION['member_id'] = $member['id'];
        $_SESSION['member_name'] = $member['name'];
        header('Location: member_dashboard.php');
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
    <title>Member Login</title>
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
            <h2>Member Login</h2>
        </div>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="member_id" class="form-label">Member ID</label>
                <input type="text" class="form-control" id="member_id" name="member_id" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
            <div class="text-center mt-3">
                <a href="forgot_password.php" class="text-light">Forgot Password?</a>
            </div>
        </form>
    </div>
</body>
</html>
