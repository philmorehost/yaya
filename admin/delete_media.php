<?php
require_once '../includes/auth_check.php';
require_once '../config/db_connect.php';
require_once '../includes/csrf_check.php';

$id = $_POST['id'];

if (isset($id)) {
    $stmt = $pdo->prepare("DELETE FROM media WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: media.php');
exit;
