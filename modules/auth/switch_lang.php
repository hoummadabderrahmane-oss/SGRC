<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_GET['lang'] ?? 'fr';

if (in_array($lang, ['fr', 'ar'])) {
    $_SESSION['lang'] = $lang;
}

// Redirect back to the referring page, or dashboard as fallback
$referer = $_SERVER['HTTP_REFERER'] ?? '/SGRC/modules/dashboard/index.php';
header('Location: ' . $referer);
exit;