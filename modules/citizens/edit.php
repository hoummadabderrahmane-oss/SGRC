<?php
$pageTitle = 'Edit Citizen - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$id = intval($_GET['id'] ?? 0);
$citizen = $db->query("SELECT * FROM citizens WHERE id = ?", [$id])->fetch();

if (!$citizen) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $first_name_ar = $_POST['first_name_ar'] ?? '';
    $last_name_ar = $_POST['last_name_ar'] ?? '';
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $place_of_birth = $_POST['place_of_birth'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $blood_type = $_POST['blood_type'] ?? '';
    $father_name = $_POST['father_name'] ?? '';
    $mother_name = $_POST['mother_name'] ?? '';
    $marital_status = $_POST['marital_status'] ?? 'single';
    
    $photo_path = $citizen['photo_path'];
    if (!empty($_FILES['photo']['name'])) {
        $uploadDir = __DIR__ . '/../../uploads/photos/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $photo_path = 'uploads/photos/' . time() . '_' . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/../../' . $photo_path);
    }
    
    $db->query("UPDATE citizens SET first_name = ?, last_name = ?, first_name_ar = ?, last_name_ar = ?, date_of_birth = ?, place_of_birth = ?, gender = ?, address = ?, phone = ?, email = ?, blood_type = ?, father_name = ?, mother_name = ?, marital_status = ?, photo_path = ? WHERE id = ?",
        [$first_name, $last_name, $first_name_ar, $last_name_ar, $date_of_birth, $place_of_birth, $gender, $address, $phone, $email, $blood_type, $father_name, $mother_name, $marital_status, $photo_path, $id]);
    
    header('Location: index.php');
    exit;
}
?>

<div class="container-fluid">
    <h2><?php echo $lang['edit'] ?? 'Edit'; ?> <?php echo $lang['citizens'] ?? 'Citizen'; ?></h2>
    
    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">National ID</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($citizen['national_id']); ?>" disabled>
        </div>
        <div class="col-md-6">
            <label class="form-label">Photo</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
            <?php if ($citizen['photo_path']): ?>
                <small>Current: <img src="/SGRC/<?php echo $citizen['photo_path']; ?>" height="50" class="mt-1"></small>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <label class="form-label">First Name *</label>
            <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($citizen['first_name']); ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Last Name *</label>
            <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($citizen['last_name']); ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">First Name (Arabic)</label>
            <input type="text" name="first_name_ar" class="form-control" dir="rtl" value="<?php echo htmlspecialchars($citizen['first_name_ar'] ?? ''); ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Last Name (Arabic)</label>
            <input type="text" name="last_name_ar" class="form-control" dir="rtl" value="<?php echo htmlspecialchars($citizen['last_name_ar'] ?? ''); ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Date of Birth *</label>
            <input type="date" name="date_of_birth" class="form-control" value="<?php echo $citizen['date_of_birth']; ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Place of Birth</label>
            <input type="text" name="place_of_birth" class="form-control" value="<?php echo htmlspecialchars($citizen['place_of_birth'] ?? ''); ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Gender *</label>
            <select name="gender" class="form-select" required>
                <option value="male" <?php echo $citizen['gender'] === 'male' ? 'selected' : ''; ?>><?php echo $lang['male'] ?? 'Male'; ?></option>
                <option value="female" <?php echo $citizen['gender'] === 'female' ? 'selected' : ''; ?>><?php echo $lang['female'] ?? 'Female'; ?></option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($citizen['phone'] ?? ''); ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($citizen['email'] ?? ''); ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Blood Type</label>
            <select name="blood_type" class="form-select">
                <option value="">--</option>
                <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt): ?>
                <option value="<?php echo $bt; ?>" <?php echo ($citizen['blood_type'] ?? '') === $bt ? 'selected' : ''; ?>><?php echo $bt; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Marital Status</label>
            <select name="marital_status" class="form-select">
                <?php foreach (['single','married','divorced','widowed'] as $ms): ?>
                <option value="<?php echo $ms; ?>" <?php echo $citizen['marital_status'] === $ms ? 'selected' : ''; ?>><?php echo $lang[$ms] ?? $ms; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Father's Name</label>
            <input type="text" name="father_name" class="form-control" value="<?php echo htmlspecialchars($citizen['father_name'] ?? ''); ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Mother's Name</label>
            <input type="text" name="mother_name" class="form-control" value="<?php echo htmlspecialchars($citizen['mother_name'] ?? ''); ?>">
        </div>
        <div class="col-12">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($citizen['address'] ?? ''); ?></textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary"><?php echo $lang['save'] ?? 'Save'; ?></button>
            <a href="index.php" class="btn btn-secondary"><?php echo $lang['cancel'] ?? 'Cancel'; ?></a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>