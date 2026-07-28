<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }

    if (isset($_SESSION['user_id'])) {
        header('Location: /SGRC/modules/dashboard/index.php');
            exit;
            }

            $pageTitle = 'Login - SGRC';
            $error = '';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                require_once __DIR__ . '/../../config/database.php';
                    
                        $username = $_POST['username'] ?? '';
                            $password = $_POST['password'] ?? '';
                                
                                    $db = Database::getInstance();
                                        $user = $db->query("SELECT * FROM users WHERE username = ? AND is_active = 1", [$username])->fetch();
                                            
                                                if ($user && password_verify($password, $user['password_hash'])) {
                                                        $_SESSION['user_id'] = $user['id'];
                                                                $_SESSION['username'] = $user['username'];
                                                                        $_SESSION['user_role'] = $user['role'];
                                                                                $_SESSION['lang'] = 'fr';
                                                                                        $_SESSION['last_activity'] = time();
                                                                                                
                                                                                                        header('Location: /SGRC/modules/dashboard/index.php');
                                                                                                                exit;
                                                                                                                    } else {
                                                                                                                            $error = 'Invalid credentials';
                                                                                                                                }
                                                                                                                                }
                                                                                                                                ?>
                                                                                                                                <!DOCTYPE html>
                                                                                                                                <html lang="fr">
                                                                                                                                <head>
                                                                                                                                    <meta charset="UTF-8">
                                                                                                                                        <title><?php echo $pageTitle; ?></title>
                                                                                                                                            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                                                                                                                                                <style>
                                                                                                                                                        body { background: #f5f5f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
                                                                                                                                                                .login-form { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
                                                                                                                                                                        .login-form h2 { margin-bottom: 30px; text-align: center; color: #333; }
                                                                                                                                                                            </style>
                                                                                                                                                                            </head>
                                                                                                                                                                            <body>
                                                                                                                                                                                <div class="login-form">
                                                                                                                                                                                        <h2>SGRC</h2>
                                                                                                                                                                                                <?php if ($error): ?>
                                                                                                                                                                                                            <div class="alert alert-danger"><?php echo $error; ?></div>
                                                                                                                                                                                                                    <?php endif; ?>
                                                                                                                                                                                                                            <form method="POST">
                                                                                                                                                                                                                                        <div class="mb-3">
                                                                                                                                                                                                                                                        <label class="form-label">Username</label>
                                                                                                                                                                                                                                                                        <input type="text" name="username" class="form-control" required autofocus>
                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                <div class="mb-3">
                                                                                                                                                                                                                                                                                                                <label class="form-label">Password</label>
                                                                                                                                                                                                                                                                                                                                <input type="password" name="password" class="form-control" required>
                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                        <button type="submit" class="btn btn-primary w-100">Login</button>
                                                                                                                                                                                                                                                                                                                                                                </form>
                                                                                                                                                                                                                                                                                                                                                                        <p class="text-center mt-3">
                                                                                                                                                                                                                                                                                                                                                                                    <a href="forgot_password.php">Forgot password?</a>
                                                                                                                                                                                                                                                                                                                                                                                            </p>
                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                </body>
                                                                                                                                                                                                                                                                                                                                                                                                </html>
                                                                                                                                                                                                                                                                                                                                                                                                