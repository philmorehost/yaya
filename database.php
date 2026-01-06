<?php
require_once 'config.php';

$configPath = __DIR__ . '/config.ini';
if (!file_exists($configPath)) {
    die("Database configuration file not found. Please create a 'config.ini' file based on 'config.example.ini'.");
}

$config = parse_ini_file($configPath, true);

// -- MySQL Connection (New) --
try {
    $dsn = "mysql:host=" . $config['database']['host'] . ";dbname=" . $config['database']['dbname'] . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $db = new PDO($dsn, $config['database']['user'], $config['database']['password'], $options);
} catch (PDOException $e) {
    // Log the error and show a generic error page
    error_log("MySQL Connection Failed: " . $e->getMessage(), 3, __DIR__ . '/logs/errors.log');
    header('Location: ' . BASE_URL . 'pages/error.php?nodb=1');
    exit;
}
?>
