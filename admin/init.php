<?php
// Core initialization file for the admin panel

// 1. Start session management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Load core configuration and database connection
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../config/app.php';

// ** One-time automatic database schema fix **
require_once __DIR__ . '/auto_db_fix.php';

// 3. Load helper functions
require_once __DIR__ . '/../includes/helpers.php';

// 4. Perform authentication and permission checks
require_once __DIR__ . '/../includes/auth_check.php';

// Note: Individual pages will still be responsible for calling check_permission('permission_name');
// after including this file.
