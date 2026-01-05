<?php
require_once 'init.php';
check_permission('manage_settings');
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';

    // Update text-based settings
    update_setting('hero_type', $_POST['hero_type']);
    update_setting('hero_video_url', $_POST['hero_video_url']);
    update_setting('upcoming_event_id', $_POST['upcoming_event_id']);
    update_setting('latest_sermon_id', $_POST['latest_sermon_id']);

    // Function to handle a file upload
    function handle_upload($file_key, $setting_name, $prefix = '') {
        if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] == 0) {
            $allowed_types = ['image/png', 'image/jpeg', 'image/gif', 'image/svg+xml'];
            $allowed_extensions = ['png', 'jpg', 'jpeg', 'gif', 'svg'];
            $max_size = 2 * 1024 * 1024; // 2MB

            $file_info = $_FILES[$file_key];
            $file_ext = strtolower(pathinfo($file_info['name'], PATHINFO_EXTENSION));

            if (in_array($file_info['type'], $allowed_types) && in_array($file_ext, $allowed_extensions) && $file_info['size'] <= $max_size) {
                $target_dir = "../uploads/";
                // To prevent file overwrites, create a unique name
                $target_file = $target_dir . $prefix . uniqid() . '.' . $file_ext;

                if (move_uploaded_file($file_info["tmp_name"], $target_file)) {
                    // Important: Save the path relative to the web root, not the file system
                    $url_path = 'uploads/' . basename($target_file);
                    update_setting($setting_name, $url_path);
                } else {
                    $_SESSION['error_message'] = "Failed to move uploaded file '$file_key'.";
                }
            } else {
                $_SESSION['error_message'] = "Invalid file type or size for '$file_key'.";
            }
        }
    }

    // Handle Hero Image and Logo Uploads
    handle_upload('hero_image', 'hero_image_url', 'hero_');
    handle_upload('site_logo', 'site_logo_url', 'logo_');

    if (!isset($_SESSION['error_message'])) {
        $_SESSION['success_message'] = "Homepage settings saved successfully!";
    }

    header('Location: homepage_settings.php');
    exit();
}

$events = $pdo->query("SELECT * FROM events ORDER BY start_time DESC")->fetchAll(PDO::FETCH_ASSOC);
$sermons = $pdo->query("SELECT * FROM media ORDER BY publication_date DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Homepage Settings</h2>

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

        <div class="card">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <h5 class="mb-3">Site Branding</h5>
                    <div class="mb-3">
                        <label for="site_logo" class="form-label">Site Logo</label>
                        <input class="form-control" type="file" id="site_logo" name="site_logo">
                        <?php if (get_setting('site_logo_url')): ?>
                            <img src="../<?php echo get_setting('site_logo_url'); ?>" class="img-thumbnail mt-2" width="150">
                        <?php endif; ?>
                    </div>

                    <hr>

                    <h5 class="mb-3">Hero Section</h5>
                    <div class="mb-3">
                        <label class="form-label">Hero Type</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="hero_type" id="hero_type_image" value="image" <?php if(get_setting('hero_type') == 'image') echo 'checked'; ?>>
                            <label class="form-check-label" for="hero_type_image">Image</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="hero_type" id="hero_type_video" value="video" <?php if(get_setting('hero_type') == 'video') echo 'checked'; ?>>
                            <label class="form-check-label" for="hero_type_video">Video URL</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="hero_image" class="form-label">Hero Image</label>
                        <input class="form-control" type="file" id="hero_image" name="hero_image">
                        <?php if (get_setting('hero_image_url')): ?>
                            <img src="../<?php echo get_setting('hero_image_url'); ?>" class="img-thumbnail mt-2" width="200">
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="hero_video_url" class="form-label">Hero Video URL</label>
                        <input type="text" class="form-control" id="hero_video_url" name="hero_video_url" value="<?php echo get_setting('hero_video_url'); ?>" placeholder="e.g., https://example.com/video.mp4">
                    </div>

                    <hr>

                    <h5 class="mb-3">Other Sections</h5>
                    <div class="mb-3">
                        <label for="upcoming_event_id" class="form-label">Upcoming Event Countdown</label>
                        <select class="form-select" id="upcoming_event_id" name="upcoming_event_id">
                            <option value="">Select Event</option>
                            <?php foreach ($events as $event): ?>
                                <option value="<?php echo $event['id']; ?>" <?php if(get_setting('upcoming_event_id') == $event['id']) echo 'selected'; ?>><?php echo htmlspecialchars($event['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="latest_sermon_id" class="form-label">Latest Sermon</label>
                        <select class="form-select" id="latest_sermon_id" name="latest_sermon_id">
                            <option value="">Select Sermon</option>
                            <?php foreach ($sermons as $sermon): ?>
                                <option value="<?php echo $sermon['id']; ?>" <?php if(get_setting('latest_sermon_id') == $sermon['id']) echo 'selected'; ?>><?php echo htmlspecialchars($sermon['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
