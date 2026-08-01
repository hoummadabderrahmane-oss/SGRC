<?php
$pageTitle = ($lang['settings'] ?? 'Settings') . ' - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$db = Database::getInstance();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['settings'] as $key => $value) {
        $db->query("UPDATE settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
    }
    $message = $lang['settings_saved'] ?? 'Settings saved successfully';
}

$settings = $db->query("SELECT * FROM settings ORDER BY setting_group, setting_key")->fetchAll();
$grouped = [];
foreach ($settings as $s) {
    $grouped[$s['setting_group']][] = $s;
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo $lang['settings'] ?? 'Settings'; ?></h2>
    </div>

    <?php if ($message): ?>
    <div class="alert alert-success d-flex align-items-center mb-4">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo $message; ?>
    </div>
    <?php endif; ?>

    <form method="POST">
        <?php foreach ($grouped as $group => $items): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0 fw-bold"><i class="fas fa-cog me-2 text-primary"></i><?php echo ucfirst($group); ?></h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach ($items as $item): ?>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold"><?php echo ucwords(str_replace('_', ' ', $item['setting_key'])); ?></label>
                        <input type="text" name="settings[<?php echo $item['setting_key']; ?>]" class="form-control" value="<?php echo htmlspecialchars($item['setting_value']); ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i><?php echo $lang['save'] ?? 'Save Settings'; ?>
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>