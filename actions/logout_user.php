<?php
session_start();
require_once dirname(__DIR__) . '/config.php';

// Unset all of the session variables
$_SESSION = [];

// Destroy the session.
session_destroy();

// Redirect to login page
header('Location: ' . BASE_URL . 'pages/login.php');
exit;
