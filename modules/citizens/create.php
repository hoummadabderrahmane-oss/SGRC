<?php
// Handle POST first (before any output)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once dirname(__FILE__) . '/../../config/database.php';
    require_once dirname(__FILE__) . '/../../includes/auth.php';
    requireAuth();
    
    $db = Database::getInstance();
    $national_id = isset($_POST['national_id']) ? $_POST['national_id'] : '';
    $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
    $family_name = isset($_POST['family_name']) ? $_POST['family_name'] : '';
    $birth_date = isset($_POST['birth_date']) ? $_POST['birth_date'] : '';
    $birth_place = isset($_POST['birth_place']) ? $_POST['birth_place'] : '';
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';
    $neighborhood = isset($_POST['neighborhood']) ? $_POST['neighborhood'] : '';
    $father_name = isset($_POST['father_name']) ? $_POST['father_name'] : '';
    $mother_name = isset($_POST['mother_name']) ? $_POST['mother_name'] : '';
    $file_number = isset($_POST['file_number']) ? $_POST['file_number'] : '';
    $file_date = isset($_POST['file_date']) ? $_POST['file_date'] : '';
    $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
    
    $exists = $db->query("SELECT id FROM citizens WHERE national_id = ?", array($national_id))->fetch();
    if (!$exists) {
        $photo_path = null;
        if (!empty($_FILES['photo']['name'])) {
            $uploadDir = dirname(__FILE__) . '/../../uploads/photos/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $photo_path = 'uploads/photos/' . time() . '_' . basename($_FILES['photo']['name']);
            move_uploaded_file($_FILES['photo']['tmp_name'], dirname(__FILE__) . '/../../' . $photo_path);
        }
        
        $db->query("INSERT INTO citizens (national_id, first_name, family_name, birth_date, birth_place, gender, address, neighborhood, father_name, mother_name, file_number, file_date, notes, photo_path, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            array($national_id, $first_name, $family_name, $birth_date, $birth_place, $gender, $address, $neighborhood, $father_name, $mother_name, $file_number, $file_date, $notes, $photo_path, $_SESSION['user_id']));
        
        header('Location: index.php');
        exit;
    }
    
    // If we get here, national_id exists — store error for display
    $error = isset($lang['national_id']) ? $lang['national_id'] . ' already exists' : 'National ID already exists';
}

// Now start output
$pageTitle = (isset($lang['create']) ? $lang['create'] : 'Create') . ' ' . (isset($lang['citizens']) ? $lang['citizens'] : 'Citizen') . ' - SGRC';
require_once dirname(__FILE__) . '/../../includes/header.php';
require_once dirname(__FILE__) . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
?>

<div class="container-fluid">
    <h2><?php echo isset($lang['create']) ? $lang['create'] : 'Create'; ?> <?php echo isset($lang['citizens']) ? $lang['citizens'] : 'Citizen'; ?></h2>
    
    <?php if (!empty($error)): ?>
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
            <label class="form-label"><?php echo isset($lang['family_name']) ? $lang['family_name'] : 'Family Name'; ?> *</label>
            <input type="text" name="family_name" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label"><?php echo isset($lang['birth_date']) ? $lang['birth_date'] : 'Birth Date'; ?> *</label>
            <input type="date" name="birth_date" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label"><?php echo isset($lang['birth_place']) ? $lang['birth_place'] : 'Birth Place'; ?></label>
            <input type="text" name="birth_place" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label"><?php echo isset($lang['gender']) ? $lang['gender'] : 'Gender'; ?> *</label>
            <select name="gender" class="form-select" required>
                <option value="male"><?php echo isset($lang['male']) ? $lang['male'] : 'Male'; ?></option>
                <option value="female"><?php echo isset($lang['female']) ? $lang['female'] : 'Female'; ?></option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['file_number']) ? $lang['file_number'] : 'File Number'; ?></label>
            <input type="text" name="file_number" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['file_date']) ? $lang['file_date'] : 'File Date'; ?></label>
            <input type="text" name="file_date" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['father_name']) ? $lang['father_name'] : "Father's Name"; ?></label>
            <input type="text" name="father_name" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['mother_name']) ? $lang['mother_name'] : "Mother's Name"; ?></label>
            <input type="text" name="mother_name" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['address']) ? $lang['address'] : 'Address'; ?></label>
            <input type="text" name="address" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['neighborhood']) ? $lang['neighborhood'] : 'Neighborhood'; ?></label>
            <input type="text" name="neighborhood" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label"><?php echo isset($lang['notes']) ? $lang['notes'] : 'Notes'; ?></label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary"><?php echo isset($lang['save']) ? $lang['save'] : 'Save'; ?></button>
            <a href="index.php" class="btn btn-secondary"><?php echo isset($lang['cancel']) ? $lang['cancel'] : 'Cancel'; ?></a>
        </div>
    </form>
</div>

<?php require_once dirname(__FILE__) . '/../../includes/footer.php'; ?