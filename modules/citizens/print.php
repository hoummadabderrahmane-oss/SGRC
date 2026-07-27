<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();

$db = Database::getInstance();
$id = intval($_GET['id'] ?? 0);
$citizen = $db->query("SELECT * FROM citizens WHERE id = ?", [$id])->fetch();

if (!$citizen) {
    die('Citizen not found');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Citizen Card - <?php echo htmlspecialchars($citizen['national_id']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .card { border: 2px solid #333; padding: 30px; max-width: 600px; margin: 0 auto; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; color: #666; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
        .info-row:last-child { border-bottom: none; }
        .label { font-weight: bold; color: #333; }
        .photo { float: right; width: 120px; height: 150px; border: 1px solid #ccc; margin-left: 20px; }
        .photo img { width: 100%; height: 100%; object-fit: cover; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #999; }
        @media print { body { margin: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()">Print</button>
    </div>
    
    <div class="card">
        <div class="header">
            <h1>REPUBLIC CIVIL REGISTRY</h1>
            <p>Citizen Information Card</p>
        </div>
        
        <?php if ($citizen['photo_path']): ?>
        <div class="photo">
            <img src="/<?php echo $citizen['photo_path']; ?>" alt="Photo">
        </div>
        <?php endif; ?>
        
        <div class="info-row">
            <span class="label">National ID:</span>
            <span><?php echo htmlspecialchars($citizen['national_id']); ?></span>
        </div>
        <div class="info-row">
            <span class="label">Full Name:</span>
            <span><?php echo htmlspecialchars($citizen['first_name'] . ' ' . $citizen['last_name']); ?></span>
        </div>
        <div class="info-row">
            <span class="label">Name (Arabic):</span>
            <span dir="rtl"><?php echo htmlspecialchars($citizen['first_name_ar'] . ' ' . $citizen['last_name_ar']); ?></span>
        </div>
        <div class="info-row">
            <span class="label">Date of Birth:</span>
            <span><?php echo $citizen['date_of_birth']; ?></span>
        </div>
        <div class="info-row">
            <span class="label">Place of Birth:</span>
            <span><?php echo htmlspecialchars($citizen['place_of_birth'] ?? '-'); ?></span>
        </div>
        <div class="info-row">
            <span class="label">Gender:</span>
            <span><?php echo ucfirst($citizen['gender']); ?></span>
        </div>
        <div class="info-row">
            <span class="label">Blood Type:</span>
            <span><?php echo htmlspecialchars($citizen['blood_type'] ?? '-'); ?></span>
        </div>
        <div class="info-row">
            <span class="label">Address:</span>
            <span><?php echo htmlspecialchars($citizen['address'] ?? '-'); ?></span>
        </div>
        
        <div class="footer">
            <p>Issued on: <?php echo date('Y-m-d'); ?></p>
            <p>SGRC System - Official Document</p>
        </div>
    </div>
</body>
</html>