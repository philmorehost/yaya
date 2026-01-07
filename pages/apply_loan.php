<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';
require_once dirname(__DIR__) . '/includes/utils.php';

if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true) {
    $_SESSION['return_to'] = BASE_URL . 'pages/apply_loan.php';
    header('Location: ' . BASE_URL . 'pages/login.php');
    exit;
}

// Redirect user to update their profile if they haven't already
$user_id = $_SESSION['user_id'];
try {
    $stmt = $db->prepare("SELECT nin FROM Users WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch();
    if (empty($user['nin'])) { // Use NIN as a proxy for a complete profile
        $_SESSION['errors'][] = 'Please complete your profile information before applying for a loan.';
        header('Location: '. BASE_URL . 'pages/apply.php');
        exit;
    }
} catch (PDOException $e) {
    // Fail gracefully if check can't be performed
    error_log("Could not check user profile completeness: " . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['errors'][] = 'CSRF token validation failed.';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // --- Basic sanitization and retrieval ---
    $hubCategory = filter_input(INPUT_POST, 'hubCategory', FILTER_SANITIZE_STRING);
    $fullName = filter_input(INPUT_POST, 'fullName', FILTER_SANITIZE_STRING);
    $membershipNumber = filter_input(INPUT_POST, 'membershipNumber', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $loanPurpose = filter_input(INPUT_POST, 'loanPurpose', FILTER_SANITIZE_STRING);
    $loanAmount = filter_input(INPUT_POST, 'loanAmount', FILTER_VALIDATE_FLOAT);
    $monthlyIncome = filter_input(INPUT_POST, 'monthlyIncome', FILTER_VALIDATE_FLOAT);
    $existingSavings = filter_input(INPUT_POST, 'existingSavings', FILTER_VALIDATE_FLOAT);
    $guarantor1Name = filter_input(INPUT_POST, 'guarantor1Name', FILTER_SANITIZE_STRING);
    // ... continue for all fields

    // --- Validation ---
    if (empty($hubCategory) || empty($fullName) || empty($email) || empty($loanAmount)) {
        $_SESSION['errors'][] = 'Please fill in all required fields.';
    }
     if (!$email) {
        $_SESSION['errors'][] = 'Invalid email address provided.';
    }

    // --- File Upload Handling ---
    $userPassportPath = upload_file($_FILES['userPassport']);
    // ... handle other file uploads similarly

    if (empty($_SESSION['errors'])) {
        try {
            $stmt = $db->prepare(
                "INSERT INTO LoanApplications (user_id, hubCategory, fullName, membershipNumber, email, phone, loanPurpose, loanAmount, monthlyIncome, existingSavings, guarantor1Name, userPassport)
                 VALUES (:user_id, :hubCategory, :fullName, :membershipNumber, :email, :phone, :loanPurpose, :loanAmount, :monthlyIncome, :existingSavings, :guarantor1Name, :userPassport)"
            );

            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':hubCategory' => $hubCategory,
                ':fullName' => $fullName,
                ':membershipNumber' => $membershipNumber,
                ':email' => $email,
                ':phone' => $phone,
                ':loanPurpose' => $loanPurpose,
                ':loanAmount' => $loanAmount,
                ':monthlyIncome' => $monthlyIncome,
                ':existingSavings' => $existingSavings,
                ':guarantor1Name' => $guarantor1Name,
                ':userPassport' => $userPassportPath
            ]);

            $_SESSION['success_message'] = "Your loan application has been submitted successfully!";
            header('Location: ' . BASE_URL . 'pages/dashboard.php');
            exit;
        } catch (PDOException $e) {
             error_log("Loan application submission failed: " . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
            $_SESSION['errors'][] = 'A database error occurred. We could not process your application.';
        }
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

include dirname(__DIR__) . '/includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h2>Loan Application Form</h2>
                </div>
                <div class="card-body">
                    <p class="card-text text-center">Take the next step towards your financial goals.</p>

                     <?php
                    if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
                        echo '<div class="alert alert-danger" role="alert">';
                        foreach ($_SESSION['errors'] as $error) {
                            echo '<p class="mb-0">' . htmlspecialchars($error) . '</p>';
                        }
                        echo '</div>';
                        unset($_SESSION['errors']);
                    }
                    ?>

                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                        <!-- All the form fields from the old apply.php file go here -->
                        <fieldset class="mb-4">
                            <legend class="h5">Step 1: Application Details</legend>
                            <div class="mb-3">
                                <label for="fullName" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="fullName" name="fullName" required>
                            </div>
                             <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <!-- ... other personal detail fields ... -->
                        </fieldset>

                        <fieldset>
                            <legend class="h5">Step 2: Loan Details</legend>
                             <div class="mb-3">
                                <label for="loanAmount" class="form-label">Amount Requested (₦)</label>
                                <input type="number" class="form-control" id="loanAmount" name="loanAmount" required>
                            </div>
                            <!-- ... other loan detail fields ... -->
                        </fieldset>

                        <fieldset>
                            <legend class="h5">Step 3: Guarantor Information</legend>
                            <div class="mb-3">
                                <label for="guarantor1Name" class="form-label">Guarantor 1 Full Name</label>
                                <input type="text" class="form-control" id="guarantor1Name" name="guarantor1Name" required>
                            </div>
                             <div class="mb-3">
                                <label for="userPassport" class="form-label">Your Passport Photo</label>
                                <input type="file" class="form-control" id="userPassport" name="userPassport" required>
                            </div>
                            <!-- ... other guarantor fields ... -->
                        </fieldset>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Submit Application</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
