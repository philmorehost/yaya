<?php session_start(); ?>
<?php require_once dirname(__DIR__) . '/config.php'; ?>
<?php
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}
?>
<?php require_once dirname(__DIR__) . '/database.php'; ?>
<?php include dirname(__DIR__) . '/includes/header.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Admin Dashboard</h1>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
    <p>Loan Applications</p>

    <?php
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
        unset($_SESSION['success_message']);
    }
    ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Hub Category</th>
                <th>Full Name</th>
                <th>Membership Number</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Loan Purpose</th>
                <th>Loan Amount</th>
                <th>Monthly Income</th>
                <th>Existing Savings</th>
                <th>Guarantor 1 Name</th>
                <th>Guarantor 1 MemberId</th>
                <th>Guarantor 2 Name</th>
                <th>Guarantor 2 MemberId</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $db->query("SELECT * FROM LoanApplications");
            $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($applications) {
                foreach ($applications as $app) {
                    echo "<tr>";
                    echo "<td>" . $app['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($app['hubCategory']) . "</td>";
                    echo "<td>" . htmlspecialchars($app['fullName']) . "</td>";
                    echo "<td>" . htmlspecialchars($app['membershipNumber']) . "</td>";
                    echo "<td>" . htmlspecialchars($app['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($app['phone']) . "</td>";
                    echo "<td>" . htmlspecialchars($app['loanPurpose']) . "</td>";
                    echo "<td>" . htmlspecialchars($app['loanAmount']) . "</td>";
                    echo "<td>" . htmlspecialchars($app['monthlyIncome']) . "</td>";
                    echo "<td>" . htmlspecialchars($app['existingSavings']) . "</td>";
                    echo "<td>" . htmlspecialchars($app['guarantor1Name']) . "</td>";
                    echo "<td>" . htmlspecialchars($app['guarantor1MemberId']) . "</td>";
                    echo "<td>" . htmlspecialchars($app['guarantor2Name']) . "</td>";
                    echo "<td>" . htmlspecialchars($app['guarantor2MemberId']) . "</td>";
                    echo "<td>" . htmlspecialchars($app['status']) . "</td>";
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
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='16' class='text-center'>No applications found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
