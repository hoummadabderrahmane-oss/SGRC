<?php

session_start();

require_once '../../config/database.php';

$db = (new Database())->connect();

$email = trim($_POST['email']);
$password = $_POST['password'];
$language = $_POST['language'];

$sql = "SELECT * FROM users WHERE email=? LIMIT 1";

$stmt = $db->prepare($sql);

$stmt->execute([$email]);

$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {

    $_SESSION['user']=$user;

        $_SESSION['language']=$language;

            header("Location: ../../index.php");

                exit;

                }

                header("Location: login.php?error=1");
                exit;