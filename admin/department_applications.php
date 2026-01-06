<?php
require_once 'init.php';
check_permission('manage_departments');
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';
    if (isset($_POST['approve_application'])) {
        $application_id = $_POST['application_id'];
        $stmt = $pdo->prepare("UPDATE department_applications SET status = 'approved' WHERE id = ?");
        $stmt->execute([$application_id]);
    } elseif (isset($_POST['deny_application'])) {
        $application_id = $_POST['application_id'];
        $stmt = $pdo->prepare("UPDATE department_applications SET status = 'denied' WHERE id = ?");
        $stmt->execute([$application_id]);
    }
    header('Location: department_applications.php');
}

$applications = $pdo->query("
    SELECT da.*, m.name as member_name, d.name as department_name
    FROM department_applications da
    JOIN members m ON da.member_id = m.id
    JOIN departments d ON da.department_id = d.id
    ORDER BY da.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Department Applications</h2>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-dark">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $application): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($application['member_name']); ?></td>
                                    <td><?php echo htmlspecialchars($application['department_name']); ?></td>
                                    <td><span class="badge bg-<?php echo $application['status'] == 'approved' ? 'success' : ($application['status'] == 'denied' ? 'danger' : 'warning'); ?>"><?php echo htmlspecialchars($application['status']); ?></span></td>
                                    <td><?php echo htmlspecialchars($application['created_at']); ?></td>
                                    <td>
                                        <?php if ($application['status'] == 'pending'): ?>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <button type="submit" name="approve_application" class="btn btn-sm btn-success">Approve</button>
                                            </form>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <button type="submit" name="deny_application" class="btn btn-sm btn-danger">Deny</button>
                                            </form>
                                        <?php endif; ?>
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

<?php require_once '../includes/footer.php'; ?>
