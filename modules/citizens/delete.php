<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$id = intval($_GET['id'] ?? 0);

if ($id) {
    $db->query("DELETE FROM registers WHERE citizen_id = ?", [$id]);
    $db->query("DELETE FROM documents WHERE citizen_id = ?", [$id]);
    $db->query("DELETE FROM citizens WHERE id = ?", [$id]);
}

header('Location: index.php');
exit;