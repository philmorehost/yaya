<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';
if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("CSRF token validation failed on update_status.php.", 3, dirname(__DIR__) . '/logs/errors.log');
        header('Location: ' . BASE_URL . 'pages/error.php');
        exit;
    }

    if (isset($_POST['id']) && isset($_POST['status'])) {
        $id = (int)$_POST['id'];
        $status = htmlspecialchars($_POST['status']);

        try {
            $stmt = $db->prepare("UPDATE LoanApplications SET status = :status WHERE id = :id");
            $stmt->execute([':status' => $status, ':id' => $id]);
            $_SESSION['success_message'] = "Application #$id status updated successfully.";
        } catch (PDOException $e) {
            error_log("Error updating status: " . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
            header('Location: ' . BASE_URL . 'pages/error.php');
            exit;
        }
    }
}

header('Location: ' . BASE_URL . 'admin/');
exit;
