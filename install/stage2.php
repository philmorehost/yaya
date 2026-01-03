<?php
$_SESSION['install_stage'] = 1;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = $_POST['db_host'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];

    // 1. Test the database connection
    try {
        $dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
        $db = new PDO($dsn, $db_user, $db_pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 2. Write the config.ini file
        $config_content = "[database]\n";
        $config_content .= "host = \"{$db_host}\"\n";
        $config_content .= "dbname = \"{$db_name}\"\n";
        $config_content .= "user = \"{$db_user}\"\n";
        $config_content .= "password = \"{$db_pass}\"\n";

        if (file_put_contents('../config.ini', $config_content) === false) {
            throw new Exception("Could not write to config.ini. Please check file permissions.");
        }

        // 3. Execute the schema.sql file
        $sql = file_get_contents('../schema.sql');
        if ($sql === false) {
            throw new Exception("Could not read schema.sql file.");
        }
        $db->exec($sql);

        $_SESSION['install_stage'] = 2;
        header('Location: index.php?stage=3');
        exit;

    } catch (PDOException $e) {
        $error = "Database connection failed: " . $e->getMessage();
    } catch (Exception $e) {
        $error = "An error occurred: " . $e->getMessage();
    }
}
?>

<h4 class="mb-4">Step 2: Database Configuration</h4>
<p>Please provide your MySQL database details below. The installer will test the connection and set up the necessary tables.</p>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form action="index.php?stage=2" method="post">
    <div class="mb-3">
        <label for="db_host" class="form-label">Database Host</label>
        <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
    </div>
    <div class="mb-3">
        <label for="db_name" class="form-label">Database Name</label>
        <input type="text" class="form-control" id="db_name" name="db_name" required>
    </div>
    <div class="mb-3">
        <label for="db_user" class="form-label">Database Username</label>
        <input type="text" class="form-control" id="db_user" name="db_user" required>
    </div>
    <div class="mb-3">
        <label for="db_pass" class="form-label">Database Password</label>
        <input type="password" class="form-control" id="db_pass" name="db_pass">
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-primary">Install Database</button>
    </div>
</form>
