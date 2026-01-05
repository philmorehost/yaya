<?php
// This is a temporary script to force a database schema update.
// It will be removed in the next commit.

// Ensure that errors are displayed, so the user can see the output.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo '<pre>'; // Use preformatted text for readable output.

// Check if the config file exists before trying to include it.
if (!file_exists(__DIR__ . '/config/db_connect.php')) {
    die("CRITICAL ERROR: The database configuration file is missing at '/config/db_connect.php'. The application cannot run. Please restore this file.");
}

require_once 'update_schema.php';
echo '</pre>';

echo '<h1>Update Complete</h1>';
echo '<p>The database schema update has been attempted. Please check the output above for success or error messages.</p>';
echo '<p><b>IMPORTANT:</b> After this update, please revert the `index.php` file to its original content. The next commit will remove this temporary script.</p>';

// We stop execution here to prevent the rest of the homepage from loading.
exit;
?>