<?php require_once dirname(__DIR__) . '/config.php'; ?>
<?php require_once dirname(__DIR__) . '/database.php'; ?>
<?php
if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}
?>
<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Users</h1>
        <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Registered Users</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Registered On</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $db->query("SELECT id, fullName, email, created_at, status FROM Users ORDER BY created_at DESC");
                        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if ($users) {
                            foreach ($users as $user) {
                                $status_badge = $user['status'] === 'suspended' ? 'badge bg-danger' : 'badge bg-success';
                                echo "<tr>";
                                echo "<td>" . $user['id'] . "</td>";
                                echo "<td>" . htmlspecialchars($user['fullName']) . "</td>";
                                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                                echo "<td>" . date('F j, Y', strtotime($user['created_at'])) . "</td>";
                                echo "<td><span class='" . $status_badge . "'>" . htmlspecialchars(ucfirst($user['status'])) . "</span></td>";
                                echo "<td>
                                        <a href='edit_user.php?id=" . $user['id'] . "' class='btn btn-primary btn-sm'>Edit</a>
                                        <a href='suspend_user.php?id=" . $user['id'] . "' class='btn btn-warning btn-sm'>" . ($user['status'] === 'suspended' ? 'Unsuspend' : 'Suspend') . "</a>
                                        <a href='delete_user.php?id=" . $user['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                        <a href='login_as_user.php?id=" . $user['id'] . "' class='btn btn-info btn-sm'>Login As</a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>No users found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
