<?php
$pageTitle = 'Edit User - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$db = Database::getInstance();
$id = intval($_GET['id'] ?? 0);
$user = $db->query("SELECT * FROM users WHERE id = ?", [$id])->fetch();

if (!$user) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $role = $_POST['role'] ?? 'operator';
    
    $db->query("UPDATE users SET email = ?, full_name = ?, role = ? WHERE id = ?", [$email, $full_name, $role, $id]);
    
    if (!empty($_POST['password'])) {
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $db->query("UPDATE users SET password_hash = ? WHERE id = ?", [$hash, $id]);
    }
    
    header('Location: index.php');
    exit;
}
?>

<div class="container-fluid">
    <h2>Edit User</h2>
    
    <form method="POST" class="row g-3 col-md-6">
        <div class="col-12">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
        </div>
        <div class="col-12">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="col-12">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
        </div>
        <div class="col-12">
            <label class="form-label">New Password (leave blank to keep current)</label>
            <input type="password" name="password" class="form-control" minlength="6">
        </div>
        <div class="col-12">
            <label class="form-label">Role</label>
            <select name="role" class="form-select">
                <option value="operator" <?php echo $user['role'] === 'operator' ? 'selected' : ''; ?>>Operator</option>
                <option value="viewer" <?php echo $user['role'] === 'viewer' ? 'selected' : ''; ?>>Viewer</option>
                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>