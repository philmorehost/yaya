<?php
require_once 'config/db_connect.php';
require_once 'includes/public_header.php';

$events = $pdo->query("SELECT * FROM events ORDER BY start_time DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h2>Our Events</h2>
        <p class="lead">Join us for our upcoming services, programs, and special events.</p>
    </div>

    <div class="row">
        <?php if ($events): ?>
            <?php foreach ($events as $event): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title" style="color: #001f3f;"><?php echo htmlspecialchars($event['name']); ?></h5>
                            <p class="card-text text-muted">
                                <?php echo date('D, M j, Y, g:i a', strtotime($event['start_time'])); ?> -
                                <?php echo date('g:i a', strtotime($event['end_time'])); ?>
                            </p>
                            <div class="card-text flex-grow-1 event-description"><?php echo sanitize_html($event['description']); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center">No events have been scheduled yet. Please check back soon!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- AddToAny BEGIN -->
<div class="a2a_kit a2a_kit_size_32 a2a_floating_style a2a_vertical_style" style="right:0px; top:150px;">
    <a class="a2a_button_facebook"></a>
    <a class="a2a_button_twitter"></a>
    <a class="a2a_button_email"></a>
    <a class="a2a_button_whatsapp"></a>
    <a class="a2a_dd" href="https://www.addtoany.com/share"></a>
</div>
<script async src="https://static.addtoany.com/menu/page.js"></script>
<!-- AddToAny END -->
<?php require_once 'includes/public_footer.php'; ?>
