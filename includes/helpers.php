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
    if (!isset($_SESSION['admin_id'])) {
        return false;
    }

    $stmt = $pdo->prepare("
        SELECT rp.role_id
        FROM admin_users au
        JOIN role_permissions rp ON au.role_id = rp.role_id
        JOIN permissions p ON rp.permission_id = p.id
        WHERE au.id = ? AND p.name = ?
    ");
    $stmt->execute([$_SESSION['admin_id'], $permission]);
    return $stmt->fetchColumn() !== false;
}

function sanitize_html($dirty_html) {
    // A basic sanitizer, allowing only a safe subset of HTML tags.
    // For a production environment, a more robust library like HTML Purifier is recommended.
    $allowed_tags = '<p><a><h1><h2><h3><h4><h5><h6><strong><em><u><ul><ol><li><br><img><iframe><figure><figcaption><oembed>';
    return strip_tags($dirty_html, $allowed_tags);
}
?>
