<?php
require_once 'init.php';
check_permission('manage_media');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';

    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $publication_date = trim($_POST['publication_date']);

    if (!empty($title) && !empty($content) && !empty($publication_date)) {
        $stmt = $pdo->prepare("INSERT INTO media (title, content, publication_date) VALUES (?, ?, ?)");
        if ($stmt->execute([$title, $content, $publication_date])) {
            $_SESSION['success_message'] = "Media post added successfully!";
            header("Location: media.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Failed to add media post.";
        }
    } else {
        $_SESSION['error_message'] = "Please fill in all required fields.";
    }
}

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Add New Media Post</h2>

        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <div class="card">
            <div class="card-body">
                <form action="add_media.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="publication_date" class="form-label">Publication Date</label>
                        <input type="date" class="form-control" id="publication_date" name="publication_date" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Post</button>
                    <a href="media.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#content'), {
            ckfinder: {
                uploadUrl: 'upload.php'
            }
        })
        .catch(error => {
            console.error(error);
        });
</script>

<?php require_once '../includes/footer.php'; ?>
