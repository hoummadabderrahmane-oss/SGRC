<?php
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function flash($key, $message = null) {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
    } else {
        $msg = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
}

function formatDate($date, $format = 'Y-m-d') {
    return $date ? date($format, strtotime($date)) : '-';
}

function generateId($prefix = '') {
    return $prefix . date('Y') . strtoupper(substr(uniqid(), -6));
}

function uploadFile($file, $directory, $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf']) {
    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes)) return false;
    
    $filename = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $path = $directory . '/' . $filename;
    
    if (!is_dir($directory)) mkdir($directory, 0755, true);
    
    return move_uploaded_file($file['tmp_name'], $path) ? $path : false;
}