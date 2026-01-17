<?php require_once dirname(__DIR__) . '/config.php'; ?>
<?php require_once dirname(__DIR__) . '/database.php'; ?>
<?php
if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true) {
    header('Location: ' . BASE_URL . 'pages/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$notifications = [];

try {
    $stmt = $db->prepare("SELECT * FROM PaymentNotifications WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->execute([':user_id' => $user_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching user notifications: " . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
}
?>
<?php include dirname(__DIR__) . '/includes/header.php'; ?>

<div class="container mt-5">
    <h1 class="mb-4">My Notifications</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Loan ID</th>
                            <th>Amount</th>
                            <th>Payment Date</th>
                            <th>Status</th>
                            <th>Submitted On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($notifications): ?>
                            <?php foreach ($notifications as $notification): ?>
                                <tr>
                                    <td>#<?php echo $notification['id']; ?></td>
                                    <td>#<?php echo $notification['loan_id']; ?></td>
                                    <td>₦<?php echo number_format($notification['amount'], 2); ?></td>
                                    <td><?php echo date('F j, Y', strtotime($notification['payment_date'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php
                                            echo $notification['status'] === 'paid' ? 'success' : ($notification['status'] === 'unpaid' ? 'danger' : 'warning');
                                        ?>">
                                            <?php echo ucfirst(htmlspecialchars($notification['status'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('F j, Y, g:i a', strtotime($notification['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No payment notifications found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
