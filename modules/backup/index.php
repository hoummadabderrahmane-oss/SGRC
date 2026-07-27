<?php
$pageTitle = 'Backup - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$message = '';
$backupsDir = __DIR__ . '/../../backups/';

if (!is_dir($backupsDir)) mkdir($backupsDir, 0755, true);

if (isset($_GET['action']) && $_GET['action'] === 'create') {
    $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $filepath = $backupsDir . $filename;
    
    // Simple database dump
    $db = Database::getInstance();
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    $output = "-- SGRC Backup\n-- Date: " . date('Y-m-d H:i:s') . "\n\n";
    $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
    
    foreach ($tables as $table) {
        $output .= "DROP TABLE IF EXISTS `$table`;\n";
        $create = $db->query("SHOW CREATE TABLE `$table`")->fetch();
        $output .= $create['Create Table'] . ";\n\n";
        
        $rows = $db->query("SELECT * FROM `$table`")->fetchAll();
        foreach ($rows as $row) {
            $columns = implode('`, `', array_keys($row));
            $values = implode(', ', array_map(function($v) use ($db) {
                return $v === null ? 'NULL' : $db->getConnection()->quote($v);
            }, $row));
            $output .= "INSERT INTO `$table` (`$columns`) VALUES ($values);\n";
        }
        $output .= "\n";
    }
    
    $output .= "SET FOREIGN_KEY_CHECKS=1;\n";
    file_put_contents($filepath, $output);
    $message = 'Backup created: ' . $filename;
}

$backups = array_filter(glob($backupsDir . '*.sql'), 'is_file');
rsort($backups);
?>

<div class="container-fluid">
    <h2>Backup & Restore</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <div class="mb-4">
        <a href="?action=create" class="btn btn-primary">Create Backup</a>
    </div>
    
    <div class="card">
        <div class="card-header"><h5>Available Backups</h5></div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Size</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($backups as $backup): ?>
                    <?php $name = basename($backup); ?>
                    <tr>
                        <td><?php echo $name; ?></td>
                        <td><?php echo round(filesize($backup) / 1024, 2); ?> KB</td>
                        <td><?php echo date('Y-m-d H:i:s', filemtime($backup)); ?></td>
                        <td>
                            <a href="/backups/<?php echo $name; ?>" download class="btn btn-sm btn-info">Download</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($backups)): ?>
                    <tr><td colspan="4" class="text-center">No backups available</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>