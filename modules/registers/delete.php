<?php
require_once __DIR__ . '/../../app/Core/App.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAuth();
if (!can('registers.delete')) { app()->redirect('index.php', 'error', trans('access_denied')); }
$id = (int)($_GET['id'] ?? 0); if (!$id) { app()->redirect('index.php', 'error', 'Invalid ID'); }
$db = app()->db();
$stmt = $db->prepare("SELECT register_number FROM register_books WHERE id = :id"); $stmt->execute([':id' => $id]);
$book = $stmt->fetch(); if (!$book) { app()->redirect('index.php', 'error', 'Register not found'); }
try {
    $pages = $db->prepare("SELECT scan_path FROM register_pages WHERE register_book_id = :id"); $pages->execute([':id' => $id]);
    foreach ($pages->fetchAll() as $p) { if ($p['scan_path'] && file_exists(BASE_PATH . '/' . $p['scan_path'])) unlink(BASE_PATH . '/' . $p['scan_path']); }
    $db->prepare("DELETE FROM register_books WHERE id = :id")->execute([':id' => $id]);
    app()->logActivity('register_deleted', "Deleted register #{$id}: {$book['register_number']}", 'registers');
    app()->redirect('index.php', 'success', trans('register_deleted'));
} catch (PDOException $e) { error_log("Delete register error: " . $e->getMessage()); app()->redirect('index.php', 'error', trans('error')); }