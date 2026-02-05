<?php
require_once 'init.php';
?>
<?php require_once 'header.php'; ?>

<h1 class="mt-5">Welcome, <?php echo htmlspecialchars($_SESSION['member_name']); ?>!</h1>
<p>This is your member dashboard. You can use the navigation menu above to access the features available to you.</p>

<?php require_once 'footer.php'; ?>
