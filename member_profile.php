<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/public_header.php';

if (!isset($_SESSION['member_loggedin'])) {
    header('Location: member_login.php');
    exit;
}

$member_id = $_SESSION['member_id'];
$success_message = '';
$error_message = '';
$password_success_message = '';
$password_error_message = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];

    $stmt = $pdo->prepare("UPDATE members SET name = ?, phone = ?, email = ?, birthday = ?, gender = ? WHERE id = ?");
    if ($stmt->execute([$name, $phone, $email, $birthday, $gender, $member_id])) {
        $success_message = 'Profile updated successfully!';
        $_SESSION['member_name'] = $name; // Update session name
    } else {
        $error_message = 'Failed to update profile.';
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $stmt = $pdo->prepare("SELECT password FROM members WHERE id = ?");
    $stmt->execute([$member_id]);
    $member = $stmt->fetch();

    if ($member && password_verify($current_password, $member['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE members SET password = ? WHERE id = ?");
            if ($stmt->execute([$hashed_password, $member_id])) {
                $password_success_message = 'Password changed successfully!';
            } else {
                $password_error_message = 'Failed to change password.';
            }
        } else {
            $password_error_message = 'New passwords do not match.';
        }
    } else {
        $password_error_message = 'Incorrect current password.';
    }
}


$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$member_id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <h2>Edit Profile</h2>
    <p>View and update your profile information.</p>
    <hr>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">Your Information</div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label for="member_id_display" class="form-label">Member ID</label>
                    <input type="text" class="form-control" id="member_id_display" value="<?php echo htmlspecialchars($member['member_id'] ?? ''); ?>" readonly>
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
                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Change Password</div>
        <div class="card-body">
            <?php if ($password_success_message): ?>
                <div class="alert alert-success"><?php echo $password_success_message; ?></div>
            <?php endif; ?>
            <?php if ($password_error_message): ?>
                <div class="alert alert-danger"><?php echo $password_error_message; ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
            </form>
        </div>
    </div>
     <a href="member_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

<?php require_once 'includes/public_footer.php'; ?>
