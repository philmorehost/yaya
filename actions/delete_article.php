<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';

if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true) {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("CSRF token validation failed on delete_article.php.", 3, dirname(__DIR__) . '/logs/errors.log');
        header('Location: ' . BASE_URL . 'pages/error.php');
        exit;
    }

    $id = (int)$_POST['id'];

    if (empty($id)) {
        $_SESSION['errors'] = ['Invalid article ID.'];
        header('Location: ' . BASE_URL . 'admin/manage_articles.php');
        exit;
    }

    try {
        $stmt = $db->prepare("DELETE FROM Articles WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $_SESSION['success_message'] = 'Article deleted successfully!';
        header('Location: ' . BASE_URL . 'admin/manage_articles.php');
        exit;
    } catch (PDOException $e) {
        error_log('Article deletion failed: ' . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
        header('Location: ' . BASE_URL . 'pages/error.php');
        exit;
    }
} else {
    header('Location: ' . BASE_URL . 'admin/manage_articles.php');
    exit;
}
?>
