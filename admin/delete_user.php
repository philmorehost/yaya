<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';

if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Delete the user
    $stmt = $db->prepare("DELETE FROM Users WHERE id = :id");
    $stmt->execute([':id' => $user_id]);

    $_SESSION['success_message'] = 'User deleted successfully.';
}

header('Location: ' . BASE_URL . 'admin/manage_users.php');
exit;
