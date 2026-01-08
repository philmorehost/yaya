<?php
require_once 'config/db_connect.php';
require_once 'includes/public_header.php';

$media_posts = $pdo->query("SELECT * FROM media ORDER BY publication_date DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h2>Media & Sermons</h2>
        <p class="lead">Engage with our latest sermons, articles, and media content.</p>
    </div>

    <div class="row">
        <?php if ($media_posts): ?>
            <?php foreach ($media_posts as $post): ?>
                <div class="col-md-12 mb-4" id="post-<?php echo $post['id']; ?>">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #001f3f;"><?php echo htmlspecialchars($post['title']); ?></h5>
                            <p class="card-text text-muted"><?php echo date('F j, Y', strtotime($post['publication_date'])); ?></p>
                            <hr>
                            <div class="card-text"><?php echo sanitize_html($post['content']); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center">No media content has been posted yet. Please check back soon!</p>
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
