<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/database.php';

// Ensure user is logged in
if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true) {
    header('Location: ' . BASE_URL . 'pages/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$loans = [];

try {
    $stmt = $db->prepare("SELECT * FROM Loans WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->execute([':user_id' => $user_id]);
    $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // A user might not have the table yet if the update script hasn't run.
    // Instead of crashing, we'll show an empty state.
    error_log("Error fetching user loans: " . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
}
?>

<div class="container my-5">
    <h2 class="text-center mb-4">My Disbursed Loans</h2>

    <?php if (empty($loans)) : ?>
        <div class="alert alert-info text-center" role="alert">
            You do not have any active loans at the moment.
        </div>
    <?php else : ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Loan ID</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Balance</th>
                        <th scope="col">Monthly Payment</th>
                        <th scope="col">Disbursed Date</th>
                        <th scope="col">Next Due Date</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loans as $loan) : ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($loan['id']); ?></td>
                            <td>₦<?php echo number_format($loan['amount'], 2); ?></td>
                            <td>₦<?php echo number_format($loan['balance'], 2); ?></td>
                            <td>₦<?php echo number_format($loan['monthly_repayment'], 2); ?></td>
                            <td><?php echo date('F j, Y', strtotime($loan['created_at'])); ?></td>
                            <td><?php echo date('F j, Y', strtotime($loan['next_due_date'])); ?></td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm view-breakdown"
                                        data-bs-toggle="modal"
                                        data-bs-target="#breakdownModal"
                                        data-loan='<?php echo json_encode($loan); ?>'>
                                    View Breakdown
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Breakdown Modal -->
<div class="modal fade" id="breakdownModal" tabindex="-1" aria-labelledby="breakdownModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="breakdownModalLabel">Loan Repayment Schedule</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h4 id="modalLoanId"></h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Month</th>
                        <th>Due Date</th>
                        <th>Amount Due</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="breakdown-table-body">
                    <!-- Schedule will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const breakdownModal = document.getElementById('breakdownModal');
    breakdownModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const loan = JSON.parse(button.getAttribute('data-loan'));

        const modalTitle = breakdownModal.querySelector('#modalLoanId');
        const tableBody = breakdownModal.querySelector('#breakdown-table-body');

        modalTitle.textContent = `Loan #${loan.id} - Breakdown`;
        tableBody.innerHTML = ''; // Clear previous content

        const totalMonths = 12;
        const monthlyPayment = parseFloat(loan.monthly_repayment);
        const totalAmount = parseFloat(loan.amount);
        // Calculate how many payments have been made based on the balance
        const paymentsMade = Math.round((totalAmount - parseFloat(loan.balance)) / monthlyPayment);

        for (let i = 1; i <= totalMonths; i++) {
            const dueDate = new Date(loan.created_at);
            dueDate.setMonth(dueDate.getMonth() + i);

            let status;
            let statusClass;

            if (i <= paymentsMade) {
                status = 'Paid';
                statusClass = 'text-success fw-bold';
            } else {
                status = 'Pending';
                statusClass = 'text-warning';
            }

            const row = `
                <tr>
                    <td>${i}</td>
                    <td>${dueDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</td>
                    <td>₦${monthlyPayment.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td class="${statusClass}">${status}</td>
                </tr>
            `;
            tableBody.innerHTML += row;
        }
    });
});
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
