<?php
$pageTitle = trans('upload_scan'); $activeModule = 'registers';
require_once __DIR__ . '/../../includes/header.php';
if (!can('registers.edit')) { app()->redirect('index.php', 'error', trans('access_denied')); }
$bookId = (int)($_GET['book_id'] ?? 0); if (!$bookId) { app()->redirect('index.php', 'error', 'Invalid Book ID'); }
$db = app()->db();
$stmt = $db->prepare("SELECT * FROM register_books WHERE id = :id"); $stmt->execute([':id' => $bookId]);
$book = $stmt->fetch(); if (!$book) { app()->redirect('index.php', 'error', 'Register not found'); }
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!app()->validateCsrf($_POST['csrf_token'] ?? '')) { $error = trans('invalid_csrf'); }
    else {
        if (empty($_FILES['scan']['tmp_name'])) { $error = 'Please select a file to upload'; }
        else {
            $up = app()->uploadFile($_FILES['scan'], 'scans', ['jpg', 'jpeg', 'png', 'pdf']);
            if ($up['success']) {
                $ocrText = '';
                $ocrConfidence = null;
                $db->prepare("INSERT INTO register_pages (register_book_id, page_number, sequential_number, record_date, full_name, scan_path, ocr_text, ocr_confidence, created_by) VALUES (:bid, :pn, :sn, :rd, :fn, :sp, :ot, :oc, :cb)")
                   ->execute([':bid' => $bookId, ':pn' => (int)($_POST['page_number'] ?? 1), ':sn' => (int)($_POST['sequential_number'] ?? 1), ':rd' => $_POST['record_date'] ?? date('Y-m-d'), ':fn' => app()->sanitize($_POST['full_name'] ?? ''), ':sp' => $up['path'], ':ot' => $ocrText ?: null, ':oc' => $ocrConfidence, ':cb' => session()->getUserId()]);
                app()->logActivity('scan_uploaded', "Uploaded scan to register #{$bookId}", 'registers');
                $success = 'Scan uploaded successfully' . ($ocrText ? ' with OCR' : '');
            } else { $error = $up['error'] ?: trans('upload_failed'); }
        }
    }
}
$lastPage = $db->prepare("SELECT MAX(page_number) as max_page, MAX(sequential_number) as max_seq FROM register_pages WHERE register_book_id = :id"); $lastPage->execute([':id' => $bookId]); $lastPage = $lastPage->fetch();
?>
<div class="page-header"><h2><i class="bi bi-upload text-primary"></i> <?php echo trans('upload_scan'); ?> - <?php echo $book['register_number']; ?></h2></div>
<?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
<div class="chart-card">
    <form method="POST" action="" enctype="multipart/form-data" class="row g-3">
        <?php csrfField(); ?>
        <div class="col-md-4"><label class="form-label"><?php echo trans('page_number'); ?></label><input type="number" class="form-control form-control-custom" name="page_number" value="<?php echo $_POST['page_number'] ?? (($lastPage['max_page'] ?? 0) + 1); ?>"></div>
        <div class="col-md-4"><label class="form-label"><?php echo trans('sequential_number'); ?></label><input type="number" class="form-control form-control-custom" name="sequential_number" value="<?php echo $_POST['sequential_number'] ?? (($lastPage['max_seq'] ?? 0) + 1); ?>"></div>
        <div class="col-md-4"><label class="form-label"><?php echo trans('record_date'); ?></label><input type="date" class="form-control form-control-custom" name="record_date" value="<?php echo $_POST['record_date'] ?? date('Y-m-d'); ?>"></div>
        <div class="col-12"><label class="form-label"><?php echo trans('full_name'); ?></label><input type="text" class="form-control form-control-custom" name="full_name" placeholder="<?php echo trans('full_name'); ?>" value="<?php echo $_POST['full_name'] ?? ''; ?>"></div>
        <div class="col-12"><label class="form-label"><?php echo trans('scan'); ?> *</label><input type="file" class="form-control" name="scan" accept="image/*,.pdf" required></div>
        <div class="col-12"><div class="alert alert-info"><i class="bi bi-info-circle"></i> <?php echo trans('ocr_processing'); ?> (Tesseract OCR - ara+fra)</div></div>
        <div class="col-12 d-flex gap-2"><button type="submit" class="btn btn-primary-custom"><i class="bi bi-cloud-upload"></i> <?php echo trans('upload'); ?></button><a href="view.php?id=<?php echo $bookId; ?>" class="btn btn-secondary"><i class="bi bi-x-lg"></i> <?php echo trans('cancel'); ?></a></div>
    </form>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>