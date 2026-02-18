<?php
// This is a one-time script to automatically fix a critical database schema issue.
// It will check for an old, incorrect table structure and replace it with the correct one.

// Check if the fix has already been applied to prevent re-running
$fix_applied_check = $pdo->query("SELECT setting_value FROM settings WHERE setting_name = 'db_fix_1_4_applied'");
if ($fix_applied_check && $fix_applied_check->fetchColumn()) {
    return; // Exit if the fix is already done
}

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

        // 1. Drop the faulty table
        $pdo->exec("DROP TABLE `role_permissions`");

        // 2. Recreate it with the correct schema
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

        // 3. Find the Super Admin role ID
        $role_stmt = $pdo->prepare("SELECT id FROM roles WHERE name = 'Super Admin' LIMIT 1");
        $role_stmt->execute();
        $super_admin_role_id = $role_stmt->fetchColumn();

        if ($super_admin_role_id) {
            // 4. Get all permission IDs
            $permissions_stmt = $pdo->query("SELECT id FROM permissions");
            $permission_ids = $permissions_stmt->fetchAll(PDO::FETCH_COLUMN);

            // 5. Re-assign all permissions to the Super Admin role
            $insert_stmt = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
            foreach ($permission_ids as $permission_id) {
                // Use IGNORE to prevent errors if a permission somehow already exists for the role
                $insert_stmt->execute([$super_admin_role_id, $permission_id]);
            }
        }

        $pdo->commit();

    } catch (Exception $e) {
        $pdo->rollBack();
        // If the fix fails, we should probably stop execution to avoid further errors.
        die("A critical error occurred while trying to automatically fix the database. Please contact support. Error: " . $e->getMessage());
    }
}

// 6. Mark the fix as applied so it doesn't run again.
$mark_fix_stmt = $pdo->prepare("INSERT INTO settings (setting_name, setting_value) VALUES ('db_fix_1_4_applied', '1') ON DUPLICATE KEY UPDATE setting_value = '1'");
$mark_fix_stmt->execute();
