<?php
$pageTitle = 'Excel Import - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$message = '';
// Placeholder - would use PhpSpreadsheet library
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['excel']['tmp_name'])) {
    $message = 'Excel import requires PhpSpreadsheet. Please install via Composer: composer require phpoffice/phpspreadsheet';
}
?>

<div class="container-fluid">
    <h2>Import Excel</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Excel File (.xlsx, .xls)</label>
            <input type="file" name="excel" class="form-control" accept=".xlsx,.xls" required>
        </div>
        <button type="submit" class="btn btn-primary">Import</button>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>