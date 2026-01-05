<?php require_once dirname(__DIR__) . '/config.php'; ?>
<?php require_once dirname(__DIR__) . '/database.php'; ?>
<?php
if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true) {
    header('Location: ' . BASE_URL . 'pages/login.php');
    exit;
}

$stmt = $db->query("SELECT * FROM AdminSettings WHERE setting_key = 'account_details'");
$account_details = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("CSRF token validation failed on payment.php.", 3, dirname(__DIR__) . '/logs/errors.log');
        header('Location: ' . BASE_URL . 'pages/error.php');
        exit;
    }

    $loan_id = (int)$_POST['loan_id'];
    $amount = (float)$_POST['amount'];
    $payment_date = $_POST['payment_date'];
    $user_id = $_SESSION['user_id'];

    if (empty($loan_id) || empty($amount) || empty($payment_date)) {
        $_SESSION['errors'] = ['All fields are required.'];
        header('Location: ' . BASE_URL . 'pages/payment.php');
        exit;
    }

    try {
        $stmt = $db->prepare("INSERT INTO PaymentNotifications (user_id, loan_id, amount, payment_date) VALUES (:user_id, :loan_id, :amount, :payment_date)");
        $stmt->execute([':user_id' => $user_id, ':loan_id' => $loan_id, ':amount' => $amount, ':payment_date' => $payment_date]);

        $_SESSION['success_message'] = 'Payment notification submitted successfully.';
        header('Location: ' . BASE_URL . 'pages/dashboard.php');
        exit;
    } catch (PDOException $e) {
        error_log('Payment notification submission failed: ' . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
        header('Location: ' . BASE_URL . 'pages/error.php');
        exit;
    }
}
?>
<?php include dirname(__DIR__) . '/includes/header.php'; ?>

<div class="container mt-5">
    <h1>Make a Payment</h1>
    <div class="card">
        <div class="card-body">
            <h2>Account Details</h2>
            <p><?php echo nl2br(htmlspecialchars($account_details['setting_value'] ?? '')); ?></p>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h2>Submit Payment Notification</h2>
            <form action="" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="mb-3">
                    <label for="loan_id" class="form-label">Loan ID</label>
                    <input type="number" class="form-control" id="loan_id" name="loan_id" required>
                </div>
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount</label>
                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                </div>
                <div class="mb-3">
                    <label for="payment_date" class="form-label">Payment Date</label>
                    <input type="date" class="form-control" id="payment_date" name="payment_date" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
