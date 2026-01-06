<?php
require_once 'init.php';
check_permission('manage_events');

// Handle form submissions for add, edit, delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';

    if (isset($_POST['add_event'])) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $date = trim($_POST['date']);
        $location = trim($_POST['location']);

        if (!empty($title) && !empty($description) && !empty($date) && !empty($location)) {
            $stmt = $pdo->prepare("INSERT INTO events (title, description, date, location) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$title, $description, $date, $location])) {
                $_SESSION['success_message'] = "Event added successfully!";
            } else {
                $_SESSION['error_message'] = "Failed to add event.";
            }
        } else {
            $_SESSION['error_message'] = "Please fill in all required fields.";
        }
        header("Location: events.php");
        exit();
    }

    if (isset($_POST['update_event'])) {
        $id = $_POST['id'];
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $date = trim($_POST['date']);
        $location = trim($_POST['location']);

        if (!empty($title) && !empty($description) && !empty($date) && !empty($location)) {
            $stmt = $pdo->prepare("UPDATE events SET title = ?, description = ?, date = ?, location = ? WHERE id = ?");
            if ($stmt->execute([$title, $description, $date, $location, $id])) {
                $_SESSION['success_message'] = "Event updated successfully!";
            } else {
                $_SESSION['error_message'] = "Failed to update event.";
            }
        } else {
            $_SESSION['error_message'] = "Please fill in all required fields.";
        }
        header("Location: events.php");
        exit();
    }

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
}

$events = $pdo->query("SELECT * FROM events ORDER BY date DESC")->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Manage Events</h2>

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
                                <th>Title</th>
                                <th>Date</th>
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($event['title']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($event['date'])); ?></td>
                                <td><?php echo htmlspecialchars($event['location']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary edit-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editEventModal"
                                            data-id="<?php echo $event['id']; ?>"
                                            data-title="<?php echo htmlspecialchars($event['title']); ?>"
                                            data-description="<?php echo htmlspecialchars($event['description']); ?>"
                                            data-date="<?php echo $event['date']; ?>"
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
<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">Add New Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="events.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="add-description" name="description" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" required>
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
<div class="modal fade" id="editEventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">Edit Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="events.php" method="POST">
                    <input type="hidden" name="id" id="edit-id">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="edit-title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="edit-title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit-description" name="description" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit-date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="edit-date" name="date" required>
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

<script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let addEditor;
    let editEditor;

    ClassicEditor
        .create(document.querySelector('#add-description'), {
            ckfinder: {
                uploadUrl: 'upload.php'
            }
        })
        .then(editor => {
            addEditor = editor;
        })
        .catch(error => {
            console.error(error);
        });

    ClassicEditor
        .create(document.querySelector('#edit-description'), {
            ckfinder: {
                uploadUrl: 'upload.php'
            }
        })
        .then(editor => {
            editEditor = editor;
        })
        .catch(error => {
            console.error(error);
        });

    var editModal = document.getElementById('editEventModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var title = button.getAttribute('data-title');
        var description = button.getAttribute('data-description');
        var date = button.getAttribute('data-date');
        var location = button.getAttribute('data-location');

        var modalTitle = editModal.querySelector('.modal-title');
        var idInput = editModal.querySelector('#edit-id');
        var titleInput = editModal.querySelector('#edit-title');
        var dateInput = editModal.querySelector('#edit-date');
        var locationInput = editModal.querySelector('#edit-location');

        modalTitle.textContent = 'Edit Event: ' + title;
        idInput.value = id;
        titleInput.value = title;
        editEditor.setData(description);
        dateInput.value = date;
        locationInput.value = location;
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
