<?php
$pageTitle = 'Create Register - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$error = '';

$citizens = $db->query("SELECT id, national_id, first_name, last_name FROM citizens ORDER BY first_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $register_number = $_POST['register_number'] ?? '';
    $register_type = $_POST['register_type'] ?? '';
    $citizen_id = $_POST['citizen_id'] ?? null;
    $event_date = $_POST['event_date'] ?? '';
    $event_place = $_POST['event_place'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    $exists = $db->query("SELECT id FROM registers WHERE register_number = ?", [$register_number])->fetch();
    if ($exists) {
        $error = 'Register number already exists';
    } else {
        $document_path = null;
        if (!empty($_FILES['document']['name'])) {
            $uploadDir = __DIR__ . '/../../uploads/documents/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $document_path = 'uploads/documents/' . time() . '_' . basename($_FILES['document']['name']);
            move_uploaded_file($_FILES['document']['tmp_name'], __DIR__ . '/../../' . $document_path);
        }
        
        $db->query("INSERT INTO registers (register_number, register_type, citizen_id, event_date, event_place, notes, document_path, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [$register_number, $register_type, $citizen_id ?: null, $event_date, $event_place, $notes, $document_path, $_SESSION['user_id']]);
        
        header('Location: index.php');
        exit;
    }
}
?>

<div class="container-fluid">
    <h2><?php echo $lang['create'] ?? 'Create'; ?> <?php echo $lang['registers'] ?? 'Register'; ?></h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Register Number *</label>
            <input type="text" name="register_number" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Register Type *</label>
            <select name="register_type" class="form-select" required>
                <?php foreach (['birth','death','marriage','divorce'] as $t): ?>
                <option value="<?php echo $t; ?>"><?php echo $lang[$t] ?? $t; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Citizen</label>
            <select name="citizen_id" class="form-select">
                <option value="">-- Select Citizen --</option>
                <?php foreach ($citizens as $c): ?>
                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['first_name'] . ' ' . $c['last_name'] . ' (' . $c['national_id'] . ')'); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Event Date *</label>
            <input type="date" name="event_date" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Event Place</label>
            <input type="text" name="event_place" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Document</label>
            <input type="file" name="document" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3"></textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary"><?php echo $lang['save'] ?? 'Save'; ?></button>
            <a href="index.php" class="btn btn-secondary"><?php echo $lang['cancel'] ?? 'Cancel'; ?></a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>