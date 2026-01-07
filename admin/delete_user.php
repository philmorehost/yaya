<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';

if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Soft delete the user by updating their status
    $stmt = $db->prepare("UPDATE Users SET status = 'deleted' WHERE id = :id");
    $stmt->execute([':id' => $user_id]);

    $_SESSION['success_message'] = 'User account has been marked as deleted.';
}

header('Location: ' . BASE_URL . 'admin/manage_users.php');
exit;
