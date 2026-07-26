<?php
/**
 * SGRC - Dashboard
 * لوحة التحكم
 */

$pageTitle = trans('dashboard');
$activeModule = 'dashboard';

require_once __DIR__ . '/../../includes/header.php';

// Get statistics
$db = app()->db();

try {
    $stats = [
        'total_citizens' => $db->query("SELECT COUNT(*) as count FROM citizens")->fetch()['count'] ?? 0,
        'total_registers' => $db->query("SELECT COUNT(*) as count FROM register_books")->fetch()['count'] ?? 0,
        'total_certificates' => $db->query("SELECT COUNT(*) as count FROM certificates")->fetch()['count'] ?? 0,
        'total_documents' => $db->query("SELECT COUNT(*) as count FROM documents")->fetch()['count'] ?? 0,
        'today_registrations' => $db->query("SELECT COUNT(*) as count FROM register_pages WHERE DATE(created_at) = CURDATE()")->fetch()['count'] ?? 0,
        'this_month' => $db->query("SELECT COUNT(*) as count FROM citizens WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())")->fetch()['count'] ?? 0,
    ];

    // Citizens by year for chart
    $citizensByYear = $db->query("
        SELECT YEAR(birth_date) as year, COUNT(*) as count 
        FROM citizens 
        WHERE birth_date IS NOT NULL 
        GROUP BY YEAR(birth_date) 
        ORDER BY year DESC 
        LIMIT 10
    ")->fetchAll();

    // Citizens by neighborhood for chart
    $citizensByNeighborhood = $db->query("
        SELECT neighborhood, COUNT(*) as count 
        FROM citizens 
        WHERE neighborhood IS NOT NULL AND neighborhood != '' 
        GROUP BY neighborhood 
        ORDER BY count DESC 
        LIMIT 8
    ")->fetchAll();

    // Recent activity
    $recentActivity = $db->query("
        SELECT al.*, u.full_name as user_name 
        FROM activity_logs al 
        LEFT JOIN users u ON al.user_id = u.id 
        ORDER BY al.created_at DESC 
        LIMIT 10
    ")->fetchAll();

    // Recent citizens
    $recentCitizens = $db->query("
        SELECT id, full_name, family_name, first_name, birth_date, created_at 
        FROM citizens 
        ORDER BY created_at DESC 
        LIMIT 5
    ")->fetchAll();

} catch (PDOException $e) {
    $stats = ['total_citizens' => 0, 'total_registers' => 0, 'total_certificates' => 0, 
              'total_documents' => 0, 'today_registrations' => 0, 'this_month' => 0];
    $citizensByYear = [];
    $citizensByNeighborhood = [];
    $recentActivity = [];
    $recentCitizens = [];
}
?>

<!-- Page Header -->
<div class="page-header d-flex justify-content-between align-items-center flex-wrap">
    <div>
        <h2><i class="bi bi-speedometer2 text-primary"></i> <?php echo trans('dashboard'); ?></h2>
        <p><?php echo trans('welcome'); ?>, <strong><?php echo $user['full_name']; ?></strong></p>
    </div>
    <div class="d-flex gap-2 mt-2 mt-md-0">
        <a href="/modules/citizens/create.php" class="btn btn-primary-custom">
            <i class="bi bi-person-plus"></i> <?php echo trans('add_citizen'); ?>
        </a>
        <a href="/modules/registers/create.php" class="btn btn-success-custom">
            <i class="bi bi-journal-plus"></i> <?php echo trans('add_register'); ?>
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stats-card primary">
            <div class="stats-icon"><i class="bi bi-people-fill"></i></div>
            <h3 class="stats-number"><?php echo app()->formatNumber($stats['total_citizens']); ?></h3>
            <p class="stats-label"><?php echo trans('total_citizens'); ?></p>
            <div class="stats-change positive">
                <i class="bi bi-arrow-up-short"></i>
                <span>+<?php echo $stats['this_month']; ?> <?php echo trans('this_month'); ?></span>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-6">
        <div class="stats-card success">
            <div class="stats-icon"><i class="bi bi-journal-text"></i></div>
            <h3 class="stats-number"><?php echo app()->formatNumber($stats['total_registers']); ?></h3>
            <p class="stats-label"><?php echo trans('total_registers'); ?></p>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-6">
        <div class="stats-card warning">
            <div class="stats-icon"><i class="bi bi-award-fill"></i></div>
            <h3 class="stats-number"><?php echo app()->formatNumber($stats['total_certificates']); ?></h3>
            <p class="stats-label"><?php echo trans('total_certificates'); ?></p>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-6">
        <div class="stats-card info">
            <div class="stats-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
            <h3 class="stats-number"><?php echo app()->formatNumber($stats['total_documents']); ?></h3>
            <p class="stats-label"><?php echo trans('total_documents'); ?></p>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-6">
        <div class="stats-card danger">
            <div class="stats-icon"><i class="bi bi-calendar-check-fill"></i></div>
            <h3 class="stats-number"><?php echo app()->formatNumber($stats['today_registrations']); ?></h3>
            <p class="stats-label"><?php echo trans('today_registrations'); ?></p>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-6">
        <div class="stats-card primary">
            <div class="stats-icon"><i class="bi bi-person-gear"></i></div>
            <h3 class="stats-number"><?php echo app()->formatNumber($stats['this_month']); ?></h3>
            <p class="stats-label"><?php echo trans('this_month'); ?></p>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="chart-card">
            <h5><i class="bi bi-graph-up"></i> <?php echo trans('citizens_by_year'); ?></h5>
            <canvas id="citizensByYearChart" height="100"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card">
            <h5><i class="bi bi-pie-chart"></i> <?php echo trans('citizens_by_neighborhood'); ?></h5>
            <canvas id="neighborhoodChart" height="200"></canvas>
        </div>
    </div>
</div>

<!-- Quick Actions + Recent Activity Row -->
<div class="row g-4 mb-4">
    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="chart-card">
            <h5><i class="bi bi-lightning-charge"></i> <?php echo trans('actions'); ?></h5>
            <div class="row g-3">
                <div class="col-6">
                    <a href="/modules/citizens/create.php" class="quick-action-btn">
                        <i class="bi bi-person-plus text-primary"></i>
                        <span><?php echo trans('add_citizen'); ?></span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="/modules/certificates/index.php" class="quick-action-btn">
                        <i class="bi bi-award text-warning"></i>
                        <span><?php echo trans('issue_certificate'); ?></span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="/modules/import/index.php" class="quick-action-btn">
                        <i class="bi bi-cloud-upload text-success"></i>
                        <span><?php echo trans('import_data'); ?></span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="/modules/reports/index.php" class="quick-action-btn">
                        <i class="bi bi-graph-up text-info"></i>
                        <span><?php echo trans('reports'); ?></span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="/modules/backup/index.php" class="quick-action-btn">
                        <i class="bi bi-hdd text-danger"></i>
                        <span><?php echo trans('backup'); ?></span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="/modules/settings/index.php" class="quick-action-btn">
                        <i class="bi bi-gear text-secondary"></i>
                        <span><?php echo trans('settings'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-lg-4">
        <div class="activity-card">
            <h5><i class="bi bi-clock-history"></i> <?php echo trans('activity_log'); ?></h5>
            <?php if (empty($recentActivity)): ?>
                <p class="text-muted text-center py-4"><?php echo trans('no_data'); ?></p>
            <?php else: ?>
                <?php foreach ($recentActivity as $activity): 
                    $iconClass = match($activity['action']) {
                        'login' => 'login bi-box-arrow-in-right',
                        'logout' => 'logout bi-box-arrow-right',
                        default => 'create bi-pencil-square'
                    };
                ?>
                <div class="activity-item">
                    <div class="activity-icon <?php echo explode(' ', $iconClass)[0]; ?>">
                        <i class="bi <?php echo explode(' ', $iconClass)[1]; ?>"></i>
                    </div>
                    <div class="activity-content">
                        <p><?php echo $activity['description']; ?></p>
                        <small>
                            <i class="bi bi-person"></i> <?php echo $activity['user_name'] ?? 'System'; ?> 
                            &bull; <?php echo app()->formatDate($activity['created_at']); ?>
                        </small>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Citizens -->
    <div class="col-lg-4">
        <div class="activity-card">
            <h5><i class="bi bi-people"></i> <?php echo trans('citizens'); ?> - <?php echo trans('recent'); ?></h5>
            <?php if (empty($recentCitizens)): ?>
                <p class="text-muted text-center py-4"><?php echo trans('no_data'); ?></p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th><?php echo trans('full_name'); ?></th>
                                <th><?php echo trans('birth_date'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentCitizens as $citizen): ?>
                            <tr>
                                <td>
                                    <a href="/modules/citizens/view.php?id=<?php echo $citizen['id']; ?>" class="text-decoration-none">
                                        <?php echo $citizen['family_name'] . ' ' . $citizen['first_name']; ?>
                                    </a>
                                </td>
                                <td><?php echo formatDate($citizen['birth_date']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Charts Scripts -->
<script>
// Citizens by Year Chart
const yearCtx = document.getElementById('citizensByYearChart').getContext('2d');
new Chart(yearCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($citizensByYear, 'year')); ?>,
        datasets: [{
            label: '<?php echo trans('total_citizens'); ?>',
            data: <?php echo json_encode(array_column($citizensByYear, 'count')); ?>,
            backgroundColor: 'rgba(26, 82, 118, 0.8)',
            borderColor: 'rgba(26, 82, 118, 1)',
            borderWidth: 1,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});

// Neighborhood Chart
const neighCtx = document.getElementById('neighborhoodChart').getContext('2d');
new Chart(neighCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_column($citizensByNeighborhood, 'neighborhood')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($citizensByNeighborhood, 'count')); ?>,
            backgroundColor: [
                '#1a5276', '#2980b9', '#27ae60', '#f39c12', 
                '#e74c3c', '#8e44ad', '#16a085', '#d35400'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { boxWidth: 12, font: { size: 11 } }
            }
        }
    }
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>