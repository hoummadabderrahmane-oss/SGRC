<?php
/**
 * SGRC - Create Admin User
 * إنشاء حساب مدير
 * 
 * SECURITY: This file should be deleted or protected after first use!
 */

require_once __DIR__ . '/../../app/Core/App.php';

// Security check - only allow if no admin exists or via secret key
$secretKey = 'SGRC_SETUP_2024'; // Change this!
$providedKey = $_GET['key'] ?? '';

$db = app()->db();
$adminExists = $db->query("SELECT COUNT(*) as count FROM users WHERE role IN ('super_admin', 'admin')")->fetch()['count'] > 0;

if ($adminExists && $providedKey !== $secretKey) {
    die('Access denied. Admin already exists.');
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = app()->sanitize($_POST['username'] ?? '');
    $fullName = app()->sanitize($_POST['full_name'] ?? '');
    $fullNameFr = app()->sanitize($_POST['full_name_fr'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'admin';

    // Validation
    if (empty($username) || empty($fullName) || empty($password)) {
        $error = trans('required_field');
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($password !== $confirmPassword) {
        $error = trans('password_mismatch');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = trans('invalid_email');
    } else {
        try {
            // Check if username exists
            $stmt = $db->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
            $stmt->execute([':username' => $username, ':email' => $email]);

            if ($stmt->fetch()) {
                $error = 'Username or email already exists';
            } else {
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);

                $stmt = $db->prepare("INSERT INTO users (username, full_name, full_name_fr, email, password_hash, role) 
                                      VALUES (:username, :full_name, :full_name_fr, :email, :password_hash, :role)");
                $stmt->execute([
                    ':username' => $username,
                    ':full_name' => $fullName,
                    ':full_name_fr' => $fullNameFr,
                    ':email' => $email,
                    ':password_hash' => $passwordHash,
                    ':role' => $role
                ]);

                $message = 'Admin user created successfully!';
                app()->logActivity('admin_created', "Admin {$username} created", 'auth');
            }
        } catch (PDOException $e) {
            error_log("Create admin error: " . $e->getMessage());
            $error = trans('error');
        }
    }
}

$lang = getLang();
$dir = getDir();
$isRTL = isRTL();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin - <?php echo trans('app_name'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.<?php echo $isRTL ? 'rtl' : 'min'; ?>.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a5276 0%, #2980b9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .setup-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 500px;
            margin: 20px;
            padding: 40px;
        }
        .setup-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .setup-header i {
            font-size: 3rem;
            color: #2980b9;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #2980b9;
            box-shadow: 0 0 0 0.2rem rgba(41, 128, 185, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #1a5276, #2980b9);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
        }
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-header">
            <i class="bi bi-person-plus-fill"></i>
            <h2>Create Admin User</h2>
            <p class="text-muted">إنشاء حساب مدير</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i> <?php echo $message; ?>
                <hr>
                <a href="login.php" class="btn btn-success w-100">
                    <i class="bi bi-box-arrow-in-right"></i> Go to Login
                </a>
            </div>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Username / اسم المستخدم</label>
                    <input type="text" class="form-control" name="username" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Full Name (AR) / الاسم الكامل</label>
                    <input type="text" class="form-control" name="full_name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Full Name (FR) / Nom complet</label>
                    <input type="text" class="form-control" name="full_name_fr">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email / البريد الإلكتروني</label>
                    <input type="email" class="form-control" name="email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role / الدور</label>
                    <select class="form-select" name="role">
                        <option value="super_admin">Super Admin / مدير عام</option>
                        <option value="admin">Admin / مدير</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password / كلمة المرور</label>
                    <input type="password" class="form-control" name="password" required minlength="6">
                </div>

                <div class="mb-4">
                    <label class="form-label">Confirm Password / تأكيد كلمة المرور</label>
                    <input type="password" class="form-control" name="confirm_password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-person-plus"></i> Create Admin / إنشاء المدير
                </button>
            </form>
        <?php endif; ?>

        <div class="text-center mt-3">
            <a href="login.php" class="text-muted text-decoration-none">
                <i class="bi bi-arrow-<?php echo $isRTL ? 'right' : 'left'; ?>"></i> 
                Back to Login / العودة لتسجيل الدخول
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>