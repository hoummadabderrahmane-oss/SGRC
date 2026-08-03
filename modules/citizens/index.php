<?php
$pageTitle = (isset($lang['citizens']) ? $lang['citizens'] : 'Citizens') . ' - SGRC';
require_once dirname(__FILE__) . '/../../includes/header.php';
require_once dirname(__FILE__) . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$page = max(1, intval(isset($_GET['page']) ? $_GET['page'] : 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$search = isset($_GET['search']) ? $_GET['search'] : '';

$where = "";
$params = array();
if ($search) {
    $where = " WHERE (first_name LIKE ? OR family_name LIKE ? OR national_id LIKE ?)";
    $params = array("%$search%", "%$search%", "%$search%");
}

$sql = "SELECT * FROM citizens" . $where . " ORDER BY created_at DESC LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;
$citizens = $db->query($sql, $params)->fetchAll();

$countSql = "SELECT COUNT(*) as total FROM citizens" . $where;
$total = $db->query($countSql, $params)->fetch()['total'];
$totalPages = ceil($total / $perPage);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo isset($lang['citizens']) ? $lang['citizens'] : 'Citizens'; ?></h2>
        <a href="create.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i><?php echo isset($lang['create']) ? $lang['create'] : 'Create'; ?>
        </a>
    </div>
    
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
                            <th><?php echo isset($lang['national_id']) ? $lang['national_id'] : 'National ID'; ?></th>
                            <th><?php echo isset($lang['full_name']) ? $lang['full_name'] : 'Name'; ?></th>
                            <th><?php echo isset($lang['birth_date']) ? $lang['birth_date'] : 'Birth Date'; ?></th>
                            <th><?php echo isset($lang['gender']) ? $lang['gender'] : 'Gender'; ?></th>
                            <th><?php echo isset($lang['address']) ? $lang['address'] : 'Address'; ?></th>
                            <th class="text-end"><?php echo isset($lang['actions']) ? $lang['actions'] : 'Actions'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($citizens as $citizen): ?>
                        <tr>
                            <td><?php echo $citizen['id']; ?></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($citizen['national_id']); ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2" style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;">
                                        <?php 
                                        $fn = isset($citizen['first_name']) ? substr($citizen['first_name'], 0, 1) : '';
                                        $ln = isset($citizen['family_name']) ? substr($citizen['family_name'], 0, 1) : '';
                                        echo strtoupper($fn . $ln); 
                                        ?>
                                    </div>
                                    <?php echo htmlspecialchars($citizen['first_name'] . ' ' . $citizen['family_name']); ?>
                                </div>
                            </td>
                            <td><?php echo !empty($citizen['birth_date']) && $citizen['birth_date'] != '0000-00-00' ? date('d/m/Y', strtotime($citizen['birth_date'])) : '-'; ?></td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-<?php echo $citizen['gender'] === 'male' ? 'mars text-primary' : 'venus text-danger'; ?> me-1"></i>
                                    <?php echo isset($lang[$citizen['gender']]) ? $lang[$citizen['gender']] : $citizen['gender']; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($citizen['address'] ?? '-'); ?></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="view.php?id=<?php echo $citizen['id']; ?>" class="btn btn-sm btn-info" title="<?php echo isset($lang['view']) ? $lang['view'] : 'View'; ?>">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?php echo $citizen['id']; ?>" class="btn btn-sm btn-warning" title="<?php echo isset($lang['edit']) ? $lang['edit'] : 'Edit'; ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="print.php?id=<?php echo $citizen['id']; ?>" class="btn btn-sm btn-secondary" target="_blank" title="<?php echo isset($lang['print']) ? $lang['print'] : 'Print'; ?>">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $citizen['id']; ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('<?php echo isset($lang['confirm_delete']) ? $lang['confirm_delete'] : 'Are you sure?'; ?>')" 
                                       title="<?php echo isset($lang['delete']) ? $lang['delete'] : 'Delete'; ?>">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($citizens)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
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
</div>

<?php require_once dirname(__FILE__) . '/../../includes/footer.php'; ?>