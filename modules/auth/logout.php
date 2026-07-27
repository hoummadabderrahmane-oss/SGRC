<?php
session_start();
session_unset();
session_destroy();
header('Location: /SGRC/modules/auth/login.php');
exit;