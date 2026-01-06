<?php
session_start();
require_once 'config/db_connect.php';

if (!isset($_SESSION['member_loggedin'])) {
    header('Location: member_login.php');
    exit;
}

// Get member's role and permissions
$member_id = $_SESSION['member_id'];
$stmt = $pdo->prepare("SELECT role_id FROM members WHERE id = ?");
$stmt->execute([$member_id]);
$role_id = $stmt->fetchColumn();

$permissions = [];
if ($role_id) {
    $stmt = $pdo->prepare("SELECT p.name FROM permissions p JOIN role_permissions rp ON p.id = rp.permission_id WHERE rp.role_id = ?");
    $stmt->execute([$role_id]);
    $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Function to check if a member has a specific permission
function member_has_permission($permission_name) {
    global $permissions;
    return in_array($permission_name, $permissions);
}
?>