<?php
$pageTitle = 'Edit Register - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$id = intval($_GET['id'] ?? 0);
$register = $db->query("SELECT * FROM registers WHERE id = ?", [$id])->fetch();

if (!$register) {
    header('Location: index.php');
    exit;
}

$citizens = $db->query("SELECT id, national_id, first_name, last_name FROM citizens ORDER BY first_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $register_type = $_POST['register_type'] ?? '';
    $citizen_id = $_POST['citizen_id'] ?: null;
    $event_date = $_POST['event_date'] ?? '';
    $event_place = $_POST['event_place'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $status = $_POST['status'] ?? 'active';
    
    $document_path = $register['document_path'];
    if (!empty($_FILES['document']['name'])) {
        $uploadDir = __DIR__ . '/../../uploads/documents/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $document_path = 'uploads/documents/' . time() . '_' . basename($_FILES['document']['name']);
        move_uploaded_file($_FILES['document']['tmp_name'], __DIR__ . '/../../' . $document_path);
    }
    
    $db->query("UPDATE registers SET register_type = ?, citizen_id = ?, event_date = ?, event_place = ?, notes = ?, document_path = ?, status = ? WHERE id = ?",
        [$register_type, $citizen_id, $event_date, $event_place, $notes, $document_path, $status, $id]);
    
    header('Location: index.php');
    exit;
}
?>

<div class="container-fluid">
    <h2><?php echo $lang['edit'] ?? 'Edit'; ?> <?php echo $lang['registers'] ?? 'Register'; ?></h2>
    
    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Register Number</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($register['register_number']); ?>" disabled>
        </div>
        <div class="col-md-6">
            <label class="form-label">Register Type *</label>
            <select name="register_type" class="form-select" required>
                <?php foreach (['birth','death','marriage','divorce'] as $t): ?>
                <option value="<?php echo $t; ?>" <?php echo $register['register_type'] === $t ? 'selected' : ''; ?>><?php echo $lang[$t] ?? $t; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Citizen</label>
            <select name="citizen_id" class="form-select">
                <option value="">-- Select Citizen --</option>
                <?php foreach ($citizens as $c): ?>
                <option value="<?php echo $c['id']; ?>" <?php echo ($register['citizen_id'] == $c['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['first_name'] . ' ' . $c['last_name'] . ' (' . $c['national_id'] . ')'); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Event Date *</label>
            <input type="date" name="event_date" class="form-control" value="<?php echo $register['event_date']; ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Event Place</label>
            <input type="text" name="event_place" class="form-control" value="<?php echo htmlspecialchars($register['event_place'] ?? ''); ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <?php foreach (['active','archived','pending'] as $s): ?>
                <option value="<?php echo $s; ?>" <?php echo $register['status'] === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Document</label>
            <input type="file" name="document" class="form-control">
            <?php if ($register['document_path']): ?>
                <small class="d-block mt-1"><a href="/SGRC/<?php echo $register['document_path']; ?>" target="_blank">View current document</a></small>
            <?php endif; ?>
        </div>
        <div class="col-12">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($register['notes'] ?? ''); ?></textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary"><?php echo $lang['save'] ?? 'Save'; ?></button>
            <a href="index.php" class="btn btn-secondary"><?php echo $lang['cancel'] ?? 'Cancel'; ?></a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>