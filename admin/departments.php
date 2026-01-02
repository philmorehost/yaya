<?php
require_once '../includes/auth_check.php';
require_once '../config/db_connect.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';
    if (isset($_POST['add_department'])) {
        $name = $_POST['name'];
        $head_id = $_POST['head_id'];
        $stmt = $pdo->prepare("INSERT INTO departments (name, head_id) VALUES (?, ?)");
        $stmt->execute([$name, $head_id]);
    } elseif (isset($_POST['edit_department'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $head_id = $_POST['head_id'];
        $stmt = $pdo->prepare("UPDATE departments SET name = ?, head_id = ? WHERE id = ?");
        $stmt->execute([$name, $head_id, $id]);
    } elseif (isset($_POST['delete_department'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM departments WHERE id = ?");
        $stmt->execute([$id]);
    }
    header('Location: departments.php');
}

// Fetch Data
$departments = $pdo->query("SELECT d.*, m.name as head_name FROM departments d LEFT JOIN members m ON d.head_id = m.id ORDER BY d.name")->fetchAll(PDO::FETCH_ASSOC);
$members = $pdo->query("SELECT * FROM members ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Departments</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">Add Department</button>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-dark">
                        <thead>
                            <tr>
                                <th>Department Name</th>
                                <th>Department Head</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($departments as $department): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($department['name']); ?></td>
                                    <td><?php echo htmlspecialchars($department['head_name'] ?: 'Not Assigned'); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editDepartmentModal-<?php echo $department['id']; ?>">Edit</button>
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="id" value="<?php echo $department['id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <button type="submit" name="delete_department" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Department Modal -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">Add New Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Department Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="head_id" class="form-label">Department Head</label>
                        <select class="form-select" id="head_id" name="head_id">
                            <option value="">Select Head</option>
                            <?php foreach ($members as $member): ?>
                                <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="add_department" class="btn btn-primary">Save Department</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Department Modals -->
<?php foreach ($departments as $department): ?>
<div class="modal fade" id="editDepartmentModal-<?php echo $department['id']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">Edit Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="id" value="<?php echo $department['id']; ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Department Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($department['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="head_id" class="form-label">Department Head</label>
                        <select class="form-select" id="head_id" name="head_id">
                            <option value="">Select Head</option>
                            <?php foreach ($members as $member): ?>
                                <option value="<?php echo $member['id']; ?>" <?php if($department['head_id'] == $member['id']) echo 'selected'; ?>><?php echo htmlspecialchars($member['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="edit_department" class="btn btn-primary">Update Department</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php require_once '../includes/footer.php'; ?>
