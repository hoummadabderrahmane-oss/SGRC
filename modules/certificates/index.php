<?php
$pageTitle = 'Certificates - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$type = $_GET['type'] ?? '';
$status = $_GET['status'] ?? '';

$sql = "SELECT c.*, r.register_number, ci.first_name, ci.last_name FROM certificates c LEFT JOIN registers r ON c.register_id = r.id LEFT JOIN citizens ci ON r.citizen_id = ci.id WHERE 1=1";
$params = [];

if ($type) {
    $sql .= " AND c.certificate_type = ?";
    $params[] = $type;
}
if ($status) {
    $sql .= " AND c.status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY c.created_at DESC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;

$certificates = $db->query($sql, $params)->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo $lang['certificates'] ?? 'Certificates'; ?></h2>
    </div>
    
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-4">
            <select name="type" class="form-select">
                <option value="">All Types</option>
                <?php foreach (['birth','death','marriage','residence','nationality'] as $t): ?>
                <option value="<?php echo $t; ?>" <?php echo $type === $t ? 'selected' : ''; ?>><?php echo $lang[$t] ?? $t; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <?php foreach (['valid','expired','revoked'] as $s): ?>
                <option value="<?php echo $s; ?>" <?php echo $status === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-outline-secondary w-100"><?php echo $lang['filter'] ?? 'Filter'; ?></button>
        </div>
    </form>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Certificate #</th>
                        <th>Type</th>
                        <th>Citizen</th>
                        <th>Issue Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($certificates as $cert): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cert['certificate_number']); ?></td>
                        <td><?php echo $lang[$cert['certificate_type']] ?? $cert['certificate_type']; ?></td>
                        <td><?php echo htmlspecialchars(($cert['first_name'] ?? '') . ' ' . ($cert['last_name'] ?? '')); ?></td>
                        <td><?php echo $cert['issue_date']; ?></td>
                        <td><span class="badge bg-<?php echo $cert['status'] === 'valid' ? 'success' : ($cert['status'] === 'expired' ? 'warning' : 'danger'); ?>"><?php echo $cert['status']; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($certificates)): ?>
                    <tr><td colspan="5" class="text-center"><?php echo $lang['no_records'] ?? 'No records found'; ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>