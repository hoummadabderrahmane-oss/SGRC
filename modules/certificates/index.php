<?php
$pageTitle = ($lang['certificates'] ?? 'Certificates') . ' - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$type = $_GET['type'] ?? '';
$status = $_GET['status'] ?? '';

$where = "WHERE 1=1";
$params = [];

if ($type) {
    $where .= " AND c.certificate_type = ?";
    $params[] = $type;
}
if ($status) {
    $where .= " AND c.status = ?";
    $params[] = $status;
}

$sql = "SELECT c.*, r.register_number, ci.first_name, ci.last_name FROM certificates c LEFT JOIN registers r ON c.register_id = r.id LEFT JOIN citizens ci ON r.citizen_id = ci.id " . $where . " ORDER BY c.created_at DESC LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;
$certificates = $db->query($sql, $params)->fetchAll();

$countSql = "SELECT COUNT(*) as total FROM certificates c " . $where;
$total = $db->query($countSql, $params)->fetch()['total'];
$totalPages = ceil($total / $perPage);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo $lang['certificates'] ?? 'Certificates'; ?></h2>
    </div>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-4">
            <select name="type" class="form-select">
                <option value=""><?php echo $lang['all_types'] ?? 'All Types'; ?></option>
                <?php foreach (['birth','death','marriage','residence','nationality'] as $t): ?>
                <option value="<?php echo $t; ?>" <?php echo $type === $t ? 'selected' : ''; ?>><?php echo $lang[$t] ?? $t; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <select name="status" class="form-select">
                <option value=""><?php echo $lang['all_status'] ?? 'All Status'; ?></option>
                <?php foreach (['valid','expired','revoked'] as $s): ?>
                <option value="<?php echo $s; ?>" <?php echo $status === $s ? 'selected' : ''; ?>><?php echo $lang[$s] ?? ucfirst($s); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-outline-secondary w-100"><?php echo $lang['filter'] ?? 'Filter'; ?></button>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th><?php echo $lang['certificate_number'] ?? 'Certificate #'; ?></th>
                            <th><?php echo $lang['type'] ?? 'Type'; ?></th>
                            <th><?php echo $lang['citizen'] ?? 'Citizen'; ?></th>
                            <th><?php echo $lang['issue_date'] ?? 'Issue Date'; ?></th>
                            <th><?php echo $lang['status'] ?? 'Status'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($certificates as $cert): ?>
                        <tr>
                            <td class="fw-semibold"><?php echo htmlspecialchars($cert['certificate_number']); ?></td>
                            <td><span class="badge bg-info"><?php echo $lang[$cert['certificate_type']] ?? $cert['certificate_type']; ?></span></td>
                            <td><?php echo htmlspecialchars(($cert['first_name'] ?? '') . ' ' . ($cert['last_name'] ?? '')); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($cert['issue_date'])); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $cert['status'] === 'valid' ? 'success' : ($cert['status'] === 'expired' ? 'warning' : 'danger'); ?>">
                                    <?php echo $lang[$cert['status']] ?? ucfirst($cert['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($certificates)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted"><i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i><?php echo $lang['no_records'] ?? 'No records found'; ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($totalPages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>"><a class="page-link" href="?page=<?php echo $i; ?>&type=<?php echo $type; ?>&status=<?php echo $status; ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>