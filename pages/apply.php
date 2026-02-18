<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';
require_once dirname(__DIR__) . '/includes/utils.php';

if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true) {
    $_SESSION['return_to'] = BASE_URL . 'pages/apply.php';
    header('Location: ' . BASE_URL . 'pages/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user = null;

// Fetch existing user data to pre-fill the form
try {
    $stmt = $db->prepare("SELECT * FROM Users WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If the table or columns don't exist yet, we can't pre-fill.
    error_log("Error fetching user data for apply form: " . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['errors'][] = 'CSRF token validation failed.';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // --- Form Data Processing ---
    $bvn = filter_input(INPUT_POST, 'bvn', FILTER_SANITIZE_STRING);
    $nin = filter_input(INPUT_POST, 'nin', FILTER_SANITIZE_STRING);
    $home_address = filter_input(INPUT_POST, 'home_address', FILTER_SANITIZE_STRING);
    $marital_status = filter_input(INPUT_POST, 'marital_status', FILTER_SANITIZE_STRING);
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
    $nationality = filter_input(INPUT_POST, 'nationality', FILTER_SANITIZE_STRING);
    $date_of_birth = filter_input(INPUT_POST, 'date_of_birth', FILTER_SANITIZE_STRING);
    $occupation = filter_input(INPUT_POST, 'occupation', FILTER_SANITIZE_STRING);

    // --- Basic Validation ---
    if (empty($bvn) || empty($nin) || empty($home_address) || empty($marital_status) || empty($gender) || empty($nationality) || empty($date_of_birth) || empty($occupation)) {
        $_SESSION['errors'][] = 'All fields are required. Please fill out the entire form.';
    }
    if (strlen($bvn) !== 11 || !ctype_digit($bvn)) {
        $_SESSION['errors'][] = 'Invalid BVN. It must be 11 digits.';
    }
    if (strlen($nin) !== 11 || !ctype_digit($nin)) {
        $_SESSION['errors'][] = 'Invalid NIN. It must be 11 digits.';
    }

    if (empty($_SESSION['errors'])) {
        try {
            $updateStmt = $db->prepare(
                "UPDATE Users SET
                    bvn = :bvn,
                    nin = :nin,
                    home_address = :home_address,
                    marital_status = :marital_status,
                    gender = :gender,
                    nationality = :nationality,
                    date_of_birth = :date_of_birth,
                    occupation = :occupation
                 WHERE id = :user_id"
            );

            $updateStmt->execute([
                ':bvn' => $bvn,
                ':nin' => $nin,
                ':home_address' => $home_address,
                ':marital_status' => $marital_status,
                ':gender' => $gender,
                ':nationality' => $nationality,
                ':date_of_birth' => $date_of_birth,
                ':occupation' => $occupation,
                ':user_id' => $user_id
            ]);

            $_SESSION['success_message'] = 'Your profile information has been updated successfully! You can now proceed with your loan application.';
            // Redirect to avoid form resubmission
            header('Location: ' . BASE_URL . 'pages/apply_loan.php');
            exit;

        } catch (PDOException $e) {
            error_log("User profile update failed: " . $e->getMessage(), 3, dirname(__DIR__) . '/logs/errors.log');
            $_SESSION['errors'][] = 'A database error occurred. Please try again.';
        }
    }
     // If there are errors, redirect back to the form to display them
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Generate a new CSRF token for the form
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

include dirname(__DIR__) . '/includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h2>Update Your Profile Information</h2>
                </div>
                <div class="card-body">
                    <p class="card-text text-center">To apply for a loan, please complete your profile with the information below.</p>

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
                     <?php
                    if (isset($_SESSION['success_message'])) {
                        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
                        unset($_SESSION['success_message']);
                    }
                    ?>

                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="bvn" class="form-label">BVN (Bank Verification Number)</label>
                                <input type="text" class="form-control" id="bvn" name="bvn" required maxlength="11" value="<?php echo htmlspecialchars($user['bvn'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nin" class="form-label">NIN (National Identification Number)</label>
                                <input type="text" class="form-control" id="nin" name="nin" required maxlength="11" value="<?php echo htmlspecialchars($user['nin'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="home_address" class="form-label">Home Address</label>
                            <textarea class="form-control" id="home_address" name="home_address" rows="3" required><?php echo htmlspecialchars($user['home_address'] ?? ''); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="marital_status" class="form-label">Marital Status</label>
                                <select class="form-select" id="marital_status" name="marital_status" required>
                                    <option value="" disabled <?php echo empty($user['marital_status']) ? 'selected' : ''; ?>>Choose...</option>
                                    <option value="Single" <?php echo (($user['marital_status'] ?? '') === 'Single') ? 'selected' : ''; ?>>Single</option>
                                    <option value="Married" <?php echo (($user['marital_status'] ?? '') === 'Married') ? 'selected' : ''; ?>>Married</option>
                                    <option value="Divorced" <?php echo (($user['marital_status'] ?? '') === 'Divorced') ? 'selected' : ''; ?>>Divorced</option>
                                    <option value="Widowed" <?php echo (($user['marital_status'] ?? '') === 'Widowed') ? 'selected' : ''; ?>>Widowed</option>
                                </select>
                            </div>
                             <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="" disabled <?php echo empty($user['gender']) ? 'selected' : ''; ?>>Choose...</option>
                                    <option value="Male" <?php echo (($user['gender'] ?? '') === 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo (($user['gender'] ?? '') === 'Female') ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6 mb-3">
                                <label for="nationality" class="form-label">Nationality</label>
                                <input type="text" class="form-control" id="nationality" name="nationality" required value="<?php echo htmlspecialchars($user['nationality'] ?? 'Nigerian'); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required value="<?php echo htmlspecialchars($user['date_of_birth'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="occupation" class="form-label">Occupation</label>
                            <input type="text" class="form-control" id="occupation" name="occupation" required value="<?php echo htmlspecialchars($user['occupation'] ?? ''); ?>">
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Save and Continue</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
