<?php
require_once 'init.php';
check_permission('manage_announcements');

// Handle form submissions for add, edit, delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';

    if (isset($_POST['add_announcement'])) {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $author_id = $_SESSION['admin_id']; // Get logged-in admin's ID

        if (!empty($title) && !empty($content)) {
            $stmt = $pdo->prepare("INSERT INTO announcements (title, content, author_id) VALUES (?, ?, ?)");
            if ($stmt->execute([$title, $content, $author_id])) {
                $_SESSION['success_message'] = "Announcement added successfully!";
            } else {
                $_SESSION['error_message'] = "Failed to add announcement.";
            }
        } else {
            $_SESSION['error_message'] = "Please fill in all required fields.";
        }
        header("Location: announcements.php");
        exit();
    }

    if (isset($_POST['update_announcement'])) {
        $id = $_POST['id'];
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        if (!empty($title) && !empty($content)) {
            $stmt = $pdo->prepare("UPDATE announcements SET title = ?, content = ? WHERE id = ?");
            if ($stmt->execute([$title, $content, $id])) {
                $_SESSION['success_message'] = "Announcement updated successfully!";
            } else {
                $_SESSION['error_message'] = "Failed to update announcement.";
            }
        } else {
            $_SESSION['error_message'] = "Please fill in all required fields.";
        }
        header("Location: announcements.php");
        exit();
    }

    if (isset($_POST['delete_announcement'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
        if ($stmt->execute([$id])) {
            $_SESSION['success_message'] = "Announcement deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to delete announcement.";
        }
        header("Location: announcements.php");
        exit();
    }
}

// Fetch announcements with author's email
$announcements = $pdo->query("
    SELECT a.*, u.email as author_email
    FROM announcements a
    LEFT JOIN admin_users u ON a.author_id = u.id
    ORDER BY a.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Manage Announcements</h2>

        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . $_SESSION['success_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . $_SESSION['error_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
            <i class="fas fa-plus"></i> Add New Announcement
        </button>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Announcements</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($announcements as $announcement): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($announcement['title']); ?></td>
                                <td><?php echo htmlspecialchars($announcement['author_email'] ?? 'N/A'); ?></td>
                                <td><?php echo date('M j, Y, g:i A', strtotime($announcement['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary edit-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editAnnouncementModal"
                                            data-id="<?php echo $announcement['id']; ?>"
                                            data-title="<?php echo htmlspecialchars($announcement['title']); ?>"
                                            data-content="<?php echo htmlspecialchars($announcement['content']); ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="announcements.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                        <input type="hidden" name="id" value="<?php echo $announcement['id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <button type="submit" name="delete_announcement" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Announcement Modal -->
<div class="modal fade" id="addAnnouncementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">Add New Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="announcements.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" name="content" rows="10" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_announcement" class="btn btn-primary">Save Announcement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Announcement Modal -->
<div class="modal fade" id="editAnnouncementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">Edit Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="announcements.php" method="POST">
                    <input type="hidden" name="id" id="edit-id">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="edit-title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="edit-title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-content" class="form-label">Content</label>
                        <textarea class="form-control" id="edit-content" name="content" rows="10" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="update_announcement" class="btn btn-primary">Update Announcement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var editModal = document.getElementById('editAnnouncementModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var title = button.getAttribute('data-title');
        var content = button.getAttribute('data-content');

        var modalTitle = editModal.querySelector('.modal-title');
        var idInput = editModal.querySelector('#edit-id');
        var titleInput = editModal.querySelector('#edit-title');
        var contentInput = editModal.querySelector('#edit-content');

        modalTitle.textContent = 'Edit Announcement: ' + title;
        idInput.value = id;
        titleInput.value = title;
        contentInput.value = content;
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
