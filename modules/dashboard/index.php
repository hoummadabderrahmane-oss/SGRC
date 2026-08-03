<?php
$pageTitle = ($lang['dashboard'] ?? 'Dashboard') . ' - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();

$stats = [
    'citizens' => $db->query("SELECT COUNT(*) as c FROM citizens")->fetch()['c'],
    'register_books' => $db->query("SELECT COUNT(*) as c FROM register_books")->fetch()['c'],
    'certificates' => $db->query("SELECT COUNT(*) as c FROM certificates")->fetch()['c'],
    'documents' => $db->query("SELECT COUNT(*) as c FROM documents")->fetch()['c'],
    'recent_registers' => $db->query("SELECT r.*, c.first_name, c.last_name FROM registers r LEFT JOIN citizens c ON r.citizen_id = c.id ORDER BY r.created_at DESC LIMIT 5")->fetchAll()
];
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><?php echo $lang['dashboard'] ?? 'Dashboard'; ?></h2>
            <p class="text-muted mb-0"><?php echo $lang['welcome_back'] ?? 'Welcome back'; ?>, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></p>
        </div>
        <a href="/SGRC/modules/citizens/create.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i><?php echo $lang['new_citizen'] ?? 'New Citizen'; ?>
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card bg-primary">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3><?php echo number_format($stats['citizens']); ?></h3>
                        <p><?php echo $lang['total_citizens'] ?? 'Total Citizens'; ?></p>
                    </div>
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-success">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3><?php echo number_format($stats['registers']); ?></h3>
                        <p><?php echo $lang['total_registrations'] ?? 'Registrations'; ?></p>
                    </div>
                    <i class="fas fa-file-alt fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-info">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3><?php echo number_format($stats['certificates']); ?></h3>
                        <p><?php echo $lang['total_certificates'] ?? 'Certificates'; ?></p>
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
                        <p><?php echo $lang['total_documents'] ?? 'Documents'; ?></p>
                    </div>
                    <i class="fas fa-folder-open fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fas fa-clock-rotate-left me-2 text-primary"></i><?php echo $lang['recent_registrations'] ?? 'Recent Registrations'; ?></h5>
            <a href="/SGRC/modules/registers/index.php" class="btn btn-sm btn-outline-primary"><?php echo $lang['view_all'] ?? 'View All'; ?></a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th><?php echo $lang['register_number'] ?? 'Register #'; ?></th>
                            <th><?php echo $lang['register_type'] ?? 'Type'; ?></th>
                            <th><?php echo $lang['citizen'] ?? 'Citizen'; ?></th>
                            <th><?php echo $lang['date'] ?? 'Date'; ?></th>
                            <th><?php echo $lang['status'] ?? 'Status'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['recent_registers'] as $reg): ?>
                        <tr>
                            <td class="fw-semibold"><?php echo htmlspecialchars($reg['register_number']); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $reg['register_type'] === 'birth' ? 'success' : 
                                        ($reg['register_type'] === 'death' ? 'dark' : 
                                        ($reg['register_type'] === 'marriage' ? 'info' : 'warning')); 
                                ?>">
                                    <i class="fas fa-<?php 
                                        echo $reg['register_type'] === 'birth' ? 'baby' : 
                                            ($reg['register_type'] === 'death' ? 'cross' : 
                                            ($reg['register_type'] === 'marriage' ? 'ring' : 'heart-broken')); 
                                    ?> me-1"></i>
                                    <?php echo $lang[$reg['register_type']] ?? $reg['register_type']; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($reg['first_name'] . ' ' . $reg['last_name']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($reg['event_date'])); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $reg['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                    <?php echo $lang[$reg['status']] ?? ucfirst($reg['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($stats['recent_registers'])): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted"><?php echo $lang['no_records'] ?? 'No recent registrations'; ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>