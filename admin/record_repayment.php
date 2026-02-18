<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';

// Ensure user is admin
if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true || $_SESSION['user_role'] !== 'admin') {
    // Redirect non-admins to a safe place
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['errors'] = ["CSRF token validation failed. Please try again."];
        header('Location: ' . BASE_URL . 'admin/manage_disbursed.php');
        exit;
    }

    $loan_id = filter_input(INPUT_POST, 'loan_id', FILTER_VALIDATE_INT);
    $amount_paid = filter_input(INPUT_POST, 'amount_paid', FILTER_VALIDATE_FLOAT);
    $payment_date = $_POST['payment_date']; // Basic validation, can be improved with date checking

    // More validation
    if (!$loan_id || !$amount_paid || empty($payment_date)) {
        $_SESSION['errors'] = ["All fields are required."];
        header('Location: ' . BASE_URL . 'admin/manage_disbursed.php');
        exit;
    }

    if ($amount_paid <= 0) {
        $_SESSION['errors'] = ["Payment amount must be a positive number."];
        header('Location: ' . BASE_URL . 'admin/manage_disbursed.php');
        exit;
    }

    try {
        $db->beginTransaction();

        // Get the current loan balance
        $stmt = $db->prepare("SELECT balance FROM Loans WHERE id = :loan_id FOR UPDATE");
        $stmt->execute([':loan_id' => $loan_id]);
        $current_balance = $stmt->fetchColumn();

        if ($current_balance === false) {
            throw new Exception("Loan not found.");
        }

        if ($amount_paid > $current_balance) {
             $_SESSION['errors'] = ["Payment amount cannot be greater than the outstanding balance (₦" . number_format($current_balance, 2) . ")."];
             header('Location: ' . BASE_URL . 'admin/manage_disbursed.php');
             $db->rollBack();
             exit;
        }

        // 1. Insert into Repayments table
        $insertStmt = $db->prepare(
            "INSERT INTO Repayments (loan_id, amount_paid, payment_date, recorded_by) VALUES (:loan_id, :amount_paid, :payment_date, :recorded_by)"
        );
        $insertStmt->execute([
            ':loan_id' => $loan_id,
            ':amount_paid' => $amount_paid,
            ':payment_date' => $payment_date,
            ':recorded_by' => $_SESSION['user_id']
        ]);

        // 2. Update the loan balance
        $new_balance = $current_balance - $amount_paid;
        $updateStmt = $db->prepare("UPDATE Loans SET balance = :new_balance WHERE id = :loan_id");
        $updateStmt->execute([
            ':new_balance' => $new_balance,
            ':loan_id' => $loan_id
        ]);

        // Update next_due_date if the loan is not fully paid
        if ($new_balance > 0) {
            $updateDueDateStmt = $db->prepare("UPDATE Loans SET next_due_date = DATE_ADD(next_due_date, INTERVAL 1 MONTH) WHERE id = :loan_id");
            $updateDueDateStmt->execute([':loan_id' => $loan_id]);
        }


        $db->commit();
        $_SESSION['success_message'] = "Payment of ₦" . number_format($amount_paid, 2) . " recorded successfully for Loan #$loan_id.";

    } catch (Exception $e) {
        $db->rollBack();
        error_log("Error recording repayment: " . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
        $_SESSION['errors'] = ["There was a database error while recording the payment."];
    }
}

// Redirect back to the management page
header('Location: ' . BASE_URL . 'admin/manage_disbursed.php');
exit;
