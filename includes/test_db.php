<?php
require_once 'db.php'; // Ou le chemin correct vers db.php
echo "Connexion à la base de données réussie !";
// Vous pouvez même essayer une petite requête pour être sûr
try {
    $stmt = $pdo->query("SELECT 1");
    if ($stmt) {
        echo "<br>Requête de test réussie.";
    }
} catch (PDOException $e) {
    echo "<br>Erreur lors de la requête de test : " . $e->getMessage();
}
?>