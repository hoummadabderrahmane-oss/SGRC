<?php
$pageTitle = trans('citizens'); $activeModule = 'citizens';
require_once __DIR__ . '/../../includes/header.php';
if (!can('citizens.view')) { app()->redirect('/modules/dashboard/index.php', 'error', trans('access_denied')); }
$db = app()->db();
$search = $_GET['search'] ?? ''; $neighborhood = $_GET['neighborhood'] ?? ''; $gender = $_GET['gender'] ?? '';
$where = []; $params = [];
if ($search) { $where[] = "(family_name LIKE :s OR first_name LIKE :s OR father_name LIKE :s OR national_id LIKE :s)"; $params[':s'] = "%$search%"; }
if ($neighborhood) { $where[] = "neighborhood = :n"; $params[':n'] = $neighborhood; }
if ($gender) { $where[] = "gender = :g"; $params[':g'] = $gender; }
$wc = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$stmt = $db->prepare("SELECT * FROM citizens $wc ORDER BY created_at DESC"); $stmt->execute($params);
$citizens = $stmt->fetchAll();
$neighborhoods = $db->query("SELECT DISTINCT neighborhood FROM citizens WHERE neighborhood IS NOT NULL AND neighborhood != '' ORDER BY neighborhood")->fetchAll(PDO::FETCH_COLUMN);
?>
<div class="page-header d-flex justify-content-between align-items-center flex-wrap">
    <div><h2><i class="bi bi-people-fill text-primary"></i> <?php echo trans('citizens'); ?></h2><p class="text-muted"><?php echo trans('total_citizens'); ?>: <?php echo count($citizens); ?></p></div>
    <?php if (can('citizens.create')): ?><a href="create.php" class="btn btn-primary-custom"><i class="bi bi-person-plus"></i> <?php echo trans('add_citizen'); ?></a><?php endif; ?>
</div>
<div class="chart-card mb-4">
    <form method="GET" action="" class="row g-3">
        <div class="col-md-4"><div class="input-group"><span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span><input type="text" class="form-control form-control-custom" name="search" placeholder="<?php echo trans('search_citizen'); ?>" value="<?php echo htmlspecialchars($search); ?>"></div></div>
        <div class="col-md-3"><select class="form-select form-control-custom" name="neighborhood"><option value=""><?php echo trans('neighborhood'); ?> - <?php echo trans('all'); ?></option><?php foreach ($neighborhoods as $n): ?><option value="<?php echo $n; ?>" <?php echo $neighborhood === $n ? 'selected' : ''; ?>><?php echo $n; ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><select class="form-select form-control-custom" name="gender"><option value=""><?php echo trans('gender'); ?> - <?php echo trans('all'); ?></option><option value="male" <?php echo $gender === 'male' ? 'selected' : ''; ?>><?php echo trans('male'); ?></option><option value="female" <?php echo $gender === 'female' ? 'selected' : ''; ?>><?php echo trans('female'); ?></option></select></div>
        <div class="col-md-2"><button type="submit" class="btn btn-primary-custom w-100"><i class="bi bi-funnel"></i> <?php echo trans('filter'); ?></button></div>
    </form>
</div>
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover" id="citizensTable">
            <thead><tr><th>#</th><th><?php echo trans('photo'); ?></th><th><?php echo trans('national_id'); ?></th><th><?php echo trans('full_name'); ?></th><th><?php echo trans('birth_date'); ?></th><th><?php echo trans('address'); ?></th><th><?php echo trans('phone'); ?></th><th><?php echo trans('actions'); ?></th></tr></thead>
            <tbody>
                <?php foreach ($citizens as $i => $c): ?>
                <tr>
                    <td><?php echo $i + 1; ?></td>
                    <td><?php if ($c['photo_path']): ?><img src="/<?php echo $c['photo_path']; ?>" class="rounded-circle" width="40" height="40" style="object-fit:cover;"><?php else: ?><div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width:40px;height:40px;"><?php echo mb_substr($c['first_name'], 0, 1); ?></div><?php endif; ?></td>
                    <td><code><?php echo $c['national_id'] ?? '-'; ?></code></td>
                    <td><strong><?php echo $c['family_name'] . ' ' . $c['first_name']; ?></strong><br><small class="text-muted"><?php echo $c['father_name'] ?? ''; ?></small></td>
                    <td><?php echo formatDate($c['birth_date']); ?></td>
                    <td><?php echo $c['address'] ?? '-'; ?></td>
                    <td><?php echo $c['phone'] ?? '-'; ?></td>
                    <td><div class="btn-group"><a href="view.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-info" title="<?php echo trans('view'); ?>"><i class="bi bi-eye"></i></a><?php if (can('citizens.edit')): ?><a href="edit.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-warning" title="<?php echo trans('edit'); ?>"><i class="bi bi-pencil"></i></a><?php endif; ?><?php if (can('citizens.delete')): ?><a href="delete.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-danger" title="<?php echo trans('delete'); ?>" onclick="return confirmDelete('<?php echo trans('delete_citizen'); ?>?')"><i class="bi bi-trash"></i></a><?php endif; ?></div></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>document.addEventListener('DOMContentLoaded', () => { initDataTable('#citizensTable', { pageLength: 25, order: [[0, 'asc']], columnDefs: [{ orderable: false, targets: [1, 7] }] }); });</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>