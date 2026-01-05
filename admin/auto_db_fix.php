<?php
// Core initialization file for the admin panel

// This script now handles schema validation and fixes before anything else.

// 1. Load the database connection first, as it's needed for schema checks.
// Using a separate require to avoid chicken-and-egg problems with init.php
require_once __DIR__ . '/../config/db_connect.php';

// --- ROBUST SCHEMA CHECKS ---
// These checks run every time to ensure the database is in the correct state.

try {
    // Check for role_id column in members table
    $check_role_id_stmt = $pdo->query("SHOW COLUMNS FROM `members` LIKE 'role_id'");
    if ($check_role_id_stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE members ADD COLUMN role_id INT(11) DEFAULT NULL;");
        $pdo->exec("ALTER TABLE members ADD CONSTRAINT fk_member_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL;");
    }

    // Check for membership_id column in members table
    $check_membership_id_stmt = $pdo->query("SHOW COLUMNS FROM `members` LIKE 'membership_id'");
    if ($check_membership_id_stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE members ADD COLUMN membership_id VARCHAR(255) DEFAULT NULL;");
        $pdo->exec("ALTER TABLE members ADD UNIQUE (membership_id);");
    }
} catch (PDOException $e) {
    die("A critical error occurred while verifying the database schema. Please check database permissions. Error: " . $e->getMessage());
}

// --- LEGACY SCHEMA FIX (run once) ---
// This handles the old, incorrect role_permissions table structure.

// Check if the fix has already been applied to prevent re-running
$fix_applied_check = $pdo->query("SELECT setting_value FROM settings WHERE setting_name = 'db_fix_1_4_applied'");
if (!$fix_applied_check || !$fix_applied_check->fetchColumn()) {

    // Check if the old, incorrect column 'permission_name' exists
    $check_column_stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'role_permissions'
        AND COLUMN_NAME = 'permission_name'
    ");
    $check_column_stmt->execute();
    $incorrect_schema_exists = $check_column_stmt->fetchColumn() > 0;

    if ($incorrect_schema_exists) {
        try {
            $pdo->beginTransaction();

            $pdo->exec("DROP TABLE `role_permissions`");

            $pdo->exec("
                CREATE TABLE `role_permissions` (
                  `role_id` int(11) NOT NULL,
                  `permission_id` int(11) NOT NULL,
                  PRIMARY KEY (`role_id`, `permission_id`),
                  KEY `permission_id` (`permission_id`),
                  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
                  CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            $role_stmt = $pdo->prepare("SELECT id FROM roles WHERE name = 'Super Admin' LIMIT 1");
            $role_stmt->execute();
            $super_admin_role_id = $role_stmt->fetchColumn();

            if ($super_admin_role_id) {
                $permissions_stmt = $pdo->query("SELECT id FROM permissions");
                $permission_ids = $permissions_stmt->fetchAll(PDO::FETCH_COLUMN);

                $insert_stmt = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
                foreach ($permission_ids as $permission_id) {
                    $insert_stmt->execute([$super_admin_role_id, $permission_id]);
                }
            }

            $pdo->commit();

        } catch (Exception $e) {
            $pdo->rollBack();
            die("A critical error occurred while trying to automatically fix the legacy database schema. Please contact support. Error: " . $e->getMessage());
        }
    }

    // Mark the legacy fix as applied so it doesn't run again.
    $mark_fix_stmt = $pdo->prepare("INSERT INTO settings (setting_name, setting_value) VALUES ('db_fix_1_4_applied', '1') ON DUPLICATE KEY UPDATE setting_value = '1'");
    $mark_fix_stmt->execute();
}
