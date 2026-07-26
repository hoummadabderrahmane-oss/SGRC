<?php
$pageTitle = trans('add') . ' ' . trans('register_pages'); $activeModule = 'registers';
require_once __DIR__ . '/../../includes/header.php';
if (!can('registers.create')) { app()->redirect('index.php', 'error', trans('access_denied')); }
$bookId = (int)($_GET['book_id'] ?? 0); if (!$bookId) { app()->redirect('index.php', 'error', 'Invalid Book ID'); }
$db = app()->db();
$stmt = $db->prepare("SELECT * FROM register_books WHERE id = :id"); $stmt->execute([':id' => $bookId]);
$book = $stmt->fetch(); if (!$book) { app()->redirect('index.php', 'error', 'Register not found'); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!app()->validateCsrf($_POST['csrf_token'] ?? '')) { $error = trans('invalid_csrf'); }
    else {
        $d = ['page_number' => (int)($_POST['page_number'] ?? 1), 'sequential_number' => (int)($_POST['sequential_number'] ?? 1), 'record_date' => $_POST['record_date'] ?? date('Y-m-d'), 'full_name' => app()->sanitize($_POST['full_name'] ?? ''), 'birth_date' => $_POST['birth_date'] ?? null, 'birth_place' => app()->sanitize($_POST['birth_place'] ?? ''), 'father_name' => app()->sanitize($_POST['father_name'] ?? ''), 'mother_name' => app()->sanitize($_POST['mother_name'] ?? ''), 'family_name' => app()->sanitize($_POST['family_name'] ?? ''), 'address' => app()->sanitize($_POST['address'] ?? ''), 'id_number' => app()->sanitize($_POST['id_number'] ?? ''), 'notes' => app()->sanitize($_POST['notes'] ?? ''), 'citizen_id' => (int)($_POST['citizen_id'] ?? 0)];
        if (empty($d['full_name'])) { $error = trans('required_field'); }
        else {
            try {
                $scanPath = null;
                if (!empty($_FILES['scan']['tmp_name'])) {
                    $up = app()->uploadFile($_FILES['scan'], 'scans', ['jpg', 'jpeg', 'png', 'pdf']);
                    if ($up['success']) $scanPath = $up['path'];
                }
                $db->prepare("INSERT INTO register_pages (register_book_id, page_number, sequential_number, record_date, full_name, birth_date, birth_place, father_name, mother_name, family_name, address, id_number, notes, scan_path, citizen_id, created_by) VALUES (:bid, :pn, :sn, :rd, :fn, :bd, :bp, :fan, :mn, :fam, :a, :idn, :no, :sp, :cid, :cb)")
                   ->execute([':bid' => $bookId, ':pn' => $d['page_number'], ':sn' => $d['sequential_number'], ':rd' => $d['record_date'], ':fn' => $d['full_name'], ':bd' => $d['birth_date'] ?: null, ':bp' => $d['birth_place'] ?: null, ':fan' => $d['father_name'] ?: null, ':mn' => $d['mother_name'] ?: null, ':fam' => $d['family_name'] ?: null, ':a' => $d['address'] ?: null, ':idn' => $d['id_number'] ?: null, ':no' => $d['notes'] ?: null, ':sp' => $scanPath, ':cid' => $d['citizen_id'] ?: null, ':cb' => session()->getUserId()]);
                app()->logActivity('register_page_created', "Added page to register #{$bookId}", 'registers');
                app()->redirect("view.php?id=$bookId", 'success', 'Page added successfully');
            } catch (PDOException $e) { error_log("Create page error: " . $e->getMessage()); $error = trans('error'); }
        }
    }
}
$citizens = $db->query("SELECT id, family_name, first_name, national_id FROM citizens ORDER BY family_name, first_name")->fetchAll();
$lastPage = $db->prepare("SELECT MAX(page_number) as max_page, MAX(sequential_number) as max_seq FROM register_pages WHERE register_book_id = :id"); $lastPage->execute([':id' => $bookId]); $lastPage = $lastPage->fetch();
?>
<div class="page-header"><h2><i class="bi bi-file-earmark-plus text-primary"></i> <?php echo trans('add'); ?> - <?php echo $book['register_number']; ?></h2></div>
<?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
<div class="chart-card">
    <form method="POST" action="" enctype="multipart/form-data" class="row g-3">
        <?php csrfField(); ?>
        <div class="col-md-3"><label class="form-label"><?php echo trans('page_number'); ?></label><input type="number" class="form-control form-control-custom" name="page_number" value="<?php echo $_POST['page_number'] ?? (($lastPage['max_page'] ?? 0) + 1); ?>"></div>
        <div class="col-md-3"><label class="form-label"><?php echo trans('sequential_number'); ?></label><input type="number" class="form-control form-control-custom" name="sequential_number" value="<?php echo $_POST['sequential_number'] ?? (($lastPage['max_seq'] ?? 0) + 1); ?>"></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('record_date'); ?></label><input type="date" class="form-control form-control-custom" name="record_date" value="<?php echo $_POST['record_date'] ?? date('Y-m-d'); ?>"></div>
        <div class="col-12"><label class="form-label"><?php echo trans('full_name'); ?> *</label><input type="text" class="form-control form-control-custom" name="full_name" required placeholder="e.g. محمد بن أحمد دريس" value="<?php echo $_POST['full_name'] ?? ''; ?>"></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('family_name'); ?></label><input type="text" class="form-control form-control-custom" name="family_name" value="<?php echo $_POST['family_name'] ?? ''; ?>"></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('father_name'); ?></label><input type="text" class="form-control form-control-custom" name="father_name" value="<?php echo $_POST['father_name'] ?? ''; ?>"></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('mother_name'); ?></label><input type="text" class="form-control form-control-custom" name="mother_name" value="<?php echo $_POST['mother_name'] ?? ''; ?>"></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('birth_date'); ?></label><input type="date" class="form-control form-control-custom" name="birth_date" value="<?php echo $_POST['birth_date'] ?? ''; ?>"></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('birth_place'); ?></label><input type="text" class="form-control form-control-custom" name="birth_place" value="<?php echo $_POST['birth_place'] ?? ''; ?>"></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('id_number'); ?></label><input type="text" class="form-control form-control-custom" name="id_number" placeholder="e.g. Y196928" value="<?php echo $_POST['id_number'] ?? ''; ?>"></div>
        <div class="col-12"><label class="form-label"><?php echo trans('address'); ?></label><textarea class="form-control form-control-custom" name="address" rows="2"><?php echo $_POST['address'] ?? ''; ?></textarea></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('scan'); ?> / <?php echo trans('upload_scan'); ?></label><input type="file" class="form-control" name="scan" accept="image/*,.pdf"></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('citizens'); ?> (<?php echo trans('attach_to_citizen'); ?>)</label><select class="form-select form-control-custom" name="citizen_id"><option value=""><?php echo trans('select'); ?></option><?php foreach ($citizens as $c): ?><option value="<?php echo $c['id']; ?>" <?php echo ($_POST['citizen_id'] ?? '') == $c['id'] ? 'selected' : ''; ?>><?php echo $c['family_name'] . ' ' . $c['first_name'] . ' (' . ($c['national_id'] ?? '') . ')'; ?></option><?php endforeach; ?></select></div>
        <div class="col-12"><label class="form-label"><?php echo trans('notes'); ?></label><textarea class="form-control form-control-custom" name="notes" rows="2"><?php echo $_POST['notes'] ?? ''; ?></textarea></div>
        <div class="col-12 d-flex gap-2"><button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-lg"></i> <?php echo trans('save'); ?></button><a href="view.php?id=<?php echo $bookId; ?>" class="btn btn-secondary"><i class="bi bi-x-lg"></i> <?php echo trans('cancel'); ?></a></div>
    </form>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>