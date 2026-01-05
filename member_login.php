<?php
require_once 'admin/init.php';
require_once 'includes/public_header.php';

$error_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $membership_id = $_POST['membership_id'];

    if (empty($membership_id)) {
        $error_message = '<div class="alert alert-danger">Please enter your Membership ID.</div>';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM members WHERE membership_id = ?");
        $stmt->execute([$membership_id]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($member) {
            $_SESSION['member_id'] = $member['id'];
            $_SESSION['member_name'] = $member['name'];
            header('Location: member_dashboard.php');
            exit();
        } else {
            $error_message = '<div class="alert alert-danger">Invalid Membership ID.</div>';
        }
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="text-center mb-4">Member Login</h2>
                    <?php echo $error_message; ?>
                    <form method="post">
                        <div class="mb-3">
                            <label for="membership_id" class="form-label">Membership ID</label>
                            <input type="text" class="form-control" id="membership_id" name="membership_id" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="login" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/public_footer.php'; ?>
