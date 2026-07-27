<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id(true);
}

if (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
}

if (time() - $_SESSION['last_activity'] > 1800) {
    session_unset();
    session_destroy();
    header('Location: /SGRC/modules/auth/login.php');
    exit;
}

$_SESSION['last_activity'] = time();

require_once __DIR__ . '/../config/database.php';

$langFile = __DIR__ . '/../lang/' . ($_SESSION['lang'] ?? 'fr') . '.php';
if (file_exists($langFile)) {
    require_once $langFile;
} else {
    $lang = [];
}