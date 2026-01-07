<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';
require_once dirname(__DIR__) . '/includes/utils.php';

if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

$page_title = 'System Update';
include 'includes/header.php';

$messages = [];
$errors = [];

// --- Utility function to check if a column exists ---
$columnExists = function ($tableName, $columnName) use ($db) {
    try {
        $stmt = $db->prepare("SHOW COLUMNS FROM `$tableName` LIKE :columnName");
        $stmt->execute([':columnName' => $columnName]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        // Table might not exist, in which case the column doesn't either.
        return false;
    }
};


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "CSRF token validation failed.";
    } else {
        $messages[] = "Starting database update...";

        // Each operation is now wrapped in its own try-catch block for granular error reporting.

        try {
            $messages[] = "Checking 'Users' table...";
            $db->exec("
                CREATE TABLE IF NOT EXISTS `Users` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `fullName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
                  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                   PRIMARY KEY (`id`),
                   UNIQUE KEY `email` (`email`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
            $messages[] = "'Users' table check complete.";
        } catch (PDOException $e) {
            $errors[] = "Error with 'Users' table: " . $e->getMessage();
        }

        try {
            $messages[] = "Checking 'LoanApplications' table...";
            $db->exec("
                CREATE TABLE IF NOT EXISTS `LoanApplications` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `hubCategory` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `fullName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `membershipNumber` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `loanPurpose` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `loanAmount` decimal(10,2) DEFAULT NULL,
                  `monthlyIncome` decimal(10,2) DEFAULT NULL,
                  `existingSavings` decimal(10,2) DEFAULT NULL,
                  `guarantor1Name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `guarantor2Name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
                  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                  `disbursed_at` timestamp NULL DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  FOREIGN KEY (`user_id`) REFERENCES `Users`(`id`) ON DELETE RESTRICT
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
            $messages[] = "'LoanApplications' table check complete.";
        } catch (PDOException $e) {
            $errors[] = "Error with 'LoanApplications' table: " . $e->getMessage();
        }

        try {
            $messages[] = "Checking 'Articles' table...";
            $db->exec("
                CREATE TABLE IF NOT EXISTS `Articles` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
                  `author_id` int(11) NOT NULL,
                  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                  PRIMARY KEY (`id`),
                  FOREIGN KEY (`author_id`) REFERENCES `Users`(`id`) ON DELETE RESTRICT
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
            $messages[] = "'Articles' table check complete.";
        } catch (PDOException $e) {
            $errors[] = "Error with 'Articles' table: " . $e->getMessage();
        }

        try {
            $messages[] = "Checking 'Loans' table...";
            $db->exec("
                CREATE TABLE IF NOT EXISTS `Loans` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `application_id` INT NOT NULL,
                    `user_id` INT NOT NULL,
                    `amount` DECIMAL(10, 2) NOT NULL,
                    `balance` DECIMAL(10, 2) NOT NULL,
                    `next_due_date` DATE NOT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (`application_id`) REFERENCES `LoanApplications`(`id`) ON DELETE RESTRICT,
                    FOREIGN KEY (`user_id`) REFERENCES `Users`(`id`) ON DELETE RESTRICT
                );
            ");
             $messages[] = "'Loans' table check complete.";
        } catch (PDOException $e) {
            $errors[] = "Error with 'Loans' table: " . $e->getMessage();
        }

        try {
            $messages[] = "Checking 'Repayments' table...";
            $db->exec("
                CREATE TABLE IF NOT EXISTS `Repayments` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `loan_id` INT NOT NULL,
                    `amount_paid` DECIMAL(10, 2) NOT NULL,
                    `payment_date` DATE NOT NULL,
                    `recorded_by` INT NOT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (`loan_id`) REFERENCES `Loans`(`id`) ON DELETE CASCADE,
                    FOREIGN KEY (`recorded_by`) REFERENCES `Users`(`id`) ON DELETE RESTRICT
                );
            ");
            $messages[] = "'Repayments' table check complete.";
        } catch (PDOException $e) {
            $errors[] = "Error with 'Repayments' table: " . $e->getMessage();
        }

        // Add columns to existing tables
        $all_columns = [
            'Users' => [
                'status' => "VARCHAR(50) NOT NULL DEFAULT 'active'"
            ],
            'LoanApplications' => [
                'loanPurpose' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL',
                'guarantor1Occupation' => 'VARCHAR(255) DEFAULT NULL',
                'guarantor1Phone' => 'VARCHAR(255) DEFAULT NULL',
                'guarantor1Passport' => 'VARCHAR(255) DEFAULT NULL',
                'guarantor2Occupation' => 'VARCHAR(255) DEFAULT NULL',
                'guarantor2Phone' => 'VARCHAR(255) DEFAULT NULL',
                'guarantor2Passport' => 'VARCHAR(255) DEFAULT NULL',
                'userPassport' => 'VARCHAR(255) DEFAULT NULL'
            ],
            'Loans' => [
                 'monthly_repayment' => 'DECIMAL(10, 2) NOT NULL DEFAULT 0.00'
            ]
        ];

        foreach ($all_columns as $table => $columns) {
            foreach ($columns as $column => $definition) {
                try {
                    if (!$columnExists($table, $column)) {
                        $messages[] = "Adding '$column' to '$table' table...";
                        $db->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition;");
                        $messages[] = "'$column' column added successfully.";
                    } else {
                        $messages[] = "'$column' column already exists in '$table'.";
                    }
                } catch (PDOException $e) {
                    $errors[] = "Error adding column '$column' to '$table': " . $e->getMessage();
                }
            }
        }

        if (empty($errors)) {
            $_SESSION['success_message'] = "Database update completed successfully!";
        }
    }
}
?>

<div class="container mt-5">
    <h1>System Database Update</h1>
    <p>This tool will update your database schema to the latest version. It is safe to run this multiple times.</p>
    <p>Click the button below to start the update process.</p>

    <?php
    if (!empty($_SESSION['success_message'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
        unset($_SESSION['success_message']);
    }
    ?>

    <?php if (!empty($messages)): ?>
        <div class="alert alert-info">
            <?php foreach ($messages as $message): ?>
                <p><?php echo htmlspecialchars($message); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>The following errors occurred:</strong>
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="system_update.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <button type="submit" class="btn btn-primary">Run Database Update</button>
            </form>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
