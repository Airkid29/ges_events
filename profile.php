<?php
// profil.php
session_start(); // Démarre la session

require_once 'includes/db.php'; // Inclut la connexion à la BDD

// Vérifie si l'utilisateur est connecté.
// Si l'utilisateur n'est pas connecté ou est un administrateur (qui aurait son propre dashboard),
// le redirige vers la page de connexion.
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] === 'admin') {
    header('Location: login.php'); // Redirige vers la page de connexion
    exit;
}

// Récupère l'ID de l'utilisateur connecté depuis la session
$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'] ?? 'Non disponible'; // Utilisez l'email de session si disponible

$user_data = null;
$message_status = '';

try {
    // Récupère les informations complètes de l'utilisateur depuis la base de données
    // IMPORTANT: NE JAMAIS RÉCUPÉRER LE MOT DE PASSE HASHÉ ICI POUR L'AFFICHAGE
    $stmt = $pdo->prepare("SELECT id, email, role, created_at FROM users WHERE id = :user_id LIMIT 1");
    $stmt->execute([':user_id' => $user_id]);
    $user_data = $stmt->fetch();

    if (!$user_data) {
        // Cela ne devrait normalement pas arriver si $_SESSION['user_id'] est défini,
        // mais c'est une sécurité.
        $message_status = "<p class='error-message'>Erreur : Impossible de trouver les informations de votre profil.</p>";
        // Déconnecter l'utilisateur si ses données ne sont plus trouvées en BDD
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit;
    }
} catch (PDOException $e) {
    $message_status = "<p class='error-message'>Une erreur est survenue lors du chargement de votre profil. Veuillez réessayer plus tard.</p>";
    error_log("Profile loading error: " . $e->getMessage());
}

// Inclut l'en-tête HTML après la logique de redirection
include_once 'includes/header.php';
?>

<h1 class="page-title">Mon Profil</h1>

<section class="profile-section">
    <div class="container">
        <?php echo $message_status; ?>

        <?php if ($user_data): ?>
            <div class="profile-card">
                <h2>Informations du compte</h2>
                <div class="profile-info">
                    <p><strong>ID Utilisateur :</strong> <?php echo htmlspecialchars($user_data['id']); ?></p>
                    <p><strong>Email :</strong> <?php echo htmlspecialchars($user_data['email']); ?></p>
                    <p><strong>Rôle :</strong> <?php echo htmlspecialchars($user_data['role']); ?></p>
                    <p><strong>Membre depuis :</strong> <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($user_data['created_at']))); ?></p>
                </div>
                <div class="profile-actions">
                    <a href="modifier_profil.php" class="btn btn-primary">Modifier le profil (à implémenter)</a>
                    <a href="mes_reservations.php" class="btn btn-secondary">Mes réservations (à implémenter)</a>
                    <a href="logout.php" class="btn btn-danger">Se déconnecter</a>
                    <br>
                    <a href="index.php" class="btn btn-danger">Retour a l'ecran d'accueil</a>
                </div>
            </div>
        <?php else: ?>
            <p>Impossible d'afficher les informations du profil.</p>
        <?php endif; ?>

        </div>
</section>

<?php
include_once 'includes/footer.php'; // Inclut le pied de page HTML
?>