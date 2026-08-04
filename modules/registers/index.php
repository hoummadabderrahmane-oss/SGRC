<?php
$pageTitle = (isset($lang['registers']) ? $lang['registers'] : 'Registers') . ' - SGRC';
require_once dirname(__FILE__) . '/../../includes/header.php';
require_once dirname(__FILE__) . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();

// Check if registers table exists
$tableExists = false;
try {
    $check = $db->query("SHOW TABLES LIKE 'registers'")->fetchAll();
    $tableExists = !empty($check);
} catch (Exception $e) {
    $tableExists = false;
}

$registers = array();
$total = 0;
$totalPages = 0;
$page = 1;
$search = '';

if ($tableExists) {
    $page = max(1, intval(isset($_GET['page']) ? $_GET['page'] : 1));
    $perPage = 20;
    $offset = ($page - 1) * $perPage;
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    $where = "";
    $params = array();
    if ($search) {
        $where = " WHERE (c.first_name LIKE ? OR c.family_name LIKE ? OR c.national_id LIKE ? OR r.description LIKE ?)";
        $params = array("%$search%", "%$search%", "%$search%", "%$search%");
    }

    $sql = "SELECT r.*, c.first_name, c.family_name, c.national_id 
            FROM registers r 
            LEFT JOIN citizens c ON r.citizen_id = c.id" . $where . " 
            ORDER BY r.created_at DESC LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;
    $registers = $db->query($sql, $params)->fetchAll();

    $countSql = "SELECT COUNT(*) as total FROM registers r LEFT JOIN citizens c ON r.citizen_id = c.id" . $where;
    $total = $db->query($countSql, $params)->fetch()['total'];
    $totalPages = ceil($total / $perPage);
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo isset($lang['registers']) ? $lang['registers'] : 'Registers'; ?></h2>
        <?php if ($tableExists): ?>
        <a href="create.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i><?php echo isset($lang['create']) ? $lang['create'] : 'Create'; ?>
        </a>
        <?php endif; ?>
    </div>

    <?php if (!$tableExists): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong><?php echo isset($lang['table_missing']) ? $lang['table_missing'] : 'Table Missing'; ?>:</strong>
        <?php echo isset($lang['registers_table_not_found']) ? $lang['registers_table_not_found'] : 'The registers table does not exist in the database. Please run the setup SQL to create it.'; ?>
    </div>
    <?php else: ?>

    <form method="GET" class="mb-4">
        <div class="input-group">
            <span class="input-group-text bg-light">
                <i class="fas fa-search text-muted"></i>
            </span>
            <input type="text" name="search" class="form-control" 
                   placeholder="<?php echo isset($lang['search']) ? $lang['search'] : 'Search'; ?>..." 
                   value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-outline-secondary">
                <?php echo isset($lang['search']) ? $lang['search'] : 'Search'; ?>
            </button>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo isset($lang['citizen']) ? $lang['citizen'] : 'Citizen'; ?></th>
                            <th><?php echo isset($lang['national_id']) ? $lang['national_id'] : 'National ID'; ?></th>
                            <th><?php echo isset($lang['description']) ? $lang['description'] : 'Description'; ?></th>
                            <th><?php echo isset($lang['date']) ? $lang['date'] : 'Date'; ?></th>
                            <th class="text-end"><?php echo isset($lang['actions']) ? $lang['actions'] : 'Actions'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registers as $reg): ?>
                        <tr>
                            <td><?php echo $reg['id']; ?></td>
                            <td>
                                <a href="../citizens/view.php?id=<?php echo $reg['citizen_id']; ?>" class="text-decoration-none fw-semibold">
                                    <?php echo htmlspecialchars(($reg['first_name'] ?? '') . ' ' . ($reg['family_name'] ?? '')); ?>
                                </a>
                            </td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($reg['national_id'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($reg['description'] ?? '-'); ?></td>
                            <td><?php echo !empty($reg['created_at']) ? date('d/m/Y H:i', strtotime($reg['created_at'])) : '-'; ?></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="view.php?id=<?php echo $reg['id']; ?>" class="btn btn-sm btn-info" title="<?php echo isset($lang['view']) ? $lang['view'] : 'View'; ?>">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?php echo $reg['id']; ?>" class="btn btn-sm btn-warning" title="<?php echo isset($lang['edit']) ? $lang['edit'] : 'Edit'; ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $reg['id']; ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('<?php echo isset($lang['confirm_delete']) ? $lang['confirm_delete'] : 'Are you sure?'; ?>')" 
                                       title="<?php echo isset($lang['delete']) ? $lang['delete'] : 'Delete'; ?>">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($registers)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                                <?php echo isset($lang['no_records']) ? $lang['no_records'] : 'No records found'; ?>
                            </td>
                        </tr>
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
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
            <?php endif; ?>
            
            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
            </li>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>

    <?php endif; ?>
</div>

<?php require_once dirname(__FILE__) . '/../../includes/footer.php'; ?>