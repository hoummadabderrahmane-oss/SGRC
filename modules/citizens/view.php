<?php
$pageTitle = 'View Citizen - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$id = intval($_GET['id'] ?? 0);
$citizen = $db->query("SELECT * FROM citizens WHERE id = ?", [$id])->fetch();

if (!$citizen) {
    header('Location: index.php');
    exit;
}

$registers = $db->query("SELECT * FROM registers WHERE citizen_id = ? ORDER BY created_at DESC", [$id])->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Citizen Details</h2>
        <div>
            <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-warning"><?php echo $lang['edit'] ?? 'Edit'; ?></a>
            <a href="print.php?id=<?php echo $id; ?>" class="btn btn-secondary" target="_blank"><?php echo $lang['print'] ?? 'Print'; ?></a>
            <a href="index.php" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <?php if ($citizen['photo_path']): ?>
                        <img src="/<?php echo $citizen['photo_path']; ?>" class="img-fluid rounded mb-3" style="max-height: 200px;">
                    <?php else: ?>
                        <div class="bg-light rounded mb-3" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                            <span class="text-muted">No Photo</span>
                        </div>
                    <?php endif; ?>
                    <h4><?php echo htmlspecialchars($citizen['first_name'] . ' ' . $citizen['last_name']); ?></h4>
                    <p class="text-muted"><?php echo htmlspecialchars($citizen['first_name_ar'] . ' ' . $citizen['last_name_ar']); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Personal Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr><td width="30%"><strong>National ID:</strong></td><td><?php echo htmlspecialchars($citizen['national_id']); ?></td></tr>
                        <tr><td><strong>Date of Birth:</strong></td><td><?php echo $citizen['date_of_birth']; ?></td></tr>
                        <tr><td><strong>Place of Birth:</strong></td><td><?php echo htmlspecialchars($citizen['place_of_birth'] ?? '-'); ?></td></tr>
                        <tr><td><strong>Gender:</strong></td><td><?php echo $lang[$citizen['gender']] ?? $citizen['gender']; ?></td></tr>
                        <tr><td><strong>Blood Type:</strong></td><td><?php echo htmlspecialchars($citizen['blood_type'] ?? '-'); ?></td></tr>
                        <tr><td><strong>Marital Status:</strong></td><td><?php echo $lang[$citizen['marital_status']] ?? $citizen['marital_status']; ?></td></tr>
                        <tr><td><strong>Father:</strong></td><td><?php echo htmlspecialchars($citizen['father_name'] ?? '-'); ?></td></tr>
                        <tr><td><strong>Mother:</strong></td><td><?php echo htmlspecialchars($citizen['mother_name'] ?? '-'); ?></td></tr>
                        <tr><td><strong>Phone:</strong></td><td><?php echo htmlspecialchars($citizen['phone'] ?? '-'); ?></td></tr>
                        <tr><td><strong>Email:</strong></td><td><?php echo htmlspecialchars($citizen['email'] ?? '-'); ?></td></tr>
                        <tr><td><strong>Address:</strong></td><td><?php echo nl2br(htmlspecialchars($citizen['address'] ?? '-')); ?></td></tr>
                    </table>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5>Registration History</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Register #</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registers as $reg): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reg['register_number']); ?></td>
                                <td><?php echo $lang[$reg['register_type']] ?? $reg['register_type']; ?></td>
                                <td><?php echo $reg['event_date']; ?></td>
                                <td><span class="badge bg-<?php echo $reg['status'] === 'active' ? 'success' : 'secondary'; ?>"><?php echo $reg['status']; ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($registers)): ?>
                            <tr><td colspan="4" class="text-center">No registrations</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>