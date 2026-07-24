<?php

require_once '../../config/database.php';

$db = (new Database())->connect();

// Check if an admin already exists
$stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
$stmt->execute();

if ($stmt->fetchColumn() > 0) {
    die("An administrator account already exists.");
    }

    // Default administrator
    $fullname = "Administrator";
    $email = "admin@sgrc.ma";
    $password = password_hash("Admin@123456", PASSWORD_DEFAULT);
    $role = "admin";
    $language = "fr";

    $sql = "INSERT INTO users (fullname, email, password, role, language)
            VALUES (?, ?, ?, ?, ?)";

            $stmt = $db->prepare($sql);

            if ($stmt->execute([$fullname, $email, $password, $role, $language])) {
                echo "<h2>Administrator account created successfully.</h2>";
                    echo "<p><strong>Email:</strong> admin@sgrc.ma</p>";
                        echo "<p><strong>Password:</strong> Admin@123456</p>";
                            echo "<p><a href='login.php'>Go to Login</a></p>";
                            } else {
                                echo "Failed to create administrator.";
                                }