<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

// Simple PDF generation using HTML-to-PDF approach
$db = Database::getInstance();
$citizens = $db->query("SELECT * FROM citizens LIMIT 100")->fetchAll();

header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Citizens Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; font-size: 12px; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Citizens Report</h1>
    <p>Generated on: <?php echo date('Y-m-d H:i:s'); ?></p>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>National ID</th>
                <th>Name</th>
                <th>DOB</th>
                <th>Gender</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($citizens as $c): ?>
            <tr>
                <td><?php echo $c['id']; ?></td>
                <td><?php echo $c['national_id']; ?></td>
                <td><?php echo $c['first_name'] . ' ' . $c['last_name']; ?></td>
                <td><?php echo $c['date_of_birth']; ?></td>
                <td><?php echo $c['gender']; ?></td>
                <td><?php echo $c['phone']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <script>window.print();</script>
</body>
</html>