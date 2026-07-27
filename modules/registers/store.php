<?php
// Process register creation via AJAX
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();
header('Content-Type: application/json');

$db = Database::getInstance();
$data = json_decode(file_get_contents('php://input'), true);

$register_number = $data['register_number'] ?? '';
$register_type = $data['register_type'] ?? '';
$citizen_id = $data['citizen_id'] ?? null;
$event_date = $data['event_date'] ?? '';

try {
    $db->query("INSERT INTO registers (register_number, register_type, citizen_id, event_date, created_by) VALUES (?, ?, ?, ?, ?)",
        [$register_number, $register_type, $citizen_id, $event_date, $_SESSION['user_id']]);
    echo json_encode(['success' => true, 'message' => 'Register created']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}