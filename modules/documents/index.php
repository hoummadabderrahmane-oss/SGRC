<?php
$pageTitle = ($lang['documents'] ?? 'Documents') . ' - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$documents = $db->query("SELECT d.*, c.first_name, c.last_name FROM documents d LEFT JOIN citizens c ON d.citizen_id = c.id ORDER BY d.uploaded_at DESC LIMIT 50")->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo $lang['documents'] ?? 'Documents'; ?></h2>
        <a href="upload.php" class="btn btn-primary">
            <i class="fas fa-upload me-2"></i><?php echo $lang['upload_document'] ?? 'Upload Document'; ?>
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th><?php echo $lang['title'] ?? 'Title'; ?></th>
                            <th><?php echo $lang['category'] ?? 'Category'; ?></th>
                            <th><?php echo $lang['citizen'] ?? 'Citizen'; ?></th>
                            <th><?php echo $lang['type'] ?? 'Type'; ?></th>
                            <th><?php echo $lang['size'] ?? 'Size'; ?></th>
                            <th><?php echo $lang['uploaded'] ?? 'Uploaded'; ?></th>
                            <th class="text-end"><?php echo $lang['actions'] ?? 'Actions'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $doc): ?>
                        <tr>
                            <td class="fw-semibold"><?php echo htmlspecialchars($doc['title']); ?></td>
                            <td><?php echo htmlspecialchars($doc['category'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars(($doc['first_name'] ?? '') . ' ' . ($doc['last_name'] ?? '')); ?></td>
                            <td><span class="badge bg-light text-dark text-uppercase"><?php echo $doc['file_type'] ?? '-'; ?></span></td>
                            <td><?php echo $doc['file_size'] ? round($doc['file_size'] / 1024, 2) . ' KB' : '-'; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($doc['uploaded_at'])); ?></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="/<?php echo $doc['file_path']; ?>" target="_blank" class="btn btn-sm btn-info" title="<?php echo $lang['view'] ?? 'View'; ?>"><i class="fas fa-eye"></i></a>
                                    <a href="download.php?id=<?php echo $doc['id']; ?>" class="btn btn-sm btn-success" title="<?php echo $lang['download'] ?? 'Download'; ?>"><i class="fas fa-download"></i></a>
                                    <a href="delete.php?id=<?php echo $doc['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('<?php echo $lang['confirm_delete'] ?? 'Are you sure?'; ?>')" title="<?php echo $lang['delete'] ?? 'Delete'; ?>"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($documents)): ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted"><i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i><?php echo $lang['no_records'] ?? 'No records found'; ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>