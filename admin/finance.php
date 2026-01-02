<?php
require_once '../includes/auth_check.php';
require_once '../config/db_connect.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';
}

// Handle Giving Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['giving_submit'])) {
    $member_id = !empty($_POST['member_id']) ? $_POST['member_id'] : null;
    $type = $_POST['type'];
    $amount = $_POST['amount'];
    $date = $_POST['giving_date'];

    $stmt = $pdo->prepare("INSERT INTO giving (member_id, type, amount, giving_date) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$member_id, $type, $amount, $date])) {
        $_SESSION['success_message'] = "Giving record saved successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to save giving record.";
    }
    header("Location: finance.php");
    exit();
}

// Handle Expenditure Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['expenditure_submit'])) {
    $purpose = $_POST['purpose'];
    $amount = $_POST['amount'];
    $date = $_POST['expenditure_date'];
    $category = $_POST['category'];

    $stmt = $pdo->prepare("INSERT INTO expenditures (purpose, amount, expenditure_date, category) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$purpose, $amount, $date, $category])) {
        $_SESSION['success_message'] = "Expenditure saved successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to save expenditure.";
    }
    header("Location: finance.php");
    exit();
}

// Fetch data for display
$members = $pdo->query("SELECT * FROM members ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
// The $givings query is corrected but not used in this view.
// It is kept for potential future use or reporting context.
$givings = $pdo->query("SELECT g.*, m.name FROM giving g LEFT JOIN members m ON g.member_id = m.id ORDER BY g.giving_date DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Finance Management</h2>

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
                    <div class="card-header">Record Giving</div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <div class="mb-3">
                                <label for="member_id" class="form-label">Member</label>
                                <select class="form-select" id="member_id" name="member_id">
                                    <option value="">Anonymous</option>
                                    <?php foreach ($members as $member): ?>
                                        <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="Tithe">Tithe</option>
                                    <option value="Offering">Offering</option>
                                    <option value="Building Fund">Building Fund</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                            </div>
                            <div class="mb-3">
                                <label for="giving_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="giving_date" name="giving_date" required>
                            </div>
                            <button type="submit" name="giving_submit" class="btn btn-primary">Save Giving</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Record Expenditure</div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <div class="mb-3">
                                <label for="purpose" class="form-label">Purpose</label>
                                <input type="text" class="form-control" id="purpose" name="purpose" required>
                            </div>
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                            </div>
                            <div class="mb-3">
                                <label for="expenditure_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="expenditure_date" name="expenditure_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <input type="text" class="form-control" id="category" name="category">
                            </div>
                            <button type="submit" name="expenditure_submit" class="btn btn-primary">Save Expenditure</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">Financial Reports</div>
            <div class="card-body">
                <!-- Reporting feature to be added here -->
                 <a href="financial_report.php" class="btn btn-info">Generate Financial Report</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
