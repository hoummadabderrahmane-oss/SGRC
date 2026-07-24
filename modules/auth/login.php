<?php
session_start();

if (isset($_SESSION['user'])) {
    header("Location: ../../index.php");
        exit;
        }
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>

        <meta charset="UTF-8">

        <title>SGRC Login</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <link rel="stylesheet" href="../../assets/css/login.css">

        </head>

        <body>

        <div class="login-card">

        <h2>SGRC</h2>

        <p>Système de Gestion des Registres Communaux</p>

        <form action="authenticate.php" method="POST">

        <div class="mb-3">

        <label>Email</label>

        <input type="email"
        class="form-control"
        name="email"
        required>

        </div>

        <div class="mb-3">

        <label>Mot de passe</label>

        <input type="password"
        class="form-control"
        name="password"
        required>

        </div>

        <div class="mb-3">

        <select class="form-select" name="language">

        <option value="fr">Français</option>

        <option value="ar">العربية</option>

        </select>

        </div>

        <button class="btn btn-primary w-100">

        Connexion

        </button>

        </form>

        </div>

        </body>

        </html>