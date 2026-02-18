<?php require_once dirname(__DIR__) . '/config.php'; ?>
<?php require_once dirname(__DIR__) . '/database.php'; ?>
<?php
if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}
?>
<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Repayments</h1>
        <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>All Repayments</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Loan ID</th>
                            <th>User ID</th>
                            <th>Amount</th>
                            <th>Payment Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $db->query("SELECT * FROM Repayments ORDER BY payment_date DESC");
                        $repayments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if ($repayments) {
                            foreach ($repayments as $repayment) {
                                echo "<tr>";
                                echo "<td>" . $repayment['id'] . "</td>";
                                echo "<td>" . $repayment['loan_id'] . "</td>";
                                echo "<td>" . $repayment['user_id'] . "</td>";
                                echo "<td>₦" . htmlspecialchars(number_format($repayment['amount'], 2)) . "</td>";
                                echo "<td>" . date('F j, Y', strtotime($repayment['payment_date'])) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>No repayments found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
