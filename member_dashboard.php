<?php
require_once 'admin/init.php';
require_once 'includes/public_header.php';

if (!isset($_SESSION['member_id'])) {
    header('Location: member_login.php');
    exit();
}

// Fetch announcements
$announcements_stmt = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");
$announcements = $announcements_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch media
$media_stmt = $pdo->query("SELECT * FROM media ORDER BY publication_date DESC LIMIT 5");
$media = $media_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['member_name']); ?>!</h2>
        <a href="logout.php" class="btn btn-secondary">Logout</a>
    </div>

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

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Recent Announcements</h4>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($announcements as $announcement): ?>
                            <li class="list-group-item">
                                <h5><?php echo htmlspecialchars($announcement['title']); ?></h5>
                                <p><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Recent Media</h4>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($media as $item): ?>
                            <li class="list-group-item">
                                <h5><?php echo htmlspecialchars($item['title']); ?></h5>
                                <p><?php echo nl2br(htmlspecialchars($item['content'])); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Giving Section -->
    <div class="card mt-4">
        <div class="card-header">
            <h4>Give</h4>
        </div>
        <div class="card-body">
            <form method="post" action="member_give.php">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="type" class="form-label">Type of Giving</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="Tithe">Tithe</option>
                                <option value="Offering">Offering</option>
                                <option value="Building Fund">Building Fund</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="giving_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="giving_date" name="giving_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                </div>
                <button type="submit" name="giving_submit" class="btn btn-primary">Submit Giving</button>
            </form>
        </div>
    </div>

    <!-- Department Application Section -->
    <div class="card mt-4">
        <div class="card-header">
            <h4>Join a Department</h4>
        </div>
        <div class="card-body">
            <form method="post" action="member_apply_department.php">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="mb-3">
                    <label for="department_id" class="form-label">Select Department</label>
                    <select class="form-select" id="department_id" name="department_id" required>
                        <option value="">Choose a department...</option>
                        <?php
                        $departments_stmt = $pdo->query("SELECT * FROM departments ORDER BY name");
                        $departments = $departments_stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($departments as $department): ?>
                            <option value="<?php echo $department['id']; ?>"><?php echo htmlspecialchars($department['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="apply_department" class="btn btn-primary">Apply</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/public_footer.php'; ?>
