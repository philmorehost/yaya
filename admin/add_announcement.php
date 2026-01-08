<?php
require_once 'init.php';
check_permission('manage_announcements');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';

    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $author_id = $_SESSION['admin_id'];

    if (!empty($title) && !empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO announcements (title, content, author_id) VALUES (?, ?, ?)");
        if ($stmt->execute([$title, $content, $author_id])) {
            $_SESSION['success_message'] = "Announcement added successfully!";
            header("Location: announcements.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Failed to add announcement.";
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
        <h2 class="mb-4">Add New Announcement</h2>

        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <div class="card">
            <div class="card-body">
                <form action="add_announcement.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Announcement</button>
                    <a href="announcements.php" class="btn btn-secondary">Cancel</a>
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
