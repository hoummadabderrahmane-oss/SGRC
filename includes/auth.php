<?php
/**
 * SGRC - Authentication Middleware
 * وسيلة التحقق من المصادقة
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require_once BASE_PATH . '/includes/session.php';

class AuthMiddleware {

    /**
     * Require authentication
     */
    public static function requireAuth(): void {
        if (!session()->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            app()->flash('warning', trans('session_expired'));
            header('Location: /modules/auth/login.php');
            exit;
        }
        // Update last activity
        session()->set('last_activity', time());
    }

    /**
     * Require specific role(s)
     */
    public static function requireRole(string|array $roles): void {
        self::requireAuth();

        $userRole = session()->getUserRole();
        $allowedRoles = is_array($roles) ? $roles : [$roles];

        if (!in_array($userRole, $allowedRoles)) {
            http_response_code(403);
            app()->flash('error', trans('access_denied'));
            header('Location: /modules/dashboard/index.php');
            exit;
        }
    }

    /**
     * Require admin or higher
     */
    public static function requireAdmin(): void {
        self::requireRole(['super_admin', 'admin']);
    }

    /**
     * Require super admin
     */
    public static function requireSuperAdmin(): void {
        self::requireRole('super_admin');
    }

    /**
     * Check if user has permission
     */
    public static function can(string $permission): bool {
        if (!session()->isLoggedIn()) return false;

        $role = session()->getUserRole();
        $permissions = self::getRolePermissions($role);

        return in_array($permission, $permissions) || in_array('*', $permissions);
    }

    /**
     * Get permissions for role
     */
    private static function getRolePermissions(string $role): array {
        $permissions = [
            'super_admin' => ['*'],
            'admin' => [
                'citizens.view', 'citizens.create', 'citizens.edit', 'citizens.delete',
                'registers.view', 'registers.create', 'registers.edit', 'registers.delete',
                'documents.view', 'documents.create', 'documents.delete',
                'certificates.view', 'certificates.create',
                'reports.view',
                'users.view', 'users.create', 'users.edit',
                'backup.view', 'backup.create',
                'settings.view', 'settings.edit',
                'import', 'export'
            ],
            'operator' => [
                'citizens.view', 'citizens.create', 'citizens.edit',
                'registers.view', 'registers.create', 'registers.edit',
                'documents.view', 'documents.create',
                'certificates.view', 'certificates.create',
                'reports.view'
            ],
            'viewer' => [
                'citizens.view',
                'registers.view',
                'documents.view',
                'certificates.view',
                'reports.view'
            ]
        ];

        return $permissions[$role] ?? [];
    }

    /**
     * Get current user data
     */
    public static function user(): ?array {
        if (!session()->isLoggedIn()) return null;

        return [
            'id' => session()->get('user_id'),
            'username' => session()->get('user_username'),
            'full_name' => session()->get('user_full_name'),
            'role' => session()->get('user_role'),
            'avatar' => session()->get('user_avatar')
        ];
    }

    /**
     * Check if user is authenticated
     */
    public static function check(): bool {
        return session()->isLoggedIn();
    }

    /**
     * Guest only (redirect if logged in)
     */
    public static function guestOnly(): void {
        if (session()->isLoggedIn()) {
            header('Location: /modules/dashboard/index.php');
            exit;
        }
    }
}

// Quick helper functions
function auth(): AuthMiddleware {
    return new AuthMiddleware();
}

function authCheck(): bool {
    return AuthMiddleware::check();
}

function authUser(): ?array {
    return AuthMiddleware::user();
}

function can(string $permission): bool {
    return AuthMiddleware::can($permission);
}

function requireAuth(): void {
    AuthMiddleware::requireAuth();
}

function requireRole(string|array $roles): void {
    AuthMiddleware::requireRole($roles);
}

function requireAdmin(): void {
    AuthMiddleware::requireAdmin();
}