<?php
require_once 'init.php';

$member_id = $_SESSION['member_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];
    $password = $_POST['password'];

    // Only update password if a new one is provided
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE members SET name = ?, phone = ?, email = ?, birthday = ?, gender = ?, password = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $email, $birthday, $gender, $hashed_password, $member_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE members SET name = ?, phone = ?, email = ?, birthday = ?, gender = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $email, $birthday, $gender, $member_id]);
    }

    $_SESSION['success_message'] = "Profile updated successfully!";
    header('Location: profile.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$member_id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<?php require_once 'header.php'; ?>

<h1 class="mt-5">My Profile</h1>
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
<?php endif; ?>
<div class="card">
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="mb-3">
                <label for="member_id" class="form-label">Member ID</label>
                <input type="text" class="form-control" id="member_id" name="member_id" value="<?php echo htmlspecialchars($member['member_id']); ?>" disabled>
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
                <label for="password" class="form-label">New Password (leave blank to keep current password)</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>
