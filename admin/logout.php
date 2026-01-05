<?php require_once dirname(__DIR__) . '/config.php'; ?>
<?php require_once dirname(__DIR__) . '/database.php'; ?>
session_destroy();
header('Location: ' . BASE_URL . 'admin/login.php');
exit;
