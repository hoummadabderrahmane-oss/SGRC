<?php

require_once __DIR__ . '/session.php';

if (!isset($_SESSION['user'])) {
    header("Location: /sgrc/modules/auth/login.php");
        exit;
        }