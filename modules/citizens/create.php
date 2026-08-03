<?php
$pageTitle = (isset($lang['create']) ? $lang['create'] : 'Create') . ' ' . (isset($lang['citizens']) ? $lang['citizens'] : 'Citizen') . ' - SGRC';
require_once dirname(__FILE__) . '/../../includes/header.php';
require_once dirname(__FILE__) . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$error = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $national_id = isset($_POST['national_id']) ? $_POST['national_id'] : '';
    $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
    $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
    $first_name_ar = isset($_POST['first_name_ar']) ? $_POST['first_name_ar'] : '';
    $last_name_ar = isset($_POST['last_name_ar']) ? $_POST['last_name_ar'] : '';
    $date_of_birth = isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : '';
    $place_of_birth = isset($_POST['place_of_birth']) ? $_POST['place_of_birth'] : '';
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';
    $blood_type = isset($_POST['blood_type']) ? $_POST['blood_type'] : '';
    $father_name = isset($_POST['father_name']) ? $_POST['father_name'] : '';
    $mother_name = isset($_POST['mother_name']) ? $_POST['mother_name'] : '';
    $marital_status = isset($_POST['marital_status']) ? $_POST['marital_status'] : 'single';
    $file_number = isset($_POST['file_number']) ? $_POST['file_number'] : '';
    $file_date = isset($_POST['file_date']) ? $_POST['file_date'] : '';
    
    $exists = $db->query("SELECT id FROM citizens WHERE national_id = ?", array($national_id))->fetch();
    if ($exists) {
        $error = isset($lang['national_id']) ? $lang['national_id'] . ' already exists' : 'National ID already exists';
    } else {
        $photo_path = null;
        if (!empty($_FILES['photo']['name'])) {
            $uploadDir = dirname(__FILE__) . '/../../uploads/photos/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $photo_path = 'uploads/photos/' . time() . '_' . basename($_FILES['photo']['name']);
            move_uploaded_file($_FILES['photo']['tmp_name'], dirname(__FILE__) . '/../../' . $photo_path);
        }
        
        $db->query("INSERT INTO citizens (national_id, first_name, last_name, first_name_ar, last_name_ar, date_of_birth, place_of_birth, gender, address, blood_type, father_name, mother_name, marital_status, file_number, file_date, photo_path, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            array($national_id, $first_name, $last_name, $first_name_ar, $last_name_ar, $date_of_birth, $place_of_birth, $gender, $address, $blood_type, $father_name, $mother_name, $marital_status, $file_number, $file_date, $photo_path, $_SESSION['user_id']));
        
        $message = isset($lang['success']) ? $lang['success'] : 'Citizen created successfully';
        header('Location: index.php');
        exit;
    }
}
?>

<div class="container-fluid">
    <h2><?php echo isset($lang['create']) ? $lang['create'] : 'Create'; ?> <?php echo isset($lang['citizens']) ? $lang['citizens'] : 'Citizen'; ?></h2>
    
    <?php if ($error != ''): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['national_id']) ? $lang['national_id'] : 'National ID'; ?> *</label>
            <input type="text" name="national_id" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['photo']) ? $lang['photo'] : 'Photo'; ?></label>
            <input type="file" name="photo" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['first_name']) ? $lang['first_name'] : 'First Name'; ?> *</label>
            <input type="text" name="first_name" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['last_name']) ? $lang['last_name'] : 'Last Name'; ?> *</label>
            <input type="text" name="last_name" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['first_name']) ? $lang['first_name'] : 'First Name'; ?> (<?php echo isset($lang['arabic']) ? $lang['arabic'] : 'Arabic'; ?>)</label>
            <input type="text" name="first_name_ar" class="form-control" dir="rtl">
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['last_name']) ? $lang['last_name'] : 'Last Name'; ?> (<?php echo isset($lang['arabic']) ? $lang['arabic'] : 'Arabic'; ?>)</label>
            <input type="text" name="last_name_ar" class="form-control" dir="rtl">
        </div>
        <div class="col-md-4">
            <label class="form-label"><?php echo isset($lang['date_of_birth']) ? $lang['date_of_birth'] : 'Date of Birth'; ?> *</label>
            <input type="date" name="date_of_birth" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label"><?php echo isset($lang['place_of_birth']) ? $lang['place_of_birth'] : 'Place of Birth'; ?></label>
            <input type="text" name="place_of_birth" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label"><?php echo isset($lang['gender']) ? $lang['gender'] : 'Gender'; ?> *</label>
            <select name="gender" class="form-select" required>
                <option value="male"><?php echo isset($lang['male']) ? $lang['male'] : 'Male'; ?></option>
                <option value="female"><?php echo isset($lang['female']) ? $lang['female'] : 'Female'; ?></option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['file_number']) ? $lang['file_number'] : 'File Number'; ?> *</label>
            <input type="text" name="file_number" class="form-control" required placeholder="<?php echo isset($lang['file_number_example']) ? $lang['file_number_example'] : 'Ex: 1233'; ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['file_date']) ? $lang['file_date'] : 'File Date'; ?> *</label>
            <input type="text" name="file_date" class="form-control" required placeholder="<?php echo isset($lang['file_date_example']) ? $lang['file_date_example'] : 'Ex: 2006/434'; ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label"><?php echo isset($lang['blood_type']) ? $lang['blood_type'] : 'Blood Type'; ?></label>
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
            <label class="form-label"><?php echo isset($lang['marital_status']) ? $lang['marital_status'] : 'Marital Status'; ?></label>
            <select name="marital_status" class="form-select">
                <option value="single"><?php echo isset($lang['single']) ? $lang['single'] : 'Single'; ?></option>
                <option value="married"><?php echo isset($lang['married']) ? $lang['married'] : 'Married'; ?></option>
                <option value="divorced"><?php echo isset($lang['divorced']) ? $lang['divorced'] : 'Divorced'; ?></option>
                <option value="widowed"><?php echo isset($lang['widowed']) ? $lang['widowed'] : 'Widowed'; ?></option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label"><?php echo isset($lang['father_name']) ? $lang['father_name'] : "Father's Name"; ?></label>
            <input type="text" name="father_name" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['mother_name']) ? $lang['mother_name'] : "Mother's Name"; ?></label>
            <input type="text" name="mother_name" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label"><?php echo isset($lang['address']) ? $lang['address'] : 'Address'; ?></label>
            <textarea name="address" class="form-control" rows="2"></textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary"><?php echo isset($lang['save']) ? $lang['save'] : 'Save'; ?></button>
            <a href="index.php" class="btn btn-secondary"><?php echo isset($lang['cancel']) ? $lang['cancel'] : 'Cancel'; ?></a>
        </div>
    </form>
</div>

<?php require_once dirname(__FILE__) . '/../../includes/footer.php'; ?>