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
?>
<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<?php
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
