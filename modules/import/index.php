<?php
$pageTitle = $lang['import'] ?? 'Import';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$message = '';
$error = '';

// Check if import_history table exists
$tableExists = false;
try {
    $db->query("SELECT 1 FROM import_history LIMIT 1");
    $tableExists = true;
} catch (PDOException $e) {
    $tableExists = false;
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['import_file']) && $tableExists) {
    $file = $_FILES['import_file'];
    $module = $_POST['module'] ?? 'citizens';

    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($ext, ['csv', 'xlsx', 'xls'])) {
            $uploadDir = __DIR__ . '/../../uploads/imports/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $filename = date('Ymd_His') . '_' . basename($file['name']);
            $filepath = $uploadDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $db->query(
                    "INSERT INTO import_history (filename, module, file_type, uploaded_by, status) VALUES (?, ?, ?, ?, ?)",
                    [$filename, $module, $ext, $_SESSION['user_id'], 'pending']
                );
                $message = $lang['import_success'] ?? 'File uploaded successfully. Processing will begin shortly.';
            } else {
                $error = 'Failed to move uploaded file.';
            }
        } else {
            $error = 'Invalid file type. Only CSV, XLSX, and XLS files are allowed.';
        }
    } else {
        $error = 'Upload error: ' . $file['error'];
    }
}

// Get import history (only if table exists)
$history = [];
if ($tableExists) {
    try {
        $history = $db->query(
            "SELECT h.*, u.username FROM import_history h LEFT JOIN users u ON h.uploaded_by = u.id ORDER BY h.created_at DESC LIMIT 20"
        )->fetchAll();
    } catch (PDOException $e) {
        $history = [];
    }
}

// Import modules available
$importModules = [
    'citizens' => ['icon' => 'fa-users', 'label' => $lang['citizens'] ?? 'Citizens', 'desc' => 'Import citizen records with personal details'],
    'registers' => ['icon' => 'fa-file-lines', 'label' => $lang['registers'] ?? 'Registers', 'desc' => 'Import birth, death, marriage records'],
    'certificates' => ['icon' => 'fa-certificate', 'label' => $lang['certificates'] ?? 'Certificates', 'desc' => 'Import certificate data'],
    'documents' => ['icon' => 'fa-folder-open', 'label' => $lang['documents'] ?? 'Documents', 'desc' => 'Import document metadata'],
];
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><?php echo $lang['import'] ?? 'Import Data'; ?></h2>
            <p class="text-muted mb-0">Upload CSV or Excel files to bulk import records</p>
        </div>
        <a href="import_history.php" class="btn btn-outline-primary">
            <i class="fas fa-history me-2"></i>View Full History
        </a>
    </div>

    <?php if (!$tableExists): ?>
    <!-- Setup Alert -->
    <div class="alert alert-warning d-flex align-items-start mb-4">
        <i class="fas fa-exclamation-triangle me-3 mt-1"></i>
        <div>
            <strong>Database table missing!</strong>
            <p class="mb-2">The <code>import_history</code> table does not exist. Please run the following SQL in phpMyAdmin:</p>
            <pre style="background:#1a1a2e;color:#fff;padding:16px;border-radius:8px;font-size:13px;overflow-x:auto;">
