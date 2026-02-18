<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';

if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: ' . BASE_URL . 'admin/index.php');
    exit;
}

$application = null;
try {
    $stmt = $db->prepare("SELECT * FROM LoanApplications WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $application = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Error fetching application: " . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
}

if (!$application) {
    header('Location: ' . BASE_URL . 'admin/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['errors'] = ["CSRF token validation failed."];
    } else {
        $fullName = filter_input(INPUT_POST, 'fullName', FILTER_SANITIZE_STRING);
        $loanAmount = filter_input(INPUT_POST, 'loanAmount', FILTER_VALIDATE_FLOAT);
        $loanDuration = filter_input(INPUT_POST, 'loanDuration', FILTER_VALIDATE_INT);
        $loanPurpose = filter_input(INPUT_POST, 'loanPurpose', FILTER_SANITIZE_STRING);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

        if ($fullName && $loanAmount && $loanDuration && $loanPurpose && $status) {
            try {
                $stmt = $db->prepare("UPDATE LoanApplications SET fullName = :fullName, loanAmount = :loanAmount, loanDuration = :loanDuration, loanPurpose = :loanPurpose, status = :status WHERE id = :id");
                $stmt->execute([
                    ':fullName' => $fullName,
                    ':loanAmount' => $loanAmount,
                    ':loanDuration' => $loanDuration,
                    ':loanPurpose' => $loanPurpose,
                    ':status' => $status,
                    ':id' => $id
                ]);
                $_SESSION['success_message'] = "Application #$id updated successfully.";
                header('Location: ' . BASE_URL . 'admin/index.php');
                exit;
            } catch (PDOException $e) {
                error_log("Error updating application: " . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
                $_SESSION['errors'] = ["Database error: " . $e->getMessage()];
            }
        } else {
            $_SESSION['errors'] = ["All fields are required."];
        }
    }
}

$page_title = "Edit Application #$id";
include 'includes/header.php';
?>

<div class="container mt-5">
    <h1>Edit Loan Application #<?php echo $id; ?></h1>

    <?php if (isset($_SESSION['errors'])): ?>
        <?php foreach ($_SESSION['errors'] as $error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endforeach; ?>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="mb-3">
                    <label for="fullName" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="fullName" name="fullName" value="<?php echo htmlspecialchars($application['fullName']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="loanAmount" class="form-label">Loan Amount (₦)</label>
                    <input type="number" step="0.01" class="form-control" id="loanAmount" name="loanAmount" value="<?php echo htmlspecialchars($application['loanAmount']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="loanDuration" class="form-label">Loan Duration (Months)</label>
                    <select class="form-select" id="loanDuration" name="loanDuration" required>
                        <?php for ($i = 1; $i <= 24; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo $application['loanDuration'] == $i ? 'selected' : ''; ?>>
                                <?php echo $i; ?> Month<?php echo $i > 1 ? 's' : ''; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="loanPurpose" class="form-label">Loan Purpose</label>
                    <select class="form-select" id="loanPurpose" name="loanPurpose" required>
                        <option value="School fees" <?php echo $application['loanPurpose'] == 'School fees' ? 'selected' : ''; ?>>School fees</option>
                        <option value="Laptop/Tools" <?php echo $application['loanPurpose'] == 'Laptop/Tools' ? 'selected' : ''; ?>>Laptop/Tools</option>
                        <option value="Small Business" <?php echo $application['loanPurpose'] == 'Small Business' ? 'selected' : ''; ?>>Small Business</option>
                        <option value="Home Improvement" <?php echo $application['loanPurpose'] == 'Home Improvement' ? 'selected' : ''; ?>>Home Improvement</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="Pending" <?php echo $application['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Approved" <?php echo $application['status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="Disapproved" <?php echo $application['status'] == 'Disapproved' ? 'selected' : ''; ?>>Disapproved</option>
                        <option value="Disbursed" <?php echo $application['status'] == 'Disbursed' ? 'selected' : ''; ?> disabled>Disbursed</option>
                    </select>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
