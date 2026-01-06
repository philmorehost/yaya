</main>
    <div class="footer-menu">
        <?php if (isset($_SESSION['is_loggedin']) && $_SESSION['is_loggedin'] === true): ?>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="<?php echo BASE_URL; ?>admin/manage_users.php"><i class="fas fa-users"></i><div>Users</div></a>
                <a href="<?php echo BASE_URL; ?>admin/manage_articles.php"><i class="fas fa-newspaper"></i><div>Articles</div></a>
                <a href="<?php echo BASE_URL; ?>admin/"><i class="fas fa-tachometer-alt"></i><div>Dashboard</div></a>
                <a href="<?php echo BASE_URL; ?>admin/notifications.php"><i class="fas fa-bell"></i><div>Notifications</div></a>
                <a href="<?php echo BASE_URL; ?>admin/settings.php"><i class="fas fa-cog"></i><div>Settings</div></a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>pages/apply.php"><i class="fas fa-file-alt"></i><div>Apply</div></a>
                <a href="<?php echo BASE_URL; ?>pages/news.php"><i class="fas fa-newspaper"></i><div>News</div></a>
                <a href="<?php echo BASE_URL; ?>pages/dashboard.php"><i class="fas fa-tachometer-alt"></i><div>Dashboard</div></a>
                <a href="<?php echo BASE_URL; ?>pages/notifications.php"><i class="fas fa-bell"></i><div>Notifications</div></a>
                <a href="<?php echo BASE_URL; ?>pages/profile.php"><i class="fas fa-user"></i><div>Profile</div></a>
            <?php endif; ?>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>pages/apply.php"><i class="fas fa-file-alt"></i><div>Apply</div></a>
            <a href="<?php echo BASE_URL; ?>pages/news.php"><i class="fas fa-newspaper"></i><div>News</div></a>
            <a href="<?php echo BASE_URL; ?>pages/login.php"><i class="fas fa-sign-in-alt"></i><div>Login</div></a>
            <a href="<?php echo BASE_URL; ?>pages/register.php"><i class="fas fa-user-plus"></i><div>Register</div></a>
        <?php endif; ?>
    </div>

    <?php
    $support_phone = '';
    if (isset($db) && $db instanceof PDO) {
        try {
            $stmt = $db->query("SELECT setting_value FROM AdminSettings WHERE setting_key = 'support_phone'");
            $support_phone = $stmt->fetchColumn();
        } catch (PDOException $e) {
            // Suppress error
        }
    }
    if ($support_phone):
    ?>
    <a href="https://wa.me/<?php echo htmlspecialchars($support_phone); ?>" class="whatsapp-mentor" target="_blank">
        <i class="fab fa-whatsapp"></i> Speak to a Financial Mentor
    </a>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
