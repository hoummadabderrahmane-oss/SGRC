<?php
$pageTitle = (isset($lang['dashboard']) ? $lang['dashboard'] : 'Dashboard') . ' - SGRC';
require_once dirname(__FILE__) . '/../../includes/header.php';
require_once dirname(__FILE__) . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();

$stats = array(
    'citizens' => $db->query("SELECT COUNT(*) as c FROM citizens")->fetch()['c'],
    'register_books' => $db->query("SELECT COUNT(*) as c FROM register_books")->fetch()['c'],
    'certificates' => $db->query("SELECT COUNT(*) as c FROM certificates")->fetch()['c'],
    'documents' => $db->query("SELECT COUNT(*) as c FROM documents")->fetch()['c'],
    'recent_pages' => $db->query("SELECT rp.*, rb.register_number, rb.register_type, c.first_name, c.family_name FROM register_pages rp LEFT JOIN register_books rb ON rp.register_book_id = rb.id LEFT JOIN citizens c ON rp.citizen_id = c.id ORDER BY rp.created_at DESC LIMIT 5")->fetchAll()
);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><?php echo isset($lang['dashboard']) ? $lang['dashboard'] : 'Dashboard'; ?></h2>
            <p class="text-muted mb-0"><?php echo isset($lang['welcome_back']) ? $lang['welcome_back'] : 'Welcome back'; ?>, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></p>
        </div>
        <a href="/SGRC/modules/citizens/create.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i><?php echo isset($lang['new_citizen']) ? $lang['new_citizen'] : 'New Citizen'; ?>
        </a>
    </div>
    
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card bg-primary">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3><?php echo number_format($stats['citizens']); ?></h3>
                        <p><?php echo isset($lang['total_citizens']) ? $lang['total_citizens'] : 'Total Citizens'; ?></p>
                    </div>
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-success">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3><?php echo number_format($stats['register_books']); ?></h3>
                        <p><?php echo isset($lang['registers']) ? $lang['registers'] : 'Registers'; ?></p>
                    </div>
                    <i class="fas fa-book fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-info">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3><?php echo number_format($stats['certificates']); ?></h3>
                        <p><?php echo isset($lang['total_certificates']) ? $lang['total_certificates'] : 'Certificates'; ?></p>
                    </div>
                    <i class="fas fa-certificate fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-warning">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3><?php echo number_format($stats['documents']); ?></h3>
                        <p><?php echo isset($lang['total_documents']) ? $lang['total_documents'] : 'Documents'; ?></p>
                    </div>
                    <i class="fas fa-folder-open fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fas fa-clock-rotate-left me-2 text-primary"></i><?php echo isset($lang['recent_registrations']) ? $lang['recent_registrations'] : 'Recent Registrations'; ?></h5>
            <a href="/SGRC/modules/registers/index.php" class="btn btn-sm btn-outline-primary"><?php echo isset($lang['view_all']) ? $lang['view_all'] : 'View All'; ?></a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th><?php echo isset($lang['register_number']) ? $lang['register_number'] : 'Register #'; ?></th>
                            <th><?php echo isset($lang['register_type']) ? $lang['register_type'] : 'Type'; ?></th>
                            <th><?php echo isset($lang['citizen']) ? $lang['citizen'] : 'Citizen'; ?></th>
                            <th><?php echo isset($lang['date']) ? $lang['date'] : 'Date'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['recent_pages'] as $reg): ?>
                        <tr>
                            <td class="fw-semibold"><?php echo htmlspecialchars($reg['register_number']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $reg['register_type'] === 'birth' ? 'success' : ($reg['register_type'] === 'death' ? 'dark' : ($reg['register_type'] === 'marriage' ? 'info' : 'warning')); ?>">
                                    <?php echo isset($lang[$reg['register_type']]) ? $lang[$reg['register_type']] : $reg['register_type']; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars(($reg['first_name'] ?? '') . ' ' . ($reg['family_name'] ?? '')); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($reg['record_date'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($stats['recent_pages'])): ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted"><?php echo isset($lang['no_records']) ? $lang['no_records'] : 'No records found'; ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__FILE__) . '/../../includes/footer.php'; ?>