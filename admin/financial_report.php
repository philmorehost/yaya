<?php
require_once 'init.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Giving Report
$giving_stmt = $pdo->prepare("SELECT type, SUM(amount) as total FROM giving WHERE giving_date BETWEEN ? AND ? GROUP BY type");
$giving_stmt->execute([$start_date, $end_date]);
$giving_report = $giving_stmt->fetchAll(PDO::FETCH_ASSOC);

// Expenditure Report
$expenditure_stmt = $pdo->prepare("SELECT category, SUM(amount) as total FROM expenditures WHERE expenditure_date BETWEEN ? AND ? GROUP BY category");
$expenditure_stmt->execute([$start_date, $end_date]);
$expenditure_report = $expenditure_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Financial Report</h2>

        <div class="card mb-4">
            <div class="card-body">
                <form class="row g-3">
                    <div class="col-md-5">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="col-md-5">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Generate</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Giving Report</div>
                    <div class="card-body">
                        <table class="table table-striped table-dark">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($giving_report as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['type']); ?></td>
                                        <td><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($row['total'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Expenditure Report</div>
                    <div class="card-body">
                        <table class="table table-striped table-dark">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($expenditure_report as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                                        <td><?php echo CURRENCY_SYMBOL; ?><?php echo number_format($row['total'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
