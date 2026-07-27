<?php
// SGRC Entry Point - Simple redirect
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/Helpers/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (!isLoggedIn()) {
    header('Location: /SGRC/modules/auth/login.php');
    exit;
}

header('Location: /SGRC/modules/dashboard/index.php');
exit;