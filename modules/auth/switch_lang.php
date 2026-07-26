<?php
/**
 * SGRC - Language Switcher
 * تبديل اللغة
 */

require_once __DIR__ . '/../../app/Core/App.php';

$lang = $_GET['lang'] ?? 'ar';

if (in_array($lang, ['ar', 'fr'])) {
    app()->switchLang($lang);
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'lang' => $lang]);