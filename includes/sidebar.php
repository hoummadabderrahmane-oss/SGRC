<?php
$currentModule = basename(dirname($_SERVER['PHP_SELF']));

$menuItems = [
    ['dashboard', 'Dashboard', 'fa-gauge-high'],
        ['citizens', 'Citizens', 'fa-users'],
            ['registers', 'Registers', 'fa-file-lines'],
                ['certificates', 'Certificates', 'fa-certificate'],
                    ['documents', 'Documents', 'fa-folder-open'],
                        ['reports', 'Reports', 'fa-chart-pie'],
                            ['import', 'Import', 'fa-file-import'],
                        ];

                        $adminItems = [
                            ['users', 'Users', 'fa-user-shield'],
                                ['settings', 'Settings', 'fa-gear'],
                                ];
                                ?>

                                <nav class="sidebar">
                                    <div class="sidebar-header">
                                            <h3><i class="fas fa-shield-alt me-2"></i>SGRC</h3>
                                                </div>
                                                    <ul class="nav flex-column">
                                                            <?php foreach ($menuItems as $item): ?>
                                                                    <li class="nav-item">
                                                                                <a class="nav-link <?php echo $currentModule === $item[0] ? 'active' : ''; ?>" href="/SGRC/modules/<?php echo $item[0]; ?>/index.php">
                                                                                                <i class="fas <?php echo $item[2]; ?>"></i>
                                                                                                                <?php echo $lang[$item[0]] ?? $item[1]; ?>
                                                                                                                            </a>
                                                                                                                                    </li>
                                                                                                                                            <?php endforeach; ?>
                                                                                                                                                    
                                                                                                                                                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                                                                                                                                                    <li class="nav-item mt-3">
                                                                                                                                                                                <small class="text-muted ms-3 text-uppercase" style="font-size: 10px; letter-spacing: 1px;">Admin</small>
                                                                                                                                                                                        </li>
                                                                                                                                                                                                <?php foreach ($adminItems as $item): ?>
                                                                                                                                                                                                        <li class="nav-item">
                                                                                                                                                                                                                    <a class="nav-link <?php echo $currentModule === $item[0] ? 'active' : ''; ?>" href="/SGRC/modules/<?php echo $item[0]; ?>/index.php">
                                                                                                                                                                                                                                    <i class="fas <?php echo $item[2]; ?>"></i>
                                                                                                                                                                                                                                                    <?php echo $lang[$item[0]] ?? $item[1]; ?>
                                                                                                                                                                                                                                                                </a>
                                                                                                                                                                                                                                                                        </li>
                                                                                                                                                                                                                                                                                <?php endforeach; ?>
                                                                                                                                                                                                                                                                                        <?php endif; ?>
                                                                                                                                                                                                                                                                                                
                                                                                                                                                                                                                                                                                                        <li class="nav-item mt-auto">
                                                                                                                                                                                                                                                                                                                    <a class="nav-link text-danger" href="/SGRC/modules/auth/logout.php">
                                                                                                                                                                                                                                                                                                                                    <i class="fas fa-right-from-bracket"></i>
                                                                                                                                                                                                                                                                                                                                                    <?php echo $lang['logout'] ?? 'Logout'; ?>
                                                                                                                                                                                                                                                                                                                                                                </a>
                                                                                                                                                                                                                                                                                                                                                                        </li>
                                                                                                                                                                                                                                                                                                                                                                            </ul>
                                                                                                                                                                                                                                                                                                                                                                            </nav>
                                                                                                                                                                                                                                                                                                                                                                            