<?php
require_once 'admin/init.php';

if (!isset($_SESSION['member_id'])) {
    header('Location: member_login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_department'])) {
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed.');
    }

    $member_id = $_SESSION['member_id'];
    $department_id = $_POST['department_id'];

    try {
        // Check if the member has already applied to this department
        $check_stmt = $pdo->prepare("SELECT * FROM department_applications WHERE member_id = ? AND department_id = ?");
        $check_stmt->execute([$member_id, $department_id]);
        if ($check_stmt->rowCount() > 0) {
            $_SESSION['error_message'] = "You have already applied to this department.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO department_applications (member_id, department_id) VALUES (?, ?)");
            $stmt->execute([$member_id, $department_id]);
            $_SESSION['success_message'] = "Your application has been submitted successfully!";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error submitting your application: " . $e->getMessage();
    }

    header('Location: member_dashboard.php');
    exit();
} else {
    header('Location: member_dashboard.php');
    exit();
}
?>
