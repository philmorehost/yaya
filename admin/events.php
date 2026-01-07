<?php
require_once 'init.php';
if (!check_permission('manage_events')) {
    $_SESSION['error_message'] = 'You do not have permission to access this page.';
    header('Location: dashboard.php');
    exit;
}

// CSRF Protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../includes/csrf_check.php';
}

// Handle Delete Event
if (isset($_POST['delete_event'])) {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['success_message'] = "Event deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to delete event.";
    }
    header("Location: events.php");
    exit();
}

// Fetch Data
$events = $pdo->query("SELECT * FROM events ORDER BY start_time DESC")->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Events Management</h2>

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

        <a href="add_event.php" class="btn btn-primary mb-3">
            <i class="fas fa-plus"></i> Add New Event
        </a>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Events</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($event['name']); ?></td>
                                <td><?php echo htmlspecialchars(substr(strip_tags($event['description']), 0, 50)); ?>...</td>
                                <td><?php echo date('M j, Y, g:i A', strtotime($event['start_time'])); ?></td>
                                <td><?php echo $event['end_time'] ? date('M j, Y, g:i A', strtotime($event['end_time'])) : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars($event['location']); ?></td>
                                <td>
                                    <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="events.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                        <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <button type="submit" name="delete_event" class="btn btn-sm btn-outline-danger">
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
