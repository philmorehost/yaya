<?php
require_once 'init.php';
check_permission('manage_attendance');
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['headcount_submit'])) {
    $date = $_POST['service_date'];
    $men = $_POST['men'];
    $women = $_POST['women'];
    $children = $_POST['children'];

    $stmt = $pdo->prepare("INSERT INTO attendance_headcount (service_date, men, women, children) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$date, $men, $women, $children])) {
        $_SESSION['success_message'] = "Headcount saved successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to save headcount.";
    }
    header("Location: attendance.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkin_submit'])) {
    $member_id = $_POST['member_id'];
    $checkin_time = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("INSERT INTO attendance_log (member_id, checkin_time) VALUES (?, ?)");
    if ($stmt->execute([$member_id, $checkin_time])) {
        $_SESSION['success_message'] = "Member checked in successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to check in member.";
    }
    header("Location: attendance.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM members ORDER BY name");
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Attendance</h2>

        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . $_SESSION['success_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . $_SESSION['error_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Manual Headcount</div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <div class="mb-3">
                                <label for="service_date" class="form-label">Service Date</label>
                                <input type="date" class="form-control" id="service_date" name="service_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="men" class="form-label">Men</label>
                                <input type="number" class="form-control" id="men" name="men" required>
                            </div>
                            <div class="mb-3">
                                <label for="women" class="form-label">Women</label>
                                <input type="number" class="form-control" id="women" name="women" required>
                            </div>
                            <div class="mb-3">
                                <label for="children" class="form-label">Children</label>
                                <input type="number" class="form-control" id="children" name="children" required>
                            </div>
                            <button type="submit" name="headcount_submit" class="btn btn-primary">Save Headcount</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Member Check-in</div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <div class="mb-3">
                                <label for="member_id" class="form-label">Select Member</label>
                                <select class="form-select" id="member_id" name="member_id" required>
                                    <option value="">Choose...</option>
                                    <?php foreach ($members as $member): ?>
                                        <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" name="checkin_submit" class="btn btn-primary">Check In</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
