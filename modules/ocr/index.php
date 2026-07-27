<?php
$pageTitle = 'OCR - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();
?>

<div class="container-fluid">
    <h2>OCR Document Processing</h2>
    
    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5>Scan Document</h5>
                    <p>Upload and extract text from scanned documents</p>
                    <a href="/modules/registers/ocr.php" class="btn btn-primary">Process Scan</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5>Upload Scan</h5>
                    <p>Upload scanned documents to registers</p>
                    <a href="/modules/registers/upload_scan.php" class="btn btn-primary">Upload Scan</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>