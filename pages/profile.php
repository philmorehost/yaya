<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';
require_once dirname(__DIR__) . '/includes/utils.php';

if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true) {
    header('Location: ' . BASE_URL . 'pages/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user = null;

try {
    $stmt = $db->prepare("SELECT * FROM Users WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Could not retrieve your profile data.";
    error_log("Error fetching user profile: " . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
}

include dirname(__DIR__) . '/includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>My Profile</h2>
                    <a href="<?php echo BASE_URL; ?>pages/apply.php" class="btn btn-primary">Edit Profile</a>
                </div>
                <div class="card-body">
                    <?php if (isset($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif ($user): ?>
                        <div class="list-group">
                            <div class="list-group-item">
                                <strong>Full Name:</strong> <?php echo htmlspecialchars($user['fullName']); ?>
                            </div>
                            <div class="list-group-item">
                                <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?>
                            </div>
                            <div class="list-group-item">
                                <strong>BVN:</strong> <?php echo htmlspecialchars(mask_string($user['bvn'] ?? '')); ?>
                            </div>
                            <div class="list-group-item">
                                <strong>NIN:</strong> <?php echo htmlspecialchars(mask_string($user['nin'] ?? '')); ?>
                            </div>
                             <div class="list-group-item">
                                <strong>Home Address:</strong> <?php echo htmlspecialchars($user['home_address'] ?? ''); ?>
                            </div>
                            <div class="list-group-item">
                                <strong>Marital Status:</strong> <?php echo htmlspecialchars($user['marital_status'] ?? ''); ?>
                            </div>
                            <div class="list-group-item">
                                <strong>Gender:</strong> <?php echo htmlspecialchars($user['gender'] ?? ''); ?>
                            </div>
                            <div class="list-group-item">
                                <strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['date_of_birth'] ?? ''); ?>
                            </div>
                             <div class="list-group-item">
                                <strong>Nationality:</strong> <?php echo htmlspecialchars($user['nationality'] ?? ''); ?>
                            </div>
                            <div class="list-group-item">
                                <strong>Occupation:</strong> <?php echo htmlspecialchars($user['occupation'] ?? ''); ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">Your profile is not yet complete. Please <a href="<?php echo BASE_URL; ?>pages/apply.php">update your information</a>.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
