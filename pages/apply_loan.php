<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';
require_once dirname(__DIR__) . '/includes/utils.php';

// --- User Authentication & Profile Check ---
if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true) {
    $_SESSION['return_to'] = BASE_URL . 'pages/apply_loan.php';
    header('Location: ' . BASE_URL . 'pages/login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
try {
    $stmt = $db->prepare("SELECT nin FROM Users WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch();
    if (empty($user['nin'])) {
        $_SESSION['errors'][] = 'Please complete your profile information before applying for a loan.';
        header('Location: ' . BASE_URL . 'pages/apply.php');
        exit;
    }
} catch (PDOException $e) {
    error_log("Could not check user profile completeness: " . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
}
// --- End Authentication ---


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- CSRF & Form Processing ---
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['errors'][] = 'CSRF token validation failed.';
    }

    $allowed_image_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_file_size = 5 * 1024 * 1024; // 5 MB

    // --- File Uploads ---
    $userPassportPath = upload_file($_FILES['userPassport'], $allowed_image_types, $max_file_size);
    $guarantor1PassportPath = upload_file($_FILES['guarantor1Passport'], $allowed_image_types, $max_file_size);
    $guarantor2PassportPath = upload_file($_FILES['guarantor2Passport'], $allowed_image_types, $max_file_size);


    // --- Form Data Retrieval & Sanitization ---
    $fields = [
        'hubCategory' => FILTER_SANITIZE_STRING, 'fullName' => FILTER_SANITIZE_STRING,
        'membershipNumber' => FILTER_SANITIZE_STRING, 'email' => FILTER_VALIDATE_EMAIL,
        'phone' => FILTER_SANITIZE_STRING, 'loanPurpose' => FILTER_SANITIZE_STRING,
        'loanAmount' => FILTER_VALIDATE_FLOAT, 'loanDuration' => FILTER_VALIDATE_INT,
        'monthlyIncome' => FILTER_VALIDATE_FLOAT,
        'existingSavings' => FILTER_VALIDATE_FLOAT, 'guarantor1Name' => FILTER_SANITIZE_STRING,
        'guarantor1Occupation' => FILTER_SANITIZE_STRING, 'guarantor1Phone' => FILTER_SANITIZE_STRING,
        'guarantor2Name' => FILTER_SANITIZE_STRING, 'guarantor2Occupation' => FILTER_SANITIZE_STRING,
        'guarantor2Phone' => FILTER_SANITIZE_STRING
    ];

    $appData = [];
    foreach ($fields as $key => $filter) {
        $appData[$key] = filter_input(INPUT_POST, $key, $filter);
    }

    // --- Validation ---
    if (in_array(false, $appData, true) || in_array(null, $appData, true)) {
        $_SESSION['errors'][] = 'Please fill in all required fields with valid data.';
    }
    if (!$userPassportPath || !$guarantor1PassportPath || !$guarantor2PassportPath) {
        $_SESSION['errors'][] = 'All passport files are required.';
    }

    if (empty($_SESSION['errors'])) {
        try {
            $sql = "INSERT INTO LoanApplications (user_id, hubCategory, fullName, membershipNumber, email, phone, loanPurpose, loanAmount, loanDuration, monthlyIncome, existingSavings, guarantor1Name, guarantor1Occupation, guarantor1Phone, guarantor1Passport, guarantor2Name, guarantor2Occupation, guarantor2Phone, guarantor2Passport, userPassport) VALUES (:user_id, :hubCategory, :fullName, :membershipNumber, :email, :phone, :loanPurpose, :loanAmount, :loanDuration, :monthlyIncome, :existingSavings, :guarantor1Name, :guarantor1Occupation, :guarantor1Phone, :guarantor1Passport, :guarantor2Name, :guarantor2Occupation, :guarantor2Phone, :guarantor2Passport, :userPassport)";

            $stmt = $db->prepare($sql);

            $params = array_merge([':user_id' => $user_id], array_combine(array_map(function($k){ return ':'.$k; }, array_keys($appData)), $appData));
            $params[':userPassport'] = $userPassportPath;
            $params[':guarantor1Passport'] = $guarantor1PassportPath;
            $params[':guarantor2Passport'] = $guarantor2PassportPath;

            $stmt->execute($params);

            $_SESSION['success_message'] = "Your loan application has been submitted successfully!";
            header('Location: ' . BASE_URL . 'pages/dashboard.php');
            exit;

        } catch (PDOException $e) {
            error_log("Loan application submission failed: " . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
            $_SESSION['errors'][] = 'A database error occurred. We could not process your application.';
        }
    }
    // Redirect back to the form on error to show messages
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

                        <fieldset class="mb-4">
                            <legend class="h5">Step 1: Eligibility & Personal Details</legend>
                             <div class="form-check"><input class="form-check-input" type="radio" name="hubCategory" id="juniorHub" value="junior" required><label class="form-check-label" for="juniorHub"><strong>Junior Hub (Ages 13-17)</strong></label></div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="hubCategory" id="nextGenHub" value="nextgen"><label class="form-check-label" for="nextGenHub"><strong>NextGen Hub (Ages 18-30)</strong></label></div>
                            <div class="form-check mb-3"><input class="form-check-input" type="radio" name="hubCategory" id="legacyHub" value="legacy"><label class="form-check-label" for="legacyHub"><strong>Legacy Hub (Ages 31+)</strong></label></div>

                            <div class="mb-3"><label for="fullName" class="form-label">Full Name</label><input type="text" class="form-control" id="fullName" name="fullName" required></div>
                            <div class="mb-3"><label for="membershipNumber" class="form-label">Cooperative Membership Number</label><input type="text" class="form-control" id="membershipNumber" name="membershipNumber" required></div>
                            <div class="row">
                                <div class="col-md-6 mb-3"><label for="email" class="form-label">Email Address</label><input type="email" class="form-control" id="email" name="email" required></div>
                                <div class="col-md-6 mb-3"><label for="phone" class="form-label">Phone Number</label><input type="tel" class="form-control" id="phone" name="phone" required></div>
                            </div>
                            <div class="mb-3"><label for="userPassport" class="form-label">Your Passport</label><input type="file" class="form-control" id="userPassport" name="userPassport" required></div>
                        </fieldset>

                        <fieldset class="mb-4">
                            <legend class="h5">Step 2: Loan & Financial Details</legend>
                            <div class="row">
                                <div class="col-md-6 mb-3"><label for="loanPurpose" class="form-label">Loan Purpose</label><select class="form-select" id="loanPurpose" name="loanPurpose" required><option selected disabled value="">Choose...</option><option>School fees</option><option>Laptop/Tools</option><option>Small Business</option><option>Home Improvement</option></select></div>
                                <div class="col-md-6 mb-3"><label for="loanAmount" class="form-label">Amount Requested (₦)</label><input type="number" class="form-control" id="loanAmount" name="loanAmount" required></div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="loanDuration" class="form-label">Loan Duration (Months)</label>
                                    <select class="form-select" id="loanDuration" name="loanDuration" required>
                                        <option selected disabled value="">Choose...</option>
                                        <?php for ($i = 1; $i <= 24; $i++): ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?> Month<?php echo $i > 1 ? 's' : ''; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3"><label for="monthlyIncome" class="form-label">Monthly Income/Allowance (₦)</label><input type="number" class="form-control" id="monthlyIncome" name="monthlyIncome" required></div>
                                <div class="col-md-6 mb-3"><label for="existingSavings" class="form-label">Existing Savings in Hub (₦)</label><input type="number" class="form-control" id="existingSavings" name="existingSavings" required></div>
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend class="h5">Step 3: Guarantor Information</legend>
                            <p>Please provide two active members of the cooperative as guarantors.</p>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h5>Guarantor 1</h5>
                                    <label for="guarantor1Name" class="form-label">Full Name</label><input type="text" class="form-control" id="guarantor1Name" name="guarantor1Name" required>
                                    <label for="guarantor1Occupation" class="form-label mt-2">Occupation</label><input type="text" class="form-control" id="guarantor1Occupation" name="guarantor1Occupation" required>
                                    <label for="guarantor1Phone" class="form-label mt-2">Phone Number</label><input type="tel" class="form-control" id="guarantor1Phone" name="guarantor1Phone" required>
                                    <label for="guarantor1Passport" class="form-label mt-2">Passport</label><input type="file" class="form-control" id="guarantor1Passport" name="guarantor1Passport" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h5>Guarantor 2</h5>
                                    <label for="guarantor2Name" class="form-label">Full Name</label><input type="text" class="form-control" id="guarantor2Name" name="guarantor2Name" required>
                                    <label for="guarantor2Occupation" class="form-label mt-2">Occupation</label><input type="text" class="form-control" id="guarantor2Occupation" name="guarantor2Occupation" required>
                                    <label for="guarantor2Phone" class="form-label mt-2">Phone Number</label><input type="tel" class="form-control" id="guarantor2Phone" name="guarantor2Phone" required>
                                    <label for="guarantor2Passport" class="form-label mt-2">Passport</label><input type="file" class="form-control" id="guarantor2Passport" name="guarantor2Passport" required>
                                </div>
                            </div>
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
