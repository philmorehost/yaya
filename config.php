<?php
// Base URL
define('BASE_URL', '/');

// Check if the config.ini file exists
if (!file_exists(dirname(__FILE__) . '/config.ini')) {
    // If not in the install directory, redirect to the installer
    if (basename(dirname($_SERVER['PHP_SELF'])) !== 'install') {
        header('Location: ' . BASE_URL . 'install/');
        exit;
    }
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
