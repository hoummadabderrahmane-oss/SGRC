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
                    <!-- Google Fonts -->
                        <link rel="preconnect" href="https://fonts.googleapis.com">
                            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
                                <!-- Bootstrap -->
                                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                                        <?php if (($_SESSION['lang'] ?? 'fr') === 'ar'): ?>
                                            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
                                                <?php endif; ?>
                                                    <!-- Font Awesome -->
                                                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                                                            <!-- Custom CSS -->
                                                                <link rel="stylesheet" href="/SGRC/assets/css/custom.css">
                                                                </head>
                                                                <body>
                                                                    <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
                                                                        <?php include __DIR__ . '/sidebar.php'; ?>
                                                                            <div class="main-content">
                                                                                <?php endif; ?>
                                                                                