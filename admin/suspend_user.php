<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';

if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Get the current status of the user
    $stmt = $db->prepare("SELECT status FROM Users WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $current_status = $stmt->fetchColumn();

    // Determine the new status
    $new_status = $current_status === 'suspended' ? 'active' : 'suspended';

    // Update the user's status
    $stmt = $db->prepare("UPDATE Users SET status = :status WHERE id = :id");
    $stmt->execute([':status' => $new_status, ':id' => $user_id]);

    $_SESSION['success_message'] = 'User status updated successfully.';
}

header('Location: ' . BASE_URL . 'admin/manage_users.php');
exit;
