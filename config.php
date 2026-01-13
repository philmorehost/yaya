<?php
session_start();

// Define BASE_PATH early
define('BASE_PATH', __DIR__ . '/');

$configPath = BASE_PATH . 'config.ini';

// If the config file doesn't exist, and we are not in the install directory, redirect to the installer.
if (!file_exists($configPath) && strpos($_SERVER['REQUEST_URI'], '/install/') === false) {
    // Dynamically determine the base URL for the redirect
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    // Get the directory of the current script, relative to the document root
    $script_dir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    $baseUrl = $protocol . $host . $script_dir;

    header('Location: ' . $baseUrl . 'install/');
    exit;
}

// Define BASE_URL after the installer check
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script_dir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
$baseUrl = $protocol . $host . $script_dir;

define('BASE_URL', $baseUrl);
