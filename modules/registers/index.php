<?php
$pageTitle = trans('registers'); $activeModule = 'registers';
require_once __DIR__ . '/../../includes/header.php';
if (!can('registers.view')) { app()->redirect('/modules/dashboard/index.php', 'error', trans('access_denied')); }
$db = app()->db();
$search = $_GET['search'] ?? '';
$type = $_GET['type'] ?? '';
$year = $_GET['year'] ?? '';
$status = $_GET['status'] ?? '';
$where = []; $params = [];
if ($search) { $where[] = "(register_number LIKE :s OR location LIKE :s)"; $params[':s'] = "%$search%"; }
if ($type) { $where[] = "register_type = :t"; $params[':t'] = $type; }
if ($year) { $where[] = "year = :y"; $params[':y'] = $year; }
if ($status) { $where[] = "status = :st"; $params[':st'] = $status; }
$wc = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$stmt = $db->prepare("SELECT * FROM register_books $wc ORDER BY year DESC, register_number DESC");
$stmt->execute($params);
$books = $stmt->fetchAll();
$years = $db->query("SELECT DISTINCT year FROM register_books ORDER BY year DESC")->fetchAll(PDO::FETCH_COLUMN);
$types = ['birth' => trans('register_type_birth'), 'death' => trans('register_type_death'), 'marriage' => trans('register_type_marriage'), 'divorce' => trans('register_type_divorce'), 'family' => trans('register_type_family'), 'residence' => trans('register_type_residence'), 'other' => trans('other')];
?>
<div class="page-header d-flex justify-content-between align-items-center flex-wrap">
    <div><h2><i class="bi bi-journal-text text-primary"></i> <?php echo trans('register_books'); ?></h2><p class="text-muted"><?php echo trans('total_registers'); ?>: <?php echo count($books); ?></p></div>
    <?php if (can('registers.create')): ?><a href="create.php" class="btn btn-primary-custom"><i class="bi bi-journal-plus"></i> <?php echo trans('add_register'); ?></a><?php endif; ?>
</div>
<div class="chart-card mb-4">
    <form method="GET" action="" class="row g-3">
        <div class="col-md-3"><div class="input-group"><span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span><input type="text" class="form-control form-control-custom" name="search" placeholder="<?php echo trans('search'); ?>..." value="<?php echo htmlspecialchars($search); ?>"></div></div>
        <div class="col-md-3"><select class="form-select form-control-custom" name="type"><option value=""><?php echo trans('register_type'); ?> - <?php echo trans('all'); ?></option><?php foreach ($types as $k => $v): ?><option value="<?php echo $k; ?>" <?php echo $type === $k ? 'selected' : ''; ?>><?php echo $v; ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><select class="form-select form-control-custom" name="year"><option value=""><?php echo trans('year'); ?></option><?php foreach ($years as $y): ?><option value="<?php echo $y; ?>" <?php echo $year == $y ? 'selected' : ''; ?>><?php echo $y; ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><select class="form-select form-control-custom" name="status"><option value=""><?php echo trans('status'); ?></option><option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>><?php echo trans('active'); ?></option><option value="archived" <?php echo $status === 'archived' ? 'selected' : ''; ?>><?php echo trans('archived'); ?></option><option value="closed" <?php echo $status === 'closed' ? 'selected' : ''; ?>><?php echo trans('inactive'); ?></option></select></div>
        <div class="col-md-2"><button type="submit" class="btn btn-primary-custom w-100"><i class="bi bi-funnel"></i> <?php echo trans('filter'); ?></button></div>
    </form>
</div>
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover" id="registersTable">
            <thead><tr><th>#</th><th><?php echo trans('register_number'); ?></th><th><?php echo trans('register_type'); ?></th><th><?php echo trans('year'); ?></th><th><?php echo trans('page_count'); ?></th><th><?php echo trans('status'); ?></th><th><?php echo trans('location'); ?></th><th><?php echo trans('actions'); ?></th></tr></thead>
            <tbody>
                <?php foreach ($books as $i => $b): ?>
                <tr>
                    <td><?php echo $i + 1; ?></td>
                    <td><span class="badge bg-primary fs-6"><?php echo $b['register_number']; ?></span></td>
                    <td><?php echo trans('register_type_' . $b['register_type']); ?></td>
                    <td><?php echo $b['year']; ?></td>
                    <td><?php echo $b['page_count']; ?></td>
                    <td><span class="badge bg-<?php echo $b['status'] === 'active' ? 'success' : ($b['status'] === 'archived' ? 'warning' : 'secondary'); ?>"><?php echo trans($b['status']); ?></span></td>
                    <td><?php echo $b['location'] ?? '-'; ?></td>
                    <td><div class="btn-group"><a href="view.php?id=<?php echo $b['id']; ?>" class="btn btn-sm btn-info" title="<?php echo trans('view'); ?>"><i class="bi bi-eye"></i></a><?php if (can('registers.edit')): ?><a href="edit.php?id=<?php echo $b['id']; ?>" class="btn btn-sm btn-warning" title="<?php echo trans('edit'); ?>"><i class="bi bi-pencil"></i></a><?php endif; ?><?php if (can('registers.delete')): ?><a href="delete.php?id=<?php echo $b['id']; ?>" class="btn btn-sm btn-danger" title="<?php echo trans('delete'); ?>" onclick="return confirmDelete('<?php echo trans('delete_register'); ?>?')"><i class="bi bi-trash"></i></a><?php endif; ?></div></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>document.addEventListener('DOMContentLoaded', () => { initDataTable('#registersTable', { pageLength: 25, order: [[3, 'desc']], columnDefs: [{ orderable: false, targets: [7] }] }); });</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>