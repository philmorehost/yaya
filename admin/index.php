<?php require_once dirname(__DIR__) . '/config.php'; ?>
<?php require_once dirname(__DIR__) . '/database.php'; ?>
<?php
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}
?>
<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Admin Dashboard</h1>
        <div>
            <a href="manage_repayments.php" class="btn btn-success">Manage Repayments</a>
            <a href="manage_users.php" class="btn btn-info">Manage Users</a>
            <a href="manage_articles.php" class="btn btn-primary">Manage Articles</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <?php
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
        unset($_SESSION['success_message']);
    }
    ?>

    <div class="card">
        <div class="card-header">
            <h4>Loan Applications</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Loan Purpose</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $db->query("SELECT id, fullName, loanPurpose, loanAmount, status FROM LoanApplications ORDER BY id DESC");
                        $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if ($applications) {
                            foreach ($applications as $app) {
                                echo "<tr>";
                                echo "<td>" . $app['id'] . "</td>";
                                echo "<td>" . htmlspecialchars($app['fullName']) . "</td>";
                                echo "<td>" . htmlspecialchars($app['loanPurpose']) . "</td>";
                                echo "<td>₦" . htmlspecialchars(number_format($app['loanAmount'], 2)) . "</td>";
                                echo "<td><span class='badge bg-" . ($app['status'] === 'Approved' ? 'success' : ($app['status'] === 'Disapproved' ? 'danger' : 'warning')) . "'>" . htmlspecialchars($app['status']) . "</span></td>";
                                echo "<td>
                                        <form action='update_status.php' method='post' style='display:inline-block;'>
                                            <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                                            <input type='hidden' name='id' value='" . $app['id'] . "'>
                                            <input type='hidden' name='status' value='Approved'>
                                            <button type='submit' class='btn btn-success btn-sm'>Approve</button>
                                        </form>
                                        <form action='update_status.php' method='post' style='display:inline-block;'>
                                            <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                                            <input type='hidden' name='id' value='" . $app['id'] . "'>
                                            <input type='hidden' name='status' value='Disapproved'>
                                            <button type='submit' class='btn btn-warning btn-sm'>Disapprove</button>
                                        </form>
                                        <form action='disburse_loan.php' method='post' style='display:inline-block;'>
                                            <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                                            <input type='hidden' name='id' value='" . $app['id'] . "'>
                                            <button type='submit' class='btn btn-info btn-sm'>Disburse</button>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>No applications found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
