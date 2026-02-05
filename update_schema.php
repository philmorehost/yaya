<?php
require_once 'config/db_connect.php';

try {
    // Add columns to members table
    $pdo->exec("ALTER TABLE members ADD COLUMN member_id VARCHAR(255) UNIQUE AFTER workforce_unit_id;");
    $pdo->exec("ALTER TABLE members ADD COLUMN password VARCHAR(255) AFTER member_id;");
    $pdo->exec("ALTER TABLE members ADD COLUMN role_id INT(11) AFTER password;");

    // Create member_sessions table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `member_sessions` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `member_id` int(11) NOT NULL,
          `token` varchar(255) NOT NULL,
          `expires_at` datetime NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    echo "Database schema updated successfully!";

} catch (PDOException $e) {
    // Create department_members table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `department_members` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `member_id` int(11) NOT NULL,
          `department_id` int(11) NOT NULL,
          `status` varchar(255) NOT NULL DEFAULT 'pending',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // If a column or table already exists, a PDOException will be thrown.
    // We can safely ignore these errors.
    if ($e->getCode() !== '42S21' && $e->getCode() !== '42S01') {
        die("Database update failed: " . $e->getMessage());
    } else {
        echo "Database schema is already up to date.";
    }
}
