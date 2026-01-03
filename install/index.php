<?php
$stage = isset($_GET['stage']) ? (int)$_GET['stage'] : 1;

// Prevent jumping to a stage without completing the previous one
if ($stage > 1 && (!isset($_SESSION['install_stage']) || $_SESSION['install_stage'] < $stage - 1)) {
    header('Location: index.php');
    exit;
}

$page_title = "Installation Wizard - Stage " . $stage;
include 'header.php';

switch ($stage) {
    case 2:
        include 'stage2.php';
        break;
    case 3:
        include 'stage3.php';
        break;
    default:
        include 'stage1.php';
        break;
}

include 'footer.php';
?>
