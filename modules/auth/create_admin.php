<?php
$pageTitle = 'Create Admin';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
                $full_name = $_POST['full_name'] ?? '';
                    
                        $db = Database::getInstance();
                            
                                $exists = $db->query("SELECT id FROM users WHERE username = ? OR email = ?", [$username, $email])->fetch();
                                    if ($exists) {
                                            $error = 'Username or email already exists';
                                                } else {
                                                        $hash = password_hash($password, PASSWORD_DEFAULT);
                                                                $db->query("INSERT INTO users (username, email, password_hash, full_name, role) VALUES (?, ?, ?, ?, 'admin')", 
                                                                            [$username, $email, $hash, $full_name]);
                                                                                    $message = 'Admin created successfully';
                                                                                        }
                                                                                        }
                                                                                        ?>

                                                                                        <div class="container-fluid">
                                                                                            <h2>Create Admin User</h2>
                                                                                                <?php if ($message): ?>
                                                                                                        <div class="alert alert-success"><?php echo $message; ?></div>
                                                                                                            <?php endif; ?>
                                                                                                                <?php if ($error): ?>
                                                                                                                        <div class="alert alert-danger"><?php echo $error; ?></div>
                                                                                                                            <?php endif; ?>
                                                                                                                                
                                                                                                                                    <form method="POST" class="col-md-6">
                                                                                                                                            <div class="mb-3">
                                                                                                                                                        <label class="form-label">Username</label>
                                                                                                                                                                    <input type="text" name="username" class="form-control" required>
                                                                                                                                                                            </div>
                                                                                                                                                                                    <div class="mb-3">
                                                                                                                                                                                                <label class="form-label">Email</label>
                                                                                                                                                                                                            <input type="email" name="email" class="form-control" required>
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                            <div class="mb-3">
                                                                                                                                                                                                                                        <label class="form-label">Full Name</label>
                                                                                                                                                                                                                                                    <input type="text" name="full_name" class="form-control" required>
                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                    <div class="mb-3">
                                                                                                                                                                                                                                                                                <label class="form-label">Password</label>
                                                                                                                                                                                                                                                                                            <input type="password" name="password" class="form-control" required minlength="6">
                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                            <button type="submit" class="btn btn-primary">Create Admin</button>
                                                                                                                                                                                                                                                                                                                </form>
                                                                                                                                                                                                                                                                                                                </div>

                                                                                                                                                                                                                                                                                                                <?php require_once __DIR__ . '/../../includes/footer.php'; ?>
                                                                                                                                                                                                                                                                                                                