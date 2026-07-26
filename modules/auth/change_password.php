<?php
/**
 * SGRC - Change Password
 * تغيير كلمة المرور
 */

require_once __DIR__ . '/../../app/Core/App.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAuth();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!app()->validateCsrf($_POST['csrf_token'] ?? '')) {
        $error = trans('invalid_csrf');
    } else {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = trans('required_field');
        } elseif (strlen($newPassword) < 6) {
            $error = 'Password must be at least 6 characters';
        } elseif ($newPassword !== $confirmPassword) {
            $error = trans('password_mismatch');
        } else {
            try {
                $db = app()->db();
                $userId = session()->getUserId();

                $stmt = $db->prepare("SELECT password_hash FROM users WHERE id = :id");
                $stmt->execute([':id' => $userId]);
                $user = $stmt->fetch();

                if ($user && password_verify($currentPassword, $user['password_hash'])) {
                    $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
                    $db->prepare("UPDATE users SET password_hash = :hash WHERE id = :id")
                       ->execute([':hash' => $newHash, ':id' => $userId]);

                    $message = 'Password changed successfully!';
                    app()->logActivity('password_changed', "User changed password", 'auth');
                } else {
                    $error = 'Current password is incorrect';
                }
            } catch (PDOException $e) {
                error_log("Change password error: " . $e->getMessage());
                $error = trans('error');
            }
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
    <title><?php echo trans('change_password'); ?> - <?php echo trans('app_name'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.<?php echo $isRTL ? 'rtl' : 'min'; ?>.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .password-container {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
        }
        .btn-primary {
            background: linear-gradient(135deg, #1a5276, #2980b9);
            border: none;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="password-container">
        <h3 class="mb-4">
            <i class="bi bi-shield-lock"></i> 
            <?php echo trans('change_password'); ?>
        </h3>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <?php csrfField(); ?>
            <div class="mb-3">
                <label class="form-label"><?php echo trans('current_password'); ?></label>
                <input type="password" class="form-control" name="current_password" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><?php echo trans('new_password'); ?></label>
                <input type="password" class="form-control" name="new_password" required minlength="6">
            </div>
            <div class="mb-4">
                <label class="form-label"><?php echo trans('confirm_password'); ?></label>
                <input type="password" class="form-control" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-check-lg"></i> <?php echo trans('change_password'); ?>
            </button>
        </form>

        <div class="mt-3 text-center">
            <a href="/modules/dashboard/index.php" class="text-decoration-none">
                <i class="bi bi-arrow-<?php echo $isRTL ? 'right' : 'left'; ?>"></i> 
                <?php echo trans('dashboard'); ?>
            </a>
        </div>
    </div>
</body>
</html>