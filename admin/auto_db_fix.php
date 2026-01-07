<?php
// This is a one-time script to automatically fix a critical database schema issue.
// It will check for an old, incorrect table structure and replace it with the correct one.

// Check if the fix has already been applied to prevent re-running
$fix_applied_check = $pdo->query("SELECT setting_value FROM settings WHERE setting_name = 'db_fix_1_6_applied'");
if ($fix_applied_check && $fix_applied_check->fetchColumn()) {
    //return; // Exit if the fix is already done
}

// Check for and add member_id column
$check_member_id = $pdo->query("SHOW COLUMNS FROM `members` LIKE 'member_id'");
if ($check_member_id->rowCount() == 0) {
    $pdo->exec("ALTER TABLE `members` ADD COLUMN `member_id` VARCHAR(255) UNIQUE AFTER `id`");
}

// Check for and add password column
$check_password = $pdo->query("SHOW COLUMNS FROM `members` LIKE 'password'");
if ($check_password->rowCount() == 0) {
    $pdo->exec("ALTER TABLE `members` ADD COLUMN `password` VARCHAR(255) AFTER `email`");
}

// Check for and add role_id column
$check_role_id = $pdo->query("SHOW COLUMNS FROM `members` LIKE 'role_id'");
if ($check_role_id->rowCount() == 0) {
    $pdo->exec("ALTER TABLE `members` ADD COLUMN `role_id` INT(11) NULL AFTER `gender`");
}

// Check for and add phone column
$check_phone = $pdo->query("SHOW COLUMNS FROM `members` LIKE 'phone'");
if ($check_phone->rowCount() == 0) {
    $pdo->exec("ALTER TABLE `members` ADD COLUMN `phone` VARCHAR(255) NULL AFTER `email`");
}

// Check for and add birthday column
$check_birthday = $pdo->query("SHOW COLUMNS FROM `members` LIKE 'birthday'");
if ($check_birthday->rowCount() == 0) {
    $pdo->exec("ALTER TABLE `members` ADD COLUMN `birthday` DATE NULL AFTER `phone`");
}

// Create giving_accounts table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `giving_accounts` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `account_name` VARCHAR(255) NOT NULL,
        `account_details` TEXT NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Check for and add instructions column to giving_accounts
$check_instructions = $pdo->query("SHOW COLUMNS FROM `giving_accounts` LIKE 'instructions'");
if ($check_instructions->rowCount() == 0) {
    $pdo->exec("ALTER TABLE `giving_accounts` ADD COLUMN `instructions` TEXT NULL AFTER `account_details`");
}

// Create department_applications table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `department_applications` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `member_id` INT(11) NOT NULL,
        `department_id` INT(11) NOT NULL,
        `status` VARCHAR(255) NOT NULL DEFAULT 'pending',
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Create password_resets table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `password_resets` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `email` VARCHAR(255) NOT NULL,
        `token` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// 6. Mark the fix as applied so it doesn't run again.
$mark_fix_stmt = $pdo->prepare("INSERT INTO settings (setting_name, setting_value) VALUES ('db_fix_1_6_applied', '1') ON DUPLICATE KEY UPDATE setting_value = '1'");
$mark_fix_stmt->execute();

?>