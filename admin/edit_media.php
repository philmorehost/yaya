<?php
require_once '../includes/auth_check.php';
require_once '../config/db_connect.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';
    $title = $_POST['title'];
    $content = $_POST['content'];
    $publication_date = $_POST['publication_date'];

    $stmt = $pdo->prepare("UPDATE media SET title = ?, content = ?, publication_date = ? WHERE id = ?");
    $stmt->execute([$title, $content, $publication_date, $id]);
    header('Location: media.php');
}

$stmt = $pdo->prepare("SELECT * FROM media WHERE id = ?");
$stmt->execute([$id]);
$media_post = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Edit Media Post</h2>
        <div class="card">
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($media_post['title']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="10" required><?php echo htmlspecialchars($media_post['content']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="publication_date" class="form-label">Publication Date</label>
                        <input type="date" class="form-control" id="publication_date" name="publication_date" value="<?php echo htmlspecialchars($media_post['publication_date']); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Post</button>
                    <a href="media.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
