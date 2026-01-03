<?php
require_once 'config.php';

// Database credentials - PLEASE UPDATE THESE
define('DB_HOST', 'your_mysql_host');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

/*
// -- SQLite Connection (Old) --
try {
    $db = new PDO('sqlite:' . __DIR__ . '/data/loan_applications.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // ... table creation was here
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage(), 3, __DIR__ . '/logs/errors.log');
    header('Location: ' . BASE_URL . 'pages/error.php');
    exit;
}
*/

// -- MySQL Connection (New) --
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $db = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Log the error and show a generic error page
    error_log("MySQL Connection Failed: " . $e->getMessage(), 3, __DIR__ . '/logs/errors.log');
    header('Location: ' . BASE_URL . 'pages/error.php');
    exit;
}
?>
