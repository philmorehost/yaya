<?php
require_once 'includes/member_init.php';
require_once 'includes/public_header.php';


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
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0" style="color: #001f3f;"><?php echo htmlspecialchars($account['account_name']); ?></h5>
                        </div>
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Account Details</h6>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($account['account_details'])); ?></p>
                            <h6 class="card-subtitle mt-4 mb-2 text-muted">Instructions</h6>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($account['instructions'])); ?></p>
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
