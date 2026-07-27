<?php
$pageTitle = 'Registers - SGRC';
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

$sql = "SELECT r.*, c.first_name, c.last_name FROM registers r LEFT JOIN citizens c ON r.citizen_id = c.id WHERE 1=1";
$params = [];

if ($type) {
    $sql .= " AND r.register_type = ?";
    $params[] = $type;
}
if ($status) {
    $sql .= " AND r.status = ?";
    $params[] = $status;
}
if ($search) {
    $sql .= " AND (r.register_number LIKE ? OR c.first_name LIKE ? OR c.last_name LIKE ?)";
    array_push($params, "%$search%", "%$search%", "%$search%");
}

$sql .= " ORDER BY r.created_at DESC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;

$registers = $db->query($sql, $params)->fetchAll();

$countSql = str_replace("r.*, c.first_name, c.last_name", "COUNT(*) as total", $sql);
$countSql = preg_replace("/ORDER BY.*$/", "", $countSql);
$total = $db->query($countSql, array_slice($params, 0, -2))->fetch()['total'];
$totalPages = ceil($total / $perPage);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo $lang['registers'] ?? 'Registers'; ?></h2>
        <a href="create.php" class="btn btn-primary"><?php echo $lang['create'] ?? 'Create'; ?></a>
    </div>
    
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3">
            <select name="type" class="form-select">
                <option value="">All Types</option>
                <?php foreach (['birth','death','marriage','divorce'] as $t): ?>
                <option value="<?php echo $t; ?>" <?php echo $type === $t ? 'selected' : ''; ?>><?php echo $lang[$t] ?? $t; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <?php foreach (['active','archived','pending'] as $s): ?>
                <option value="<?php echo $s; ?>" <?php echo $status === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-secondary w-100"><?php echo $lang['filter'] ?? 'Filter'; ?></button>
        </div>
    </form>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Register #</th>
                        <th>Type</th>
                        <th>Citizen</th>
                        <th>Event Date</th>
                        <th>Status</th>
                        <th><?php echo $lang['actions'] ?? 'Actions'; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registers as $reg): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reg['register_number']); ?></td>
                        <td><?php echo $lang[$reg['register_type']] ?? $reg['register_type']; ?></td>
                        <td><?php echo htmlspecialchars(($reg['first_name'] ?? '') . ' ' . ($reg['last_name'] ?? '')); ?></td>
                        <td><?php echo $reg['event_date']; ?></td>
                        <td><span class="badge bg-<?php echo $reg['status'] === 'active' ? 'success' : ($reg['status'] === 'pending' ? 'warning' : 'secondary'); ?>"><?php echo $reg['status']; ?></span></td>
                        <td>
                            <a href="view.php?id=<?php echo $reg['id']; ?>" class="btn btn-sm btn-info"><?php echo $lang['view'] ?? 'View'; ?></a>
                            <a href="edit.php?id=<?php echo $reg['id']; ?>" class="btn btn-sm btn-warning"><?php echo $lang['edit'] ?? 'Edit'; ?></a>
                            <a href="archive.php?id=<?php echo $reg['id']; ?>" class="btn btn-sm btn-secondary" onclick="return confirm('Archive this record?')">Archive</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($registers)): ?>
                    <tr><td colspan="6" class="text-center"><?php echo $lang['no_records'] ?? 'No records found'; ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if ($totalPages > 1): ?>
    <nav class="mt-3">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>&type=<?php echo $type; ?>&status=<?php echo $status; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>