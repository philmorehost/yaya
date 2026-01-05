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
        CREATE TABLE IF NOT EXISTS AdminSettings (
            setting_key VARCHAR(255) PRIMARY KEY,
            setting_value TEXT
        );
    ");
    echo "<p class='success'>'AdminSettings' table created or already exists.</p>";

    // Create PaymentNotifications table
    echo "<p>Creating 'PaymentNotifications' table if it doesn't exist...</p>";
    $db->exec("
        CREATE TABLE IF NOT EXISTS PaymentNotifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            loan_id INT NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            payment_date DATE NOT NULL,
            status VARCHAR(50) DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE,
            FOREIGN KEY (loan_id) REFERENCES Loans(id) ON DELETE CASCADE
        );
    ");
    echo "<p class='success'>'PaymentNotifications' table created or already exists.</p>";

    // Add 'passport' column to 'Users' table
    $stmt = $db->query("SHOW COLUMNS FROM `Users` LIKE 'passport'");
    if ($stmt->rowCount() == 0) {
        echo "<p>Adding 'passport' column to 'Users' table...</p>";
        $db->exec("ALTER TABLE Users ADD COLUMN passport VARCHAR(255) DEFAULT NULL;");
        echo "<p class='success'>'passport' column added.</p>";
    } else {
        echo "<p class='info'>'passport' column already exists in 'Users' table.</p>";
    }

    // Add guarantor columns to 'Loans' table
    $loan_columns = [
        'guarantor_occupation' => 'VARCHAR(255) DEFAULT NULL',
        'guarantor_phone' => 'VARCHAR(255) DEFAULT NULL',
        'guarantor_passport' => 'VARCHAR(255) DEFAULT NULL'
    ];

    foreach ($loan_columns as $column => $definition) {
        $stmt = $db->query("SHOW COLUMNS FROM `Loans` LIKE '$column'");
        if ($stmt->rowCount() == 0) {
            echo "<p>Adding '$column' column to 'Loans' table...</p>";
            $db->exec("ALTER TABLE Loans ADD COLUMN $column $definition;");
            echo "<p class='success'>'$column' column added.</p>";
        } else {
            echo "<p class='info'>'$column' column already exists in 'Loans' table.</p>";
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
