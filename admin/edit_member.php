<?php
require_once 'init.php';
check_permission('manage_members');
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$member_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$member_id) {
    header("Location: members.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$member_id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    header("Location: members.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];
    $role_id = !empty($_POST['role_id']) ? $_POST['role_id'] : null;

    $stmt = $pdo->prepare("UPDATE members SET name = ?, phone = ?, email = ?, birthday = ?, gender = ?, role_id = ? WHERE id = ?");
    $stmt->execute([$name, $phone, $email, $birthday, $gender, $role_id, $member_id]);

    $_SESSION['success_message'] = "Member updated successfully.";
    header("Location: members.php");
    exit();
}

$roles = $pdo->query("SELECT id, name FROM roles ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Edit Member: <?php echo htmlspecialchars($member['name']); ?></h2>

        <div class="card">
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
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
                            <option value="">Member (No Role)</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['id']; ?>" <?php if ($member['role_id'] == $role['id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($role['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="members.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
