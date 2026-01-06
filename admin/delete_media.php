<?php
require_once 'init.php';
check_permission('manage_media');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';

    $media_id = $_POST['id'] ?? null;
    if ($media_id) {
        $stmt = $pdo->prepare("DELETE FROM media WHERE id = ?");
        if ($stmt->execute([$media_id])) {
            $_SESSION['success_message'] = "Media post deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to delete media post.";
        }
    }
}

header("Location: media.php");
exit();
