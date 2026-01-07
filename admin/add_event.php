<?php
require_once 'init.php';
check_permission('manage_events');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $start_time = $_POST['start_time'];
    $end_time = !empty($_POST['end_time']) ? $_POST['end_time'] : null;
    $location = trim($_POST['location']);

    if (!empty($name) && !empty($start_time) && !empty($location)) {
        $stmt = $pdo->prepare("INSERT INTO events (name, description, start_time, end_time, location) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $description, $start_time, $end_time, $location])) {
            $_SESSION['success_message'] = "Event added successfully!";
            header("Location: events.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Failed to add event.";
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
        <h2 class="mb-4">Add New Event</h2>

        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <div class="card">
            <div class="card-body">
                <form action="add_event.php" method="POST">
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
                    <button type="submit" class="btn btn-primary">Add Event</button>
                    <a href="events.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#description'), {
            ckfinder: {
                uploadUrl: 'upload.php?csrf_token=<?php echo $_SESSION['csrf_token']; ?>'
            },
            contentsCss: ['../assets/css/editor_style.css']
        })
        .catch(error => {
            console.error(error);
        });
</script>

<?php require_once '../includes/footer.php'; ?>
