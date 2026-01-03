<?php
session_start();
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $errors = [];
    if (empty($fullName) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = 'All fields are required.';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    // Check if user already exists
    $stmt = $db->prepare("SELECT * FROM Users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        $errors[] = 'A user with this email already exists.';
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: ' . BASE_URL . 'pages/register.php');
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $db->prepare("INSERT INTO Users (fullName, email, password) VALUES (:fullName, :email, :password)");
        $stmt->execute([':fullName' => $fullName, ':email' => $email, ':password' => $hashed_password]);

        // Log the user in after successful registration
        $_SESSION['user_loggedin'] = true;
        $_SESSION['user_id'] = $db->lastInsertId();
        $_SESSION['user_name'] = $fullName;
        $_SESSION['user_email'] = $email;

        header('Location: ' . BASE_URL . 'pages/dashboard.php');
        exit;
    } catch (PDOException $e) {
        error_log('User registration failed: ' . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
        header('Location: ' . BASE_URL . 'pages/error.php');
        exit;
    }
} else {
    header('Location: ' . BASE_URL . 'pages/register.php');
    exit;
}
?>
