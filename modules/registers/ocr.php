<?php
$pageTitle = 'OCR Scan - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$extractedText = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['scan']['name'])) {
    $uploadDir = __DIR__ . '/../../uploads/scans/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $scanPath = $uploadDir . time() . '_' . basename($_FILES['scan']['name']);
    move_uploaded_file($_FILES['scan']['tmp_name'], $scanPath);
    
    // Placeholder for OCR - integrate Tesseract or similar
    $extractedText = "OCR processing would extract text here.\nFile: " . basename($scanPath);
    
    // Save scan reference
    if (isset($_POST['register_id'])) {
        $db = Database::getInstance();
        $db->query("UPDATE registers SET scan_path = ? WHERE id = ?", ['uploads/scans/' . basename($scanPath), $_POST['register_id']]);
    }
}
?>

<div class="container-fluid">
    <h2>OCR Document Scan</h2>
    
    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Upload Scan (Image/PDF)</label>
            <input type="file" name="scan" class="form-control" accept="image/*,.pdf" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Link to Register (Optional)</label>
            <input type="number" name="register_id" class="form-control" placeholder="Register ID">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Process OCR</button>
        </div>
    </form>
    
    <?php if ($extractedText): ?>
    <div class="card mt-4">
        <div class="card-header">
            <h5>Extracted Text</h5>
        </div>
        <div class="card-body">
            <pre><?php echo htmlspecialchars($extractedText); ?></pre>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>