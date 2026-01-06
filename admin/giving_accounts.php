<?php
require_once 'init.php';
check_permission('manage_finance');
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../includes/csrf_check.php';
    if (isset($_POST['add_account'])) {
        $account_name = $_POST['account_name'];
        $account_details = $_POST['account_details'];
        $instructions = $_POST['instructions'];
        $stmt = $pdo->prepare("INSERT INTO giving_accounts (account_name, account_details, instructions) VALUES (?, ?, ?)");
        $stmt->execute([$account_name, $account_details, $instructions]);
    } elseif (isset($_POST['edit_account'])) {
        $id = $_POST['id'];
        $account_name = $_POST['account_name'];
        $account_details = $_POST['account_details'];
        $instructions = $_POST['instructions'];
        $stmt = $pdo->prepare("UPDATE giving_accounts SET account_name = ?, account_details = ?, instructions = ? WHERE id = ?");
        $stmt->execute([$account_name, $account_details, $instructions, $id]);
    } elseif (isset($_POST['delete_account'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM giving_accounts WHERE id = ?");
        $stmt->execute([$id]);
    }
    header('Location: giving_accounts.php');
}

$accounts = $pdo->query("SELECT * FROM giving_accounts ORDER BY account_name")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Giving Accounts</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addAccountModal">Add Account</button>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-dark">
                        <thead>
                            <tr>
                                <th>Account Name</th>
                                <th>Account Details</th>
                                <th>Instructions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($accounts as $account): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($account['account_name']); ?></td>
                                    <td><?php echo nl2br(htmlspecialchars($account['account_details'])); ?></td>
                                    <td><?php echo nl2br(htmlspecialchars($account['instructions'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editAccountModal-<?php echo $account['id']; ?>">Edit</button>
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="id" value="<?php echo $account['id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <button type="submit" name="delete_account" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Account Modal -->
<div class="modal fade" id="addAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">Add New Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="account_name" class="form-label">Account Name</label>
                        <input type="text" class="form-control" id="account_name" name="account_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="account_details" class="form-label">Account Details</label>
                        <textarea class="form-control" id="account_details" name="account_details" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="instructions" class="form-label">Instructions</label>
                        <textarea class="form-control" id="instructions" name="instructions" rows="3"></textarea>
                    </div>
                    <button type="submit" name="add_account" class="btn btn-primary">Save Account</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Account Modals -->
<?php foreach ($accounts as $account): ?>
<div class="modal fade" id="editAccountModal-<?php echo $account['id']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">Edit Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="id" value="<?php echo $account['id']; ?>">
                    <div class="mb-3">
                        <label for="account_name" class="form-label">Account Name</label>
                        <input type="text" class="form-control" id="account_name" name="account_name" value="<?php echo htmlspecialchars($account['account_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="account_details" class="form-label">Account Details</label>
                        <textarea class="form-control" id="account_details" name="account_details" rows="3" required><?php echo htmlspecialchars($account['account_details']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="instructions" class="form-label">Instructions</label>
                        <textarea class="form-control" id="instructions" name="instructions" rows="3"><?php echo htmlspecialchars($account['instructions']); ?></textarea>
                    </div>
                    <button type="submit" name="edit_account" class="btn btn-primary">Update Account</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php require_once '../includes/footer.php'; ?>
