<?php
require_once __DIR__ . '/../../app/Core/App.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();
if (!can('citizens.view')) die(trans('access_denied'));
$id = (int)($_GET['id'] ?? 0); if (!$id) die('Invalid ID');
$db = app()->db();
$stmt = $db->prepare("SELECT c.*, u.full_name as created_by_name FROM citizens c LEFT JOIN users u ON c.created_by = u.id WHERE c.id = :id"); $stmt->execute([':id'=>$id]);
$citizen = $stmt->fetch(); if (!$citizen) die('Citizen not found');
$communeName = app()->setting('commune_name', trans('app_name'));
$lang = getLang(); $dir = getDir();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $dir; ?>">
<head><meta charset="UTF-8"><title><?php echo trans('print_profile'); ?> - <?php echo $citizen['family_name'] . ' ' . $citizen['first_name']; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.<?php echo $lang==='ar'?'rtl':'min'; ?>.css" rel="stylesheet">
<style>
@media print { .no-print { display: none !important; } body { background: white; } .print-container { box-shadow: none; margin: 0; max-width: 100%; } }
body { background: #f5f5f5; padding: 20px; }
.print-container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
.print-header { text-align: center; border-bottom: 3px double #1a5276; padding-bottom: 20px; margin-bottom: 30px; }
.print-header h2 { color: #1a5276; font-weight: bold; }
.info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #ddd; }
.info-label { font-weight: bold; color: #1a5276; }
.photo-box { width: 120px; height: 150px; border: 2px solid #1a5276; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; overflow: hidden; }
.photo-box img { width: 100%; height: 100%; object-fit: cover; }
.qr-box { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #1a5276; }
.stamp-area { margin-top: 50px; display: flex; justify-content: space-between; }
.stamp-box { width: 150px; height: 150px; border: 2px dashed #999; display: flex; align-items: center; justify-content: center; color: #999; }
</style></head>
<body>
<div class="no-print text-center mb-3">
    <button onclick="window.print()" class="btn btn-primary btn-lg"><i class="bi bi-printer"></i> <?php echo trans('print'); ?></button>
    <button onclick="window.close()" class="btn btn-secondary btn-lg"><i class="bi bi-x-lg"></i> <?php echo trans('close'); ?></button>
</div>
<div class="print-container">
    <div class="print-header"><h4><?php echo $communeName; ?></h4><h2><?php echo trans('app_name'); ?></h2><p class="mb-0"><?php echo trans('citizen_details'); ?></p></div>
    <div class="row">
        <div class="col-md-3 text-center">
            <div class="photo-box"><?php if ($citizen['photo_path']): ?><img src="/<?php echo $citizen['photo_path']; ?>"><?php else: ?><span class="text-muted"><?php echo trans('photo'); ?></span><?php endif; ?></div>
        </div>
        <div class="col-md-9">
            <div class="info-row"><span class="info-label"><?php echo trans('national_id'); ?>:</span><span><?php echo $citizen['national_id'] ?? '-'; ?></span></div>
            <div class="info-row"><span class="info-label"><?php echo trans('family_name'); ?>:</span><span><?php echo $citizen['family_name']; ?></span></div>
            <div class="info-row"><span class="info-label"><?php echo trans('first_name'); ?>:</span><span><?php echo $citizen['first_name']; ?></span></div>
            <div class="info-row"><span class="info-label"><?php echo trans('father_name'); ?>:</span><span><?php echo $citizen['father_name'] ?? '-'; ?></span></div>
            <div class="info-row"><span class="info-label"><?php echo trans('mother_name'); ?>:</span><span><?php echo $citizen['mother_name'] ?? '-'; ?></span></div>
            <div class="info-row"><span class="info-label"><?php echo trans('birth_date'); ?>:</span><span><?php echo formatDate($citizen['birth_date']); ?></span></div>
            <div class="info-row"><span class="info-label"><?php echo trans('birth_place'); ?>:</span><span><?php echo $citizen['birth_place'] ?? '-'; ?></span></div>
            <div class="info-row"><span class="info-label"><?php echo trans('gender'); ?>:</span><span><?php echo $citizen['gender'] ? trans($citizen['gender']) : '-'; ?></span></div>
            <div class="info-row"><span class="info-label"><?php echo trans('address'); ?>:</span><span><?php echo $citizen['address'] ?? '-'; ?></span></div>
            <div class="info-row"><span class="info-label"><?php echo trans('neighborhood'); ?>:</span><span><?php echo $citizen['neighborhood'] ?? '-'; ?></span></div>
            <div class="info-row"><span class="info-label"><?php echo trans('phone'); ?>:</span><span><?php echo $citizen['phone'] ?? '-'; ?></span></div>
        </div>
    </div>
    <div class="qr-box"><?php if ($citizen['qr_code']): ?><img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=<?php echo urlencode($citizen['qr_code']); ?>" alt="QR"><p class="small text-muted mt-2"><?php echo $citizen['qr_code']; ?></p><?php endif; ?></div>
    <div class="stamp-area"><div><p class="text-muted small"><?php echo trans('issue_date'); ?>: <?php echo date('Y-m-d'); ?></p></div><div class="stamp-box"><span class="text-muted"><?php echo trans('stamp'); ?></span></div></div>
</div>
</body></html>