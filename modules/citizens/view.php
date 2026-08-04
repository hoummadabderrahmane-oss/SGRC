<?php
$pageTitle = (isset($lang['view']) ? $lang['view'] : 'View') . ' Citizen - SGRC';
require_once dirname(__FILE__) . '/../../includes/header.php';
require_once dirname(__FILE__) . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$citizen = $db->query("SELECT * FROM citizens WHERE id = ?", array($id))->fetch();
if (!$citizen) {
    echo '<div class="alert alert-danger">Citizen not found</div>';
    echo '<a href="index.php" class="btn btn-primary">' . (isset($lang['go_back']) ? $lang['go_back'] : 'Go Back') . '</a>';
    require_once dirname(__FILE__) . '/../../includes/footer.php';
    exit;
}

// Safely check if 'registers' table exists before querying
$registers = array();
try {
    $tables = $db->query("SHOW TABLES LIKE 'registers'")->fetchAll();
    if (!empty($tables)) {
        $registers = $db->query("SELECT * FROM registers WHERE citizen_id = ? ORDER BY created_at DESC", array($id))->fetchAll();
    }
} catch (Exception $e) {
    $registers = array();
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo isset($lang['view']) ? $lang['view'] : 'View'; ?> <?php echo isset($lang['citizen']) ? $lang['citizen'] : 'Citizen'; ?></h2>
        <div>
            <a href="edit.php?id=<?php echo $citizen['id']; ?>" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> <?php echo isset($lang['edit']) ? $lang['edit'] : 'Edit'; ?>
            </a>
            <a href="print.php?id=<?php echo $citizen['id']; ?>" class="btn btn-secondary" target="_blank">
                <i class="fas fa-print me-1"></i> <?php echo isset($lang['print']) ? $lang['print'] : 'Print'; ?>
            </a>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> <?php echo isset($lang['go_back']) ? $lang['go_back'] : 'Back'; ?>
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Citizen Info Card -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-id-card me-2"></i><?php echo isset($lang['personal_info']) ? $lang['personal_info'] : 'Personal Information'; ?></h5>
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($citizen['photo_path'])): ?>
                        <img src="/SGRC/<?php echo $citizen['photo_path']; ?>" class="img-thumbnail mb-3" style="max-width:150px;max-height:150px;border-radius:8px;">
                    <?php else: ?>
                        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center bg-light text-muted" style="width:150px;height:150px;border-radius:8px;font-size:48px;">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                    <h4><?php echo htmlspecialchars($citizen['first_name'] . ' ' . $citizen['family_name']); ?></h4>
                    <?php if (!empty($citizen['first_name_ar']) || !empty($citizen['last_name_ar'])): ?>
                        <p class="text-muted" dir="rtl"><?php echo htmlspecialchars(($citizen['first_name_ar'] ?? '') . ' ' . ($citizen['last_name_ar'] ?? '')); ?></p>
                    <?php endif; ?>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted"><?php echo isset($lang['national_id']) ? $lang['national_id'] : 'National ID'; ?></span>
                        <span class="fw-semibold"><?php echo htmlspecialchars($citizen['national_id']); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted"><?php echo isset($lang['birth_date']) ? $lang['birth_date'] : 'Birth Date'; ?></span>
                        <span class="fw-semibold"><?php echo !empty($citizen['birth_date']) && $citizen['birth_date'] != '0000-00-00' ? date('d/m/Y', strtotime($citizen['birth_date'])) : '-'; ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted"><?php echo isset($lang['birth_place']) ? $lang['birth_place'] : 'Birth Place'; ?></span>
                        <span class="fw-semibold"><?php echo htmlspecialchars($citizen['birth_place'] ?? '-'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted"><?php echo isset($lang['gender']) ? $lang['gender'] : 'Gender'; ?></span>
                        <span class="fw-semibold">
                            <i class="fas fa-<?php echo $citizen['gender'] === 'male' ? 'mars text-primary' : 'venus text-danger'; ?> me-1"></i>
                            <?php echo isset($lang[$citizen['gender']]) ? $lang[$citizen['gender']] : $citizen['gender']; ?>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted"><?php echo isset($lang['file_number']) ? $lang['file_number'] : 'File Number'; ?></span>
                        <span class="fw-semibold"><?php echo !empty($citizen['file_number']) ? htmlspecialchars($citizen['file_number']) : '-'; ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted"><?php echo isset($lang['file_date']) ? $lang['file_date'] : 'File Date'; ?></span>
                        <span class="fw-semibold"><?php echo !empty($citizen['file_date']) ? htmlspecialchars($citizen['file_date']) : '-'; ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted"><?php echo isset($lang['address']) ? $lang['address'] : 'Address'; ?></span>
                        <span class="fw-semibold"><?php echo htmlspecialchars($citizen['address'] ?? '-'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted"><?php echo isset($lang['neighborhood']) ? $lang['neighborhood'] : 'Neighborhood'; ?></span>
                        <span class="fw-semibold"><?php echo htmlspecialchars($citizen['neighborhood'] ?? '-'); ?></span>
                    </li>
                    <?php if (!empty($citizen['notes'])): ?>
                    <li class="list-group-item">
                        <span class="text-muted d-block mb-1"><?php echo isset($lang['notes']) ? $lang['notes'] : 'Notes'; ?></span>
                        <span><?php echo nl2br(htmlspecialchars($citizen['notes'])); ?></span>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Registers / History Section -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i><?php echo isset($lang['registers']) ? $lang['registers'] : 'Registers / Records'; ?></h5>
                    <?php if (!empty($registers)): ?>
                        <span class="badge bg-white text-info"><?php echo count($registers); ?></span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (empty($registers)): ?>
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                            <?php echo isset($lang['no_registers']) ? $lang['no_registers'] : 'No registers found. The registers table may not exist yet.'; ?>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo isset($lang['date']) ? $lang['date'] : 'Date'; ?></th>
                                        <th><?php echo isset($lang['description']) ? $lang['description'] : 'Description'; ?></th>
                                        <th class="text-end"><?php echo isset($lang['actions']) ? $lang['actions'] : 'Actions'; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($registers as $reg): ?>
                                    <tr>
                                        <td><?php echo $reg['id']; ?></td>
                                        <td><?php echo !empty($reg['created_at']) ? date('d/m/Y', strtotime($reg['created_at'])) : '-'; ?></td>
                                        <td><?php echo htmlspecialchars($reg['description'] ?? '-'); ?></td>
                                        <td class="text-end">
                                            <a href="../registers/view.php?id=<?php echo $reg['id']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__FILE__) . '/../../includes/footer.php'; ?>