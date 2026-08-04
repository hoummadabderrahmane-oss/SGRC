<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$currentModule = basename(dirname($_SERVER['PHP_SELF']));
?>

<style>
.sidebar {
    width: 260px;
    min-height: 100vh;
    background: #1a1f37;
    color: #fff;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1000;
    padding: 0;
}
.sidebar-header {
    padding: 20px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.sidebar-brand {
    color: #fff;
    text-decoration: none;
    font-size: 22px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}
.sidebar-brand i {
    color: #6366f1;
}
.sidebar-menu {
    list-style: none;
    padding: 15px 0;
    margin: 0;
}
.sidebar-menu li {
    margin: 2px 0;
}
.sidebar-menu li a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    color: #94a3b8;
    text-decoration: none;
    transition: all 0.2s;
    font-size: 14px;
}
.sidebar-menu li a:hover,
.sidebar-menu li.active a {
    color: #fff;
    background: rgba(99,102,241,0.15);
    border-right: 3px solid #6366f1;
}
.sidebar-menu li a i {
    width: 20px;
    text-align: center;
}
.sidebar-menu li a.text-danger {
    color: #ef4444;
}
.sidebar-menu li a.text-danger:hover {
    color: #f87171;
    background: rgba(239,68,68,0.1);
    border-right-color: #ef4444;
}
.sidebar-divider {
    height: 1px;
    background: rgba(255,255,255,0.08);
    margin: 10px 15px;
}
</style>

<nav class="sidebar">
    <div class="sidebar-header">
        <a href="/SGRC/modules/dashboard/index.php" class="sidebar-brand">
            <i class="fas fa-shield-alt"></i>
            <span>SGRC</span>
        </a>
    </div>
    
    <ul class="sidebar-menu">
        <li class="<?php echo $currentModule === 'dashboard' ? 'active' : ''; ?>">
            <a href="/SGRC/modules/dashboard/index.php">
                <i class="fas fa-tachometer-alt"></i>
                <span>Tableau de bord</span>
            </a>
        </li>
        
        <li class="<?php echo $currentModule === 'citizens' ? 'active' : ''; ?>">
            <a href="/SGRC/modules/citizens/index.php">
                <i class="fas fa-users"></i>
                <span>Citoyens</span>
            </a>
        </li>
        
        <li class="<?php echo $currentModule === 'import' ? 'active' : ''; ?>">
            <a href="/SGRC/modules/import/index.php">
                <i class="fas fa-file-import"></i>
                <span>Importation</span>
            </a>
        </li>
        
        <li class="sidebar-divider"></li>
        
        <li class="<?php echo $currentModule === 'users' ? 'active' : ''; ?>">
            <a href="/SGRC/modules/users/index.php">
                <i class="fas fa-user-cog"></i>
                <span>Utilisateurs</span>
            </a>
        </li>
        
        <li class="<?php echo $currentModule === 'settings' ? 'active' : ''; ?>">
            <a href="/SGRC/modules/settings/index.php">
                <i class="fas fa-cog"></i>
                <span>Paramètres</span>
            </a>
        </li>
        
        <li class="sidebar-divider"></li>
        
        <li>
            <a href="/SGRC/logout.php" class="text-danger">
                <i class="fas fa-sign-out-alt"></i>
                <span>Déconnexion</span>
            </a>
        </li>
    </ul>
</nav>