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
?>
<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<?php
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

<?php require_once '../includes/footer.php'; ?>
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<script>
$(document).ready(function() {
    $('#content').summernote({
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onImageUpload: function(files) {
                var formData = new FormData();
                formData.append('upload', files[0]);
                formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
                $.ajax({
                    url: 'upload.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        var response = JSON.parse(data);
                        if (response.url) {
                            $('#content').summernote('insertImage', response.url);
                        } else if (response.error) {
                            alert(response.error.message);
                        }
                    },
                    error: function() {
                        alert('Error uploading image.');
                    }
                });
            }
        }
    });

    $('form').on('submit', function() {
        $('#content').val($('#content').summernote('code'));
    });
});
</script>
