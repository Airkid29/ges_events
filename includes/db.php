<?php
// includes/db.php

$host = 'localhost'; // Généralement localhost en développement
$db   = 'gestion_espaces'; // Nom de ta base de données
$user = 'root';      // Ton nom d'utilisateur MySQL
$pass = '';          // Ton mot de passe MySQL (laisse vide si tu n'en as pas en local, mais PAS en production)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Active les exceptions pour les erreurs SQL
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Récupère les résultats sous forme de tableau associatif
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Désactive l'émulation des requêtes préparées (meilleure sécurité)
];

try {
    // Crée une nouvelle instance de PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // En cas d'erreur de connexion, affiche un message et arrête le script
    error_log("Erreur de connexion à la base de données : " . $e->getMessage()); // Pour les logs serveur
    die("Désolé, une erreur est survenue lors de la connexion à la base de données. Veuillez réessayer plus tard.");
}
?>