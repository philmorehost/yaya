<?php
require_once 'init.php';

$member_id = $_SESSION['member_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_department'])) {
    require_once '../includes/csrf_check.php';
    $department_id = $_POST['department_id'];

    // Check if member has already applied
    $stmt = $pdo->prepare("SELECT * FROM department_members WHERE member_id = ? AND department_id = ?");
    $stmt->execute([$member_id, $department_id]);
    if ($stmt->fetch()) {
        $_SESSION['error_message'] = "You have already applied to this department.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO department_members (member_id, department_id, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$member_id, $department_id]);
        $_SESSION['success_message'] = "Application submitted successfully!";
    }
    header('Location: departments.php');
    exit;
}

$departments = $pdo->query("SELECT * FROM departments ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php require_once 'header.php'; ?>

<h1 class="mt-5">Departments</h1>
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
<?php endif; ?>
<div class="list-group">
    <?php foreach ($departments as $department): ?>
        <div class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1"><?php echo htmlspecialchars($department['name']); ?></h5>
                <form method="post" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="department_id" value="<?php echo $department['id']; ?>">
                    <button type="submit" name="apply_department" class="btn btn-primary btn-sm">Apply</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once 'footer.php'; ?>
