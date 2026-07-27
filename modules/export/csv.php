<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=citizens_export_' . date('Y-m-d') . '.csv');

$db = Database::getInstance();
$citizens = $db->query("SELECT national_id, first_name, last_name, first_name_ar, last_name_ar, date_of_birth, place_of_birth, gender, address, phone, email, blood_type, marital_status, father_name, mother_name, created_at FROM citizens")->fetchAll();

$output = fopen('php://output', 'w');
fputcsv($output, ['National ID', 'First Name', 'Last Name', 'First Name (AR)', 'Last Name (AR)', 'Date of Birth', 'Place of Birth', 'Gender', 'Address', 'Phone', 'Email', 'Blood Type', 'Marital Status', 'Father Name', 'Mother Name', 'Created At']);

foreach ($citizens as $c) {
    fputcsv($output, $c);
}
fclose($output);
exit;