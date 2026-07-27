<?php
$pageTitle = 'CSV Import - SGRC';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$message = '';
$error = '';
$imported = 0;
$failed = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['csv']['tmp_name'])) {
    $file = $_FILES['csv']['tmp_name'];
    $handle = fopen($file, 'r');
    $headers = fgetcsv($handle);
    
    $expected = ['national_id', 'first_name', 'last_name', 'date_of_birth', 'gender'];
    $missing = array_diff($expected, $headers);
    
    if (!empty($missing)) {
        $error = 'Missing columns: ' . implode(', ', $missing);
    } else {
        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, $row);
            try {
                $exists = $db->query("SELECT id FROM citizens WHERE national_id = ?", [$data['national_id']])->fetch();
                if (!$exists) {
                    $db->query("INSERT INTO citizens (national_id, first_name, last_name, date_of_birth, gender, place_of_birth, address, phone, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                        [$data['national_id'], $data['first_name'], $data['last_name'], $data['date_of_birth'], $data['gender'], $data['place_of_birth'] ?? '', $data['address'] ?? '', $data['phone'] ?? '', $_SESSION['user_id']]);
                    $imported++;
                } else {
                    $failed++;
                }
            } catch (Exception $e) {
                $failed++;
            }
        }
        fclose($handle);
        
        $db->query("INSERT INTO import_history (import_type, file_name, records_processed, records_success, records_failed, imported_by) VALUES (?, ?, ?, ?, ?, ?)",
            ['csv', $_FILES['csv']['name'], $imported + $failed, $imported, $failed, $_SESSION['user_id']]);
        
        $message = "Imported: $imported, Failed: $failed";
    }
}
?>

<div class="container-fluid">
    <h2>Import CSV</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="card mb-4">
        <div class="card-body">
            <h5>Required CSV Format</h5>
            <code>national_id,first_name,last_name,date_of_birth,gender,place_of_birth,address,phone</code>
        </div>
    </div>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">CSV File</label>
            <input type="file" name="csv" class="form-control" accept=".csv" required>
        </div>
        <button type="submit" class="btn btn-primary">Import</button>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>