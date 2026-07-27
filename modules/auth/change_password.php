<?php
$pageTitle = 'Change Password';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
                
                    if ($new !== $confirm) {
                            $error = 'Passwords do not match';
                                } elseif (strlen($new) < 6) {
                                        $error = 'Password must be at least 6 characters';
                                            } else {
                                                    $db = Database::getInstance();
                                                            $user = $db->query("SELECT password_hash FROM users WHERE id = ?", [$_SESSION['user_id']])->fetch();
                                                                    
                                                                            if (password_verify($current, $user['password_hash'])) {
                                                                                        $hash = password_hash($new, PASSWORD_DEFAULT);
                                                                                                    $db->query("UPDATE users SET password_hash = ? WHERE id = ?", [$hash, $_SESSION['user_id']]);
                                                                                                                $message = 'Password changed successfully';
                                                                                                                        } else {
                                                                                                                                    $error = 'Current password is incorrect';
                                                                                                                                            }
                                                                                                                                                }
                                                                                                                                                }
                                                                                                                                                ?>

                                                                                                                                                <div class="container-fluid">
                                                                                                                                                    <h2><?php echo $lang['change_password'] ?? 'Change Password'; ?></h2>
                                                                                                                                                        
                                                                                                                                                            <?php if ($message): ?>
                                                                                                                                                                    <div class="alert alert-success"><?php echo $message; ?></div>
                                                                                                                                                                        <?php endif; ?>
                                                                                                                                                                            <?php if ($error): ?>
                                                                                                                                                                                    <div class="alert alert-danger"><?php echo $error; ?></div>
                                                                                                                                                                                        <?php endif; ?>
                                                                                                                                                                                            
                                                                                                                                                                                                <form method="POST" class="col-md-6">
                                                                                                                                                                                                        <div class="mb-3">
                                                                                                                                                                                                                    <label class="form-label">Current Password</label>
                                                                                                                                                                                                                                <input type="password" name="current_password" class="form-control" required>
                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                <div class="mb-3">
                                                                                                                                                                                                                                                            <label class="form-label">New Password</label>
                                                                                                                                                                                                                                                                        <input type="password" name="new_password" class="form-control" required>
                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                        <div class="mb-3">
                                                                                                                                                                                                                                                                                                    <label class="form-label">Confirm New Password</label>
                                                                                                                                                                                                                                                                                                                <input type="password" name="confirm_password" class="form-control" required>
                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                <button type="submit" class="btn btn-primary">Change Password</button>
                                                                                                                                                                                                                                                                                                                                    </form>
                                                                                                                                                                                                                                                                                                                                    </div>

                                                                                                                                                                                                                                                                                                                                    <?php require_once __DIR__ . '/../../includes/footer.php'; ?>
                                                                                                                                                                                                                                                                                                                                    