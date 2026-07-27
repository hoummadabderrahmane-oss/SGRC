<?php
// SGRC Entry Point
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base path for the project
define('BASE_PATH', __DIR__);
define('BASE_URL', '/SGRC');

// Load config and helpers
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/Helpers/functions.php';
require_once BASE_PATH . '/includes/auth.php';

// Route handling
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/SGRC', '', $uri);
$uri = trim($uri, '/');

// Default route
if (empty($uri) || $uri === 'index.php') {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/modules/auth/login.php');
        exit;
    }
    header('Location: ' . BASE_URL . '/modules/dashboard/index.php');
    exit;
}

// Check if direct file access
$filePath = BASE_PATH . '/' . $uri;
if (file_exists($filePath) && is_file($filePath)) {
    return false;
}

// Handle module routing
$parts = explode('/', $uri);
$module = $parts[0] ?? 'dashboard';
$action = $parts[1] ?? 'index';

// Auth check for protected routes
if (!isLoggedIn() && !in_array($module, ['modules/auth', 'modules/auth/login.php'])) {
    header('Location: ' . BASE_URL . '/modules/auth/login.php');
    exit;
}

// Direct module access fallback
$moduleFile = BASE_PATH . '/' . $uri;
if (file_exists($moduleFile)) {
    require_once $moduleFile;
    exit;
}

// 404
http_response_code(404);
echo '<h1>404 - Page Not Found</h1>';
echo '<p>The page you requested does not exist.</p>';
echo '<p><a href="' . BASE_URL . '/modules/dashboard/index.php">Go to Dashboard</a></p>';