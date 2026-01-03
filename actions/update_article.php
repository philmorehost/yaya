<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("CSRF token validation failed on update_article.php.", 3, dirname(__DIR__) . '/logs/errors.log');
        header('Location: ' . BASE_URL . 'pages/error.php');
        exit;
    }

    $id = (int)$_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    if (empty($id) || empty($title) || empty($content)) {
        $_SESSION['errors'] = ['Title and content are required.'];
        header('Location: ' . BASE_URL . 'admin/edit_article.php?id=' . $id);
        exit;
    }

    try {
        $stmt = $db->prepare("UPDATE Articles SET title = :title, content = :content WHERE id = :id");
        $stmt->execute([':title' => $title, ':content' => $content, ':id' => $id]);

        $_SESSION['success_message'] = 'Article updated successfully!';
        header('Location: ' . BASE_URL . 'admin/manage_articles.php');
        exit;
    } catch (PDOException $e) {
        error_log('Article update failed: ' . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
        header('Location: ' . BASE_URL . 'pages/error.php');
        exit;
    }
} else {
    header('Location: ' . BASE_URL . 'admin/manage_articles.php');
    exit;
}
?>
