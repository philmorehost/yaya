<?php
// Set the session variable to mark this stage as "visited"
$_SESSION['install_stage'] = 0;

// -- Requirements Check --
$php_version_required = "7.4.0";
$php_version_ok = version_compare(PHP_VERSION, $php_version_required, '>=');
$pdo_mysql_ok = extension_loaded('pdo_mysql');

$all_ok = $php_version_ok && $pdo_mysql_ok;

// If requirements are met, set the session to allow progression
if ($all_ok) {
    $_SESSION['install_stage'] = 1;
}
?>

<div class="text-center">
    <h4>Welcome to the Finance Hub Installation!</h4>
    <p>This wizard will guide you through the setup process. Please ensure the following requirements are met before proceeding.</p>
</div>

<?php if (isset($_GET['error']) && $_GET['error'] === 'requirements'): ?>
    <div class="alert alert-danger mt-3">You must meet all server requirements to proceed.</div>
<?php endif; ?>

<h5 class="mt-4">Server Requirements</h5>
<ul class="list-group mt-3">
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>PHP Version >= <?php echo $php_version_required; ?></span>
        <?php if ($php_version_ok): ?>
            <span class="badge bg-success">OK (<?php echo PHP_VERSION; ?>)</span>
        <?php else: ?>
            <span class="badge bg-danger">FAIL (<?php echo PHP_VERSION; ?>)</span>
        <?php endif; ?>
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>PDO MySQL Extension</span>
        <?php if ($pdo_mysql_ok): ?>
            <span class="badge bg-success">Enabled</span>
        <?php else: ?>
            <span class="badge bg-danger">Not Found</span>
        <?php endif; ?>
    </li>
</ul>

<div class="text-center mt-4">
    <a href="index.php?stage=2" class="btn btn-primary <?php if (!$all_ok) echo 'disabled-link'; ?>" id="next-button">
        Next Step: Database Setup
    </a>
</div>

<script>
    document.getElementById('next-button').addEventListener('click', function(e) {
        if (this.classList.contains('disabled-link')) {
            e.preventDefault();
            alert('Please ensure all server requirements are met before proceeding.');
        }
    });
</script>
