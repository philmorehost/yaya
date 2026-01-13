<?php require_once dirname(__DIR__) . '/config.php'; ?>
<?php require_once dirname(__DIR__) . '/database.php'; ?>
<?php include dirname(__DIR__) . '/includes/header.php'; ?>

<div class="container mt-5">
    <div class="text-center mb-5">
        <h1>The Watchmen News</h1>
        <p class="lead">Financial literacy tips for teens and investment advice for adults.</p>
    </div>

    <div class="row">
        <?php
        $stmt = $db->query("SELECT id, title, content, created_at FROM Articles ORDER BY created_at DESC");
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($articles) {
            foreach ($articles as $article) {
                echo '<div class="col-md-6 col-lg-4 mb-4">';
                echo '<div class="card h-100">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . htmlspecialchars($article['title']) . '</h5>';
                echo '<p class="card-text">' . substr(strip_tags($article['content']), 0, 150) . '...</p>';
                echo '<a href="' . BASE_URL . 'pages/article.php?id=' . $article['id'] . '" class="btn btn-primary">Read More</a>';
                echo '</div>';
                echo '<div class="card-footer text-muted">';
                echo 'Posted on ' . date('F j, Y', strtotime($article['created_at']));
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<div class="col"><p class="text-center">No articles have been published yet.</p></div>';
        }
        ?>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
