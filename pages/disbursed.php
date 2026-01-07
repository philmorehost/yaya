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
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
