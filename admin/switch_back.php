<?php
session_start();

if (isset($_SESSION['admin_original_session'])) {
    // Restore admin session
    $_SESSION['admin_loggedin'] = $_SESSION['admin_original_session']['admin_loggedin'];
    $_SESSION['admin_id'] = $_SESSION['admin_original_session']['admin_id'];
    $_SESSION['admin_email'] = $_SESSION['admin_original_session']['admin_email'];
    $_SESSION['admin_role_id'] = $_SESSION['admin_original_session']['admin_role_id'];

    // Unset member session
    unset($_SESSION['member_loggedin']);
    unset($_SESSION['member_id']);
    unset($_SESSION['member_name']);

    // Clean up
    unset($_SESSION['admin_original_session']);

    header('Location: dashboard.php');
    exit;
}

header('Location: ../index.php');
exit;
?>