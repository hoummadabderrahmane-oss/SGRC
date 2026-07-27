<?php
$currentModule = basename(dirname($_SERVER['PHP_SELF']));
$baseUrl = BASE_URL;
?>
<nav class="sidebar">
    <div class="sidebar-header">
        <h3>SGRC</h3>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo $currentModule === 'dashboard' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>/modules/dashboard/index.php">
                <?php echo $lang['dashboard'] ?? 'Dashboard'; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $currentModule === 'citizens' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>/modules/citizens/index.php">
                <?php echo $lang['citizens'] ?? 'Citizens'; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $currentModule === 'registers' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>/modules/registers/index.php">
                <?php echo $lang['registers'] ?? 'Registers'; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $currentModule === 'certificates' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>/modules/certificates/index.php">
                <?php echo $lang['certificates'] ?? 'Certificates'; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $currentModule === 'documents' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>/modules/documents/index.php">
                <?php echo $lang['documents'] ?? 'Documents'; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $currentModule === 'reports' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>/modules/reports/index.php">
                <?php echo $lang['reports'] ?? 'Reports'; ?>
            </a>
        </li>
        <?php if (isAdmin()): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo $currentModule === 'users' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>/modules/users/index.php">
                <?php echo $lang['users'] ?? 'Users'; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $currentModule === 'settings' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>/modules/settings/index.php">
                <?php echo $lang['settings'] ?? 'Settings'; ?>
            </a>
        </li>
        <?php endif; ?>
        <li class="nav-item mt-4">
            <a class="nav-link text-danger" href="<?php echo $baseUrl; ?>/modules/auth/logout.php">
                <?php echo $lang['logout'] ?? 'Logout'; ?>
            </a>
        </li>
    </ul>
</nav>