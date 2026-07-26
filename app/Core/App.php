<?php
/**
 * SGRC - Application Core
 * Système de Gestion des Registres de la Commune
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}

require_once BASE_PATH . '/config/database.php';

class App {
    private static ?App $instance = null;
    private PDO $db;
    private array $settings = [];
    private string $language = 'ar';
    private array $translations = [];

    private function __construct() {
        session_start();
        $this->db = Database::getInstance();
        $this->loadSettings();
        $this->setLanguage();
        $this->loadTranslations();
    }

    public static function getInstance(): App {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Load system settings from database
     */
    private function loadSettings(): void {
        try {
            $stmt = $this->db->query("SELECT setting_key, setting_value FROM settings");
            while ($row = $stmt->fetch()) {
                $this->settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (PDOException $e) {
            error_log("Settings load error: " . $e->getMessage());
        }
    }

    /**
     * Get setting value
     */
    public function setting(string $key, mixed $default = null): mixed {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Set application language
     */
    private function setLanguage(): void {
        // Check session first
        if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], ['ar', 'fr'])) {
            $this->language = $_SESSION['lang'];
        } else {
            $this->language = $this->setting('default_language', 'ar');
            $_SESSION['lang'] = $this->language;
        }
    }

    /**
     * Get current language
     */
    public function getLang(): string {
        return $this->language;
    }

    /**
     * Switch language
     */
    public function switchLang(string $lang): void {
        if (in_array($lang, ['ar', 'fr'])) {
            $_SESSION['lang'] = $lang;
            $this->language = $lang;
            $this->loadTranslations();
        }
    }

    /**
     * Check if current language is Arabic
     */
    public function isRTL(): bool {
        return $this->language === 'ar';
    }

    /**
     * Get text direction
     */
    public function getDir(): string {
        return $this->isRTL() ? 'rtl' : 'ltr';
    }

    /**
     * Load translations
     */
    private function loadTranslations(): void {
        $langFile = BASE_PATH . "/lang/{$this->language}.php";
        if (file_exists($langFile)) {
            $this->translations = require $langFile;
        }
    }

    /**
     * Translate text
     */
    public function trans(string $key, array $params = []): string {
        $text = $this->translations[$key] ?? $key;
        foreach ($params as $k => $v) {
            $text = str_replace("{{$k}}", (string)$v, $text);
        }
        return $text;
    }

    /**
     * Get database instance
     */
    public function db(): PDO {
        return $this->db;
    }

    /**
     * Generate CSRF token
     */
    public function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validate CSRF token
     */
    public function validateCsrf(string $token): bool {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Sanitize input
     */
    public function sanitize(string $input): string {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Flash message
     */
    public function flash(string $type, string $message): void {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    /**
     * Get and clear flash message
     */
    public function getFlash(): ?array {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    /**
     * Log activity
     */
    public function logActivity(string $action, string $description = '', string $module = ''): void {
        $userId = $_SESSION['user_id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        try {
            $stmt = $this->db->prepare(
                "INSERT INTO activity_logs (user_id, action, description, module, ip_address, user_agent) 
                 VALUES (:user_id, :action, :description, :module, :ip, :ua)"
            );
            $stmt->execute([
                ':user_id' => $userId,
                ':action' => $action,
                ':description' => $description,
                ':module' => $module,
                ':ip' => $ip,
                ':ua' => $userAgent
            ]);
        } catch (PDOException $e) {
            error_log("Activity log error: " . $e->getMessage());
        }
    }

    /**
     * Generate unique number
     */
    public function generateNumber(string $prefix = '', int $length = 6): string {
        $timestamp = date('YmdHis');
        $random = strtoupper(substr(uniqid(), -$length));
        return $prefix . $timestamp . $random;
    }

    /**
     * Format date based on language
     */
    public function formatDate(?string $date, string $format = null): string {
        if (!$date) return '-';
        $dt = new DateTime($date);
        if ($this->language === 'ar') {
            return $dt->format($format ?? 'Y/m/d');
        }
        return $dt->format($format ?? 'd/m/Y');
    }

    /**
     * Format number
     */
    public function formatNumber(int|float $number, int $decimals = 0): string {
        if ($this->language === 'ar') {
            return number_format($number, $decimals);
        }
        return number_format($number, $decimals, ',', ' ');
    }

    /**
     * Upload file
     */
    public function uploadFile(array $file, string $directory, array $allowedTypes = []): array {
        $result = ['success' => false, 'path' => '', 'error' => ''];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $result['error'] = $this->trans('upload_error');
            return $result;
        }

        $fileName = basename($file['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!empty($allowedTypes) && !in_array($fileExt, $allowedTypes)) {
            $result['error'] = $this->trans('invalid_file_type');
            return $result;
        }

        $newName = uniqid() . '_' . time() . '.' . $fileExt;
        $uploadPath = BASE_PATH . "/uploads/{$directory}/" . $newName;

        if (!is_dir(dirname($uploadPath))) {
            mkdir(dirname($uploadPath), 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $result['success'] = true;
            $result['path'] = "uploads/{$directory}/" . $newName;
        } else {
            $result['error'] = $this->trans('upload_failed');
        }

        return $result;
    }

    /**
     * Check if user is authenticated
     */
    public function isAuth(): bool {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Check user role
     */
    public function hasRole(string|array $roles): bool {
        if (!$this->isAuth()) return false;
        $userRole = $_SESSION['user_role'] ?? '';
        if (is_array($roles)) {
            return in_array($userRole, $roles);
        }
        return $userRole === $roles;
    }

    /**
     * Require authentication
     */
    public function requireAuth(): void {
        if (!$this->isAuth()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: /modules/auth/login.php');
            exit;
        }
    }

    /**
     * Require specific role
     */
    public function requireRole(string|array $roles): void {
        $this->requireAuth();
        if (!$this->hasRole($roles)) {
            http_response_code(403);
            die($this->trans('access_denied'));
        }
    }

    /**
     * Redirect with flash
     */
    public function redirect(string $url, string $type = '', string $message = ''): void {
        if ($type && $message) {
            $this->flash($type, $message);
        }
        header("Location: {$url}");
        exit;
    }
}

// Global helper functions
function app(): App {
    return App::getInstance();
}

function trans(string $key, array $params = []): string {
    return app()->trans($key, $params);
}

function __t(string $key): void {
    echo trans($key);
}

function csrf(): string {
    return app()->csrfToken();
}

function csrfField(): void {
    echo '<input type="hidden" name="csrf_token" value="' . csrf() . '">';
}

function sanitize(string $input): string {
    return app()->sanitize($input);
}

function formatDate(?string $date): string {
    return app()->formatDate($date);
}

function isRTL(): bool {
    return app()->isRTL();
}

function getDir(): string {
    return app()->getDir();
}

function getLang(): string {
    return app()->getLang();
}