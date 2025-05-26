<?php
// detail_espace.php
session_start(); // Démarre la session pour accéder à $_SESSION['user_id']
require_once 'includes/db.php';
// Nous inclurons le header plus tard, après les éventuelles redirections ou traitements de formulaire

$espace_id = $_GET['id'] ?? 0; // Récupère l'ID de l'espace depuis l'URL

if (!is_numeric($espace_id) || $espace_id <= 0) {
    header('Location: espaces.php'); // Redirige si l'ID est invalide
    exit;
}

$espace = null; // Initialisation de $espace
$message_contact_status = ''; // Message pour le formulaire de contact

try {
    $stmt = $pdo->prepare("SELECT e.*, c.nom_categorie FROM espaces e JOIN categories c ON e.id_categorie = c.id WHERE e.id = :id AND e.actif = TRUE");
    $stmt->execute([':id' => $espace_id]);
    $espace = $stmt->fetch();

    if (!$espace) {
        // Si aucun espace trouvé avec cet ID
        include_once 'includes/header.php'; // Inclure le header ici car on va afficher du HTML
        echo "<section class='content-section'><div class='container'><p class='error-message'>Désolé, cet espace n'existe pas ou n'est plus disponible.</p></div></section>";
        include_once 'includes/footer.php';
        exit;
    }

    // Traitement des images secondaires (si stockées en CSV)
    $images_secondaires = [];
    if (!empty($espace['images_secondaires'])) {
        $images_secondaires = explode(',', $espace['images_secondaires']);
    }

    // Traitement des équipements (si stockés en CSV)
    $equipements = [];
    if (!empty($espace['equipements'])) {
        $equipements = explode(',', $espace['equipements']);
    }

} catch (PDOException $e) {
    include_once 'includes/header.php'; // Inclure le header ici car on va afficher du HTML
    echo "<section class='content-section'><div class='container'><p class='error-message'>Une erreur est survenue lors du chargement des détails de l'espace. Veuillez réessayer plus tard.</p></div></section>";
    error_log("Erreur détail espace: " . $e->getMessage());
    include_once 'includes/footer.php';
    exit;
}


