<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';
require_once dirname(__DIR__) . '/includes/utils.php';

// Admin authentication check
if (!isset($_SESSION['is_loggedin']) || $_SESSION['is_loggedin'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

$page_title = 'System Update';
include 'includes/header.php';

$messages = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "CSRF token validation failed.";
    } else {
        try {
            $messages[] = "Starting database update...";

            // Create AdminSettings table
            $messages[] = "Creating 'AdminSettings' table if it doesn't exist...";
            $db->exec("
                CREATE TABLE IF NOT EXISTS `AdminSettings` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `setting_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                  `setting_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `setting_key` (`setting_key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
            $messages[] = "'AdminSettings' table created or already exists.";

            // Create PaymentNotifications table
            $messages[] = "Creating 'PaymentNotifications' table if it doesn't exist...";
            $db->exec("
                CREATE TABLE IF NOT EXISTS `PaymentNotifications` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `loan_id` int(11) NOT NULL,
                  `amount` decimal(10,2) NOT NULL,
                  `payment_date` date NOT NULL,
                  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
                  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
            $messages[] = "'PaymentNotifications' table created or already exists.";

            // Add guarantor columns to 'LoanApplications' table
            $loan_columns = [
                'guarantor1Occupation' => 'VARCHAR(255) DEFAULT NULL',
                'guarantor1Phone' => 'VARCHAR(255) DEFAULT NULL',
                'guarantor1Passport' => 'VARCHAR(255) DEFAULT NULL',
                'guarantor2Occupation' => 'VARCHAR(255) DEFAULT NULL',
                'guarantor2Phone' => 'VARCHAR(255) DEFAULT NULL',
                'guarantor2Passport' => 'VARCHAR(255) DEFAULT NULL',
                'userPassport' => 'VARCHAR(255) DEFAULT NULL'
            ];

            foreach ($loan_columns as $column => $definition) {
                $stmt = $db->query("SHOW COLUMNS FROM `LoanApplications` LIKE '$column'");
                if ($stmt->rowCount() == 0) {
                    $messages[] = "Adding '$column' column to 'LoanApplications' table...";
                    $db->exec("ALTER TABLE LoanApplications ADD COLUMN $column $definition;");
                    $messages[] = "'$column' column added.";
                } else {
                    $messages[] = "'$column' column already exists in 'LoanApplications' table.";
                }
            }

            // Add status column to 'Users' table
            $stmt = $db->query("SHOW COLUMNS FROM `Users` LIKE 'status'");
            if ($stmt->rowCount() == 0) {
                $messages[] = "Adding 'status' column to 'Users' table...";
                $db->exec("ALTER TABLE Users ADD COLUMN status VARCHAR(50) NOT NULL DEFAULT 'active';");
                $messages[] = "'status' column added.";
            } else {
                $messages[] = "'status' column already exists in 'Users' table.";
            }

            $messages[] = "Database update completed successfully!";

            // Add monthly_repayment column to 'Loans' table
            $stmt = $db->query("SHOW COLUMNS FROM `Loans` LIKE 'monthly_repayment'");
            if ($stmt->rowCount() == 0) {
                $messages[] = "Adding 'monthly_repayment' column to 'Loans' table...";
                $db->exec("ALTER TABLE Loans ADD COLUMN monthly_repayment DECIMAL(10, 2) NOT NULL DEFAULT 0.00;");
                $messages[] = "'monthly_repayment' column added.";
            } else {
                $messages[] = "'monthly_repayment' column already exists in 'Loans' table.";
            }

            // Create Repayments table
            $messages[] = "Creating 'Repayments' table if it doesn't exist...";
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
            $messages[] = "'Repayments' table created or already exists.";

        } catch (PDOException $e) {
            $errors[] = "An error occurred during the update: " . $e->getMessage();
        }
    }
}
?>

<div class="container mt-5">
    <h1>System Database Update</h1>
    <p>This tool will update your database schema to the latest version. It is safe to run this multiple times.</p>
    <p>Click the button below to start the update process.</p>

    <?php if (!empty($messages)): ?>
        <div class="alert alert-info">
            <?php foreach ($messages as $message): ?>
                <p><?php echo htmlspecialchars($message); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
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
