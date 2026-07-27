<?php
$pageTitle = 'Documents - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$documents = $db->query("SELECT d.*, c.first_name, c.last_name FROM documents d LEFT JOIN citizens c ON d.citizen_id = c.id ORDER BY d.uploaded_at DESC")->fetchAll();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo $lang['documents'] ?? 'Documents'; ?></h2>
        <a href="upload.php" class="btn btn-primary">Upload Document</a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Citizen</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Uploaded</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $doc): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($doc['title']); ?></td>
                        <td><?php echo htmlspecialchars($doc['category'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars(($doc['first_name'] ?? '') . ' ' . ($doc['last_name'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($doc['file_type'] ?? '-'); ?></td>
                        <td><?php echo $doc['file_size'] ? round($doc['file_size'] / 1024, 2) . ' KB' : '-'; ?></td>
                        <td><?php echo $doc['uploaded_at']; ?></td>
                        <td>
                            <a href="/<?php echo $doc['file_path']; ?>" target="_blank" class="btn btn-sm btn-info">View</a>
                            <a href="delete.php?id=<?php echo $doc['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this document?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($documents)): ?>
                    <tr><td colspan="7" class="text-center"><?php echo $lang['no_records'] ?? 'No records found'; ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>