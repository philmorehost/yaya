<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';
require_once dirname(__DIR__) . '/includes/utils.php';

if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true) {
    header('Location: ' . BASE_URL . 'pages/login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("CSRF token validation failed.", 3, BASE_PATH . 'logs/errors.log');
        header('Location: ' . BASE_URL . 'pages/error.php');
        exit();
    }

    // Sanitize input
    foreach ($_POST as $key => $value) {
        $_POST[$key] = htmlspecialchars($value);
    }

    // Enhanced validation
    $requiredFields = [
        'hubCategory', 'fullName', 'membershipNumber', 'email', 'phone',
        'loanPurpose', 'loanAmount', 'monthlyIncome', 'existingSavings',
        'guarantor1Name', 'guarantor1Occupation', 'guarantor1Phone',
        'guarantor2Name', 'guarantor2Occupation', 'guarantor2Phone',
    ];

    $errors = [];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = "Please fill all required fields. Missing: $field";
        }
    }

    $userPassport = upload_file($_FILES['userPassport'], ['image/jpeg', 'image/png', 'image/gif'], 5 * 1024 * 1024);
    $guarantor1Passport = upload_file($_FILES['guarantor1Passport'], ['image/jpeg', 'image/png', 'image/gif'], 5 * 1024 * 1024);
    $guarantor2Passport = upload_file($_FILES['guarantor2Passport'], ['image/jpeg', 'image/png', 'image/gif'], 5 * 1024 * 1024);

    if (!$userPassport || !$guarantor1Passport || !$guarantor2Passport) {
        $errors[] = "Invalid file upload.";
    }


    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header('Location: ' . BASE_URL . 'pages/apply.php');
        exit();
    }

    try {
        $stmt = $db->prepare("INSERT INTO LoanApplications (
            user_id, hubCategory, fullName, membershipNumber, email, phone,
            loanPurpose, loanAmount, monthlyIncome, existingSavings,
            guarantor1Name, guarantor1Occupation, guarantor1Phone, guarantor1Passport,
            guarantor2Name, guarantor2Occupation, guarantor2Phone, guarantor2Passport,
            userPassport, status
        ) VALUES (
            :user_id, :hubCategory, :fullName, :membershipNumber, :email, :phone,
            :loanPurpose, :loanAmount, :monthlyIncome, :existingSavings,
            :guarantor1Name, :guarantor1Occupation, :guarantor1Phone, :guarantor1Passport,
            :guarantor2Name, :guarantor2Occupation, :guarantor2Phone, :guarantor2Passport,
            :userPassport, :status
        )");

        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':hubCategory' => $_POST['hubCategory'],
            ':fullName' => $_POST['fullName'],
            ':membershipNumber' => $_POST['membershipNumber'],
            ':email' => $_POST['email'],
            ':phone' => $_POST['phone'],
            ':loanPurpose' => $_POST['loanPurpose'],
            ':loanAmount' => $_POST['loanAmount'],
            ':monthlyIncome' => $_POST['monthlyIncome'],
            ':existingSavings' => $_POST['existingSavings'],
            ':guarantor1Name' => $_POST['guarantor1Name'],
            ':guarantor1Occupation' => $_POST['guarantor1Occupation'],
            ':guarantor1Phone' => $_POST['guarantor1Phone'],
            ':guarantor1Passport' => $guarantor1Passport,
            ':guarantor2Name' => $_POST['guarantor2Name'],
            ':guarantor2Occupation' => $_POST['guarantor2Occupation'],
            ':guarantor2Phone' => $_POST['guarantor2Phone'],
            ':guarantor2Passport' => $guarantor2Passport,
            ':userPassport' => $userPassport,
            ':status' => 'Pending'
        ]);

        header('Location: ' . BASE_URL . 'pages/thank_you.php');
        exit();
    } catch (PDOException $e) {
        error_log("Error inserting data: " . $e->getMessage(), 3, BASE_PATH . 'logs/errors.log');
        header('Location: ' . BASE_URL . 'pages/error.php');
        exit;
    }
} else {
    header('Location: ' . BASE_URL . 'pages/apply.php');
    exit();
}
?>
