<?php
function get_setting($name) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_name = ?");
    $stmt->execute([$name]);
    return $stmt->fetchColumn();
}

function check_permission($permission_name) {
    global $pdo;
    if (!isset($_SESSION['admin_role_id'])) {
        // Default to no permissions if role is not set
        die('You do not have permission to access this page.');
    }

    // Super Admin role has all permissions implicitly
    if (isset($_SESSION['admin_role_name']) && $_SESSION['admin_role_name'] === 'Super Admin') {
        return true;
    }

    $role_id = $_SESSION['admin_role_id'];

    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM role_permissions rp
        JOIN permissions p ON rp.permission_id = p.id
        WHERE rp.role_id = ? AND p.name = ?
    ");
    $stmt->execute([$role_id, $permission_name]);

    if ($stmt->fetchColumn() > 0) {
        return true;
    } else {
        // Optional: Redirect or show an error message
        die('You do not have permission to access this page.');
    }
}

function update_setting($name, $value) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO settings (setting_name, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    $stmt->execute([$name, $value, $value]);
}
