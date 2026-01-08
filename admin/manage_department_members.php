<?php
require_once 'init.php';
if (!check_permission('manage_departments')) {
    $_SESSION['error_message'] = 'You do not have permission to access this page.';
    header('Location: dashboard.php');
    exit;
}

$department_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$department_id) {
    header('Location: departments.php');
    exit;
}

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';
    if (isset($_POST['add_member'])) {
        $member_id = $_POST['member_id'];
        // Check if the member is already in the department
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM department_members WHERE department_id = ? AND member_id = ?");
        $stmt->execute([$department_id, $member_id]);
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO department_members (department_id, member_id) VALUES (?, ?)");
            $stmt->execute([$department_id, $member_id]);
        }
    } elseif (isset($_POST['remove_member'])) {
        $member_id = $_POST['member_id'];
        $stmt = $pdo->prepare("DELETE FROM department_members WHERE department_id = ? AND member_id = ?");
        $stmt->execute([$department_id, $member_id]);
    }
    header('Location: manage_department_members.php?id=' . $department_id);
    exit;
}

// Fetch Department Info
$stmt = $pdo->prepare("SELECT * FROM departments WHERE id = ?");
$stmt->execute([$department_id]);
$department = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch Members in Department
$stmt = $pdo->prepare("SELECT m.id, m.name FROM members m JOIN department_members dm ON m.id = dm.member_id WHERE dm.department_id = ? ORDER BY m.name");
$stmt->execute([$department_id]);
$department_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch All Members (for adding)
$all_members = $pdo->query("SELECT id, name FROM members ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Manage Members for <?php echo htmlspecialchars($department['name']); ?></h2>
        <a href="departments.php" class="btn btn-secondary mb-3">Back to Departments</a>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Members in this Department</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php foreach ($department_members as $member): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo htmlspecialchars($member['name']); ?>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">
                                        <button type="submit" name="remove_member" class="btn btn-sm btn-danger">Remove</button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add Member to Department</h4>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <div class="mb-3">
                                <label for="member_id" class="form-label">Select Member</label>
                                <select class="form-select" id="member_id" name="member_id">
                                    <?php foreach ($all_members as $member): ?>
                                        <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" name="add_member" class="btn btn-primary">Add Member</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
