<?php
$pageTitle = 'Settings - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$db = Database::getInstance();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['settings'] as $key => $value) {
        $db->query("UPDATE settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
    }
    $message = 'Settings saved successfully';
}

$settings = $db->query("SELECT * FROM settings ORDER BY setting_group, setting_key")->fetchAll();
$grouped = [];
foreach ($settings as $s) {
    $grouped[$s['setting_group']][] = $s;
}
?>

<div class="container-fluid">
    <h2><?php echo $lang['settings'] ?? 'Settings'; ?></h2>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <?php foreach ($grouped as $group => $items): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><?php echo ucfirst($group); ?></h5>
            </div>
            <div class="card-body">
                <?php foreach ($items as $item): ?>
                <div class="mb-3">
                    <label class="form-label"><?php echo ucwords(str_replace('_', ' ', $item['setting_key'])); ?></label>
                    <input type="text" name="settings[<?php echo $item['setting_key']; ?>]" class="form-control" value="<?php echo htmlspecialchars($item['setting_value']); ?>">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
        
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>