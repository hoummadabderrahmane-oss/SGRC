<?php
$pageTitle = ($lang['users'] ?? 'Users') . ' - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$db = Database::getInstance();
$users = $db->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 50")->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo $lang['users'] ?? 'Users'; ?></h2>
        <a href="create.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i><?php echo $lang['add_user'] ?? 'Add User'; ?>
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo $lang['username'] ?? 'Username'; ?></th>
                            <th><?php echo $lang['full_name'] ?? 'Full Name'; ?></th>
                            <th><?php echo $lang['email'] ?? 'Email'; ?></th>
                            <th><?php echo $lang['role'] ?? 'Role'; ?></th>
                            <th><?php echo $lang['status'] ?? 'Status'; ?></th>
                            <th class="text-end"><?php echo $lang['actions'] ?? 'Actions'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?php echo $u['id']; ?></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($u['username']); ?></td>
                            <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $u['role'] === 'admin' ? 'danger' : 'info'; ?>">
                                    <?php echo $lang[$u['role']] ?? ucfirst($u['role']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $u['is_active'] ? 'success' : 'secondary'; ?>">
                                    <?php echo $u['is_active'] ? ($lang['active'] ?? 'Active') : ($lang['inactive'] ?? 'Inactive'); ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="edit.php?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-warning" title="<?php echo $lang['edit'] ?? 'Edit'; ?>"><i class="fas fa-edit"></i></a>
                                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                    <a href="toggle.php?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-secondary" title="<?php echo $u['is_active'] ? ($lang['deactivate'] ?? 'Deactivate') : ($lang['activate'] ?? 'Activate'); ?>">
                                        <i class="fas fa-<?php echo $u['is_active'] ? 'ban' : 'check'; ?>"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($users)): ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted"><i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i><?php echo $lang['no_records'] ?? 'No records found'; ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>