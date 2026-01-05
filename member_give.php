<?php
require_once 'admin/init.php';

if (!isset($_SESSION['member_id'])) {
    header('Location: member_login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['giving_submit'])) {
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed.');
    }

    $member_id = $_SESSION['member_id'];
    $type = $_POST['type'];
    $amount = $_POST['amount'];
    $date = $_POST['giving_date'];

    try {
        $stmt = $pdo->prepare("INSERT INTO giving (member_id, type, amount, giving_date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$member_id, $type, $amount, $date]);

        // Set a success message and redirect back to the dashboard
        $_SESSION['success_message'] = "Your giving has been recorded successfully!";
    } catch (PDOException $e) {
        // Set an error message and redirect
        $_SESSION['error_message'] = "Error recording your giving: " . $e->getMessage();
    }

    header('Location: member_dashboard.php');
    exit();
} else {
    // Redirect if accessed directly without POST
    header('Location: member_dashboard.php');
    exit();
}
?>