CREATE TABLE IF NOT EXISTS import_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    module VARCHAR(50) NOT NULL,
    file_type VARCHAR(10) NOT NULL,
    file_path VARCHAR(500),
    total_records INT DEFAULT 0,
    records_processed INT DEFAULT 0,
    records_failed INT DEFAULT 0,
    status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    error_log TEXT,
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;</pre>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($message): ?>
    <div class="alert alert-success d-flex align-items-center mb-4">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo $message; ?>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-danger d-flex align-items-center mb-4">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php echo $error; ?>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Upload Card -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-cloud-upload-alt me-2 text-primary"></i>Upload File</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" id="importForm">
                        <!-- Module Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Select Module</label>
                            <div class="row g-3">
                                <?php foreach ($importModules as $key => $mod): ?>
                                <div class="col-md-6">
                                    <label class="module-card <?php echo ($key === 'citizens') ? 'active' : ''; ?>" data-module="<?php echo $key; ?>">
                                        <input type="radio" name="module" value="<?php echo $key; ?>" 
                                               <?php echo ($key === 'citizens') ? 'checked' : ''; ?> class="d-none">
                                        <div class="module-card-inner">
                                            <div class="module-icon">
                                                <i class="fas <?php echo $mod['icon']; ?>"></i>
                                            </div>
                                            <div class="module-info">
                                                <div class="module-title"><?php echo $mod['label']; ?></div>
                                                <div class="module-desc"><?php echo $mod['desc']; ?></div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- File Upload Zone -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Upload File</label>
                            <div class="upload-zone" id="uploadZone">
                                <input type="file" name="import_file" id="importFile" accept=".csv,.xlsx,.xls" required class="d-none" <?php echo !$tableExists ? 'disabled' : ''; ?>>
                                <div class="upload-zone-content">
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <p class="upload-text">Drag & drop your file here, or <span>browse</span></p>
                                    <p class="upload-hint">Supports CSV, XLSX, XLS (Max 10MB)</p>
                                </div>
                                <div class="upload-file-info d-none" id="fileInfo">
                                    <div class="file-icon"><i class="fas fa-file-csv"></i></div>
                                    <div class="file-details">
                                        <div class="file-name" id="fileName"></div>
                                        <div class="file-size" id="fileSize"></div>
                                    </div>
                                    <button type="button" class="file-remove" id="removeFile"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </div>

                        <!-- Template Download -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle text-info me-2"></i>
                                <span class="text-muted small">Download a template file to ensure correct format:</span>
                                <a href="template.php?module=citizens" class="btn btn-sm btn-outline-info ms-auto" id="templateLink">
                                    <i class="fas fa-download me-1"></i>Download Template
                                </a>
                            </div>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary w-100" id="submitBtn" disabled <?php echo !$tableExists ? 'disabled' : ''; ?>>
                            <i class="fas fa-upload me-2"></i>Import Data
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Instructions Card -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-lightbulb me-2 text-warning"></i>Guidelines</h5>
                </div>
                <div class="card-body">
                    <div class="guideline-item">
                        <div class="guideline-number">1</div>
                        <div class="guideline-text">
                            <strong>Choose Module</strong>
                            <p class="text-muted small mb-0">Select the correct module for your data type</p>
                        </div>
                    </div>
                    <div class="guideline-item">
                        <div class="guideline-number">2</div>
                        <div class="guideline-text">
                            <strong>Download Template</strong>
                            <p class="text-muted small mb-0">Use the provided template for correct column headers</p>
                        </div>
                    </div>
                    <div class="guideline-item">
                        <div class="guideline-number">3</div>
                        <div class="guideline-text">
                            <strong>Fill Data</strong>
                            <p class="text-muted small mb-0">Enter your data following the template format</p>
                        </div>
                    </div>
                    <div class="guideline-item">
                        <div class="guideline-number">4</div>
                        <div class="guideline-text">
                            <strong>Upload & Import</strong>
                            <p class="text-muted small mb-0">Upload the file and click Import Data</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import History -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fas fa-clock-rotate-left me-2 text-primary"></i>Recent Imports</h5>
            <span class="badge bg-primary"><?php echo count($history); ?> total</span>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Module</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Records</th>
                        <th>Uploaded By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $item): ?>
                    <tr>
                        <td class="fw-semibold">
                            <i class="fas fa-file-<?php echo $item['file_type'] === 'csv' ? 'csv' : 'excel'; ?> text-muted me-2"></i>
                            <?php echo htmlspecialchars($item['filename']); ?>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">
                                <?php echo $lang[$item['module']] ?? ucfirst($item['module']); ?>
                            </span>
                        </td>
                        <td><span class="text-uppercase small"><?php echo $item['file_type']; ?></span></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $item['status'] === 'completed' ? 'success' : 
                                    ($item['status'] === 'processing' ? 'warning' : 
                                    ($item['status'] === 'failed' ? 'danger' : 'secondary')); 
                            ?>">
                                <i class="fas fa-<?php 
                                    echo $item['status'] === 'completed' ? 'check' : 
                                        ($item['status'] === 'processing' ? 'spinner fa-spin' : 
                                        ($item['status'] === 'failed' ? 'times' : 'clock')); 
                                ?> me-1"></i>
                                <?php echo ucfirst($item['status']); ?>
                            </span>
                        </td>
                        <td><?php echo $item['records_processed'] ?? 0; ?> / <?php echo $item['total_records'] ?? 0; ?></td>
                        <td><?php echo htmlspecialchars($item['username'] ?? 'Unknown'); ?></td>
                        <td><?php echo date('M d, Y H:i', strtotime($item['created_at'])); ?></td>
                        <td>
                            <a href="view_import.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if ($item['status'] === 'failed'): ?>
                            <a href="retry.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-warning" title="Retry">
                                <i class="fas fa-redo"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($history)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            No import history found
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Module card selection
document.querySelectorAll('.module-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.module-card').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        this.querySelector('input').checked = true;

        // Update template link
        const module = this.dataset.module;
        document.getElementById('templateLink').href = 'template.php?module=' + module;
    });
});

// File upload zone
document.getElementById('uploadZone').addEventListener('click', function(e) {
    if (e.target.closest('.file-remove')) return;
    document.getElementById('importFile').click();
});

document.getElementById('importFile').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
        document.querySelector('.upload-zone-content').classList.add('d-none');
        document.getElementById('fileInfo').classList.remove('d-none');
        document.getElementById('submitBtn').disabled = false;
    }
});

document.getElementById('removeFile').addEventListener('click', function(e) {
    e.stopPropagation();
    document.getElementById('importFile').value = '';
    document.querySelector('.upload-zone-content').classList.remove('d-none');
    document.getElementById('fileInfo').classList.add('d-none');
    document.getElementById('submitBtn').disabled = true;
});

// Drag and drop
document.getElementById('uploadZone').addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('dragover');
});

document.getElementById('uploadZone').addEventListener('dragleave', function() {
    this.classList.remove('dragover');
});

document.getElementById('uploadZone').addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('dragover');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('importFile').files = files;
        document.getElementById('fileName').textContent = files[0].name;
        document.getElementById('fileSize').textContent = (files[0].size / 1024).toFixed(1) + ' KB';
        document.querySelector('.upload-zone-content').classList.add('d-none');
        document.getElementById('fileInfo').classList.remove('d-none');
        document.getElementById('submitBtn').disabled = false;
    }
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>