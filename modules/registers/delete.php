<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$id = intval($_GET['id'] ?? 0);

if ($id) {
    $db->query("DELETE FROM certificates WHERE register_id = ?", [$id]);
    $db->query("DELETE FROM registers WHERE id = ?", [$id]);
}

header('Location: index.php');
exit;