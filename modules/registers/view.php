<?php
$pageTitle = 'View Register - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$id = intval($_GET['id'] ?? 0);
$register = $db->query("SELECT r.*, c.first_name, c.last_name, c.national_id, c.date_of_birth, c.gender FROM registers r LEFT JOIN citizens c ON r.citizen_id = c.id WHERE r.id = ?", [$id])->fetch();

if (!$register) {
    header('Location: index.php');
    exit;
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Register Details</h2>
        <div>
            <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-warning"><?php echo $lang['edit'] ?? 'Edit'; ?></a>
            <a href="index.php" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-borderless">
                <tr><td width="30%"><strong>Register Number:</strong></td><td><?php echo htmlspecialchars($register['register_number']); ?></td></tr>
                <tr><td><strong>Type:</strong></td><td><?php echo $lang[$register['register_type']] ?? $register['register_type']; ?></td></tr>
                <tr><td><strong>Status:</strong></td><td><span class="badge bg-<?php echo $register['status'] === 'active' ? 'success' : 'secondary'; ?>"><?php echo $register['status']; ?></span></td></tr>
                <tr><td><strong>Event Date:</strong></td><td><?php echo $register['event_date']; ?></td></tr>
                <tr><td><strong>Event Place:</strong></td><td><?php echo htmlspecialchars($register['event_place'] ?? '-'); ?></td></tr>
                <tr><td><strong>Notes:</strong></td><td><?php echo nl2br(htmlspecialchars($register['notes'] ?? '-')); ?></td></tr>
                <tr><td><strong>Created:</strong></td><td><?php echo $register['created_at']; ?></td></tr>
            </table>
            
            <?php if ($register['citizen_id']): ?>
            <hr>
            <h5>Associated Citizen</h5>
            <table class="table table-borderless">
                <tr><td width="30%"><strong>National ID:</strong></td><td><?php echo htmlspecialchars($register['national_id']); ?></td></tr>
                <tr><td><strong>Name:</strong></td><td><?php echo htmlspecialchars($register['first_name'] . ' ' . $register['last_name']); ?></td></tr>
                <tr><td><strong>Date of Birth:</strong></td><td><?php echo $register['date_of_birth']; ?></td></tr>
                <tr><td><strong>Gender:</strong></td><td><?php echo ucfirst($register['gender']); ?></td></tr>
            </table>
            <a href="/SGRC/modules/citizens/view.php?id=<?php echo $register['citizen_id']; ?>" class="btn btn-sm btn-info">View Full Citizen Profile</a>
            <?php endif; ?>
            
            <?php if ($register['document_path']): ?>
            <hr>
            <h5>Attached Document</h5>
            <a href="/SGRC/<?php echo $register['document_path']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">View Document</a>
            <?php endif; ?>
            
            <?php if ($register['scan_path']): ?>
            <hr>
            <h5>Scanned Document</h5>
            <a href="/SGRC/<?php echo $register['scan_path']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">View Scan</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>