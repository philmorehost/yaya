<?php
require_once 'includes/member_init.php';
require_once 'includes/public_header.php';

if ($role_id && !member_has_permission('view_departments')) {
    header('Location: member_dashboard.php');
    exit;
}

$departments = $pdo->query("SELECT d.*, m.name as head_name FROM departments d LEFT JOIN members m ON d.head_id = m.id ORDER BY d.name")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h2>Departments</h2>
        <p class="lead">Explore our departments and find a place to serve.</p>
    </div>

    <div class="row">
        <?php if ($departments): ?>
            <?php foreach ($departments as $department): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title text-center" style="color: #001f3f;"><?php echo htmlspecialchars($department['name']); ?></h5>
                            <hr>
                            <p class="card-text"><strong>Department Head:</strong> <?php echo htmlspecialchars($department['head_name'] ?: 'Not Assigned'); ?></p>
                            <button class="btn btn-primary w-100">Apply to Join</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center">No departments have been created yet. Please check back later.</p>
            </div>
        <?php endif; ?>
    </div>
    <a href="member_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

<?php require_once 'includes/public_footer.php'; ?>
