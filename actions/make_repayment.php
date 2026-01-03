<?php
session_start();
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';

if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true) {
    header('Location: ' . BASE_URL . 'pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("CSRF token validation failed on make_repayment.php.", 3, dirname(__DIR__) . '/logs/errors.log');
        header('Location: ' . BASE_URL . 'pages/error.php');
        exit;
    }

    $loan_id = (int)$_POST['loan_id'];
    $amount = (float)$_POST['amount'];
    $user_id = $_SESSION['user_id'];

    if (empty($loan_id) || empty($amount) || $amount <= 0) {
        $_SESSION['errors'] = ['Invalid repayment amount.'];
        header('Location: ' . BASE_URL . 'pages/dashboard.php');
        exit;
    }

    try {
        // Insert repayment record
        $stmt = $db->prepare("INSERT INTO Repayments (loan_id, user_id, amount, payment_date) VALUES (:loan_id, :user_id, :amount, :payment_date)");
        $stmt->execute([
            ':loan_id' => $loan_id,
            ':user_id' => $user_id,
            ':amount' => $amount,
            ':payment_date' => date('Y-m-d')
        ]);

        // Update the next due date (simplified logic)
        $updateStmt = $db->prepare("UPDATE Loans SET next_due_date = DATE(next_due_date, '+1 month') WHERE id = :loan_id AND user_id = :user_id");
        $updateStmt->execute([':loan_id' => $loan_id, ':user_id' => $user_id]);

        $_SESSION['success_message'] = 'Repayment of $' . number_format($amount, 2) . ' made successfully!';
        header('Location: ' . BASE_URL . 'pages/dashboard.php');
        exit;
    } catch (PDOException $e) {
        error_log('Repayment failed: ' . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
        header('Location: ' . BASE_URL . 'pages/error.php');
        exit;
    }
} else {
    header('Location: ' . BASE_URL . 'pages/dashboard.php');
    exit;
}
?>
