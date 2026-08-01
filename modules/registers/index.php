<?php
$pageTitle = ($lang['registers'] ?? 'Registers') . ' - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$type = $_GET['type'] ?? '';
$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$where = "WHERE 1=1";
$params = [];

if ($type) {
    $where .= " AND r.register_type = ?";
    $params[] = $type;
}
if ($status) {
    $where .= " AND r.status = ?";
    $params[] = $status;
}
if ($search) {
    $where .= " AND (r.register_number LIKE ? OR c.first_name LIKE ? OR c.last_name LIKE ?)";
    array_push($params, "%$search%", "%$search%", "%$search%");
}

$sql = "SELECT r.*, c.first_name, c.last_name FROM registers r LEFT JOIN citizens c ON r.citizen_id = c.id " . $where . " ORDER BY r.created_at DESC LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;
$registers = $db->query($sql, $params)->fetchAll();

$countSql = "SELECT COUNT(*) as total FROM registers r LEFT JOIN citizens c ON r.citizen_id = c.id " . $where;
$total = $db->query($countSql, $params)->fetch()['total'];
$totalPages = ceil($total / $perPage);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo $lang['registers'] ?? 'Registers'; ?></h2>
        <a href="create.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i><?php echo $lang['create'] ?? 'Create'; ?>
        </a>
    </div>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3">
            <select name="type" class="form-select">
                <option value=""><?php echo $lang['all_types'] ?? 'All Types'; ?></option>
                <?php foreach (['birth','death','marriage','divorce'] as $t): ?>
                <option value="<?php echo $t; ?>" <?php echo $type === $t ? 'selected' : ''; ?>><?php echo $lang[$t] ?? $t; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value=""><?php echo $lang['all_status'] ?? 'All Status'; ?></option>
                <?php foreach (['active','archived','pending'] as $s): ?>
                <option value="<?php echo $s; ?>" <?php echo $status === $s ? 'selected' : ''; ?>><?php echo $lang[$s] ?? ucfirst($s); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="<?php echo $lang['search'] ?? 'Search'; ?>..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-secondary w-100"><?php echo $lang['filter'] ?? 'Filter'; ?></button>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th><?php echo $lang['register_number'] ?? 'Register #'; ?></th>
                            <th><?php echo $lang['register_type'] ?? 'Type'; ?></th>
                            <th><?php echo $lang['citizen'] ?? 'Citizen'; ?></th>
                            <th><?php echo $lang['event_date'] ?? 'Event Date'; ?></th>
                            <th><?php echo $lang['status'] ?? 'Status'; ?></th>
                            <th class="text-end"><?php echo $lang['actions'] ?? 'Actions'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registers as $reg): ?>
                        <tr>
                            <td class="fw-semibold"><?php echo htmlspecialchars($reg['register_number']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $reg['register_type'] === 'birth' ? 'success' : ($reg['register_type'] === 'death' ? 'dark' : ($reg['register_type'] === 'marriage' ? 'info' : 'warning')); ?>">
                                    <?php echo $lang[$reg['register_type']] ?? $reg['register_type']; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars(($reg['first_name'] ?? '') . ' ' . ($reg['last_name'] ?? '')); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($reg['event_date'])); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $reg['status'] === 'active' ? 'success' : ($reg['status'] === 'pending' ? 'warning' : 'secondary'); ?>">
                                    <?php echo $lang[$reg['status']] ?? ucfirst($reg['status']); ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="view.php?id=<?php echo $reg['id']; ?>" class="btn btn-sm btn-info" title="<?php echo $lang['view'] ?? 'View'; ?>"><i class="fas fa-eye"></i></a>
                                    <a href="edit.php?id=<?php echo $reg['id']; ?>" class="btn btn-sm btn-warning" title="<?php echo $lang['edit'] ?? 'Edit'; ?>"><i class="fas fa-edit"></i></a>
                                    <a href="archive.php?id=<?php echo $reg['id']; ?>" class="btn btn-sm btn-secondary" onclick="return confirm('<?php echo $lang['confirm_delete'] ?? 'Are you sure?'; ?>')" title="<?php echo $lang['archive'] ?? 'Archive'; ?>"><i class="fas fa-archive"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($registers)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted"><i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i><?php echo $lang['no_records'] ?? 'No records found'; ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($totalPages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
            <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>&type=<?php echo $type; ?>&status=<?php echo $status; ?>&search=<?php echo urlencode($search); ?>"><i class="fas fa-chevron-<?php echo $isRTL ? 'right' : 'left'; ?>"></i></a></li>
            <?php endif; ?>
            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>"><a class="page-link" href="?page=<?php echo $i; ?>&type=<?php echo $type; ?>&status=<?php echo $status; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
            <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>&type=<?php echo $type; ?>&status=<?php echo $status; ?>&search=<?php echo urlencode($search); ?>"><i class="fas fa-chevron-<?php echo $isRTL ? 'left' : 'right'; ?>"></i></a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>