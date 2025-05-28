<?php
session_start();

// --- 1. Vérification de session et des droits d'admin ---
// Utilisation de isset() pour éviter le warning si la clé n'existe pas.
// Si user_id n'est pas défini OU si type_utilisateur n'est pas défini OU si type_utilisateur n'est pas 'admin'.
if (!isset($_SESSION['user_id']) || !isset($_SESSION['type_utilisateur']) || $_SESSION['type_utilisateur'] !== 'admin') {
    // Redirige vers la page de connexion ou une page d'accès refusé
    // Assurez-vous qu'aucun espace ou BOM n'est avant <?php
    header('Location: login.php'); // Remplacez par votre page de connexion
    exit(); // Très important pour arrêter l'exécution du script après la redirection
}

// --- 2. Inclusion du fichier de connexion à la base de données ---
require_once 'includes/db_connect.php'; // Adaptez le chemin si nécessaire

// --- 3. Récupération des statistiques pour le tableau de bord ---
$total_espaces = 0;
$reservations_en_attente = 0;
$total_utilisateurs = 0;

try {
    // Nombre total d'espaces
    $stmt_espaces = $pdo->query("SELECT COUNT(id_espace) AS total_espaces FROM espaces");
    $result_espaces = $stmt_espaces->fetch(PDO::FETCH_ASSOC);
    $total_espaces = $result_espaces['total_espaces'];

    // Nombre de réservations en attente
    $stmt_reservations = $pdo->prepare("SELECT COUNT(id_reservation) AS reservations_en_attente FROM reservations WHERE statut_reservation = ?");
    $stmt_reservations->execute(['En attente']); // Adaptez la valeur du statut si différente
    $result_reservations = $stmt_reservations->fetch(PDO::FETCH_ASSOC);
    $reservations_en_attente = $result_reservations['reservations_en_attente'];

    // Nombre total d'utilisateurs
    $stmt_utilisateurs = $pdo->query("SELECT COUNT(id_utilisateur) AS total_utilisateurs FROM utilisateurs");
    $result_utilisateurs = $stmt_utilisateurs->fetch(PDO::FETCH_ASSOC);
    $total_utilisateurs = $result_utilisateurs['total_utilisateurs'];

} catch (PDOException $e) {
    // En cas d'erreur de base de données
    echo "Erreur de base de données : " . $e->getMessage();
    // En production, loggez l'erreur et affichez un message générique
    exit();
}

// Nom de l'administrateur connecté (pour l'affichage)
// Utilisation de l'opérateur de coalescence nulle pour éviter le warning si 'user_prenom' n'est pas défini
$admin_name = $_SESSION['user_prenom'] ?? 'Administrateur'; 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Admin - Gestion Espaces Événementiels</title>
    <link rel="stylesheet" href="../css/admin_style.css"> <style>
        /* Styles simples pour le tableau de bord, à compléter ou à déplacer dans admin_style.css */
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        .admin-header { background-color: #333; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .admin-header h1 { margin: 0; font-size: 24px; }
        .admin-header nav ul { list-style: none; margin: 0; padding: 0; display: flex; }
        .admin-header nav ul li { margin-left: 20px; }
        .admin-header nav ul li a { color: white; text-decoration: none; font-weight: bold; }
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; background-color: white; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background-color: #e9e9e9; padding: 20px; border-radius: 5px; text-align: center; }
        .stat-card h3 { color: #333; margin-top: 0; }
        .stat-card p { font-size: 2em; font-weight: bold; color: #007bff; margin-bottom: 0; }
        .action-links { text-align: center; }
        .action-links a { display: inline-block; background-color: #007bff; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; margin: 10px; transition: background-color 0.3s ease; }
        .action-links a:hover { background-color: #0056b3; }
        .logout-btn { background-color: #dc3545; }
        .logout-btn:hover { background-color: #c82333; }
    </style>
</head>
<body>
    <header class="admin-header">
        <h1>Tableau de Bord Admin</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Accueil Admin</a></li>
                <li><a href="manage_espaces.php">Gérer Espaces</a></li>
                <li><a href="manage_reservations.php">Gérer Réservations</a></li>
                <li><a href="manage_users.php">Gérer Utilisateurs</a></li>
                <li><a href="logout.php" class="logout-btn">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h2>Bienvenue, <?php echo htmlspecialchars($admin_name); ?> !</h2>
        <p>Voici un aperçu rapide de l'état de votre système.</p>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Espaces</h3>
                <p><?php echo $total_espaces; ?></p>
            </div>
            <div class="stat-card">
                <h3>Réservations en Attente</h3>
                <p><?php echo $reservations_en_attente; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Utilisateurs</h3>
                <p><?php echo $total_utilisateurs; ?></p>
            </div>
            </div>

        <div class="action-links">
            <h3>Actions Rapides</h3>
            <a href="manage_espaces.php">Ajouter un nouvel espace</a>
            <a href="manage_reservations.php?status=En attente">Voir les réservations en attente</a>
            <a href="manage_users.php">Gérer les comptes utilisateurs</a>
        </div>
    </div>
</body>
</html>

<?php
// Fermeture de la connexion à la base de données (si non automatique par PHP)
$pdo = null;
?>