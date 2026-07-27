<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

header('Content-Type: application/json');
echo json_encode(['authenticated' => true, 'user' => getCurrentUser()]);
