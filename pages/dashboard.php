<?php require_once dirname(__DIR__) . '/config.php'; ?>
<?php
if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true) {
    header('Location: ' . BASE_URL . 'pages/login.php');
    exit;
}
?>
<?php require_once dirname(__DIR__) . '/database.php'; ?>
<?php include dirname(__DIR__) . '/includes/header.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <a href="<?php echo BASE_URL; ?>actions/logout_user.php" class="btn btn-danger">Logout</a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h4>Important Notice</h4>
        </div>
        <div class="card-body">
            <p class="mb-0">Please be aware that defaulting on your loan payment will attract a <strong>10% late fee</strong> for the month in which you failed to pay on the due date.</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h4>My Active Loans</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Loan ID</th>
                            <th>Amount</th>
                            <th>Balance</th>
                            <th>Next Due Date</th>
                            <th>Make a Repayment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $db->prepare("SELECT id, amount, balance, next_due_date FROM Loans WHERE user_id = :user_id");
                        $stmt->execute([':user_id' => $_SESSION['user_id']]);
                        $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($loans) {
                            foreach ($loans as $loan) {
                                echo "<tr>";
                                echo "<td>" . $loan['id'] . "</td>";
                                echo "<td>$" . htmlspecialchars(number_format($loan['amount'], 2)) . "</td>";
                                echo "<td>$" . htmlspecialchars(number_format($loan['balance'], 2)) . "</td>";
                                echo "<td>" . date('F j, Y', strtotime($loan['next_due_date'])) . "</td>";
                                echo "<td>
                                        <form action='" . BASE_URL . "actions/make_repayment.php' method='post'>
                                            <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                                            <input type='hidden' name='loan_id' value='" . $loan['id'] . "'>
                                            <div class='input-group'>
                                                <input type='number' step='0.01' class='form-control' name='amount' placeholder='Amount' required>
                                                <button type='submit' class='btn btn-success'>Pay</button>
                                            </div>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>You have no active loans.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>My Loan Application History</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Loan Purpose</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Applied On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $db->prepare("SELECT id, loanPurpose, loanAmount, status, created_at FROM LoanApplications WHERE user_id = :user_id ORDER BY created_at DESC");
                        $stmt->execute([':user_id' => $_SESSION['user_id']]);
                        $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($applications) {
                            foreach ($applications as $app) {
                                echo "<tr>";
                                echo "<td>" . $app['id'] . "</td>";
                                echo "<td>" . htmlspecialchars($app['loanPurpose']) . "</td>";
                                echo "<td>$" . htmlspecialchars(number_format($app['loanAmount'], 2)) . "</td>";
                                echo "<td><span class='badge bg-" . ($app['status'] === 'Approved' ? 'success' : ($app['status'] === 'Disapproved' ? 'danger' : 'warning')) . "'>" . htmlspecialchars($app['status']) . "</span></td>";
                                echo "<td>" . date('F j, Y', strtotime($app['created_at'])) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>You have not made any loan applications yet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
