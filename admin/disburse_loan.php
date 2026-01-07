<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';

if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

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
            $db->beginTransaction();

            // Get application details and lock the row
            $stmt = $db->prepare("SELECT * FROM LoanApplications WHERE id = :id FOR UPDATE");
            $stmt->execute([':id' => $id]);
            $application = $stmt->fetch();

            if ($application && $application['status'] === 'Approved') {
                // Update application status
                $updateStmt = $db->prepare("UPDATE LoanApplications SET status = 'Disbursed', disbursed_at = CURRENT_TIMESTAMP WHERE id = :id");
                $updateStmt->execute([':id' => $id]);

                // Calculate monthly repayment (assuming 12 months, 0 interest)
                $monthly_repayment = $application['loanAmount'] / 12;

                // Create a new loan
                $loanStmt = $db->prepare(
                    "INSERT INTO Loans (application_id, user_id, amount, balance, next_due_date, monthly_repayment)
                     VALUES (:application_id, :user_id, :amount, :balance, :next_due_date, :monthly_repayment)"
                );
                $loanStmt->execute([
                    ':application_id' => $id,
                    ':user_id' => $application['user_id'],
                    ':amount' => $application['loanAmount'],
                    ':balance' => $application['loanAmount'],
                    ':next_due_date' => date('Y-m-d', strtotime('+1 month')),
                    ':monthly_repayment' => $monthly_repayment
                ]);

                $db->commit();
                $_SESSION['success_message'] = "Loan #$id has been disbursed successfully.";
            } else {
                $db->rollBack();
                $_SESSION['errors'] = ["Loan #$id could not be disbursed. Ensure it has been approved and not already disbursed."];
            }
        } catch (PDOException $e) {
            error_log("Error disbursing loan: " . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
            header('Location: ' . BASE_URL . 'pages/error.php');
            exit;
        }
    }
}

header('Location: ' . BASE_URL . 'admin/index.php');
exit;
