<?php
$pageTitle = 'Citizens - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$search = $_GET['search'] ?? '';
$params = [];

$sql = "SELECT * FROM citizens WHERE 1=1";
if ($search) {
    $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR national_id LIKE ?)";
    $params = array_fill(0, 3, "%$search%");
}
$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;

$citizens = $db->query($sql, $params)->fetchAll();

$countSql = "SELECT COUNT(*) as total FROM citizens WHERE 1=1";
$countParams = [];
if ($search) {
    $countSql .= " AND (first_name LIKE ? OR last_name LIKE ? OR national_id LIKE ?)";
    $countParams = array_fill(0, 3, "%$search%");
}
$total = $db->query($countSql, $countParams)->fetch()['total'];
$totalPages = ceil($total / $perPage);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo $lang['citizens'] ?? 'Citizens'; ?></h2>
        <a href="create.php" class="btn btn-primary"><?php echo $lang['create'] ?? 'Create'; ?></a>
    </div>
    
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by name or national ID..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-outline-secondary"><?php echo $lang['search'] ?? 'Search'; ?></button>
        </div>
    </form>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>National ID</th>
                        <th>Name</th>
                        <th>Date of Birth</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th><?php echo $lang['actions'] ?? 'Actions'; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($citizens as $citizen): ?>
                    <tr>
                        <td><?php echo $citizen['id']; ?></td>
                        <td><?php echo htmlspecialchars($citizen['national_id']); ?></td>
                        <td><?php echo htmlspecialchars($citizen['first_name'] . ' ' . $citizen['last_name']); ?></td>
                        <td><?php echo $citizen['date_of_birth']; ?></td>
                        <td><?php echo $lang[$citizen['gender']] ?? $citizen['gender']; ?></td>
                        <td><?php echo htmlspecialchars($citizen['phone'] ?? '-'); ?></td>
                        <td>
                            <a href="view.php?id=<?php echo $citizen['id']; ?>" class="btn btn-sm btn-info"><?php echo $lang['view'] ?? 'View'; ?></a>
                            <a href="edit.php?id=<?php echo $citizen['id']; ?>" class="btn btn-sm btn-warning"><?php echo $lang['edit'] ?? 'Edit'; ?></a>
                            <a href="print.php?id=<?php echo $citizen['id']; ?>" class="btn btn-sm btn-secondary" target="_blank"><?php echo $lang['print'] ?? 'Print'; ?></a>
                            <a href="delete.php?id=<?php echo $citizen['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('<?php echo $lang['confirm_delete'] ?? 'Are you sure?'; ?>')"><?php echo $lang['delete'] ?? 'Delete'; ?></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($citizens)): ?>
                    <tr><td colspan="7" class="text-center"><?php echo $lang['no_records'] ?? 'No records found'; ?></td></tr>
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
                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>