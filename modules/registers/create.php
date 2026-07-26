<?php
$pageTitle = trans('add_register'); $activeModule = 'registers';
require_once __DIR__ . '/../../includes/header.php';
if (!can('registers.create')) { app()->redirect('index.php', 'error', trans('access_denied')); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!app()->validateCsrf($_POST['csrf_token'] ?? '')) { $error = trans('invalid_csrf'); }
    else {
        $d = ['register_number' => app()->sanitize($_POST['register_number'] ?? ''), 'register_type' => $_POST['register_type'] ?? 'birth', 'year' => (int)($_POST['year'] ?? date('Y')), 'page_count' => (int)($_POST['page_count'] ?? 0), 'location' => app()->sanitize($_POST['location'] ?? ''), 'notes' => app()->sanitize($_POST['notes'] ?? '')];
        if (empty($d['register_number'])) { $error = trans('required_field'); }
        else {
            try {
                $db = app()->db();
                $chk = $db->prepare("SELECT id FROM register_books WHERE register_number = :rn"); $chk->execute([':rn' => $d['register_number']]); if ($chk->fetch()) { $error = 'Register number already exists'; }
                if (!$error) {
                    $db->prepare("INSERT INTO register_books (register_number, register_type, register_type_label, register_type_label_fr, year, page_count, location, notes, created_by) VALUES (:rn, :rt, :rtl, :rtlf, :y, :pc, :l, :no, :cb)")->execute([':rn' => $d['register_number'], ':rt' => $d['register_type'], ':rtl' => trans('register_type_' . $d['register_type']), ':rtlf' => $d['register_type'], ':y' => $d['year'], ':pc' => $d['page_count'], ':l' => $d['location'] ?: null, ':no' => $d['notes'] ?: null, ':cb' => session()->getUserId()]);
                    $rid = $db->lastInsertId();
                    app()->logActivity('register_created', "Created register #{$rid}: {$d['register_number']}", 'registers');
                    app()->redirect("view.php?id=$rid", 'success', trans('register_added'));
                }
            } catch (PDOException $e) { error_log("Create register error: " . $e->getMessage()); $error = trans('error'); }
        }
    }
}
$types = ['birth' => trans('register_type_birth'), 'death' => trans('register_type_death'), 'marriage' => trans('register_type_marriage'), 'divorce' => trans('register_type_divorce'), 'family' => trans('register_type_family'), 'residence' => trans('register_type_residence'), 'other' => trans('other')];
?>
<div class="page-header"><h2><i class="bi bi-journal-plus text-primary"></i> <?php echo trans('add_register'); ?></h2></div>
<?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
<div class="chart-card">
    <form method="POST" action="" class="row g-3">
        <?php csrfField(); ?>
        <div class="col-md-6"><label class="form-label"><?php echo trans('register_number'); ?> *</label><input type="text" class="form-control form-control-custom" name="register_number" required placeholder="e.g. 1980/530" value="<?php echo $_POST['register_number'] ?? ''; ?>"></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('register_type'); ?> *</label><select class="form-select form-control-custom" name="register_type" required><?php foreach ($types as $k => $v): ?><option value="<?php echo $k; ?>" <?php echo ($_POST['register_type'] ?? 'birth') === $k ? 'selected' : ''; ?>><?php echo $v; ?></option><?php endforeach; ?></select></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('year'); ?> *</label><input type="number" class="form-control form-control-custom" name="year" required value="<?php echo $_POST['year'] ?? date('Y'); ?>"></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('page_count'); ?></label><input type="number" class="form-control form-control-custom" name="page_count" value="<?php echo $_POST['page_count'] ?? '0'; ?>"></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('location'); ?></label><input type="text" class="form-control form-control-custom" name="location" placeholder="e.g. Main Archive" value="<?php echo $_POST['location'] ?? ''; ?>"></div>
        <div class="col-12"><label class="form-label"><?php echo trans('notes'); ?></label><textarea class="form-control form-control-custom" name="notes" rows="3"><?php echo $_POST['notes'] ?? ''; ?></textarea></div>
        <div class="col-12 d-flex gap-2"><button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-lg"></i> <?php echo trans('save'); ?></button><a href="index.php" class="btn btn-secondary"><i class="bi bi-x-lg"></i> <?php echo trans('cancel'); ?></a></div>
    </form>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>