<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/session.php';

if (!isLoggedIn()) {
    header('Location: /modules/auth/login.php');
        exit;
        }

        header('Location: /modules/dashboard/index.php');
        