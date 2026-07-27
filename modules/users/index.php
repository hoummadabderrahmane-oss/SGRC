<?php
$pageTitle = 'Users - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$db = Database::getInstance();
$users = $db->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo $lang['users'] ?? 'Users'; ?></h2>
        <a href="create.php" class="btn btn-primary">Add User</a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><?php echo htmlspecialchars($u['username']); ?></td>
                        <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><span class="badge bg-<?php echo $u['role'] === 'admin' ? 'danger' : 'info'; ?>"><?php echo $u['role']; ?></span></td>
                        <td><span class="badge bg-<?php echo $u['is_active'] ? 'success' : 'secondary'; ?>"><?php echo $u['is_active'] ? 'Active' : 'Inactive'; ?></span></td>
                        <td>
                            <a href="edit.php?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                            <a href="toggle.php?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-secondary"><?php echo $u['is_active'] ? 'Deactivate' : 'Activate'; ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>