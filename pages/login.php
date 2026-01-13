<?php require_once dirname(__DIR__) . '/config.php'; ?>
<?php include dirname(__DIR__) . '/includes/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h2>User Login</h2>
                </div>
                <div class="card-body">
                    <?php
                    if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
                        echo '<div class="alert alert-danger" role="alert">';
                        foreach ($_SESSION['errors'] as $error) {
                            echo '<p class="mb-0">' . $error . '</p>';
                        }
                        echo '</div>';
                        unset($_SESSION['errors']);
                    }
                    ?>
                    <form action="<?php echo BASE_URL; ?>actions/login_user.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
