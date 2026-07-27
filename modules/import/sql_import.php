<?php
$pageTitle = 'SQL Import - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$db = Database::getInstance();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['sql']['tmp_name'])) {
    $sql = file_get_contents($_FILES['sql']['tmp_name']);
    try {
        $db->getConnection()->exec($sql);
        $message = 'SQL imported successfully';
        
        $db->query("INSERT INTO import_history (import_type, file_name, records_processed, records_success, imported_by) VALUES (?, ?, ?, ?, ?)",
            ['sql', $_FILES['sql']['name'], 0, 0, $_SESSION['user_id']]);
    } catch (Exception $e) {
        $error = 'Import failed: ' . $e->getMessage();
    }
}
?>

<div class="container-fluid">
    <h2>Import SQL</h2>
    <div class="alert alert-warning">Warning: This will execute SQL directly. Use with caution!</div>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">SQL File (.sql)</label>
            <input type="file" name="sql" class="form-control" accept=".sql" required>
        </div>
        <button type="submit" class="btn btn-danger">Import SQL</button>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>