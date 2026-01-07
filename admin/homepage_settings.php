<?php
require_once 'init.php';
if (!check_permission('manage_settings')) {
    $_SESSION['error_message'] = 'You do not have permission to access this page.';
    header('Location: dashboard.php');
    exit;
}
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';

    // Handle Logo Upload
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $target_file = $target_dir . basename($_FILES["site_logo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["site_logo"]["tmp_name"]);
        if($check !== false) {
            // Allow certain file formats
            if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif" ) {
                if (move_uploaded_file($_FILES["site_logo"]["tmp_name"], $target_file)) {
                    // Path stored should be relative to the root
                    $logo_url = "uploads/" . basename($_FILES["site_logo"]["name"]);
                    update_setting('site_logo_url', $logo_url);
                }
            }
        }
    }

    update_setting('hero_type', $_POST['hero_type']);
    update_setting('hero_image_url', $_POST['hero_image_url']);
    update_setting('hero_video_url', $_POST['hero_video_url']);
    update_setting('upcoming_event_id', $_POST['upcoming_event_id']);

    $_SESSION['success_message'] = "Homepage settings updated successfully!";
    header('Location: homepage_settings.php');
    exit;
}

// Fetch Data
$events = $pdo->query("SELECT id, name FROM events ORDER BY start_time DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Homepage Settings</h2>

        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="mb-3">
                        <label for="site_logo" class="form-label">Site Logo</label>
                        <input type="file" class="form-control" id="site_logo" name="site_logo">
                        <?php $logo_url = get_setting('site_logo_url'); ?>
                        <?php if ($logo_url): ?>
                            <div class="mt-2">
                                <small>Current Logo:</small><br>
                                <img src="../<?php echo htmlspecialchars($logo_url); ?>" alt="Site Logo" style="max-height: 50px; background-color: #fff; padding: 5px;">
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="hero_type" class="form-label">Hero Section Type</label>
                        <select class="form-select" id="hero_type" name="hero_type">
                            <option value="image" <?php if(get_setting('hero_type') == 'image') echo 'selected'; ?>>Image</option>
                            <option value="video" <?php if(get_setting('hero_type') == 'video') echo 'selected'; ?>>Video</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="hero_image_url" class="form-label">Hero Image URL</label>
                        <input type="text" class="form-control" id="hero_image_url" name="hero_image_url" value="<?php echo htmlspecialchars(get_setting('hero_image_url')); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="hero_video_url" class="form-label">Hero Video URL (MP4)</label>
                        <input type="text" class="form-control" id="hero_video_url" name="hero_video_url" value="<?php echo htmlspecialchars(get_setting('hero_video_url')); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="upcoming_event_id" class="form-label">Featured Upcoming Event</label>
                        <select class="form-select" id="upcoming_event_id" name="upcoming_event_id">
                            <option value="">None</option>
                            <?php foreach($events as $event): ?>
                                <option value="<?php echo $event['id']; ?>" <?php if(get_setting('upcoming_event_id') == $event['id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($event['name']); ?>
                                </option>
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
