<?php
$pageTitle = trans('view'); $activeModule = 'registers';
require_once __DIR__ . '/../../includes/header.php';
if (!can('registers.view')) { app()->redirect('index.php', 'error', trans('access_denied')); }
$id = (int)($_GET['id'] ?? 0); if (!$id) { app()->redirect('index.php', 'error', 'Invalid ID'); }
$db = app()->db();
$stmt = $db->prepare("SELECT rp.*, rb.register_number, rb.register_type, c.family_name, c.first_name, c.id as cid FROM register_pages rp JOIN register_books rb ON rp.register_book_id = rb.id LEFT JOIN citizens c ON rp.citizen_id = c.id WHERE rp.id = :id"); $stmt->execute([':id' => $id]);
$page = $stmt->fetch(); if (!$page) { app()->redirect('index.php', 'error', 'Page not found'); }
?>
<div class="page-header d-flex justify-content-between align-items-center flex-wrap">
    <div><h2><i class="bi bi-file-earmark-text text-primary"></i> <?php echo trans('register_pages'); ?> #<?php echo $page['sequential_number']; ?></h2><p class="text-muted"><?php echo $page['register_number']; ?> | <?php echo trans('page_number'); ?>: <?php echo $page['page_number']; ?></p></div>
    <div class="d-flex gap-2 mt-2 mt-md-0">
        <?php if ($page['scan_path']): ?><a href="/<?php echo $page['scan_path']; ?>" target="_blank" class="btn btn-secondary"><i class="bi bi-file-image"></i> <?php echo trans('view_scan'); ?></a><?php endif; ?>
        <?php if (can('registers.edit')): ?><a href="edit_page.php?id=<?php echo $id; ?>" class="btn btn-warning"><i class="bi bi-pencil"></i> <?php echo trans('edit'); ?></a><?php endif; ?>
        <a href="view.php?id=<?php echo $page['register_book_id']; ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-<?php echo isRTL() ? 'right' : 'left'; ?>"></i> <?php echo trans('back'); ?></a>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="chart-card">
            <h5><i class="bi bi-person-lines-fill"></i> <?php echo trans('citizen_details'); ?></h5>
            <div class="row g-3">
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('sequential_number'); ?></label><p><span class="badge bg-dark fs-6"><?php echo $page['sequential_number']; ?></span></p></div>
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('page_number'); ?></label><p><?php echo $page['page_number']; ?></p></div>
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('record_date'); ?></label><p><?php echo formatDate($page['record_date']); ?></p></div>
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('full_name'); ?></label><p class="fw-bold"><?php echo $page['full_name']; ?></p></div>
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('family_name'); ?></label><p><?php echo $page['family_name'] ?? '-'; ?></p></div>
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('father_name'); ?></label><p><?php echo $page['father_name'] ?? '-'; ?></p></div>
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('mother_name'); ?></label><p><?php echo $page['mother_name'] ?? '-'; ?></p></div>
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('birth_date'); ?></label><p><?php echo formatDate($page['birth_date']); ?></p></div>
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('birth_place'); ?></label><p><?php echo $page['birth_place'] ?? '-'; ?></p></div>
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('id_number'); ?></label><p><code><?php echo $page['id_number'] ?? '-'; ?></code></p></div>
                <div class="col-12"><label class="text-muted small"><?php echo trans('address'); ?></label><p><?php echo $page['address'] ?? '-'; ?></p></div>
                <?php if ($page['notes']): ?><div class="col-12"><label class="text-muted small"><?php echo trans('notes'); ?></label><p><?php echo nl2br(htmlspecialchars($page['notes'])); ?></p></div><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <?php if ($page['scan_path']): ?>
        <div class="chart-card">
            <h5><i class="bi bi-file-image"></i> <?php echo trans('scan'); ?></h5>
            <a href="/<?php echo $page['scan_path']; ?>" target="_blank"><img src="/<?php echo $page['scan_path']; ?>" class="img-fluid rounded" style="max-height:400px;object-fit:contain;"></a>
        </div>
        <?php endif; ?>
        <?php if ($page['ocr_text']): ?>
        <div class="chart-card mt-4">
            <h5><i class="bi bi-eye"></i> OCR</h5>
            <div class="bg-light p-3 rounded"><pre class="mb-0" style="white-space:pre-wrap;"><?php echo htmlspecialchars($page['ocr_text']); ?></pre></div>
            <?php if ($page['ocr_confidence']): ?><p class="text-muted small mt-2"><?php echo trans('ocr_confidence'); ?>: <?php echo $page['ocr_confidence']; ?>%</p><?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if ($page['citizen_id']): ?>
        <div class="chart-card mt-4">
            <h5><i class="bi bi-link"></i> <?php echo trans('citizens'); ?></h5>
            <a href="/modules/citizens/view.php?id=<?php echo $page['citizen_id']; ?>" class="btn btn-primary-custom w-100"><i class="bi bi-person"></i> <?php echo ($page['family_name'] ?? '') . ' ' . ($page['first_name'] ?? ''); ?></a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>