<?php
require_once 'includes/member_init.php';
require_once 'includes/public_header.php';

if ($role_id && !member_has_permission('view_announcements')) {
    header('Location: member_dashboard.php');
    exit;
}

$announcements = $pdo->query("
    SELECT a.*, u.email as author_email
    FROM announcements a
    LEFT JOIN admin_users u ON a.author_id = u.id
    ORDER BY a.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h2>Announcements</h2>
        <p class="lead">Stay up to date with the latest news and announcements.</p>
    </div>

    <div class="list-group">
        <?php if ($announcements): ?>
            <?php foreach ($announcements as $announcement): ?>
                <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><?php echo htmlspecialchars($announcement['title']); ?></h5>
                        <small><?php echo date('M d, Y', strtotime($announcement['created_at'])); ?></small>
                    </div>
                    <p class="mb-1">
                        <small>By <?php echo htmlspecialchars($announcement['author_email'] ?? 'Admin'); ?></small>
                    </p>
                    <hr>
                    <p class="mb-1"><?php echo nl2br($announcement['content']); ?></p>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">No recent announcements.</p>
        <?php endif; ?>
    </div>
    <a href="member_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>

<?php require_once 'includes/public_footer.php'; ?>