// Gérer la soumission du formulaire de contact
$nom_form = $_POST['nom'] ?? '';
$email_form = $_POST['email'] ?? '';
$telephone_form = $_POST['telephone'] ?? '';
$date_evenement_form = $_POST['date_evenement'] ?? '';
$message_form = $_POST['message'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
    $nom_form = htmlspecialchars(trim($_POST['nom']));
    $email_form = htmlspecialchars(trim($_POST['email']));
    $telephone_form = htmlspecialchars(trim($_POST['telephone']));
    $message_form = htmlspecialchars(trim($_POST['message']));
    $date_evenement_form = htmlspecialchars(trim($_POST['date_evenement']));

    if (empty($nom_form) || empty($email_form) || empty($message_form)) {
        $message_contact_status = "<p class='error-message'>Veuillez remplir tous les champs obligatoires (Nom, Email, Message).</p>";
    } elseif (!filter_var($email_form, FILTER_VALIDATE_EMAIL)) {
        $message_contact_status = "<p class='error-message'>Veuillez entrer une adresse email valide.</p>";
    } else {
        // Déterminer l'ID de l'utilisateur. Si non connecté, ce sera NULL.
        $id_utilisateur = $_SESSION['user_id'] ?? null;
        $espace_id_for_db = $espace['id']; // L'ID de l'espace actuel

        try {
            // Insérer la demande dans la table demandes_contact
            $stmt_insert = $pdo->prepare("
                INSERT INTO demandes_contact
                (id_utilisateur, id_espace, nom_contact, email_contact, telephone_contact, date_evenement, message)
                VALUES (:id_utilisateur, :id_espace, :nom_contact, :email_contact, :telephone_contact, :date_evenement, :message)
            ");

            $stmt_insert->execute([
                ':id_utilisateur' => $id_utilisateur,
                ':id_espace' => $espace_id_for_db,
                ':nom_contact' => $nom_form,
                ':email_contact' => $email_form,
                ':telephone_contact' => $telephone_form,
                ':date_evenement' => !empty($date_evenement_form) ? $date_evenement_form : null, // Gérer les dates vides
                ':message' => $message_form
            ]);

            // Envoi d'email (optionnel, nécessite configuration SMTP)
            // Pour l'exemple, on met en commentaire. Décommentez et configurez si besoin.
            // $sujet_email_client = "Confirmation de votre demande pour l'espace: " . $espace['nom'];
            // $corps_email_client = "Bonjour $nom_form,\n\nNous avons bien reçu votre demande concernant l'espace " . $espace['nom'] . ".\nLe propriétaire vous contactera bientôt.\n\nCordialement,\nL'équipe Espace Événementiel";
            // mail($email_form, $sujet_email_client, $corps_email_client, "From: no-reply@ton-site.com");

            // $sujet_email_proprietaire = "Nouvelle demande de contact pour votre espace: " . $espace['nom'];
            // $corps_email_proprietaire = "Une nouvelle demande a été soumise pour l'espace " . $espace['nom'] . ".\n\n" .
            //                             "Nom: $nom_form\n" .
            //                             "Email: $email_form\n" .
            //                             "Téléphone: $telephone_form\n" .
            //                             "Date d'événement souhaitée: $date_evenement_form\n\n" .
            //                             "Message:\n$message_form\n";
            // mail("contact_proprietaire@ton-site.com", $sujet_email_proprietaire, $corps_email_proprietaire, "From: systeme@ton-site.com");


            $message_contact_status = "<p class='success-message'>Votre demande a été envoyée avec succès ! Le propriétaire de l'espace vous recontactera bientôt.</p>";
            // Réinitialiser les champs du formulaire après l'envoi réussi
            $nom_form = '';
            $email_form = '';
            $telephone_form = '';
            $date_evenement_form = '';
            $message_form = '';

        } catch (PDOException $e) {
            $message_contact_status = "<p class='error-message'>Une erreur est survenue lors de l'envoi de votre demande. Veuillez réessayer.</p>";
            error_log("Error saving contact form: " . $e->getMessage());
        }
    }
}

// Inclure l'en-tête HTML ici, après tous les traitements PHP qui pourraient envoyer des headers.
include_once 'includes/header.php';
?>

<section class="detail-espace-section">
    <div class="espace-header">
        <h1><?php echo htmlspecialchars($espace['nom']); ?></h1>
        <p class="location"><?php echo htmlspecialchars($espace['adresse']); ?></p>
        <p class="category-tag"><?php echo htmlspecialchars($espace['nom_categorie']); ?></p>
    </div>

    <div class="gallery">
        <img src="assets/images/<?php echo htmlspecialchars($espace['image_principale']); ?>" alt="Image principale de <?php echo htmlspecialchars($espace['nom']); ?>" class="main-image">
        <?php if (!empty($images_secondaires)): ?>
            <div class="thumbnail-gallery">
                <?php foreach ($images_secondaires as $img_path): ?>
                    <img src="assets/images/<?php echo htmlspecialchars(trim($img_path)); ?>" alt="Image secondaire" class="thumbnail-image">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="espace-details-content">
        <div class="details-left">
            <h2>Description</h2>
            <p><?php echo nl2br(htmlspecialchars($espace['description'])); ?></p>

            <h3>Caractéristiques</h3>
            <ul>
                <li>Capacité : <strong><?php echo htmlspecialchars($espace['capacite']); ?></strong> personnes</li>
                <li>Prix : <strong><?php echo !empty($espace['prix']) ? htmlspecialchars($espace['prix']) : 'Sur devis'; ?></strong></li>
            </ul>

            <?php if (!empty($equipements)): ?>
                <h3>Équipements inclus</h3>
                <ul class="equipements-list">
                    <?php foreach ($equipements as $equip): ?>
                        <li><?php echo htmlspecialchars(trim($equip)); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <div class="google-maps">
                <h3>Localisation</h3>
                <p>Voir sur la carte : <iframe src="https://maps.google.com/maps?q=<?php echo urlencode($espace['adresse']); ?>&output=embed" width="100%" height="300" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe></p>
            </div>
        </div>

        <div class="details-right contact-form-section">
            <h2>Demander un devis / Contact</h2>
            <?php echo $message_contact_status; ?>
            <form action="detail_espace.php?id=<?php echo htmlspecialchars($espace['id']); ?>" method="POST" class="contact-form">
                <div class="form-group">
                    <label for="nom">Votre Nom *</label>
                    <input type="text" id="nom" name="nom" required value="<?php echo $nom_form; ?>">
                </div>
                <div class="form-group">
                    <label for="email">Votre Email *</label>
                    <input type="email" id="email" name="email" required value="<?php echo $email_form; ?>">
                </div>
                <div class="form-group">
                    <label for="telephone">Votre Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" value="<?php echo $telephone_form; ?>">
                </div>
                <div class="form-group">
                    <label for="date_evenement">Date d'événement souhaitée</label>
                    <input type="date" id="date_evenement" name="date_evenement" value="<?php echo $date_evenement_form; ?>">
                </div>
                <div class="form-group">
                    <label for="message">Votre Message *</label>
                    <textarea id="message" name="message" rows="5" required><?php echo $message_form; ?></textarea>
                </div>
                <button type="submit" name="submit_contact" class="btn btn-submit">Envoyer la demande</button>
            </form>
        </div>
    </div>
</section>

<?php
include_once 'includes/footer.php';
?>