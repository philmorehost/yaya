<?php require_once dirname(__DIR__) . '/config.php'; ?>
<?php require_once dirname(__DIR__) . '/database.php'; ?>
<?php
if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("CSRF token validation failed on notifications.php.", 3, dirname(__DIR__) . '/logs/errors.log');
        header('Location: ' . BASE_URL . 'pages/error.php');
        exit;
    }

    $id = (int)$_POST['id'];
    $new_status = htmlspecialchars($_POST['status']);

    try {
        $db->beginTransaction();

        // Get current notification details
        $stmt = $db->prepare("SELECT * FROM PaymentNotifications WHERE id = :id FOR UPDATE");
        $stmt->execute([':id' => $id]);
        $notification = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$notification) {
            throw new Exception("Notification not found.");
        }

        if ($notification['status'] !== 'paid' && $new_status === 'paid') {
            // Process the payment
            $loan_id = $notification['loan_id'];
            $amount = $notification['amount'];

            // Get loan details
            $loanStmt = $db->prepare("SELECT balance FROM Loans WHERE id = :loan_id FOR UPDATE");
            $loanStmt->execute([':loan_id' => $loan_id]);
            $loan = $loanStmt->fetch(PDO::FETCH_ASSOC);

            if ($loan) {
                // Update loan balance
                $new_balance = $loan['balance'] - $amount;
                if ($new_balance < 0) $new_balance = 0;

                $updateLoanStmt = $db->prepare("UPDATE Loans SET balance = :new_balance WHERE id = :loan_id");
                $updateLoanStmt->execute([':new_balance' => $new_balance, ':loan_id' => $loan_id]);

                // Update next_due_date if balance > 0
                if ($new_balance > 0) {
                    $updateDateStmt = $db->prepare("UPDATE Loans SET next_due_date = DATE_ADD(next_due_date, INTERVAL 1 MONTH) WHERE id = :loan_id");
                    $updateDateStmt->execute([':loan_id' => $loan_id]);
                }

                // Record repayment
                $repaymentStmt = $db->prepare("INSERT INTO Repayments (loan_id, amount_paid, payment_date, recorded_by) VALUES (:loan_id, :amount_paid, :payment_date, :recorded_by)");
                $repaymentStmt->execute([
                    ':loan_id' => $loan_id,
                    ':amount_paid' => $amount,
                    ':payment_date' => $notification['payment_date'],
                    ':recorded_by' => $_SESSION['user_id']
                ]);
            }
        }

        $updateStmt = $db->prepare("UPDATE PaymentNotifications SET status = :status WHERE id = :id");
        $updateStmt->execute([':status' => $new_status, ':id' => $id]);

        $db->commit();
        $_SESSION['success_message'] = "Payment notification #$id status updated successfully.";
    } catch (Exception $e) {
        $db->rollBack();
        error_log("Error updating payment notification: " . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
        $_SESSION['errors'] = ["Error updating payment notification: " . $e->getMessage()];
    }

    header('Location: ' . BASE_URL . 'admin/notifications.php');
    exit;
}

$notifications = [];
try {
    $stmt = $db->query("SELECT * FROM PaymentNotifications ORDER BY created_at DESC");
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log the error or handle it gracefully
    // For now, we'll just suppress the error and show an empty list
}
?>
<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <h1>Payment Notifications</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['errors'])): ?>
        <?php foreach ($_SESSION['errors'] as $error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endforeach; ?>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Loan ID</th>
                            <th>Amount</th>
                            <th>Payment Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($notifications): ?>
                            <?php foreach ($notifications as $notification): ?>
                                <tr>
                                    <td><?php echo $notification['id']; ?></td>
                                    <td><?php echo $notification['user_id']; ?></td>
                                    <td><?php echo $notification['loan_id']; ?></td>
                                    <td>₦<?php echo htmlspecialchars(number_format($notification['amount'], 2)); ?></td>
                                    <td><?php echo date('F j, Y', strtotime($notification['payment_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($notification['status']); ?></td>
                                    <td>
                                        <form action="" method="post" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="id" value="<?php echo $notification['id']; ?>">
                                            <input type="hidden" name="status" value="paid">
                                            <button type="submit" class="btn btn-success btn-sm">Mark as Paid</button>
                                        </form>
                                        <form action="" method="post" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="id" value="<?php echo $notification['id']; ?>">
                                            <input type="hidden" name="status" value="unpaid">
                                            <button type="submit" class="btn btn-warning btn-sm">Mark as Unpaid</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No payment notifications found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
