<?php 
// session.php handles session_start() and lang loading
require_once __DIR__ . '/session.php'; 
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] ?? 'fr'; ?>" dir="<?php echo ($_SESSION['lang'] ?? 'fr') === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $pageTitle ?? 'SGRC'; ?></title>
                <link rel="stylesheet" href="/SGRC/assets/css/custom.css">
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                        <?php if (($_SESSION['lang'] ?? 'fr') === 'ar'): ?>
                            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
                                <?php endif; ?>
                                </head>
                                <body>
                                    <?php 
                                        // Simple inline auth check - no external file needed
                                            if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): 
                                                ?>
                                                    <?php include __DIR__ . '/sidebar.php'; ?>
                                                        <div class="main-content">
                                                            <?php endif; ?>
                                                            