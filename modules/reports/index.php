<?php
$pageTitle = ($lang['reports'] ?? 'Reports') . ' - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();

// Summary stats
$summary = [
    'total_citizens' => $db->query("SELECT COUNT(*) as c FROM citizens")->fetch()['c'],
    'total_births' => $db->query("SELECT COUNT(*) as c FROM registers WHERE register_type = 'birth'")->fetch()['c'],
    'total_deaths' => $db->query("SELECT COUNT(*) as c FROM registers WHERE register_type = 'death'")->fetch()['c'],
    'total_marriages' => $db->query("SELECT COUNT(*) as c FROM registers WHERE register_type = 'marriage'")->fetch()['c'],
    'total_certificates' => $db->query("SELECT COUNT(*) as c FROM certificates")->fetch()['c'],
    'total_documents' => $db->query("SELECT COUNT(*) as c FROM documents")->fetch()['c'],
];

// Monthly registrations (last 6 months)
$monthly = $db->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count, register_type
    FROM registers
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m'), register_type
    ORDER BY month DESC
")->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo $lang['reports'] ?? 'Reports'; ?></h2>
        <div class="btn-group">
            <a href="export.php?type=pdf" class="btn btn-outline-danger"><i class="fas fa-file-pdf me-2"></i>PDF</a>
            <a href="export.php?type=excel" class="btn btn-outline-success"><i class="fas fa-file-excel me-2"></i>Excel</a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card bg-primary">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3><?php echo number_format($summary['total_citizens']); ?></h3>
                        <p><?php echo $lang['total_citizens'] ?? 'Total Citizens'; ?></p>
                    </div>
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-success">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3><?php echo number_format($summary['total_births']); ?></h3>
                        <p><?php echo $lang['birth'] ?? 'Births'; ?></p>
                    </div>
                    <i class="fas fa-baby fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-info">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3><?php echo number_format($summary['total_marriages']); ?></h3>
                        <p><?php echo $lang['marriage'] ?? 'Marriages'; ?></p>
                    </div>
                    <i class="fas fa-ring fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Report Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 fw-bold"><i class="fas fa-chart-bar me-2 text-primary"></i><?php echo $lang['reports'] ?? 'Monthly Report'; ?></h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th><?php echo $lang['date'] ?? 'Month'; ?></th>
                            <th><?php echo $lang['birth'] ?? 'Births'; ?></th>
                            <th><?php echo $lang['death'] ?? 'Deaths'; ?></th>
                            <th><?php echo $lang['marriage'] ?? 'Marriages'; ?></th>
                            <th><?php echo $lang['divorce'] ?? 'Divorces'; ?></th>
                            <th><?php echo $lang['total'] ?? 'Total'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $months = [];
                        foreach ($monthly as $m) {
                            $months[$m['month']][$m['register_type']] = $m['count'];
                        }
                        foreach ($months as $month => $data): 
                            $total = array_sum($data);
                        ?>
                        <tr>
                            <td class="fw-semibold"><?php echo date('F Y', strtotime($month . '-01')); ?></td>
                            <td><?php echo $data['birth'] ?? 0; ?></td>
                            <td><?php echo $data['death'] ?? 0; ?></td>
                            <td><?php echo $data['marriage'] ?? 0; ?></td>
                            <td><?php echo $data['divorce'] ?? 0; ?></td>
                            <td><span class="badge bg-primary"><?php echo $total; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($months)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted"><i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i><?php echo $lang['no_records'] ?? 'No data available'; ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>