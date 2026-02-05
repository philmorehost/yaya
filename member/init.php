<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/helpers.php';

if (!isset($_SESSION['member_loggedin'])) {
    header('Location: ../member_login.php');
    exit;
}

function check_member_permission($permission, $redirect = true) {
    global $pdo;
    $role_id = $_SESSION['member_role_id'];

    if (!$role_id) {
        if ($redirect) {
            header('Location: ../member/dashboard.php'); // Or a dedicated "access denied" page
            exit;
        }
        return false;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM role_permissions rp JOIN permissions p ON rp.permission_id = p.id WHERE rp.role_id = ? AND p.name = ?");
    $stmt->execute([$role_id, $permission]);

    if ($stmt->fetchColumn() > 0) {
        return true;
    } else {
        if ($redirect) {
            header('Location: ../member/dashboard.php'); // Or a dedicated "access denied" page
            exit;
        }
        return false;
    }
}
