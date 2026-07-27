<?php
$pageTitle = 'Forgot Password';
$error = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../config/database.php';
    $email = $_POST['email'] ?? '';
    $db = Database::getInstance();
    $user = $db->query("SELECT id FROM users WHERE email = ?", [$email])->fetch();
    
    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $db->query("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?", [$token, $expires, $user['id']]);
        $message = 'Password reset link sent to your email';
    } else {
        $error = 'Email not found';
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
        .form-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
    </style>
</head>
<body>
    <div class="form-box">
        <h2 class="text-center mb-4">Forgot Password</h2>
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
        </form>
        <p class="text-center mt-3"><a href="login.php">Back to login</a></p>
    </div>
</body>
</html>