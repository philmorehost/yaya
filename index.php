<?php
// Redirect to installer if the config file doesn't exist.
if (!file_exists('config/db_connect.php')) {
    header('Location: installer/');
    exit;
}

require_once 'config/db_connect.php';
require_once 'includes/public_header.php';
require_once 'includes/helpers.php';

// Hero Section Data
$hero_type = get_setting('hero_type');
$hero_image_url = get_setting('hero_image_url');
$hero_video_url = get_setting('hero_video_url');

// Upcoming Event Data
$upcoming_event_id = get_setting('upcoming_event_id');
if ($upcoming_event_id) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$upcoming_event_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Latest Sermon Data
$latest_sermon_id = get_setting('latest_sermon_id');
if ($latest_sermon_id) {
    $stmt = $pdo->prepare("SELECT * FROM media WHERE id = ?");
    $stmt->execute([$latest_sermon_id]);
    $sermon = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Announcements
$announcements = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Hero Section -->
<header class="hero-section" style="<?php echo ($hero_type == 'image' && $hero_image_url) ? 'background-image: url(' . htmlspecialchars($hero_image_url) . '); background-size: cover; background-position: center;' : ''; ?>">
    <?php if ($hero_type == 'video' && $hero_video_url): ?>
        <video playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop">
            <source src="<?php echo htmlspecialchars($hero_video_url); ?>" type="video/mp4">
        </video>
    <?php endif; ?>
    <div class="overlay"></div>
    <div class="container text-center" style="z-index: 1;">
        <h1 class="display-4">Welcome to RCCG YAYA</h1>
        <p class="lead">Young Adults and Youth Affairs</p>
    </div>
</header>

<div class="container">
    <!-- Upcoming Event Countdown -->
    <?php if (isset($event) && $event): ?>
    <div class="countdown-card text-center">
        <h3 class="mb-3"><?php echo htmlspecialchars($event['name']); ?></h3>
        <div id="countdown" class="display-4" style="font-weight: bold;"></div>
    </div>
    <?php endif; ?>
</div>

<div class="container my-5">
    <div class="row">
        <!-- Latest Sermon -->
        <div class="col-md-8">
            <h2 class="mb-4">Latest Sermon</h2>
            <?php if (isset($sermon) && $sermon): ?>
            <div class="card sermon-card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($sermon['title']); ?></h5>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars(substr($sermon['content'], 0, 200))); ?>...</p>
                    <a href="media.php#post-<?php echo $sermon['id']; ?>" class="btn btn-primary" style="background-color: #007bff; border-color: #007bff;">Read More</a>
                </div>
            </div>
            <?php else: ?>
            <p>No sermon has been featured yet.</p>
            <?php endif; ?>
        </div>

        <!-- Announcements -->
        <div class="col-md-4">
            <h2 class="mb-4">Announcements</h2>
            <div class="list-group">
                <?php if ($announcements): ?>
                    <?php foreach ($announcements as $announcement): ?>
                        <div class="list-group-item list-group-item-action flex-column align-items-start">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1"><?php echo htmlspecialchars($announcement['title']); ?></h5>
                                <small><?php echo date('M d', strtotime($announcement['created_at'])); ?></small>
                            </div>
                            <p class="mb-1"><?php echo htmlspecialchars(substr($announcement['content'], 0, 100)); ?>...</p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                <p>No recent announcements.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (isset($event) && $event): ?>
<script>
    // Countdown Timer
    var countDownDate = new Date("<?php echo $event['start_time']; ?>").getTime();
    var x = setInterval(function() {
        var now = new Date().getTime();
        var distance = countDownDate - now;
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("countdown").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";

        if (distance < 0) {
            clearInterval(x);
            document.getElementById("countdown").innerHTML = "EVENT STARTED";
        }
    }, 1000);
</script>
<?php endif; ?>

<?php require_once 'includes/public_footer.php'; ?>
