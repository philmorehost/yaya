<?php
require_once 'init.php';
check_permission('delete_members');
require_once '../includes/csrf_check.php';

$id = $_POST['id'];

if (isset($id)) {
    $stmt = $pdo->prepare("DELETE FROM members WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: members.php');
exit;
