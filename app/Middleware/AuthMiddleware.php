<?php
namespace App\Middleware;

class AuthMiddleware {
    public static function handle() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /modules/auth/login.php');
            exit;
        }
        
        if (time() - $_SESSION['last_activity'] > 1800) {
            session_unset();
            session_destroy();
            header('Location: /modules/auth/login.php');
            exit;
        }
        
        $_SESSION['last_activity'] = time();
    }
}