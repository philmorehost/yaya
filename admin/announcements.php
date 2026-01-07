<?php
require_once 'init.php';
if (!check_permission('manage_announcements')) {
    $_SESSION['error_message'] = 'You do not have permission to access this page.';
    header('Location: dashboard.php');
    exit;
}

// Handle form submissions for delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';

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

        <a href="add_announcement.php" class="btn btn-primary mb-3">
            <i class="fas fa-plus"></i> Add New Announcement
        </a>

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
                                    <a href="edit_announcement.php?id=<?php echo $announcement['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
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

<?php require_once '../includes/footer.php'; ?>
