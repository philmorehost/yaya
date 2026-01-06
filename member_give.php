<?php
require_once 'includes/member_init.php';
require_once 'includes/public_header.php';

if ($role_id && !member_has_permission('view_give')) {
    header('Location: member_dashboard.php');
    exit;
}

$accounts = $pdo->query("SELECT * FROM giving_accounts ORDER BY account_name")->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h2>Give</h2>
        <p class="lead">Your generous giving supports our mission and ministries.</p>
    </div>

    <div class="row">
        <?php if ($accounts): ?>
            <?php foreach ($accounts as $account): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title text-center" style="color: #001f3f;"><?php echo htmlspecialchars($account['account_name']); ?></h5>
                            <hr>
                            <div class="card-text"><?php echo nl2br(htmlspecialchars($account['account_details'])); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center">Giving details are not available at the moment. Please check back later.</p>
            </div>
        <?php endif; ?>
    </div>
    <a href="member_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

<?php require_once 'includes/public_footer.php'; ?>
