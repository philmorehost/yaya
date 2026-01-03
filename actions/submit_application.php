<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';

if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true) {
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
        'guarantor1Name', 'guarantor1MemberId', 'guarantor2Name', 'guarantor2MemberId'
    ];

    $errors = [];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = "Please fill all required fields. Missing: $field";
        }
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
            guarantor1Name, guarantor1MemberId, guarantor2Name, guarantor2MemberId, status
        ) VALUES (
            :user_id, :hubCategory, :fullName, :membershipNumber, :email, :phone,
            :loanPurpose, :loanAmount, :monthlyIncome, :existingSavings,
            :guarantor1Name, :guarantor1MemberId, :guarantor2Name, :guarantor2MemberId, :status
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
            ':guarantor1MemberId' => $_POST['guarantor1MemberId'],
            ':guarantor2Name' => $_POST['guarantor2Name'],
            ':guarantor2MemberId' => $_POST['guarantor2MemberId'],
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
