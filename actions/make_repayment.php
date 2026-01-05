<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';

if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true) {
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
        // Get current loan balance
        $stmt = $db->prepare("SELECT balance FROM Loans WHERE id = :loan_id AND user_id = :user_id");
        $stmt->execute([':loan_id' => $loan_id, ':user_id' => $user_id]);
        $loan = $stmt->fetch();

        if ($loan) {
            $repayment_amount = $amount;
            if ($repayment_amount > $loan['balance']) {
                $repayment_amount = $loan['balance'];
            }

            $new_balance = $loan['balance'] - $repayment_amount;

            // Insert repayment record
            $stmt = $db->prepare("INSERT INTO Repayments (loan_id, user_id, amount, payment_date) VALUES (:loan_id, :user_id, :amount, :payment_date)");
            $stmt->execute([
                ':loan_id' => $loan_id,
                ':user_id' => $user_id,
                ':amount' => $repayment_amount,
                ':payment_date' => date('Y-m-d')
            ]);

            // Update loan balance and next due date
            $updateStmt = $db->prepare("UPDATE Loans SET balance = :balance, next_due_date = DATE_ADD(next_due_date, INTERVAL 1 MONTH) WHERE id = :loan_id AND user_id = :user_id");
            $updateStmt->execute([':balance' => $new_balance, ':loan_id' => $loan_id, ':user_id' => $user_id]);

            $_SESSION['success_message'] = 'Repayment of $' . number_format($repayment_amount, 2) . ' made successfully!';
        } else {
            $_SESSION['errors'] = ['Loan not found.'];
        }

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
