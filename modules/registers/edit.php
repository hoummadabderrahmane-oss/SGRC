<?php
$pageTitle = trans('edit_register'); $activeModule = 'registers';
require_once __DIR__ . '/../../includes/header.php';
if (!can('registers.edit')) { app()->redirect('index.php', 'error', trans('access_denied')); }
$id = (int)($_GET['id'] ?? 0); if (!$id) { app()->redirect('index.php', 'error', 'Invalid ID'); }
$db = app()->db();
$stmt = $db->prepare("SELECT * FROM register_books WHERE id = :id"); $stmt->execute([':id' => $id]);
$book = $stmt->fetch(); if (!$book) { app()->redirect('index.php', 'error', 'Register not found'); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!app()->validateCsrf($_POST['csrf_token'] ?? '')) { $error = trans('invalid_csrf'); }
    else {
        $d = ['register_number' => app()->sanitize($_POST['register_number'] ?? ''), 'register_type' => $_POST['register_type'] ?? 'birth', 'year' => (int)($_POST['year'] ?? date('Y')), 'page_count' => (int)($_POST['page_count'] ?? 0), 'status' => $_POST['status'] ?? 'active', 'location' => app()->sanitize($_POST['location'] ?? ''), 'notes' => app()->sanitize($_POST['notes'] ?? '')];
        if (empty($d['register_number'])) { $error = trans('required_field'); }
        else {
            try {
                $db->prepare("UPDATE register_books SET register_number=:rn, register_type=:rt, register_type_label=:rtl, register_type_label_fr=:rtlf, year=:y, page_count=:pc, status=:st, location=:l, notes=:no WHERE id=:id")
                   ->execute([':rn' => $d['register_number'], ':rt' => $d['register_type'], ':rtl' => trans('register_type_' . $d['register_type']), ':rtlf' => $d['register_type'], ':y' => $d['year'], ':pc' => $d['page_count'], ':st' => $d['status'], ':l' => $d['location'] ?: null, ':no' => $d['notes'] ?: null, ':id' => $id]);
                app()->logActivity('register_updated', "Updated register #{$id}: {$d['register_number']}", 'registers');
                app()->redirect("view.php?id=$id", 'success', trans('register_updated'));
            } catch (PDOException $e) { error_log("Edit register error: " . $e->getMessage()); $error = trans('error'); }
        }
    }
}
$types = ['birth' => trans('register_type_birth'), 'death' => trans('register_type_death'), 'marriage' => trans('register_type_marriage'), 'divorce' => trans('register_type_divorce'), 'family' => trans('register_type_family'), 'residence' => trans('register_type_residence'), 'other' => trans('other')];
?>
<div class="page-header"><h2><i class="bi bi-pencil text-warning"></i> <?php echo trans('edit_register'); ?></h2></div>
<?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
<div class="chart-card">
    <form method="POST" action="" class="row g-3">
        <?php csrfField(); ?>
        <div class="col-md-6"><label class="form-label"><?php echo trans('register_number'); ?> *</label><input type="text" class="form-control form-control-custom" name="register_number" required value="<?php echo $book['register_number']; ?>"></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('register_type'); ?> *</label><select class="form-select form-control-custom" name="register_type" required><?php foreach ($types as $k => $v): ?><option value="<?php echo $k; ?>" <?php echo $book['register_type'] === $k ? 'selected' : ''; ?>><?php echo $v; ?></option><?php endforeach; ?></select></div>
        <div class="col-md-4"><label class="form-label"><?php echo trans('year'); ?> *</label><input type="number" class="form-control form-control-custom" name="year" required value="<?php echo $book['year']; ?>"></div>
        <div class="col-md-4"><label class="form-label"><?php echo trans('page_count'); ?></label><input type="number" class="form-control form-control-custom" name="page_count" value="<?php echo $book['page_count']; ?>"></div>
        <div class="col-md-4"><label class="form-label"><?php echo trans('status'); ?></label><select class="form-select form-control-custom" name="status"><option value="active" <?php echo $book['status'] === 'active' ? 'selected' : ''; ?>><?php echo trans('active'); ?></option><option value="archived" <?php echo $book['status'] === 'archived' ? 'selected' : ''; ?>><?php echo trans('archived'); ?></option><option value="closed" <?php echo $book['status'] === 'closed' ? 'selected' : ''; ?>><?php echo trans('inactive'); ?></option></select></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('location'); ?></label><input type="text" class="form-control form-control-custom" name="location" value="<?php echo $book['location'] ?? ''; ?>"></div>
        <div class="col-12"><label class="form-label"><?php echo trans('notes'); ?></label><textarea class="form-control form-control-custom" name="notes" rows="3"><?php echo $book['notes'] ?? ''; ?></textarea></div>
        <div class="col-12 d-flex gap-2"><button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-lg"></i> <?php echo trans('save'); ?></button><a href="view.php?id=<?php echo $id; ?>" class="btn btn-secondary"><i class="bi bi-x-lg"></i> <?php echo trans('cancel'); ?></a></div>
    </form>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>