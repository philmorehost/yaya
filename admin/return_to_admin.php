<?php
require_once dirname(__DIR__) . '/config.php';

if (isset($_SESSION['admin_return_session'])) {
    $_SESSION = $_SESSION['admin_return_session'];
    unset($_SESSION['admin_return_session']);
}

header('Location: ' . BASE_URL . 'admin/manage_users.php');
exit;
