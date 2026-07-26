<?php
$pageTitle = trans('citizen_details'); $activeModule = 'citizens';
require_once __DIR__ . '/../../includes/header.php';
if (!can('citizens.view')) { app()->redirect('index.php', 'error', trans('access_denied')); }
$id = (int)($_GET['id'] ?? 0); if (!$id) { app()->redirect('index.php', 'error', 'Invalid ID'); }
$db = app()->db();
$stmt = $db->prepare("SELECT c.*, u.full_name as created_by_name FROM citizens c LEFT JOIN users u ON c.created_by = u.id WHERE c.id = :id"); $stmt->execute([':id'=>$id]);
$citizen = $stmt->fetch(); if (!$citizen) { app()->redirect('index.php', 'error', 'Citizen not found'); }
$documents = $db->prepare("SELECT * FROM documents WHERE citizen_id = :id ORDER BY created_at DESC"); $documents->execute([':id'=>$id]); $documents = $documents->fetchAll();
$certificates = $db->prepare("SELECT * FROM certificates WHERE citizen_id = :id ORDER BY issue_date DESC"); $certificates->execute([':id'=>$id]); $certificates = $certificates->fetchAll();
$registerPages = $db->prepare("SELECT rp.*, rb.register_number, rb.register_type FROM register_pages rp JOIN register_books rb ON rp.register_book_id = rb.id WHERE rp.citizen_id = :id ORDER BY rp.record_date DESC"); $registerPages->execute([':id'=>$id]); $registerPages = $registerPages->fetchAll();
?>
<div class="page-header d-flex justify-content-between align-items-center flex-wrap">
    <div><h2><i class="bi bi-person-vcard text-primary"></i> <?php echo trans('citizen_details'); ?></h2></div>
    <div class="d-flex gap-2 mt-2 mt-md-0">
        <a href="print.php?id=<?php echo $id; ?>" target="_blank" class="btn btn-secondary"><i class="bi bi-printer"></i> <?php echo trans('print'); ?></a>
        <?php if (can('citizens.edit')): ?><a href="edit.php?id=<?php echo $id; ?>" class="btn btn-warning"><i class="bi bi-pencil"></i> <?php echo trans('edit'); ?></a><?php endif; ?>
        <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-<?php echo isRTL()?'right':'left'; ?>"></i> <?php echo trans('back'); ?></a>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="chart-card text-center">
            <div class="position-relative d-inline-block mb-3">
                <?php if ($citizen['photo_path']): ?><img src="/<?php echo $citizen['photo_path']; ?>" class="rounded-circle" style="width:150px;height:150px;object-fit:cover;border:4px solid var(--primary-color);"><?php else: ?><div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white mx-auto" style="width:150px;height:150px;font-size:4rem;"><?php echo mb_substr($citizen['first_name'], 0, 1); ?></div><?php endif; ?>
            </div>
            <h4 class="mb-1"><?php echo $citizen['family_name'] . ' ' . $citizen['first_name']; ?></h4>
            <p class="text-muted mb-3"><code><?php echo $citizen['national_id'] ?? '-'; ?></code></p>
            <?php if ($citizen['qr_code']): ?><div class="mb-3"><img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($citizen['qr_code']); ?>" class="img-fluid" style="max-width:120px;"><p class="small text-muted mt-1"><?php echo trans('qr_code'); ?></p></div><?php endif; ?>
            <div class="d-flex justify-content-center gap-2">
                <?php if (can('certificates.create')): ?><a href="/modules/certificates/create.php?citizen_id=<?php echo $id; ?>" class="btn btn-sm btn-success"><i class="bi bi-award"></i> <?php echo trans('issue_certificate'); ?></a><?php endif; ?>
                <?php if (can('citizens.delete')): ?><a href="delete.php?id=<?php echo $id; ?>" class="btn btn-sm btn-danger" onclick="return confirmDelete('<?php echo trans('delete_citizen'); ?>?')"><i class="bi bi-trash"></i> <?php echo trans('delete'); ?></a><?php endif; ?>
            </div>
        </div>
        <div class="chart-card mt-4">
            <h5><i class="bi bi-file-earmark-text"></i> <?php echo trans('documents'); ?></h5>
            <?php if (empty($documents)): ?><p class="text-muted text-center py-3"><?php echo trans('no_data'); ?></p>
            <?php else: ?><div class="list-group list-group-flush"><?php foreach ($documents as $doc): ?><a href="/<?php echo $doc['file_path']; ?>" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"><span><i class="bi bi-file-earmark"></i> <?php echo $doc['file_name']; ?></span><i class="bi bi-download text-primary"></i></a><?php endforeach; ?></div><?php endif; ?>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="chart-card">
            <h5><i class="bi bi-person"></i> <?php echo trans('citizen_details'); ?></h5>
            <div class="row g-3">
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('family_name'); ?></label><p class="fw-bold"><?php echo $citizen['family_name']; ?></p></div>
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('first_name'); ?></label><p class="fw-bold"><?php echo $citizen['first_name']; ?></p></div>
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('father_name'); ?></label><p><?php echo $citizen['father_name'] ?? '-'; ?></p></div>
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('mother_name'); ?></label><p><?php echo $citizen['mother_name'] ?? '-'; ?></p></div>
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('birth_date'); ?></label><p><?php echo formatDate($citizen['birth_date']); ?></p></div>
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('birth_place'); ?></label><p><?php echo $citizen['birth_place'] ?? '-'; ?></p></div>
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('gender'); ?></label><p><?php if ($citizen['gender']): ?><span class="badge bg-<?php echo $citizen['gender']==='male'?'primary':'danger'; ?>"><i class="bi bi-gender-<?php echo $citizen['gender']; ?>"></i> <?php echo trans($citizen['gender']); ?></span><?php else: ?>-<?php endif; ?></p></div>
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('national_id'); ?></label><p><code><?php echo $citizen['national_id'] ?? '-'; ?></code></p></div>
            </div>
        </div>
        <div class="chart-card mt-4">
            <h5><i class="bi bi-geo-alt"></i> <?php echo trans('address'); ?></h5>
            <div class="row g-3">
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('address'); ?></label><p><?php echo $citizen['address'] ?? '-'; ?></p></div>
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('neighborhood'); ?></label><p><span class="badge bg-info"><?php echo $citizen['neighborhood'] ?? '-'; ?></span></p></div>
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('phone'); ?></label><p><a href="tel:<?php echo $citizen['phone']; ?>"><?php echo $citizen['phone'] ?? '-'; ?></a></p></div>
                <div class="col-md-6"><label class="text-muted small"><?php echo trans('email'); ?></label><p><a href="mailto:<?php echo $citizen['email']; ?>"><?php echo $citizen['email'] ?? '-'; ?></a></p></div>
            </div>
        </div>
        <?php if (!empty($registerPages)): ?><div class="chart-card mt-4"><h5><i class="bi bi-journal-text"></i> <?php echo trans('register_pages'); ?></h5><div class="table-responsive"><table class="table table-sm"><thead><tr><th><?php echo trans('register_number'); ?></th><th><?php echo trans('page_number'); ?></th><th><?php echo trans('record_date'); ?></th><th><?php echo trans('actions'); ?></th></tr></thead><tbody><?php foreach ($registerPages as $rp): ?><tr><td><span class="badge bg-primary"><?php echo $rp['register_number']; ?></span></td><td><?php echo $rp['page_number']; ?></td><td><?php echo formatDate($rp['record_date']); ?></td><td><a href="/modules/registers/view.php?id=<?php echo $rp['register_book_id']; ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a></td></tr><?php endforeach; ?></tbody></table></div></div><?php endif; ?>
        <?php if (!empty($certificates)): ?><div class="chart-card mt-4"><h5><i class="bi bi-award"></i> <?php echo trans('certificates'); ?></h5><div class="table-responsive"><table class="table table-sm"><thead><tr><th><?php echo trans('certificate_number'); ?></th><th><?php echo trans('certificate_type'); ?></th><th><?php echo trans('issue_date'); ?></th><th><?php echo trans('actions'); ?></th></tr></thead><tbody><?php foreach ($certificates as $cert): ?><tr><td><code><?php echo $cert['certificate_number']; ?></code></td><td><?php echo trans('certificate_type_' . $cert['certificate_type']); ?></td><td><?php echo formatDate($cert['issue_date']); ?></td><td><a href="/modules/certificates/view.php?id=<?php echo $cert['id']; ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a></td></tr><?php endforeach; ?></tbody></table></div></div><?php endif; ?>
        <?php if ($citizen['notes']): ?><div class="chart-card mt-4"><h5><i class="bi bi-sticky"></i> <?php echo trans('notes'); ?></h5><p class="mb-0"><?php echo nl2br(htmlspecialchars($citizen['notes'])); ?></p></div><?php endif; ?>
        <div class="text-muted small mt-3"><i class="bi bi-clock"></i> <?php echo trans('created_at'); ?>: <?php echo $citizen['created_at']; ?><?php if ($citizen['created_by_name']): ?> | <?php echo trans('by'); ?>: <?php echo $citizen['created_by_name']; ?><?php endif; ?></div>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>