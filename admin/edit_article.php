<?php require_once dirname(__DIR__) . '/config.php'; ?>
<?php
if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}
?>
<?php require_once dirname(__DIR__) . '/database.php'; ?>
<?php
if (!isset($_GET['id'])) {
    header('Location: ' . BASE_URL . 'admin/manage_articles.php');
    exit;
}
$stmt = $db->prepare("SELECT * FROM Articles WHERE id = :id");
$stmt->execute([':id' => $_GET['id']]);
$article = $stmt->fetch();
if (!$article) {
    header('Location: ' . BASE_URL . 'admin/manage_articles.php');
    exit;
}
?>
<?php include dirname(__DIR__) . '/includes/header.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Article</h1>
        <a href="manage_articles.php" class="btn btn-secondary">Back to Articles</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Edit Article #<?php echo $article['id']; ?></h4>
        </div>
        <div class="card-body">
            <form action="<?php echo BASE_URL; ?>actions/update_article.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" value="<?php echo $article['id']; ?>">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($article['title']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Content</label>
                    <textarea class="form-control" id="content" name="content" rows="10"><?php echo htmlspecialchars($article['content']); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update Article</button>
            </form>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
