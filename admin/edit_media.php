<?php
require_once 'init.php';
check_permission('manage_media');

$media_id = $_GET['id'] ?? null;
if (!$media_id) {
    header("Location: media.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM media WHERE id = ?");
$stmt->execute([$media_id]);
$media_post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$media_post) {
    header("Location: media.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';

    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $publication_date = trim($_POST['publication_date']);

    if (!empty($title) && !empty($content) && !empty($publication_date)) {
        $stmt = $pdo->prepare("UPDATE media SET title = ?, content = ?, publication_date = ? WHERE id = ?");
        if ($stmt->execute([$title, $content, $publication_date, $media_id])) {
            $_SESSION['success_message'] = "Media post updated successfully!";
            header("Location: media.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Failed to update media post.";
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
        <h2 class="mb-4">Edit Media Post</h2>

        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <div class="card">
            <div class="card-body">
                <form action="edit_media.php?id=<?php echo $media_id; ?>" method="POST">
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

<script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
<script>
    let editor;
    ClassicEditor
        .create(document.querySelector('#content'), {
            ckfinder: {
                uploadUrl: 'upload.php?csrf_token=<?php echo $_SESSION['csrf_token']; ?>'
            },
            contentsCss: ['../assets/css/editor_style.css']
        })
        .then(newEditor => {
            editor = newEditor;
        })
        .catch(error => {
            console.error(error);
        });

    // More robustly ensure the textarea is updated before the form submits
    // by attaching the logic to the button's click event.
    const submitButton = document.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.addEventListener('click', function() {
            if (editor) {
                const contentArea = document.querySelector('#content');
                if (contentArea) {
                    contentArea.value = editor.getData();
                }
            }
        });
    }
</script>

<?php require_once '../includes/footer.php'; ?>
