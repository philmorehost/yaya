<?php
require_once __DIR__ . '/../config/db_connect.php';

function get_setting($setting_name) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_name = ?");
    $stmt->execute([$setting_name]);
    return $stmt->fetchColumn();
}

function update_setting($setting_name, $setting_value) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO settings (setting_name, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    return $stmt->execute([$setting_name, $setting_value]);
}

function check_permission($permission) {
    global $pdo;

    // Defensive check for the role_id in the session
    $role_id = $_SESSION['role_id'] ?? $_SESSION['admin_role_id'] ?? null;
    if (!$role_id) {
        return false;
    }

    // Super Admin (role_id = 1) has all permissions
    if ($role_id == 1) {
        return true;
    }

    $stmt = $pdo->prepare("
        SELECT rp.role_id
        FROM role_permissions rp
        JOIN permissions p ON rp.permission_id = p.id
        WHERE rp.role_id = ? AND p.name = ?
    ");
    $stmt->execute([$role_id, $permission]);
    return $stmt->fetchColumn() !== false;
}

function sanitize_html($dirty_html) {
    // A basic sanitizer, allowing only a safe subset of HTML tags.
    // For a production environment, a more robust library like HTML Purifier is recommended.
    $allowed_tags = '<p><a><h1><h2><h3><h4><h5><h6><strong><em><u><ul><ol><li><br><img><iframe><figure><figcaption><oembed>';
    return strip_tags($dirty_html, $allowed_tags);
}
?>
