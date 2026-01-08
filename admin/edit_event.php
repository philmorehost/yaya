<?php
require_once 'init.php';
check_permission('manage_events');

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($event_id <= 0) {
    header("Location: events.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
    $_SESSION['error_message'] = "Event not found.";
    header("Location: events.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';

    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $start_time = $_POST['start_time'];
    $end_time = !empty($_POST['end_time']) ? $_POST['end_time'] : null;
    $location = trim($_POST['location']);

    if (!empty($name) && !empty($start_time) && !empty($location)) {
        $stmt = $pdo->prepare("UPDATE events SET name = ?, description = ?, start_time = ?, end_time = ?, location = ? WHERE id = ?");
        if ($stmt->execute([$name, $description, $start_time, $end_time, $location, $id])) {
            $_SESSION['success_message'] = "Event updated successfully!";
            header("Location: events.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Failed to update event.";
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
        <h2 class="mb-4">Edit Event</h2>

        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <div class="card">
            <div class="card-body">
                <form action="edit_event.php?id=<?php echo $event['id']; ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Event Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($event['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description"><?php echo htmlspecialchars($event['description']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="datetime-local" class="form-control" id="start_time" name="start_time" value="<?php echo date('Y-m-d\TH:i', strtotime($event['start_time'])); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_time" class="form-label">End Time (Optional)</label>
                        <input type="datetime-local" class="form-control" id="end_time" name="end_time" value="<?php echo $event['end_time'] ? date('Y-m-d\TH:i', strtotime($event['end_time'])) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($event['location']); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Event</button>
                    <a href="events.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
<script>
    let editor;
    ClassicEditor
        .create(document.querySelector('#description'), {
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
                const contentArea = document.querySelector('#description');
                if (contentArea) {
                    contentArea.value = editor.getData();
                }
            }
        });
    }
</script>

<?php require_once '../includes/footer.php'; ?>
