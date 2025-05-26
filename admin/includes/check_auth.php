<?php
// admin/includes/check_auth.php
// Démarre la session si ce n'est pas déjà fait
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifie si l'utilisateur est connecté ET s'il a le rôle 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    // Si non, redirige vers la page de connexion admin
    header('Location: admin_login.php');
    exit;
}
?>