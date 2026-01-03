<?php
require_once 'config.php';
try {
    $db = new PDO('sqlite:' . __DIR__ . '/data/loan_applications.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db->exec("CREATE TABLE IF NOT EXISTS LoanApplications (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        hubCategory TEXT,
        fullName TEXT,
        membershipNumber TEXT,
        email TEXT,
        phone TEXT,
        loanPurpose TEXT,
        loanAmount REAL,
        monthlyIncome REAL,
        existingSavings REAL,
        guarantor1Name TEXT,
        guarantor1MemberId TEXT,
        guarantor2Name TEXT,
        guarantor2MemberId TEXT,
        status TEXT
    )");
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage(), 3, __DIR__ . '/logs/errors.log');
    header('Location: ' . BASE_URL . 'pages/error.php');
    exit;
}
?>
