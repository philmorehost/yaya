<?php
session_start();
require_once dirname(__DIR__) . '/config.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}
require_once dirname(__DIR__) . '/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("CSRF token validation failed on disburse_loan.php.", 3, dirname(__DIR__) . '/logs/errors.log');
        header('Location: ' . BASE_URL . 'pages/error.php');
        exit;
    }

    if (isset($_POST['id'])) {
        $id = (int)$_POST['id'];

        try {
            // Get application details
            $stmt = $db->prepare("SELECT * FROM LoanApplications WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $application = $stmt->fetch();

            if ($application && $application['status'] === 'Approved') {
                // Update application status
                $updateStmt = $db->prepare("UPDATE LoanApplications SET status = 'Disbursed', disbursed_at = CURRENT_TIMESTAMP WHERE id = :id");
                $updateStmt->execute([':id' => $id]);

                // Create a new loan
                $loanStmt = $db->prepare("INSERT INTO Loans (application_id, user_id, amount, balance, next_due_date) VALUES (:application_id, :user_id, :amount, :balance, :next_due_date)");
                $loanStmt->execute([
                    ':application_id' => $id,
                    ':user_id' => $application['user_id'],
                    ':amount' => $application['loanAmount'],
                    ':balance' => $application['loanAmount'],
                    ':next_due_date' => date('Y-m-d', strtotime('+1 month'))
                ]);

                $_SESSION['success_message'] = "Loan #$id has been disbursed successfully.";
            } else {
                $_SESSION['errors'] = ["Loan #$id could not be disbursed. Ensure it has been approved."];
            }
        } catch (PDOException $e) {
            error_log("Error disbursing loan: " . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
            header('Location: ' . BASE_URL . 'pages/error.php');
            exit;
        }
    }
}

header('Location: ' . BASE_URL . 'admin/');
exit;
