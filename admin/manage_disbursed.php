<?php
require_once dirname(__DIR__) . '/config.php';
require_once 'includes/header.php';
require_once dirname(__DIR__) . '/database.php';

// Ensure user is admin
if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

$loans = [];

try {
    $stmt = $db->query(
        "SELECT l.*, u.fullName, la.loanPurpose
         FROM Loans l
         JOIN Users u ON l.user_id = u.id
         JOIN LoanApplications la ON l.application_id = la.id
         ORDER BY l.created_at DESC"
    );
    $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching all loans for admin: " . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
    // Display a friendly error on the page if the query fails
    $_SESSION['errors'] = ["There was an error retrieving the loan data. Please ensure the database is up to date."];
}
?>

<div class="container-fluid my-5">
    <h2 class="text-center mb-4">Manage Disbursed Loans</h2>

    <?php
    if (!empty($_SESSION['success_message'])) {
        echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
        unset($_SESSION['success_message']);
    }
    if (!empty($_SESSION['errors'])) {
        echo '<div class="alert alert-danger">';
        foreach ($_SESSION['errors'] as $error) {
            echo '<div>' . htmlspecialchars($error) . '</div>';
        }
        echo '</div>';
        unset($_SESSION['errors']);
    }
    ?>

    <?php if (empty($loans) && empty($_SESSION['errors'])) : ?>
        <div class="alert alert-info text-center" role="alert">
            No loans have been disbursed yet.
        </div>
    <?php elseif (!empty($loans)) : ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Loan ID</th>
                        <th scope="col">Borrower</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Balance</th>
                        <th scope="col">Monthly Payment</th>
                        <th scope="col">Disbursed Date</th>
                        <th scope="col">Next Due Date</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loans as $loan) : ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($loan['id']); ?></td>
                            <td><?php echo htmlspecialchars($loan['fullName']); ?></td>
                            <td>₦<?php echo number_format($loan['amount'], 2); ?></td>
                            <td>₦<?php echo number_format($loan['balance'], 2); ?></td>
                            <td>₦<?php echo number_format($loan['monthly_repayment'], 2); ?></td>
                            <td><?php echo date('F j, Y', strtotime($loan['created_at'])); ?></td>
                            <td><?php echo date('F j, Y', strtotime($loan['next_due_date'])); ?></td>
                            <td>
                                <?php
                                $status = 'Ongoing'; // Placeholder
                                if ($loan['balance'] <= 0) {
                                    $status = 'Paid';
                                }
                                echo htmlspecialchars($status);
                                ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-success record-payment-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#recordPaymentModal"
                                        data-loan-id="<?php echo $loan['id']; ?>"
                                        title="Record Payment"
                                        <?php if ($loan['balance'] <= 0) echo 'disabled'; ?>>
                                    <i class="fas fa-dollar-sign"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Record Payment Modal -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-labelledby="recordPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recordPaymentModalLabel">Record a Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="record_repayment.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="loan_id" id="modal_loan_id">

                    <div class="mb-3">
                        <label for="amount_paid" class="form-label">Amount Paid</label>
                        <div class="input-group">
                            <span class="input-group-text">₦</span>
                            <input type="number" class="form-control" id="amount_paid" name="amount_paid" step="0.01" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Payment Date</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var recordPaymentModal = document.getElementById('recordPaymentModal');
    recordPaymentModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var loanId = button.getAttribute('data-loan-id');
        var modalLoanIdInput = recordPaymentModal.querySelector('#modal_loan_id');
        modalLoanIdInput.value = loanId;
    });
});
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
