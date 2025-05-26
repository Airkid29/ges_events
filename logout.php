<?php
// logout.php
session_start(); // Démarrer la session pour pouvoir la détruire

// Détruire toutes les variables de session
session_unset();

// Détruire la session
session_destroy();

// Rediriger l'utilisateur vers la page d'accueil ou de connexion
header('Location: index.php');
exit;
?>