<?php
require_once '../includes/auth_check.php';
require_once '../config/db_connect.php';

// CSRF Protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../includes/csrf_check.php';
}

// Ensure a CSRF token is available for forms
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';

// Handle Add Event
if (isset($_POST['add_event'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $location = trim($_POST['location']);

    if (!empty($name) && !empty($start_time) && !empty($location)) {
        $stmt = $pdo->prepare("INSERT INTO events (name, description, start_time, end_time, location) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $description, $start_time, $end_time, $location])) {
            $message = '<div class="alert alert-success">Event added successfully!</div>';
        } else {
            $message = '<div class="alert alert-danger">Failed to add event.</div>';
        }
    } else {
        $message = '<div class="alert alert-warning">Please fill in all required fields.</div>';
    }
}

// Handle Update Event
if (isset($_POST['update_event'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $location = trim($_POST['location']);

    if (!empty($name) && !empty($start_time) && !empty($location)) {
        $stmt = $pdo->prepare("UPDATE events SET name = ?, description = ?, start_time = ?, end_time = ?, location = ? WHERE id = ?");
        if ($stmt->execute([$name, $description, $start_time, $end_time, $location, $id])) {
            $message = '<div class="alert alert-success">Event updated successfully!</div>';
        } else {
            $message = '<div class="alert alert-danger">Failed to update event.</div>';
        }
    } else {
        $message = '<div class="alert alert-warning">Please fill in all required fields.</div>';
    }
}

// Handle Delete Event
if (isset($_POST['delete_event'])) {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    if ($stmt->execute([$id])) {
        $message = '<div class="alert alert-success">Event deleted successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Failed to delete event.</div>';
    }
}

// Fetch Data
$events = $pdo->query("SELECT * FROM events ORDER BY start_time DESC")->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Events Management</h2>

        <?php echo $message; ?>

        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addEventModal">
            <i class="fas fa-plus"></i> Add New Event
        </button>

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
                                <td><?php echo htmlspecialchars($event['description']); ?></td>
                                <td><?php echo date('M j, Y, g:i A', strtotime($event['start_time'])); ?></td>
                                <td><?php echo $event['end_time'] ? date('M j, Y, g:i A', strtotime($event['end_time'])) : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars($event['location']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary edit-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editEventModal"
                                            data-id="<?php echo $event['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($event['name']); ?>"
                                            data-description="<?php echo htmlspecialchars($event['description']); ?>"
                                            data-start_time="<?php echo $event['start_time']; ?>"
                                            data-end_time="<?php echo $event['end_time']; ?>"
                                            data-location="<?php echo htmlspecialchars($event['location']); ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
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

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEventModalLabel">Add New Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="events.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Event Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_time" class="form-label">End Time (Optional)</label>
                        <input type="datetime-local" class="form-control" id="end_time" name="end_time">
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_event" class="btn btn-primary">Save Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Event Modal -->
<div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEventModalLabel">Edit Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="events.php" method="POST">
                    <input type="hidden" name="id" id="edit-id">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="edit-name" class="form-label">Event Name</label>
                        <input type="text" class="form-control" id="edit-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit-description" name="description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit-start_time" class="form-label">Start Time</label>
                        <input type="datetime-local" class="form-control" id="edit-start_time" name="start_time" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-end_time" class="form-label">End Time (Optional)</label>
                        <input type="datetime-local" class="form-control" id="edit-end_time" name="end_time">
                    </div>
                    <div class="mb-3">
                        <label for="edit-location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="edit-location" name="location" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="update_event" class="btn btn-primary">Update Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var editEventModal = document.getElementById('editEventModal');
    editEventModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var name = button.getAttribute('data-name');
        var description = button.getAttribute('data-description');
        var startTime = button.getAttribute('data-start_time');
        var endTime = button.getAttribute('data-end_time');
        var location = button.getAttribute('data-location');

        var modalTitle = editEventModal.querySelector('.modal-title');
        var modalBodyInputId = editEventModal.querySelector('#edit-id');
        var modalBodyInputName = editEventModal.querySelector('#edit-name');
        var modalBodyInputDescription = editEventModal.querySelector('#edit-description');
        var modalBodyInputStartTime = editEventModal.querySelector('#edit-start_time');
        var modalBodyInputEndTime = editEventModal.querySelector('#edit-end_time');
        var modalBodyInputLocation = editEventModal.querySelector('#edit-location');

        modalTitle.textContent = 'Edit Event: ' + name;
        modalBodyInputId.value = id;
        modalBodyInputName.value = name;
        modalBodyInputDescription.value = description;
        modalBodyInputStartTime.value = startTime;
        modalBodyInputEndTime.value = endTime;
        modalBodyInputLocation.value = location;
    });
});
</script>
