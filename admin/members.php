<?php
require_once 'init.php';
check_permission('manage_members');
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_member'])) {
    require_once '../includes/csrf_check.php';
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];
    $role_id = !empty($_POST['role_id']) ? $_POST['role_id'] : null;

    $pdo->beginTransaction();

    try {
        // Insert member first to get the new ID
        $stmt = $pdo->prepare("INSERT INTO members (name, phone, email, birthday, gender, role_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $phone, $email, $birthday, $gender, $role_id]);
        $member_id = $pdo->lastInsertId();

        // Generate and update membership ID
        $membership_id = 'rccg-sop-' . str_pad($member_id, 4, '0', STR_PAD_LEFT);
        $update_stmt = $pdo->prepare("UPDATE members SET membership_id = ? WHERE id = ?");
        $update_stmt->execute([$membership_id, $member_id]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        // Handle error, maybe log it or show a message
    }

    header('Location: members.php');
    exit();
}

// Fetch members and their roles
$stmt = $pdo->query("
    SELECT m.*, r.name as role_name
    FROM members m
    LEFT JOIN roles r ON m.role_id = r.id
    ORDER BY m.name
");
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all roles for the dropdown
$roles_stmt = $pdo->query("SELECT * FROM roles ORDER BY name");
$roles = $roles_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Members</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addMemberModal">Add Member</button>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-dark">
                        <thead>
                            <tr>
                                <th>Membership ID</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Birthday</th>
                                <th>Gender</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($member['membership_id'] ?: 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($member['name']); ?></td>
                                    <td>
                                        <?php if ($member['role_id']): ?>
                                            <span class="badge bg-primary"><?php echo htmlspecialchars($member['role_name']); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Member</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($member['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($member['email']); ?></td>
                                    <td><?php echo htmlspecialchars($member['birthday']); ?></td>
                                    <td><?php echo htmlspecialchars($member['gender']); ?></td>
                                    <td>
                                        <a href="edit_member.php?id=<?php echo $member['id']; ?>" class="btn btn-sm btn-info">Edit</a>
                                        <form method="post" action="delete_member.php" class="d-inline">
                                            <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
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

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">Add New Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="birthday" class="form-label">Birthday</label>
                        <input type="date" class="form-control" id="birthday" name="birthday">
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select" id="gender" name="gender">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role</label>
                        <select class="form-select" id="role_id" name="role_id">
                            <option value="">Select a role (optional)</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="add_member" class="btn btn-primary">Save Member</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
