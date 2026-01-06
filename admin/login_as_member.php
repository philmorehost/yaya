<?php
require_once 'init.php';
check_permission('manage_members');

if (isset($_GET['id'])) {
    $member_id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([$member_id]);
    $member = $stmt->fetch();

    if ($member) {
        // Store admin's session
        $_SESSION['admin_original_session'] = [
            'admin_loggedin' => $_SESSION['admin_loggedin'],
            'admin_id' => $_SESSION['admin_id'],
            'admin_email' => $_SESSION['admin_email'],
            'admin_role_id' => $_SESSION['admin_role_id']
        ];

        // Log in as member
        $_SESSION['member_loggedin'] = true;
        $_SESSION['member_id'] = $member['id'];
        $_SESSION['member_name'] = $member['name'];

        header('Location: ../member_dashboard.php');
        exit;
    }
}
header('Location: members.php');
exit;
?>