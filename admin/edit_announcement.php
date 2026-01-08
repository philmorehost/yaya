<?php
require_once 'init.php';
check_permission('manage_announcements');

$announcement_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($announcement_id <= 0) {
    header("Location: announcements.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM announcements WHERE id = ?");
$stmt->execute([$announcement_id]);
$announcement = $stmt->fetch();

if (!$announcement) {
    $_SESSION['error_message'] = "Announcement not found.";
    header("Location: announcements.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';

    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $id = $_POST['id'];

    if (!empty($title) && !empty($content)) {
        $stmt = $pdo->prepare("UPDATE announcements SET title = ?, content = ? WHERE id = ?");
        if ($stmt->execute([$title, $content, $id])) {
            $_SESSION['success_message'] = "Announcement updated successfully!";
            header("Location: announcements.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Failed to update announcement.";
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
        <h2 class="mb-4">Edit Announcement</h2>

        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <div class="card">
            <div class="card-body">
                <form action="edit_announcement.php?id=<?php echo $announcement['id']; ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="id" value="<?php echo $announcement['id']; ?>">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($announcement['title']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="10" required><?php echo htmlspecialchars($announcement['content']); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Announcement</button>
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

    document.querySelector('form').addEventListener('submit', function(event) {
        if (editor) {
            document.querySelector('#content').value = editor.getData();
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>
