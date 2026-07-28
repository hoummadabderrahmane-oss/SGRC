<?php
// NOTE: session.php must be loaded BEFORE this file
// DO NOT put session_start() here to avoid conflicts

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    function isAdmin() {
        return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'admin';
        }

        function requireAuth() {
            if (!isLoggedIn()) {
                    header('Location: /SGRC/modules/auth/login.php');
                            exit;
                                }
                                }

                                function requireAdmin() {
                                    requireAuth();
                                        if (!isAdmin()) {
                                                header('Location: /SGRC/modules/dashboard/index.php');
                                                        exit;
                                                            }
                                                            }

                                                            function getCurrentUser() {
                                                                if (!isLoggedIn()) return null;
                                                                    $db = Database::getInstance();
                                                                        return $db->query("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']])->fetch();
                                                                        }
                                                                        