<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$id = intval($_GET['id'] ?? 0);

if ($id) {
    $db->query("UPDATE registers SET status = 'archived' WHERE id = ?", [$id]);
}

header('Location: index.php');
exit;