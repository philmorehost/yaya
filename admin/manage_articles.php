<?php require_once dirname(__DIR__) . '/config.php'; ?>
<?php
if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}
?>
<?php require_once dirname(__DIR__) . '/database.php'; ?>
<?php include dirname(__DIR__) . '/includes/header.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Articles</h1>
        <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Create New Article</h4>
        </div>
        <div class="card-body">
            <form action="<?php echo BASE_URL; ?>actions/create_article.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Content</label>
                    <textarea class="form-control" id="content" name="content" rows="10"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Publish Article</button>
            </form>
        </div>
    </div>

    <div class="card mt-5">
        <div class="card-header">
            <h4>Published Articles</h4>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <?php
                $stmt = $db->query("SELECT id, title, created_at FROM Articles ORDER BY created_at DESC");
                $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($articles) {
                    foreach ($articles as $article) {
                        echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                        echo htmlspecialchars($article['title']);
                        echo '<div>';
                        echo '<a href="' . BASE_URL . 'pages/article.php?id=' . $article['id'] . '" class="btn btn-info btn-sm">View</a> ';
                        echo '<a href="edit_article.php?id=' . $article['id'] . '" class="btn btn-warning btn-sm">Edit</a> ';
                        echo '<form action="' . BASE_URL . 'actions/delete_article.php" method="post" style="display:inline-block;" onsubmit="return confirm(\'Are you sure you want to delete this article?\');">';
                        echo '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
                        echo '<input type="hidden" name="id" value="' . $article['id'] . '">';
                        echo '<button type="submit" class="btn btn-danger btn-sm">Delete</button>';
                        echo '</form>';
                        echo '</div>';
                        echo '</li>';
                    }
                } else {
                    echo '<li class="list-group-item">No articles found.</li>';
                }
                ?>
            </ul>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
