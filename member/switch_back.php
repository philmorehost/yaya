<?php
session_start();

if (isset($_SESSION['admin_relogin'])) {
    // Restore admin session
    $_SESSION['admin_loggedin'] = true;
    $_SESSION['admin_id'] = $_SESSION['admin_relogin']['admin_id'];
    $_SESSION['admin_role_id'] = $_SESSION['admin_relogin']['admin_role_id'];

    // Unset member session data
    unset($_SESSION['member_loggedin']);
    unset($_SESSION['member_id']);
    unset($_SESSION['member_name']);
    unset($_SESSION['member_role_id']);
    unset($_SESSION['admin_relogin']);

    header('Location: ../admin/members.php');
    exit;
} else {
    // If there's no admin relogin data, just log the member out
    header('Location: ../member_logout.php');
    exit;
}
