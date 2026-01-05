<?php
// This is a one-time, standalone script to forcefully update the database schema.

// 1. Load the database connection details directly.
// We are not using init.php to avoid any potential conflicts.
require_once __DIR__ . '/config/db_connect.php';

echo "Attempting to update database schema...\n";

try {
    // 2. Check for role_id column in members table
    $check_role_id_stmt = $pdo->query("SHOW COLUMNS FROM `members` LIKE 'role_id'");
    if ($check_role_id_stmt->rowCount() == 0) {
        echo "Column 'role_id' not found. Adding it now...\n";
        $pdo->exec("ALTER TABLE members ADD COLUMN role_id INT(11) DEFAULT NULL;");
        $pdo->exec("ALTER TABLE members ADD CONSTRAINT fk_member_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL;");
        echo "'role_id' column and foreign key added successfully.\n";
    } else {
        echo "'role_id' column already exists. No action taken.\n";
    }

    // 3. Check for membership_id column in members table
    $check_membership_id_stmt = $pdo->query("SHOW COLUMNS FROM `members` LIKE 'membership_id'");
    if ($check_membership_id_stmt->rowCount() == 0) {
        echo "Column 'membership_id' not found. Adding it now...\n";
        $pdo->exec("ALTER TABLE members ADD COLUMN membership_id VARCHAR(255) DEFAULT NULL;");
        $pdo->exec("ALTER TABLE members ADD UNIQUE (membership_id);");
        echo "'membership_id' column and unique key added successfully.\n";
    } else {
        echo "'membership_id' column already exists. No action taken.\n";
    }

    echo "Schema update process complete.\n";

} catch (PDOException $e) {
    // Provide a clear error message if something goes wrong.
    die("A critical error occurred during the schema update. Please check database permissions. Error: " . $e->getMessage());
}
?>