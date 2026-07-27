<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

// Simple HTML table as Excel-compatible output
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename=citizens_export_' . date('Y-m-d') . '.xls');

$db = Database::getInstance();
$citizens = $db->query("SELECT * FROM citizens")->fetchAll();
?>
<table border="1">
    <tr>
        <th>National ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Date of Birth</th>
        <th>Gender</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Created At</th>
    </tr>
    <?php foreach ($citizens as $c): ?>
    <tr>
        <td><?php echo $c['national_id']; ?></td>
        <td><?php echo $c['first_name']; ?></td>
        <td><?php echo $c['last_name']; ?></td>
        <td><?php echo $c['date_of_birth']; ?></td>
        <td><?php echo $c['gender']; ?></td>
        <td><?php echo $c['phone']; ?></td>
        <td><?php echo $c['email']; ?></td>
        <td><?php echo $c['created_at']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>