<?php
// AJAX update endpoint
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();
header('Content-Type: application/json');

$db = Database::getInstance();
$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id'] ?? 0);

try {
    $db->query("UPDATE registers SET register_type = ?, citizen_id = ?, event_date = ?, status = ? WHERE id = ?",
        [$data['register_type'], $data['citizen_id'], $data['event_date'], $data['status'], $id]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}