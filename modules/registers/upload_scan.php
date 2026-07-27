<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();
header('Content-Type: application/json');

$db = Database::getInstance();
$id = intval($_POST['register_id'] ?? 0);

if ($id && !empty($_FILES['scan']['name'])) {
    $uploadDir = __DIR__ . '/../../uploads/scans/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $path = 'uploads/scans/' . time() . '_' . basename($_FILES['scan']['name']);
    move_uploaded_file($_FILES['scan']['tmp_name'], __DIR__ . '/../../' . $path);
    
    $db->query("UPDATE registers SET scan_path = ? WHERE id = ?", [$path, $id]);
    echo json_encode(['success' => true, 'path' => $path]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}