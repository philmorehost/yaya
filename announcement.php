<?php
require_once 'config/db_connect.php';
require_once 'includes/public_header.php';

$announcement_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$announcement_id) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("
    SELECT a.*, u.email as author_email
    FROM announcements a
    LEFT JOIN admin_users u ON a.author_id = u.id
    WHERE a.id = ?
");
$stmt->execute([$announcement_id]);
$announcement = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$announcement) {
    // Or display a 'Not Found' message
    header("Location: index.php");
    exit();
}
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <h1 class="mb-3"><?php echo htmlspecialchars($announcement['title']); ?></h1>
            <p class="text-muted">
                Posted on <?php echo date('F j, Y', strtotime($announcement['created_at'])); ?>
                by <?php echo htmlspecialchars($announcement['author_email'] ?? 'Admin'); ?>
            </p>
            <hr>
            <div class="announcement-content">
                <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
            </div>
            <hr>
            <a href="index.php" class="btn btn-primary">Back to Home</a>
        </div>
    </div>
</div>

<?php require_once 'includes/public_footer.php'; ?>
