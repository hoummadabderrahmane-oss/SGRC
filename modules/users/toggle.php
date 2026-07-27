<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$db = Database::getInstance();
$id = intval($_GET['id'] ?? 0);

if ($id && $id != $_SESSION['user_id']) {
    $user = $db->query("SELECT is_active FROM users WHERE id = ?", [$id])->fetch();
    if ($user) {
        $newStatus = $user['is_active'] ? 0 : 1;
        $db->query("UPDATE users SET is_active = ? WHERE id = ?", [$newStatus, $id]);
    }
}

header('Location: index.php');
exit;