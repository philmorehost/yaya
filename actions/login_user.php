<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $errors = [];
    if (empty($email) || empty($password)) {
        $errors[] = 'Both email and password are required.';
    }

    $stmt = $db->prepare("SELECT * FROM Users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Password is correct, start a new session
        $_SESSION['is_loggedin'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['fullName'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

        if (isset($_SESSION['return_to'])) {
            $return_to = $_SESSION['return_to'];
            unset($_SESSION['return_to']);
            header('Location: ' . $return_to);
        } else {
            header('Location: ' . BASE_URL . 'pages/dashboard.php');
        }
        exit;
    } else {
        $errors[] = 'Invalid email or password.';
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: ' . BASE_URL . 'pages/login.php');
        exit;
    }
} else {
    header('Location: ' . BASE_URL . 'pages/login.php');
    exit;
}
?>
