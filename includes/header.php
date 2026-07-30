<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load database connection
require_once __DIR__ . '/../config/database.php';

// Load language file
$langFile = __DIR__ . '/../lang/' . ($_SESSION['lang'] ?? 'fr') . '.php';
if (file_exists($langFile)) {
    require_once $langFile;
} else {
    require_once __DIR__ . '/../lang/fr.php';
}

// Determine current language and direction
$currentLang = $_SESSION['lang'] ?? 'fr';
$isRTL = ($currentLang === 'ar');
$dir = $isRTL ? 'rtl' : 'ltr';

// Language display names
$langNames = [
    'fr' => ['name' => 'Français', 'flag' => '🇫🇷'],
    'ar' => ['name' => 'العربية', 'flag' => '🇸🇦']
];
?>
<!DOCTYPE html>
<html lang="<?php echo $currentLang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'SGRC'; ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <?php if ($isRTL): ?>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <?php endif; ?>
    <!-- Bootstrap -->
    <?php if ($isRTL): ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <?php else: ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php endif; ?>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/SGRC/assets/css/custom.css">

    <!-- Inline fallback styles for language switcher -->
    <style>
        .top-header {
            background: #ffffff;
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            margin-bottom: 24px;
        }
        .lang-switcher {
            position: relative;
            display: inline-block;
        }
        .lang-switcher-btn {
            background: linear-gradient(135deg, #4361ee, #3a56d4);
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .lang-switcher-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            min-width: 180px;
            overflow: hidden;
            z-index: 1100;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            transition: all 0.2s ease;
        }
        .lang-switcher:hover .lang-switcher-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .lang-switcher-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            text-decoration: none;
            color: #343a40;
            font-size: 14px;
            border-bottom: 1px solid #e9ecef;
            transition: background 0.15s;
        }
        .lang-switcher-item:last-child { border-bottom: none; }
        .lang-switcher-item:hover { background: #f8f9fa; color: #4361ee; }
        .lang-switcher-item.active { background: rgba(67,97,238,0.08); color: #4361ee; font-weight: 600; }
        .lang-check { margin-left: auto; font-size: 12px; color: #06d6a0; }
        [dir="rtl"] .lang-switcher-menu { right: auto; left: 0; }
        [dir="rtl"] .lang-check { margin-left: 0; margin-right: auto; }
        [dir="rtl"] body { font-family: 'Noto Sans Arabic', 'Inter', sans-serif; }
    </style>
    <?php if ($isRTL): ?>
    <style>
        body { font-family: 'Noto Sans Arabic', 'Inter', sans-serif; }
    </style>
    <?php endif; ?>
</head>
<body>
    <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="main-content">
        <!-- Top Header Bar with Language Switcher -->
        <div class="top-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 fw-bold"><?php echo $pageTitle ?? 'SGRC'; ?></h4>
                </div>

                <!-- Language Switcher Dropdown -->
                <div class="lang-switcher">
                    <button class="lang-switcher-btn" type="button">
                        <span class="lang-flag"><?php echo $langNames[$currentLang]['flag']; ?></span>
                        <span class="lang-name"><?php echo $langNames[$currentLang]['name']; ?></span>
                        <i class="fas fa-chevron-down" style="font-size:10px;margin-left:4px;"></i>
                    </button>
                    <div class="lang-switcher-menu">
                        <?php foreach ($langNames as $code => $info): ?>
                        <a href="/SGRC/modules/auth/switch_lang.php?lang=<?php echo $code; ?>" 
                           class="lang-switcher-item <?php echo ($currentLang === $code) ? 'active' : ''; ?>">
                            <span class="lang-flag"><?php echo $info['flag']; ?></span>
                            <span class="lang-name"><?php echo $info['name']; ?></span>
                            <?php if ($currentLang === $code): ?>
                            <i class="fas fa-check lang-check"></i>
                            <?php endif; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>