<?php
$pageTitle = 'Import Data - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$history = $db->query("SELECT * FROM import_history ORDER BY imported_at DESC LIMIT 10")->fetchAll();
?>

<div class="container-fluid">
    <h2><?php echo $lang['import'] ?? 'Import'; ?> Data</h2>
    
    <div class="row g-4 mt-2">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>CSV Import</h5>
                    <p>Import citizens from CSV file</p>
                    <a href="csv_import.php" class="btn btn-primary">Import CSV</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Excel Import</h5>
                    <p>Import citizens from Excel file</p>
                    <a href="excel_import.php" class="btn btn-primary">Import Excel</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>SQL Import</h5>
                    <p>Import database from SQL dump</p>
                    <a href="sql_import.php" class="btn btn-primary">Import SQL</a>
                </div>
            </div>
        </div>
    </div>
    
    <h4 class="mt-5">Import History</h4>
    <div class="card">
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>File</th>
                        <th>Records</th>
                        <th>Success</th>
                        <th>Failed</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $h): ?>
                    <tr>
                        <td><?php echo strtoupper($h['import_type']); ?></td>
                        <td><?php echo htmlspecialchars($h['file_name'] ?? '-'); ?></td>
                        <td><?php echo $h['records_processed']; ?></td>
                        <td class="text-success"><?php echo $h['records_success']; ?></td>
                        <td class="text-danger"><?php echo $h['records_failed']; ?></td>
                        <td><?php echo $h['imported_at']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($history)): ?>
                    <tr><td colspan="6" class="text-center">No import history</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>