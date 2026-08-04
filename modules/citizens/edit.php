<?php
// Handle POST first (before any output)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    require_once dirname(__FILE__) . '/../../config/database.php';
    require_once dirname(__FILE__) . '/../../includes/auth.php';
    requireAuth();
    
    $db = Database::getInstance();
    $id = intval($_GET['id']);
    
    $citizen = $db->query("SELECT * FROM citizens WHERE id = ?", array($id))->fetch();
    if (!$citizen) {
        header('Location: index.php');
        exit;
    }
    
    $national_id = isset($_POST['national_id']) ? $_POST['national_id'] : '';
    $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
    $family_name = isset($_POST['family_name']) ? $_POST['family_name'] : '';
    $father_name = isset($_POST['father_name']) ? $_POST['father_name'] : '';
    $mother_name = isset($_POST['mother_name']) ? $_POST['mother_name'] : '';
    $birth_date = isset($_POST['birth_date']) ? $_POST['birth_date'] : '';
    $birth_place = isset($_POST['birth_place']) ? $_POST['birth_place'] : '';
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';
    $neighborhood = isset($_POST['neighborhood']) ? $_POST['neighborhood'] : '';
    $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
    $file_number = isset($_POST['file_number']) ? $_POST['file_number'] : '';
    $file_date = isset($_POST['file_date']) ? $_POST['file_date'] : '';
    
    $photo_path = $citizen['photo_path'];
    if (!empty($_FILES['photo']['name'])) {
        $uploadDir = dirname(__FILE__) . '/../../uploads/photos/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $photo_path = 'uploads/photos/' . time() . '_' . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], dirname(__FILE__) . '/../../' . $photo_path);
    }
    
    $db->query("UPDATE citizens SET national_id = ?, first_name = ?, family_name = ?, father_name = ?, mother_name = ?, birth_date = ?, birth_place = ?, gender = ?, address = ?, neighborhood = ?, notes = ?, file_number = ?, file_date = ?, photo_path = ?, updated_by = ? WHERE id = ?",
        array($national_id, $first_name, $family_name, $father_name, $mother_name, $birth_date, $birth_place, $gender, $address, $neighborhood, $notes, $file_number, $file_date, $photo_path, $_SESSION['user_id'], $id));
    
    header('Location: index.php');
    exit;
}

// Now start output
$pageTitle = (isset($lang['edit']) ? $lang['edit'] : 'Edit') . ' Citizen - SGRC';
require_once dirname(__FILE__) . '/../../includes/header.php';
require_once dirname(__FILE__) . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$citizen = $db->query("SELECT * FROM citizens WHERE id = ?", array($id))->fetch();
if (!$citizen) {
    echo '<div class="alert alert-danger">Citizen not found</div>';
    echo '<a href="index.php" class="btn btn-primary">' . (isset($lang['go_back']) ? $lang['go_back'] : 'Go Back') . '</a>';
    require_once dirname(__FILE__) . '/../../includes/footer.php';
    exit;
}
?>

<div class="container-fluid">
    <h2><?php echo isset($lang['edit']) ? $lang['edit'] : 'Edit'; ?> <?php echo isset($lang['citizens']) ? $lang['citizens'] : 'Citizen'; ?></h2>
    
    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['national_id']) ? $lang['national_id'] : 'National ID'; ?></label>
            <input type="text" name="national_id" class="form-control" value="<?php echo htmlspecialchars($citizen['national_id']); ?>" readonly>
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['photo']) ? $lang['photo'] : 'Photo'; ?></label>
            <input type="file" name="photo" class="form-control">
            <?php if (!empty($citizen['photo_path'])): ?>
                <img src="/SGRC/<?php echo $citizen['photo_path']; ?>" class="mt-2" style="max-width:100px;max-height:100px;border-radius:8px;">
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['first_name']) ? $lang['first_name'] : 'First Name'; ?> *</label>
            <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($citizen['first_name']); ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['family_name']) ? $lang['family_name'] : 'Family Name'; ?> *</label>
            <input type="text" name="family_name" class="form-control" value="<?php echo htmlspecialchars($citizen['family_name']); ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['first_name']) ? $lang['first_name'] : 'First Name'; ?> (<?php echo isset($lang['arabic']) ? $lang['arabic'] : 'Arabic'; ?>)</label>
            <input type="text" name="first_name_ar" class="form-control" dir="rtl" value="<?php echo htmlspecialchars(isset($citizen['first_name_ar']) ? $citizen['first_name_ar'] : ''); ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['family_name']) ? $lang['family_name'] : 'Family Name'; ?> (<?php echo isset($lang['arabic']) ? $lang['arabic'] : 'Arabic'; ?>)</label>
            <input type="text" name="last_name_ar" class="form-control" dir="rtl" value="<?php echo htmlspecialchars(isset($citizen['last_name_ar']) ? $citizen['last_name_ar'] : ''); ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label"><?php echo isset($lang['birth_date']) ? $lang['birth_date'] : 'Birth Date'; ?> *</label>
            <input type="date" name="birth_date" class="form-control" value="<?php echo $citizen['birth_date']; ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label"><?php echo isset($lang['birth_place']) ? $lang['birth_place'] : 'Birth Place'; ?></label>
            <input type="text" name="birth_place" class="form-control" value="<?php echo htmlspecialchars(isset($citizen['birth_place']) ? $citizen['birth_place'] : ''); ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label"><?php echo isset($lang['gender']) ? $lang['gender'] : 'Gender'; ?> *</label>
            <select name="gender" class="form-select" required>
                <option value="male" <?php echo $citizen['gender'] === 'male' ? 'selected' : ''; ?>><?php echo isset($lang['male']) ? $lang['male'] : 'Male'; ?></option>
                <option value="female" <?php echo $citizen['gender'] === 'female' ? 'selected' : ''; ?>><?php echo isset($lang['female']) ? $lang['female'] : 'Female'; ?></option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['file_number']) ? $lang['file_number'] : 'File Number'; ?></label>
            <input type="text" name="file_number" class="form-control" value="<?php echo htmlspecialchars(isset($citizen['file_number']) ? $citizen['file_number'] : ''); ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['file_date']) ? $lang['file_date'] : 'File Date'; ?></label>
            <input type="text" name="file_date" class="form-control" value="<?php echo htmlspecialchars(isset($citizen['file_date']) ? $citizen['file_date'] : ''); ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['address']) ? $lang['address'] : 'Address'; ?></label>
            <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars(isset($citizen['address']) ? $citizen['address'] : ''); ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label"><?php echo isset($lang['neighborhood']) ? $lang['neighborhood'] : 'Neighborhood'; ?></label>
            <input type="text" name="neighborhood" class="form-control" value="<?php echo htmlspecialchars(isset($citizen['neighborhood']) ? $citizen['neighborhood'] : ''); ?>">
        </div>
        <div class="col-12">
            <label class="form-label"><?php echo isset($lang['notes']) ? $lang['notes'] : 'Notes'; ?></label>
            <textarea name="notes" class="form-control" rows="2"><?php echo htmlspecialchars(isset($citizen['notes']) ? $citizen['notes'] : ''); ?></textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary"><?php echo isset($lang['save']) ? $lang['save'] : 'Save'; ?></button>
            <a href="index.php" class="btn btn-secondary"><?php echo isset($lang['cancel']) ? $lang['cancel'] : 'Cancel'; ?></a>
        </div>
    </form>
</div>

<?php require_once dirname(__FILE__) . '/../../includes/footer.php'; ?>