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
    $status = htmlspecialchars($_POST['status']);

    try {
        $stmt = $db->prepare("UPDATE PaymentNotifications SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $id]);
        $_SESSION['success_message'] = "Payment notification #$id status updated successfully.";
    } catch (PDOException $e) {
        error_log("Error updating payment notification status: " . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
        header('Location: ' . BASE_URL . 'pages/error.php');
        exit;
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
