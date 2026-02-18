<?php require_once dirname(__DIR__) . '/config.php'; ?>
<?php require_once dirname(__DIR__) . '/database.php'; ?>
<?php
if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true) {
    header('Location: ' . BASE_URL . 'pages/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$repayments = [];

try {
    $query = "
        SELECT
            r.*,
            l.amount as total_loan_amount
        FROM Repayments r
        JOIN Loans l ON r.loan_id = l.id
        WHERE l.user_id = :user_id
        ORDER BY r.created_at DESC
    ";
    $stmt = $db->prepare($query);
    $stmt->execute([':user_id' => $user_id]);
    $repayments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching user repayments: " . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
}
?>
<?php include dirname(__DIR__) . '/includes/header.php'; ?>

<div class="container mt-5">
    <h1 class="mb-4">My Repayment History</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Repayment ID</th>
                            <th>Loan ID</th>
                            <th>Amount Paid</th>
                            <th>Payment Date</th>
                            <th>Date Recorded</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($repayments): ?>
                            <?php foreach ($repayments as $repayment): ?>
                                <tr>
                                    <td>#<?php echo $repayment['id']; ?></td>
                                    <td>#<?php echo $repayment['loan_id']; ?></td>
                                    <td>₦<?php echo number_format($repayment['amount_paid'], 2); ?></td>
                                    <td><?php echo date('F j, Y', strtotime($repayment['payment_date'])); ?></td>
                                    <td><?php echo date('F j, Y, g:i a', strtotime($repayment['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No repayments found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="<?php echo BASE_URL; ?>pages/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
