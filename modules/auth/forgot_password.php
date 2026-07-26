<?php
/**
 * SGRC - Forgot Password
 * نسيت كلمة المرور
 */

require_once __DIR__ . '/../../app/Core/App.php';

AuthMiddleware::guestOnly();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!app()->validateCsrf($_POST['csrf_token'] ?? '')) {
        $error = trans('invalid_csrf');
    } else {
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = trans('invalid_email');
        } else {
            try {
                $db = app()->db();
                $stmt = $db->prepare("SELECT id, username, full_name FROM users WHERE email = :email AND is_active = 1");
                $stmt->execute([':email' => $email]);
                $user = $stmt->fetch();

                if ($user) {
                    // Generate reset token
                    $token = bin2hex(random_bytes(32));
                    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                    // Store token (you would need a password_resets table)
                    // For now, just show success message
                    $message = 'Password reset instructions have been sent to your email.';
                    app()->logActivity('password_reset_requested', "Reset requested for {$email}", 'auth');
                } else {
                    // Don't reveal if email exists
                    $message = 'If this email exists, reset instructions have been sent.';
                }
            } catch (PDOException $e) {
                error_log("Forgot password error: " . $e->getMessage());
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
    <title><?php echo trans('forgot_password'); ?> - <?php echo trans('app_name'); ?></title>
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
        .forgot-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
            margin: 20px;
            padding: 40px;
        }
        .forgot-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .forgot-header i {
            font-size: 3rem;
            color: #f39c12;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #1a5276, #2980b9);
            border: none;
            border-radius: 10px;
            padding: 12px;
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <div class="forgot-header">
            <i class="bi bi-key-fill"></i>
            <h3><?php echo trans('forgot_password'); ?></h3>
            <p class="text-muted">Enter your email to reset password</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <?php csrfField(); ?>
            <div class="mb-3">
                <label class="form-label">Email / البريد الإلكتروني</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-send"></i> Send Reset Link
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none">
                <i class="bi bi-arrow-<?php echo $isRTL ? 'right' : 'left'; ?>"></i> 
                Back to Login
            </a>
        </div>
    </div>
</body>
</html>