<?php
/**
 * SGRC - Logout
 * تسجيل الخروج
 */

require_once __DIR__ . '/../../app/Core/App.php';
require_once __DIR__ . '/../../includes/session.php';

// Log the logout if user was logged in
if (session()->isLoggedIn()) {
    $user = authUser();
    app()->logActivity('logout', "User {$user['username']} logged out", 'auth');
}

// Clear remember me cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}

// Destroy session
session()->destroy();

// Set flash message
$_SESSION['flash'] = ['type' => 'success', 'message' => trans('logout_success')];

// Redirect to login
header('Location: /modules/auth/login.php');
exit;