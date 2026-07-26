<?php
/**
 * SGRC - Main Header Template
 * القالب الرئيسي للهيدر
 * 
 * Usage: require_once __DIR__ . '/../../includes/header.php';
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 2));
}

require_once BASE_PATH . '/app/Core/App.php';
require_once BASE_PATH . '/includes/session.php';
require_once BASE_PATH . '/includes/auth.php';

// Ensure user is authenticated
requireAuth();

$user = authUser();
$lang = getLang();
$dir = getDir();
$isRTL = isRTL();
$theme = $_SESSION['theme'] ?? 'light';

// Get notifications count
try {
    $notifCount = app()->db()->query(
        "SELECT COUNT(*) as count FROM notifications WHERE user_id = " . ($user['id'] ?? 0) . " AND is_read = 0"
    )->fetch()['count'] ?? 0;
} catch (PDOException $e) {
    $notifCount = 0;
}

// Page title
$pageTitle = $pageTitle ?? trans('dashboard');
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $dir; ?>" data-theme="<?php echo $theme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo csrf(); ?>">
    <title><?php echo $pageTitle; ?> - <?php echo trans('app_name'); ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.<?php echo $isRTL ? 'rtl' : 'min'; ?>.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/custom.css">

    <style>
        /* Inline critical styles for faster rendering */
        body { opacity: 0; transition: opacity 0.2s; }
        body.loaded { opacity: 1; }
    </style>
