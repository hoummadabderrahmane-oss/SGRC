<?php
$pageTitle = 'Create Citizen - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$error = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $national_id = $_POST['national_id'] ?? '';
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
    
    $exists = $db->query("SELECT id FROM citizens WHERE national_id = ?", [$national_id])->fetch();
    if ($exists) {
        $error = 'National ID already exists';
    } else {
        $photo_path = null;
        if (!empty($_FILES['photo']['name'])) {
            $uploadDir = __DIR__ . '/../../uploads/photos/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $photo_path = 'uploads/photos/' . time() . '_' . basename($_FILES['photo']['name']);
            move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/../../' . $photo_path);
        }
        
        $db->query("INSERT INTO citizens (national_id, first_name, last_name, first_name_ar, last_name_ar, date_of_birth, place_of_birth, gender, address, phone, email, blood_type, father_name, mother_name, marital_status, photo_path, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$national_id, $first_name, $last_name, $first_name_ar, $last_name_ar, $date_of_birth, $place_of_birth, $gender, $address, $phone, $email, $blood_type, $father_name, $mother_name, $marital_status, $photo_path, $_SESSION['user_id']]);
        
        $message = 'Citizen created successfully';
        header('Location: index.php');
        exit;
    }
}
?>

<div class="container-fluid">
    <h2><?php echo $lang['create'] ?? 'Create'; ?> <?php echo $lang['citizens'] ?? 'Citizen'; ?></h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">National ID *</label>
            <input type="text" name="national_id" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Photo</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
        </div>
        <div class="col-md-6">
            <label class="form-label">First Name *</label>
            <input type="text" name="first_name" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Last Name *</label>
            <input type="text" name="last_name" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">First Name (Arabic)</label>
            <input type="text" name="first_name_ar" class="form-control" dir="rtl">
        </div>
        <div class="col-md-6">
            <label class="form-label">Last Name (Arabic)</label>
            <input type="text" name="last_name_ar" class="form-control" dir="rtl">
        </div>
        <div class="col-md-4">
            <label class="form-label">Date of Birth *</label>
            <input type="date" name="date_of_birth" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Place of Birth</label>
            <input type="text" name="place_of_birth" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Gender *</label>
            <select name="gender" class="form-select" required>
                <option value="male"><?php echo $lang['male'] ?? 'Male'; ?></option>
                <option value="female"><?php echo $lang['female'] ?? 'Female'; ?></option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="tel" name="phone" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Blood Type</label>
            <select name="blood_type" class="form-select">
                <option value="">--</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Marital Status</label>
            <select name="marital_status" class="form-select">
                <option value="single"><?php echo $lang['single'] ?? 'Single'; ?></option>
                <option value="married"><?php echo $lang['married'] ?? 'Married'; ?></option>
                <option value="divorced"><?php echo $lang['divorced'] ?? 'Divorced'; ?></option>
                <option value="widowed"><?php echo $lang['widowed'] ?? 'Widowed'; ?></option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Father's Name</label>
            <input type="text" name="father_name" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Mother's Name</label>
            <input type="text" name="mother_name" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2"></textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary"><?php echo $lang['save'] ?? 'Save'; ?></button>
            <a href="index.php" class="btn btn-secondary"><?php echo $lang['cancel'] ?? 'Cancel'; ?></a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>