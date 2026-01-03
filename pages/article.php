<?php require_once dirname(__DIR__) . '/config.php'; ?>
<?php require_once dirname(__DIR__) . '/database.php'; ?>
<?php include dirname(__DIR__) . '/includes/header.php'; ?>

<div class="container mt-5">
    <?php
    if (!isset($_GET['id'])) {
        header('Location: ' . BASE_URL . 'pages/news.php');
        exit;
    }

    $stmt = $db->prepare("SELECT * FROM Articles WHERE id = :id");
    $stmt->execute([':id' => $_GET['id']]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$article) {
        header('Location: ' . BASE_URL . 'pages/news.php');
        exit;
    }
    ?>

    <div class="card">
        <div class="card-header">
            <h2><?php echo htmlspecialchars($article['title']); ?></h2>
        </div>
        <div class="card-body">
            <div class="text-muted mb-4">
                Posted on <?php echo date('F j, Y', strtotime($article['created_at'])); ?>
            </div>
            <div>
                <?php echo $article['content']; ?>
            </div>
        </div>
    </div>
    <div class="text-center mt-4">
        <a href="<?php echo BASE_URL; ?>pages/news.php" class="btn btn-secondary">Back to News</a>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
