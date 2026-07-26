<?php
require_once __DIR__ . '/../../app/Core/App.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();
if (!can('citizens.delete')) { app()->redirect('index.php', 'error', trans('access_denied')); }
$id = (int)($_GET['id'] ?? 0); if (!$id) { app()->redirect('index.php', 'error', 'Invalid ID'); }
$db = app()->db();
$stmt = $db->prepare("SELECT photo_path, CONCAT(family_name, ' ', first_name) as full_name FROM citizens WHERE id = :id"); $stmt->execute([':id'=>$id]);
$citizen = $stmt->fetch(); if (!$citizen) { app()->redirect('index.php', 'error', 'Citizen not found'); }
try {
    if ($citizen['photo_path'] && file_exists(BASE_PATH . '/' . $citizen['photo_path'])) unlink(BASE_PATH . '/' . $citizen['photo_path']);
    $docs = $db->prepare("SELECT file_path FROM documents WHERE citizen_id = :id"); $docs->execute([':id'=>$id]);
    foreach ($docs->fetchAll() as $doc) { if ($doc['file_path'] && file_exists(BASE_PATH . '/' . $doc['file_path'])) unlink(BASE_PATH . '/' . $doc['file_path']); }
    $db->prepare("DELETE FROM citizens WHERE id = :id")->execute([':id'=>$id]);
    app()->logActivity('citizen_deleted', "Deleted citizen #{$id}: {$citizen['full_name']}", 'citizens');
    app()->redirect('index.php', 'success', trans('citizen_deleted'));
} catch (PDOException $e) { error_log("Delete citizen error: " . $e->getMessage()); app()->redirect('index.php', 'error', trans('error')); }