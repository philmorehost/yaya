<?php require_once __DIR__ . '/config.php'; ?>
<?php require_once __DIR__ . '/database.php'; ?>
<?php include __DIR__ . '/includes/header.php'; ?>

<?php
$settings = [];
try {
    $stmt = $db->query("SELECT setting_key, setting_value FROM AdminSettings");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    // Log the error or handle it gracefully
    // For now, we'll just suppress the error
}
?>

<div class="container mt-5">
    <?php if (isset($settings['hero_image'])): ?>
        <div class="hero-section" style="background-image: url('<?php echo BASE_URL . htmlspecialchars($settings['hero_image']); ?>');">
    <?php else: ?>
        <div class="hero-section">
    <?php endif; ?>
        <h1>Empowering Generations, Securing Futures.</h1>
        <p class="lead">From your first savings account to your first business venture, Watchmen Finance Hub is here to help teens, youths, and adults thrive together.</p>
        <p class="h4 text-secondary"><strong>ZERO INTEREST ON ALL MONEY BORROWED</strong></p>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card info-card">
                <div class="card-body">
                    <h2 class="card-title">For Teens</h2>
                    <p class="card-text">Start small, learn big. Micro-loans for educational tools and projects.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card info-card">
                <div class="card-body">
                    <h2 class="card-title">For Youth</h2>
                    <p class="card-text">Fuel your ambition. Equipment loans and startup capital.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card info-card">
                <div class="card-body">
                    <h2 class="card-title">For Adults</h2>
                    <p class="card-text">Build your legacy. Personal, mortgage, and business expansion loans.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-5">
        <div class="card-body text-center">
            <h2 class="card-title">REGISTRATION FEE: #4,000</h2>
            <p class="card-text">Weekly Contribution: #1,200</p>
            <a href="<?php echo BASE_URL; ?>pages/apply.php" class="btn btn-primary btn-lg mt-3">Apply Now</a>
        </div>
    </div>

    <?php if (isset($settings['support_phone'])): ?>
    <div class="whatsapp-support">
        <a href="https://wa.me/<?php echo htmlspecialchars($settings['support_phone']); ?>" target="_blank">
            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp Support">
        </a>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
