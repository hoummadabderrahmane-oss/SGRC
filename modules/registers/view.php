<?php
$pageTitle = trans('register_pages'); $activeModule = 'registers';
require_once __DIR__ . '/../../includes/header.php';
if (!can('registers.view')) { app()->redirect('index.php', 'error', trans('access_denied')); }
$id = (int)($_GET['id'] ?? 0); if (!$id) { app()->redirect('index.php', 'error', 'Invalid ID'); }
$db = app()->db();
$stmt = $db->prepare("SELECT rb.*, u.full_name as created_by_name FROM register_books rb LEFT JOIN users u ON rb.created_by = u.id WHERE rb.id = :id"); $stmt->execute([':id' => $id]);
$book = $stmt->fetch(); if (!$book) { app()->redirect('index.php', 'error', 'Register not found'); }
$pages = $db->prepare("SELECT rp.*, c.family_name, c.first_name FROM register_pages rp LEFT JOIN citizens c ON rp.citizen_id = c.id WHERE rp.register_book_id = :id ORDER BY rp.sequential_number ASC"); $pages->execute([':id' => $id]); $pages = $pages->fetchAll();
?>
<div class="page-header d-flex justify-content-between align-items-center flex-wrap">
    <div><h2><i class="bi bi-journal-text text-primary"></i> <?php echo $book['register_number']; ?></h2><p class="text-muted"><?php echo trans('register_type_' . $book['register_type']); ?> | <?php echo trans('year'); ?>: <?php echo $book['year']; ?> | <?php echo trans('status'); ?>: <span class="badge bg-<?php echo $book['status'] === 'active' ? 'success' : ($book['status'] === 'archived' ? 'warning' : 'secondary'); ?>"><?php echo trans($book['status']); ?></span></p></div>
    <div class="d-flex gap-2 mt-2 mt-md-0">
        <a href="upload_scan.php?book_id=<?php echo $id; ?>" class="btn btn-info"><i class="bi bi-upload"></i> <?php echo trans('upload_scan'); ?></a>
        <?php if (can('registers.edit')): ?><a href="edit.php?id=<?php echo $id; ?>" class="btn btn-warning"><i class="bi bi-pencil"></i> <?php echo trans('edit'); ?></a><?php endif; ?>
        <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-<?php echo isRTL() ? 'right' : 'left'; ?>"></i> <?php echo trans('back'); ?></a>
    </div>
</div>
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="chart-card">
            <h5><i class="bi bi-info-circle"></i> <?php echo trans('register_details'); ?></h5>
            <div class="mb-2"><label class="text-muted small"><?php echo trans('register_number'); ?></label><p class="fw-bold"><?php echo $book['register_number']; ?></p></div>
            <div class="mb-2"><label class="text-muted small"><?php echo trans('register_type'); ?></label><p><?php echo trans('register_type_' . $book['register_type']); ?></p></div>
            <div class="mb-2"><label class="text-muted small"><?php echo trans('year'); ?></label><p><?php echo $book['year']; ?></p></div>
            <div class="mb-2"><label class="text-muted small"><?php echo trans('page_count'); ?></label><p><?php echo count($pages); ?> / <?php echo $book['page_count']; ?></p></div>
            <div class="mb-2"><label class="text-muted small"><?php echo trans('location'); ?></label><p><?php echo $book['location'] ?? '-'; ?></p></div>
            <?php if ($book['notes']): ?><div class="mb-2"><label class="text-muted small"><?php echo trans('notes'); ?></label><p><?php echo $book['notes']; ?></p></div><?php endif; ?>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="chart-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> <?php echo trans('register_pages'); ?></h5>
                <?php if (can('registers.create')): ?><a href="create_page.php?book_id=<?php echo $id; ?>" class="btn btn-sm btn-primary-custom"><i class="bi bi-plus-lg"></i> <?php echo trans('add'); ?></a><?php endif; ?>
            </div>
            <?php if (empty($pages)): ?><p class="text-muted text-center py-4"><?php echo trans('no_data'); ?></p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover" id="pagesTable">
                    <thead><tr><th><?php echo trans('sequential_number'); ?></th><th><?php echo trans('page_number'); ?></th><th><?php echo trans('record_date'); ?></th><th><?php echo trans('full_name'); ?></th><th><?php echo trans('birth_date'); ?></th><th><?php echo trans('address'); ?></th><th><?php echo trans('actions'); ?></th></tr></thead>
                    <tbody>
                        <?php foreach ($pages as $p): ?>
                        <tr>
                            <td><span class="badge bg-dark"><?php echo $p['sequential_number']; ?></span></td>
                            <td><?php echo $p['page_number']; ?></td>
                            <td><?php echo formatDate($p['record_date']); ?></td>
                            <td><strong><?php echo $p['full_name']; ?></strong><?php if ($p['family_name']): ?><br><small class="text-muted"><?php echo $p['family_name'] . ' ' . $p['first_name']; ?></small><?php endif; ?></td>
                            <td><?php echo formatDate($p['birth_date']); ?></td>
                            <td><?php echo $p['address'] ?? '-'; ?></td>
                            <td><div class="btn-group"><a href="view_page.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-info" title="<?php echo trans('view'); ?>"><i class="bi bi-eye"></i></a><?php if ($p['scan_path']): ?><a href="/<?php echo $p['scan_path']; ?>" target="_blank" class="btn btn-sm btn-secondary" title="<?php echo trans('view_scan'); ?>"><i class="bi bi-file-image"></i></a><?php endif; ?><?php if (can('registers.edit')): ?><a href="edit_page.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-warning" title="<?php echo trans('edit'); ?>"><i class="bi bi-pencil"></i></a><?php endif; ?></div></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>document.addEventListener('DOMContentLoaded', () => { initDataTable('#pagesTable', { pageLength: 50, order: [[0, 'asc']], columnDefs: [{ orderable: false, targets: [6] }] }); });</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>