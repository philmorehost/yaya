<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/helpers.php';

if (isset($_SESSION['member_loggedin'])) {
    header('Location: member/dashboard.php');
    exit;
}

// Check for "remember me" cookie
if (isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];
    $stmt = $pdo->prepare("SELECT * FROM member_sessions WHERE token = ? AND expires_at > NOW()");
    $stmt->execute([$token]);
    $session = $stmt->fetch();

    if ($session) {
        $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
        $stmt->execute([$session['member_id']]);
        $member = $stmt->fetch();

        if ($member) {
            $_SESSION['member_loggedin'] = true;
            $_SESSION['member_id'] = $member['id'];
            $_SESSION['member_name'] = $member['name'];
            $_SESSION['member_role_id'] = $member['role_id'];
            header('Location: member/dashboard.php');
            exit;
        }
    }
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
        $_SESSION['member_role_id'] = $member['role_id'];

        if (isset($_POST['remember_me'])) {
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));
            $stmt = $pdo->prepare("INSERT INTO member_sessions (member_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$member['id'], $token, $expires_at]);
            setcookie('remember_me', $token, time() + (86400 * 30), "/");
        }

        header('Location: member/dashboard.php');
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
            <?php
            $logo_url = get_setting('site_logo_url');
            if ($logo_url) {
                echo '<img src="' . htmlspecialchars($logo_url) . '" alt="Site Logo" class="img-fluid" style="max-height: 70px;">';
            } else {
                echo '<h2>Member Login</h2>';
            }
            ?>
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
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                <label class="form-check-label" for="remember_me">Remember me</label>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
    </div>
</body>
</html>
