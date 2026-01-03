<?php
session_start();
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("CSRF token validation failed on create_article.php.", 3, dirname(__DIR__) . '/logs/errors.log');
        header('Location: ' . BASE_URL . 'pages/error.php');
        exit;
    }

    $title = $_POST['title'];
    $content = $_POST['content'];
    $author_id = $_SESSION['user_id']; // Assuming admin is a user

    if (empty($title) || empty($content)) {
        $_SESSION['errors'] = ['Title and content are required.'];
        header('Location: ' . BASE_URL . 'admin/manage_articles.php');
        exit;
    }

    try {
        $stmt = $db->prepare("INSERT INTO Articles (title, content, author_id) VALUES (:title, :content, :author_id)");
        $stmt->execute([':title' => $title, ':content' => $content, ':author_id' => $author_id]);

        $_SESSION['success_message'] = 'Article published successfully!';
        header('Location: ' . BASE_URL . 'admin/manage_articles.php');
        exit;
    } catch (PDOException $e) {
        error_log('Article creation failed: ' . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
        header('Location: ' . BASE_URL . 'pages/error.php');
        exit;
    }
} else {
    header('Location: ' . BASE_URL . 'admin/manage_articles.php');
    exit;
}
?>
