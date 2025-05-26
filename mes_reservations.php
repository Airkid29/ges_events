<?php
// mes_reservations.php
session_start(); // Démarre la session

require_once 'includes/db.php'; // Inclut la connexion à la BDD

// Vérifie si l'utilisateur est connecté et n'est pas un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] === 'admin') {
    header('Location: login.php'); // Redirige vers la page de connexion
    exit;
}

$user_id = $_SESSION['user_id'];
$reservations = [];
$message_status = '';

try {
    // Récupérer toutes les demandes/réservations de l'utilisateur
    // Jointure avec la table 'espaces' pour obtenir les noms des espaces
    $stmt = $pdo->prepare("
        SELECT
            dc.id,
            dc.nom_contact,
            dc.email_contact,
            dc.telephone_contact,
            dc.date_evenement,
            dc.message,
            dc.date_demande,
            dc.statut,
            e.nom AS nom_espace,
            e.image_principale AS image_espace
        FROM
            demandes_contact dc
        JOIN
            espaces e ON dc.id_espace = e.id
        WHERE
            dc.id_utilisateur = :user_id
        ORDER BY
            dc.date_demande DESC
    ");
    $stmt->execute([':user_id' => $user_id]);
    $reservations = $stmt->fetchAll();

    if (empty($reservations)) {
        $message_status = "<p class='info-message'>Vous n'avez pas encore fait de demandes ou de réservations.</p>";
    }

} catch (PDOException $e) {
    $message_status = "<p class='error-message'>Une erreur est survenue lors du chargement de vos réservations. Veuillez réessayer plus tard.</p>";
    error_log("Reservations loading error: " . $e->getMessage());
}

// Inclut l'en-tête HTML après la logique de redirection
include_once 'includes/header.php';
?>

<h1 class="page-title">Mes Réservations</h1>

<section class="reservations-list-section">
    <div class="container">
        <?php echo $message_status; ?>

        <?php if (!empty($reservations)): ?>
            <div class="reservations-grid">
                <?php foreach ($reservations as $reservation): ?>
                    <div class="reservation-card">
                        <div class="reservation-image">
                            <img src="assets/images/<?php echo htmlspecialchars($reservation['image_espace']); ?>" alt="Image de l'espace">
                        </div>
                        <div class="reservation-details">
                            <h3>Demande pour : <?php echo htmlspecialchars($reservation['nom_espace']); ?></h3>
                            <p><strong>Contact :</strong> <?php echo htmlspecialchars($reservation['nom_contact']); ?> (<?php echo htmlspecialchars($reservation['email_contact']); ?>)</p>
                            <?php if (!empty($reservation['telephone_contact'])): ?>
                                <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($reservation['telephone_contact']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($reservation['date_evenement'])): ?>
                                <p><strong>Date souhaitée :</strong> <?php echo htmlspecialchars(date('d/m/Y', strtotime($reservation['date_evenement']))); ?></p>
                            <?php endif; ?>
                            <p><strong>Message :</strong> <?php echo nl2br(htmlspecialchars($reservation['message'])); ?></p>
                            <p><strong>Date de la demande :</strong> <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($reservation['date_demande']))); ?></p>
                            <p class="reservation-status status-<?php echo htmlspecialchars($reservation['statut']); ?>">
                                <strong>Statut :</strong> <?php echo htmlspecialchars($reservation['statut']); ?>
                            </p>
                            </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <p class="auth-link"><a href="profil.php">Retour au profil</a></p>
    </div>
</section>

<?php
include_once 'includes/footer.php'; // Inclut le pied de page HTML
?>