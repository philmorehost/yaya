<?php
require_once 'includes/member_init.php';
require_once 'includes/public_header.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_department'])) {
    $department_id = $_POST['department_id'];
    $member_id = $_SESSION['member_id'];
    $stmt = $pdo->prepare("INSERT INTO department_applications (member_id, department_id) VALUES (?, ?)");
    $stmt->execute([$member_id, $department_id]);
    $_SESSION['success_message'] = 'Your application has been submitted!';
    header('Location: member_departments.php');
    exit;
}

$departments = $pdo->query("SELECT d.*, m.name as head_name FROM departments d LEFT JOIN members m ON d.head_id = m.id ORDER BY d.name")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h2>Departments</h2>
        <p class="lead">Explore our departments and find a place to serve.</p>
    </div>

    <?php
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
        unset($_SESSION['success_message']);
    }
    ?>

    <div class="row">
        <?php if ($departments): ?>
            <?php foreach ($departments as $department): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title text-center" style="color: #001f3f;"><?php echo htmlspecialchars($department['name']); ?></h5>
                            <hr>
                            <p class="card-text"><strong>Department Head:</strong> <?php echo htmlspecialchars($department['head_name'] ?: 'Not Assigned'); ?></p>
                            <form method="post">
                                <input type="hidden" name="department_id" value="<?php echo $department['id']; ?>">
                                <button type="submit" name="apply_department" class="btn btn-primary w-100">Apply to Join</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center">No departments have been created yet. Please check back later.</p>
            </div>
        <?php endif; ?>
    </div>
    <a href="member_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

<?php require_once 'includes/public_footer.php'; ?>
