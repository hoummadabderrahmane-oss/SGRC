<?php
/**
 * SGRC - Session Management
 * إدارة الجلسات
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require_once BASE_PATH . '/app/Core/App.php';

class SessionManager {
    private static ?SessionManager $instance = null;
    private int $timeout;

    private function __construct() {
        // Session configuration
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.gc_maxlifetime', 3600);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->timeout = (int)(app()->setting('session_timeout', 30)) * 60;
        $this->checkTimeout();
        $this->regenerateIfNeeded();
    }

    public static function getInstance(): SessionManager {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Check session timeout
     */
    private function checkTimeout(): void {
        if (isset($_SESSION['last_activity'])) {
            $inactive = time() - $_SESSION['last_activity'];
            if ($inactive > $this->timeout) {
                $this->destroy();
                $_SESSION['flash'] = [
                    'type' => 'warning',
                    'message' => trans('session_expired')
                ];
                header('Location: /modules/auth/login.php');
                exit;
            }
        }
        $_SESSION['last_activity'] = time();
    }

    /**
     * Regenerate session ID periodically
     */
    private function regenerateIfNeeded(): void {
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }

    /**
     * Set user session
     */
    public function setUser(array $user): void {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_full_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_avatar'] = $user['avatar'] ?? null;
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['created'] = time();
    }

    /**
     * Get current user ID
     */
    public function getUserId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current user role
     */
    public function getUserRole(): ?string {
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn(): bool {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Destroy session
     */
    public function destroy(): void {
        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
        }
        session_destroy();
    }

    /**
     * Get session data
     */
    public function get(string $key, mixed $default = null): mixed {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Set session data
     */
    public function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    /**
     * Remove session data
     */
    public function remove(string $key): void {
        unset($_SESSION[$key]);
    }
}

// Global helper
function session(): SessionManager {
    return SessionManager::getInstance();
}