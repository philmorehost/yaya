<?php
function get_setting($name) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_name = ?");
    $stmt->execute([$name]);
    return $stmt->fetchColumn();
}

function update_setting($name, $value) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO settings (setting_name, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    $stmt->execute([$name, $value, $value]);
}
