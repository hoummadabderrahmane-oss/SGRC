<?php
$pageTitle = 'Reports - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();

$stats = [
    'total_citizens' => $db->query("SELECT COUNT(*) as c FROM citizens")->fetch()['c'],
    'total_registers' => $db->query("SELECT COUNT(*) as c FROM registers")->fetch()['c'],
    'by_gender' => $db->query("SELECT gender, COUNT(*) as c FROM citizens GROUP BY gender")->fetchAll(),
    'by_type' => $db->query("SELECT register_type, COUNT(*) as c FROM registers GROUP BY register_type")->fetchAll(),
    'monthly' => $db->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as c FROM registers GROUP BY month ORDER BY month DESC LIMIT 12")->fetchAll(),
];
?>

<div class="container-fluid">
    <h2><?php echo $lang['reports'] ?? 'Reports'; ?></h2>
    
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h3><?php echo $stats['total_citizens']; ?></h3>
                    <p class="mb-0">Total Citizens</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h3><?php echo $stats['total_registers']; ?></h3>
                    <p class="mb-0">Total Registrations</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5>By Gender</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <?php foreach ($stats['by_gender'] as $g): ?>
                        <tr>
                            <td><?php echo ucfirst($g['gender']); ?></td>
                            <td class="text-end"><strong><?php echo $g['c']; ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5>By Register Type</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <?php foreach ($stats['by_type'] as $t): ?>
                        <tr>
                            <td><?php echo $lang[$t['register_type']] ?? $t['register_type']; ?></td>
                            <td class="text-end"><strong><?php echo $t['c']; ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header"><h5>Monthly Registrations (Last 12 Months)</h5></div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr><th>Month</th><th class="text-end">Count</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['monthly'] as $m): ?>
                    <tr>
                        <td><?php echo $m['month']; ?></td>
                        <td class="text-end"><?php echo $m['c']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>