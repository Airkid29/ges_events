<?php
// includes/header.php
// Démarre la session PHP si ce n'est pas déjà fait
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Inclure le fichier de connexion à la BDD si besoin dans le header,
// mais il est souvent mieux de l'inclure dans les pages spécifiques qui en ont besoin.
// include_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Événementiel - Trouvez le lieu parfait</title>
    <link rel="stylesheet" href="assets/css/style.css">
    </head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="index.php">
                    <img src="assets/images/logo.png" alt="Logo Espace Événementiel">
                    <h1></h1>
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="espaces.php">Nos Espaces</a></li>
                    <li><a href="comment_ca_marche.php">Comment ça marche ?</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin'): ?>
                        <li><a href="admin/dashboard.php">Admin</a></li>
                        <li><a href="admin/logout.php">Déconnexion</a></li>
                    <?php elseif (isset($_SESSION['user_id'])): ?>
                        <li><a href="profile.php">Mon Profil</a></li>
                        <li><a href="logout.php">Déconnexion</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Connexion / Inscription</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <div class="container">