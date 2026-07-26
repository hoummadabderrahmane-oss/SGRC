<?php
/**
 * SGRC - Main Entry Point
 * نقطة الدخول الرئيسية
 */

// Define base path
define('BASE_PATH', __DIR__);

// Load core
require_once BASE_PATH . '/app/Core/App.php';

// Redirect to dashboard if logged in, else to login
if (authCheck()) {
    header('Location: /modules/dashboard/index.php');
} else {
    header('Location: /modules/auth/login.php');
}
exit;