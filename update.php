<?php
// update.php

/**
 * This script updates the database schema to the latest version.
 * It is designed to be run from the browser.
 * It is idempotent, meaning it can be run multiple times without causing errors.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Database Update</title>";
echo "<style>body { font-family: sans-serif; padding: 2em; } .success { color: green; } .error { color: red; } .info { color: blue; }</style>";
echo "</head><body>";
echo "<h1>Database Update Script</h1>";

try {
    $db->beginTransaction();

    echo "<p class='info'>Starting database update...</p>";

    // Create AdminSettings table
    echo "<p>Creating 'AdminSettings' table if it doesn't exist...</p>";
    $db->exec("
        CREATE TABLE IF NOT EXISTS `AdminSettings` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `setting_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
          `setting_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `setting_key` (`setting_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "<p class='success'>'AdminSettings' table created or already exists.</p>";

    // Create PaymentNotifications table
    echo "<p>Creating 'PaymentNotifications' table if it doesn't exist...</p>";
    $db->exec("
        CREATE TABLE IF NOT EXISTS `PaymentNotifications` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) NOT NULL,
          `loan_id` int(11) NOT NULL,
          `amount` decimal(10,2) NOT NULL,
          `payment_date` date NOT NULL,
          `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "<p class='success'>'PaymentNotifications' table created or already exists.</p>";

    // Add guarantor columns to 'LoanApplications' table
    $loan_columns = [
        'guarantor1Occupation' => 'VARCHAR(255) DEFAULT NULL',
        'guarantor1Phone' => 'VARCHAR(255) DEFAULT NULL',
        'guarantor1Passport' => 'VARCHAR(255) DEFAULT NULL',
        'guarantor2Occupation' => 'VARCHAR(255) DEFAULT NULL',
        'guarantor2Phone' => 'VARCHAR(255) DEFAULT NULL',
        'guarantor2Passport' => 'VARCHAR(255) DEFAULT NULL',
        'userPassport' => 'VARCHAR(255) DEFAULT NULL'
    ];

    foreach ($loan_columns as $column => $definition) {
        $stmt = $db->query("SHOW COLUMNS FROM `LoanApplications` LIKE '$column'");
        if ($stmt->rowCount() == 0) {
            echo "<p>Adding '$column' column to 'LoanApplications' table...</p>";
            $db->exec("ALTER TABLE LoanApplications ADD COLUMN $column $definition;");
            echo "<p class='success'>'$column' column added.</p>";
        } else {
            echo "<p class='info'>'$column' column already exists in 'LoanApplications' table.</p>";
        }
    }

    $db->commit();
    echo "<h2 class='success'>Database update completed successfully!</h2>";
    echo "<p>You can now safely remove the <strong>update.php</strong> file.</p>";

} catch (PDOException $e) {
    $db->rollBack();
    echo "<h2 class='error'>An error occurred during the update:</h2>";
    echo "<p class='error'>" . $e->getMessage() . "</p>";
    echo "<p class='error'>The database has been restored to its previous state.</p>";
    die();
}

echo "</body></html>";
