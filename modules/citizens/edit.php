<?php
$pageTitle = trans('add_citizen'); $activeModule = 'citizens';
require_once __DIR__ . '/../../includes/header.php';
if (!can('citizens.create')) { app()->redirect('index.php', 'error', trans('access_denied')); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!app()->validateCsrf($_POST['csrf_token'] ?? '')) { $error = trans('invalid_csrf'); }
    else {
        $d = ['national_id' => app()->sanitize($_POST['national_id'] ?? ''), 'family_name' => app()->sanitize($_POST['family_name'] ?? ''), 'first_name' => app()->sanitize($_POST['first_name'] ?? ''), 'father_name' => app()->sanitize($_POST['father_name'] ?? ''), 'mother_name' => app()->sanitize($_POST['mother_name'] ?? ''), 'birth_date' => $_POST['birth_date'] ?? null, 'birth_place' => app()->sanitize($_POST['birth_place'] ?? ''), 'gender' => $_POST['gender'] ?? null, 'address' => app()->sanitize($_POST['address'] ?? ''), 'neighborhood' => app()->sanitize($_POST['neighborhood'] ?? ''), 'phone' => app()->sanitize($_POST['phone'] ?? ''), 'email' => filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL), 'notes' => app()->sanitize($_POST['notes'] ?? '')];
        if (empty($d['family_name']) || empty($d['first_name'])) { $error = trans('required_field'); }
        elseif ($d['email'] && !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) { $error = trans('invalid_email'); }
        else {
            try {
                $db = app()->db();
                if ($d['national_id']) { $chk = $db->prepare("SELECT id FROM citizens WHERE national_id = :nid"); $chk->execute([':nid' => $d['national_id']]); if ($chk->fetch()) { $error = 'National ID already exists'; } }
                if (!$error) {
                    $pp = null; if (!empty($_FILES['photo']['tmp_name'])) { $up = app()->uploadFile($_FILES['photo'], 'photos', ['jpg','jpeg','png']); if ($up['success']) $pp = $up['path']; }
                    $qr = 'QR_' . uniqid();
                    $db->prepare("INSERT INTO citizens (national_id,family_name,first_name,father_name,mother_name,birth_date,birth_place,gender,address,neighborhood,phone,email,photo_path,qr_code,notes,created_by) VALUES (:nid,:fn,:sn,:fan,:mn,:bd,:bp,:g,:a,:n,:p,:e,:pp,:qr,:no,:cb)")->execute([':nid'=>$d['national_id']?:null,':fn'=>$d['family_name'],':sn'=>$d['first_name'],':fan'=>$d['father_name']?:null,':mn'=>$d['mother_name']?:null,':bd'=>$d['birth_date']?:null,':bp'=>$d['birth_place']?:null,':g'=>$d['gender'],':a'=>$d['address']?:null,':n'=>$d['neighborhood']?:null,':p'=>$d['phone']?:null,':e'=>$d['email']?:null,':pp'=>$pp,':qr'=>$qr,':no'=>$d['notes']?:null,':cb'=>session()->getUserId()]);
                    $cid = $db->lastInsertId();
                    app()->logActivity('citizen_created', "Created citizen #{$cid}: {$d['family_name']} {$d['first_name']}", 'citizens');
                    app()->redirect("view.php?id=$cid", 'success', trans('citizen_added'));
                }
            } catch (PDOException $e) { error_log("Create citizen error: " . $e->getMessage()); $error = trans('error'); }
        }
    }
}
$neighborhoods = app()->db()->query("SELECT DISTINCT neighborhood FROM citizens WHERE neighborhood IS NOT NULL AND neighborhood != '' ORDER BY neighborhood")->fetchAll(PDO::FETCH_COLUMN);
?>
<div class="page-header"><h2><i class="bi bi-person-plus text-primary"></i> <?php echo trans('add_citizen'); ?></h2></div>
<?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
<div class="chart-card">
    <form method="POST" action="" enctype="multipart/form-data" class="row g-3">
        <?php csrfField(); ?>
        <div class="col-12"><label class="form-label"><?php echo trans('photo'); ?></label><div class="d-flex align-items-center gap-3"><div id="photoPreview" class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width:100px;height:100px;overflow:hidden;"><i class="bi bi-person" style="font-size:3rem;color:#ccc;"></i></div><div><input type="file" class="form-control" name="photo" id="photoInput" accept="image/*"><small class="text-muted">JPG, PNG (max 2MB)</small></div></div></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('national_id'); ?></label><input type="text" class="form-control form-control-custom" name="national_id" value="<?php echo $_POST['national_id'] ?? ''; ?>"></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('family_name'); ?> *</label><input type="text" class="form-control form-control-custom" name="family_name" required value="<?php echo $_POST['family_name'] ?? ''; ?>"></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('first_name'); ?> *</label><input type="text" class="form-control form-control-custom" name="first_name" required value="<?php echo $_POST['first_name'] ?? ''; ?>"></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('father_name'); ?></label><input type="text" class="form-control form-control-custom" name="father_name" value="<?php echo $_POST['father_name'] ?? ''; ?>"></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('mother_name'); ?></label><input type="text" class="form-control form-control-custom" name="mother_name" value="<?php echo $_POST['mother_name'] ?? ''; ?>"></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('birth_date'); ?></label><input type="date" class="form-control form-control-custom" name="birth_date" value="<?php echo $_POST['birth_date'] ?? ''; ?>"></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('birth_place'); ?></label><input type="text" class="form-control form-control-custom" name="birth_place" value="<?php echo $_POST['birth_place'] ?? ''; ?>"></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('gender'); ?></label><select class="form-select form-control-custom" name="gender"><option value=""><?php echo trans('select'); ?></option><option value="male" <?php echo ($_POST['gender']??'')==='male'?'selected':''; ?>><?php echo trans('male'); ?></option><option value="female" <?php echo ($_POST['gender']??'')==='female'?'selected':''; ?>><?php echo trans('female'); ?></option></select></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('neighborhood'); ?></label><input type="text" class="form-control form-control-custom" name="neighborhood" list="neighborhoodsList" value="<?php echo $_POST['neighborhood'] ?? ''; ?>"><datalist id="neighborhoodsList"><?php foreach ($neighborhoods as $n): ?><option value="<?php echo $n; ?>"><?php endforeach; ?></datalist></div>
        <div class="col-12"><label class="form-label"><?php echo trans('address'); ?></label><textarea class="form-control form-control-custom" name="address" rows="2"><?php echo $_POST['address'] ?? ''; ?></textarea></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('phone'); ?></label><input type="tel" class="form-control form-control-custom" name="phone" value="<?php echo $_POST['phone'] ?? ''; ?>"></div>
        <div class="col-md-6"><label class="form-label"><?php echo trans('email'); ?></label><input type="email" class="form-control form-control-custom" name="email" value="<?php echo $_POST['email'] ?? ''; ?>"></div>
        <div class="col-12"><label class="form-label"><?php echo trans('notes'); ?></label><textarea class="form-control form-control-custom" name="notes" rows="3"><?php echo $_POST['notes'] ?? ''; ?></textarea></div>
        <div class="col-12 d-flex gap-2"><button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-lg"></i> <?php echo trans('save'); ?></button><a href="index.php" class="btn btn-secondary"><i class="bi bi-x-lg"></i> <?php echo trans('cancel'); ?></a></div>
    </form>
</div>
<script>document.getElementById('photoInput').addEventListener('change', function(e){const f=e.target.files[0];if(f){const r=new FileReader();r.onload=function(e){document.getElementById('photoPreview').innerHTML=`<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`};r.readAsDataURL(f)}});</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>