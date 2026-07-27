<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/session.php'; 
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] ?? 'fr'; ?>" dir="<?php echo ($_SESSION['lang'] ?? 'fr') === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'SGRC'; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/custom.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php if (($_SESSION['lang'] ?? 'fr') === 'ar'): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <?php endif; ?>
</head>
<body>
    <?php if (isLoggedIn()): ?>
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="main-content">
    <?php endif; ?>