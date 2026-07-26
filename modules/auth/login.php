<?php
/**
 * SGRC - Login Page
 * صفحة تسجيل الدخول
 */

require_once __DIR__ . '/../../app/Core/App.php';
require_once __DIR__ . '/../../includes/auth.php';

// Redirect if already logged in
AuthMiddleware::guestOnly();

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF
    if (!app()->validateCsrf($_POST['csrf_token'] ?? '')) {
        $error = trans('invalid_csrf');
    } else {
        $username = app()->sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        if (empty($username) || empty($password)) {
            $error = trans('required_field');
        } else {
            try {
                $db = app()->db();
                $stmt = $db->prepare("SELECT * FROM users WHERE username = :username AND is_active = 1 LIMIT 1");
                $stmt->execute([':username' => $username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password_hash'])) {
                    // Set session
                    session()->setUser($user);

                    // Update last login
                    $db->prepare("UPDATE users SET last_login = NOW(), last_ip = :ip WHERE id = :id")
                       ->execute([':ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown', ':id' => $user['id']]);

                    // Remember me
                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        setcookie('remember_token', $token, [
                            'expires' => time() + 30 * 24 * 60 * 60,
                            'path' => '/',
                            'secure' => isset($_SERVER['HTTPS']),
                            'httponly' => true,
                            'samesite' => 'Strict'
                        ]);
                    }

                    // Log activity
                    app()->logActivity('login', "User {$user['username']} logged in", 'auth');

                    // Redirect
                    $redirect = $_SESSION['redirect_after_login'] ?? '/modules/dashboard/index.php';
                    unset($_SESSION['redirect_after_login']);
                    app()->flash('success', trans('login_success'));
                    header("Location: {$redirect}");
                    exit;
                } else {
                    $error = trans('login_failed');
                    app()->logActivity('login_failed', "Failed login attempt for: {$username}", 'auth');
                }
            } catch (PDOException $e) {
                error_log("Login error: " . $e->getMessage());
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
    <title><?php echo trans('login'); ?> - <?php echo trans('app_name'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.<?php echo $isRTL ? 'rtl' : 'min'; ?>.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #1a5276;
            --secondary-color: #2980b9;
            --accent-color: #f39c12;
        }
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: <?php echo $isRTL ? "'Segoe UI', 'Tahoma', sans-serif" : "'Segoe UI', 'Helvetica Neue', sans-serif"; ?>;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            margin: 20px;
        }
        .login-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .login-header i {
            font-size: 3rem;
            margin-bottom: 15px;
            display: block;
        }
        .login-header h1 {
            font-size: 1.5rem;
            margin: 0;
            font-weight: 600;
        }
        .login-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }
        .login-body {
            padding: 40px 30px;
        }
        .form-floating {
            margin-bottom: 20px;
        }
        .form-floating > .form-control {
            border-radius: 12px;
            border: 2px solid #e0e0e0;
            height: 56px;
        }
        .form-floating > .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(41, 128, 185, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(41, 128, 185, 0.4);
            color: white;
        }
        .lang-switcher {
            position: absolute;
            top: 20px;
            <?php echo $isRTL ? 'left' : 'right'; ?>: 20px;
        }
        .lang-switcher .btn {
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.3);
            color: white;
        }
        .lang-switcher .btn:hover {
            background: rgba(255,255,255,0.3);
            color: white;
        }
        .alert {
            border-radius: 12px;
            border: none;
        }
        .form-check-input:checked {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        .input-group-text {
            background: transparent;
            border: 2px solid #e0e0e0;
            border-<?php echo $isRTL ? 'left' : 'right'; ?>: none;
            color: #6c757d;
        }
        .form-floating .form-control.with-icon {
            border-<?php echo $isRTL ? 'right' : 'left'; ?>: none;
            border-top-<?php echo $isRTL ? 'right' : 'left'; ?>-radius: 0;
            border-bottom-<?php echo $isRTL ? 'right' : 'left'; ?>-radius: 0;
        }
        .input-group .input-group-text {
            border-top-<?php echo $isRTL ? 'left' : 'right'; ?>-radius: 12px;
            border-bottom-<?php echo $isRTL ? 'left' : 'right'; ?>-radius: 12px;
        }
        .footer-text {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-size: 0.85rem;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <!-- Language Switcher -->
    <div class="lang-switcher">
        <?php if ($lang === 'ar'): ?>
            <a href="?lang=fr" class="btn btn-sm"><i class="bi bi-globe"></i> Français</a>
        <?php else: ?>
            <a href="?lang=ar" class="btn btn-sm"><i class="bi bi-globe"></i> العربية</a>
        <?php endif; ?>
    </div>

    <div class="login-container">
        <div class="login-header">
            <i class="bi bi-shield-lock-fill"></i>
            <h1><?php echo trans('app_name'); ?></h1>
            <p><?php echo trans('login'); ?></p>
        </div>

        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" autocomplete="off">
                <?php csrfField(); ?>

                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                    <div class="form-floating flex-grow-1">
                        <input type="text" class="form-control with-icon" id="username" name="username" 
                               placeholder="<?php echo trans('username'); ?>" 
                               value="<?php echo htmlspecialchars($username); ?>" required autofocus>
                        <label for="username"><?php echo trans('username'); ?></label>
                    </div>
                </div>

                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <div class="form-floating flex-grow-1">
                        <input type="password" class="form-control with-icon" id="password" name="password" 
                               placeholder="<?php echo trans('password'); ?>" required>
                        <label for="password"><?php echo trans('password'); ?></label>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        <?php echo trans('remember_me'); ?>
                    </label>
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-<?php echo $isRTL ? 'left' : 'right'; ?>"></i>
                    <?php echo trans('login'); ?>
                </button>
            </form>
        </div>

        <div class="footer-text">
            <i class="bi bi-building"></i> 
            <?php echo app()->setting('commune_name', trans('app_name')); ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle language switch
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('lang')) {
            fetch('switch_lang.php?lang=' + urlParams.get('lang'))
                .then(() => window.location.href = window.location.pathname);
        }
    </script>
</body>
</html>