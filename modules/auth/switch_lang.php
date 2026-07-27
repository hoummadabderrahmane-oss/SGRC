<?php
session_start();
$lang = $_GET['lang'] ?? 'fr';
if (in_array($lang, ['fr', 'ar'])) {
    $_SESSION['lang'] = $lang;
}
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/SGRC/modules/dashboard/index.php'));
exit;