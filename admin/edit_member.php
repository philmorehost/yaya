<?php
require_once 'init.php';
check_permission('edit_members');
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];
    $member_id_field = $_POST['member_id'];
    $role_id = $_POST['role_id'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE members SET name = ?, phone = ?, email = ?, birthday = ?, gender = ?, member_id = ?, role_id = ?, password = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $email, $birthday, $gender, $member_id_field, $role_id, $hashed_password, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE members SET name = ?, phone = ?, email = ?, birthday = ?, gender = ?, member_id = ?, role_id = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $email, $birthday, $gender, $member_id_field, $role_id, $id]);
    }

    header('Location: members.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch roles for dropdown
$roles_stmt = $pdo->query("SELECT id, name FROM roles ORDER BY name");
$roles = $roles_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Edit Member</h2>
        <div class="card">
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="member_id" class="form-label">Member ID</label>
                        <input type="text" class="form-control" id="member_id" name="member_id" value="<?php echo htmlspecialchars($member['member_id']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($member['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($member['phone']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($member['email']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="birthday" class="form-label">Birthday</label>
                        <input type="date" class="form-control" id="birthday" name="birthday" value="<?php echo htmlspecialchars($member['birthday']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select" id="gender" name="gender">
                            <option value="Male" <?php if ($member['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                            <option value="Female" <?php if ($member['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role</label>
                        <select class="form-select" id="role_id" name="role_id">
                            <option value="">Select Role</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['id']; ?>" <?php if ($member['role_id'] == $role['id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($role['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Reset Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small class="form-text text-muted">Leave blank to keep the current password.</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Member</button>
                    <a href="members.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
