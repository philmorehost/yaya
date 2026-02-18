<?php
require_once 'config/db_connect.php';
require_once 'includes/public_header.php';

$workforce_units = $pdo->query("SELECT * FROM workforce_units ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $workforce_unit_id = $_POST['workforce_unit_id'];

    $stmt = $pdo->prepare("INSERT INTO connection_requests (name, email, phone, workforce_unit_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $workforce_unit_id]);

    $message = '<div class="alert alert-success mt-4">Thank you for your interest! We will get back to you shortly.</div>';
}
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h2>Connect With Us</h2>
        <p class="lead">Join a workforce unit and serve with your gifts.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form method="post">
                        <?php echo $message; ?>
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="workforce_unit_id" class="form-label">I'm interested in...</label>
                            <select class="form-select" id="workforce_unit_id" name="workforce_unit_id" required>
                                <option value="">Choose a unit...</option>
                                <?php foreach ($workforce_units as $unit): ?>
                                    <option value="<?php echo $unit['id']; ?>"><?php echo htmlspecialchars($unit['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" style="background-color: #007bff; border-color: #007bff;">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/public_footer.php'; ?>