</head>
<body class="<?php echo $theme; ?>">

    <!-- Flash Messages Container -->
    <div class="flash-container">
        <?php 
        $flash = app()->getFlash();
        if ($flash): 
        ?>
            <div class="flash-message <?php echo $flash['type']; ?>">
                <i class="bi bi-<?php 
                    echo match($flash['type']) {
                        'success' => 'check-circle-fill',
                        'error' => 'x-circle-fill',
                        'warning' => 'exclamation-triangle-fill',
                        default => 'info-circle-fill'
                    };
                ?>"></i>
                <span><?php echo $flash['message']; ?></span>
            </div>
        <?php endif; ?>
    </div>

    <div class="wrapper">

        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-brand">
                <img src="<?php echo app()->setting('logo_path', '/assets/img/logo.png'); ?>" 
                     alt="Logo" onerror="this.style.display='none'">
                <h4><?php echo trans('app_name_short'); ?></h4>
            </div>

            <ul class="nav flex-column mt-3">
                <li class="nav-item">
                    <a class="nav-link <?php echo $activeModule === 'dashboard' ? 'active' : ''; ?>" 
                       href="/modules/dashboard/index.php">
                        <i class="bi bi-speedometer2"></i>
                        <span><?php echo trans('dashboard'); ?></span>
                    </a>
                </li>

                <div class="sidebar-divider"></div>

                <li class="nav-item">
                    <a class="nav-link <?php echo $activeModule === 'citizens' ? 'active' : ''; ?>" 
                       href="/modules/citizens/index.php">
                        <i class="bi bi-people-fill"></i>
                        <span><?php echo trans('citizens'); ?></span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo $activeModule === 'registers' ? 'active' : ''; ?>" 
                       href="/modules/registers/index.php">
                        <i class="bi bi-journal-text"></i>
                        <span><?php echo trans('registers'); ?></span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo $activeModule === 'documents' ? 'active' : ''; ?>" 
                       href="/modules/documents/index.php">
                        <i class="bi bi-file-earmark-text-fill"></i>
                        <span><?php echo trans('documents'); ?></span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo $activeModule === 'certificates' ? 'active' : ''; ?>" 
                       href="/modules/certificates/index.php">
                        <i class="bi bi-award-fill"></i>
                        <span><?php echo trans('certificates'); ?></span>
                    </a>
                </li>

                <div class="sidebar-divider"></div>

                <li class="nav-item">
                    <a class="nav-link <?php echo $activeModule === 'import' ? 'active' : ''; ?>" 
                       href="/modules/import/index.php">
                        <i class="bi bi-cloud-upload-fill"></i>
                        <span><?php echo trans('import'); ?></span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo $activeModule === 'export' ? 'active' : ''; ?>" 
                       href="/modules/export/index.php">
                        <i class="bi bi-cloud-download-fill"></i>
                        <span><?php echo trans('export'); ?></span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo $activeModule === 'ocr' ? 'active' : ''; ?>" 
                       href="/modules/ocr/index.php">
                        <i class="bi bi-eye-fill"></i>
                        <span><?php echo trans('ocr'); ?></span>
                    </a>
                </li>

                <div class="sidebar-divider"></div>

                <li class="nav-item">
                    <a class="nav-link <?php echo $activeModule === 'reports' ? 'active' : ''; ?>" 
                       href="/modules/reports/index.php">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span><?php echo trans('reports'); ?></span>
                    </a>
                </li>

                <?php if (can('users.view')): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $activeModule === 'users' ? 'active' : ''; ?>" 
                       href="/modules/users/index.php">
                        <i class="bi bi-person-gear"></i>
                        <span><?php echo trans('users'); ?></span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (can('backup.view')): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $activeModule === 'backup' ? 'active' : ''; ?>" 
                       href="/modules/backup/index.php">
                        <i class="bi bi-hdd-fill"></i>
                        <span><?php echo trans('backup'); ?></span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (can('settings.view')): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $activeModule === 'settings' ? 'active' : ''; ?>" 
                       href="/modules/settings/index.php">
                        <i class="bi bi-gear-fill"></i>
                        <span><?php echo trans('settings'); ?></span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">

            <!-- Top Header -->
            <header class="top-header">
                <div class="d-flex align-items-center gap-3">
                    <button id="sidebarToggle" class="toggle-sidebar d-none d-md-block">
                        <i class="bi bi-list"></i>
                    </button>
                    <button id="mobileToggle" class="toggle-sidebar d-md-none">
                        <i class="bi bi-list"></i>
                    </button>
                    <h5 class="mb-0 d-none d-lg-block"><?php echo $pageTitle; ?></h5>
                </div>

                <div class="header-actions">
                    <!-- Language Switcher -->
                    <div class="dropdown">
                        <button class="header-btn" data-bs-toggle="dropdown">
                            <i class="bi bi-globe"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item <?php echo $lang === 'ar' ? 'active' : ''; ?> lang-switch" 
                                   href="#" data-lang="ar">
                                    🇩🇿 العربية
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $lang === 'fr' ? 'active' : ''; ?> lang-switch" 
                                   href="#" data-lang="fr">
                                    🇫🇷 Français
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Theme Toggle -->
                    <button id="themeToggle" class="header-btn">
                        <i class="bi bi-moon-fill"></i>
                    </button>

                    <!-- Notifications -->
                    <div class="dropdown">
                        <button class="header-btn" data-bs-toggle="dropdown">
                            <i class="bi bi-bell-fill"></i>
                            <?php if ($notifCount > 0): ?>
                                <span class="badge-notification"><?php echo $notifCount; ?></span>
                            <?php endif; ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                            <h6 class="dropdown-header"><?php echo trans('notifications'); ?></h6>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center text-muted" href="#">
                                <?php echo trans('no_data'); ?>
                            </a>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="dropdown user-dropdown">
                        <button class="dropdown-toggle" data-bs-toggle="dropdown">
                            <?php if (!empty($user['avatar'])): ?>
                                <img src="<?php echo $user['avatar']; ?>" alt="" class="user-avatar">
                            <?php else: ?>
                                <div class="user-avatar-placeholder">
                                    <?php echo mb_substr($user['full_name'] ?? 'U', 0, 1); ?>
                                </div>
                            <?php endif; ?>
                            <span class="d-none d-md-inline"><?php echo $user['full_name'] ?? ''; ?></span>
                            <i class="bi bi-chevron-down small"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="dropdown-header">
                                <?php echo trans('role'); ?>: 
                                <span class="badge bg-primary">
                                    <?php echo trans('role_' . ($user['role'] ?? 'viewer')); ?>
                                </span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="/modules/users/profile.php">
                                    <i class="bi bi-person"></i> <?php echo trans('profile'); ?>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/modules/auth/change_password.php">
                                    <i class="bi bi-shield-lock"></i> <?php echo trans('change_password'); ?>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="/modules/auth/logout.php">
                                    <i class="bi bi-box-arrow-right"></i> <?php echo trans('logout'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">