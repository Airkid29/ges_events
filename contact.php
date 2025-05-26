<?php
// contact.php
include_once 'includes/header.php'; // Inclut l'en-tête HTML

$nom = '';
$email = '';
$sujet = '';
$message = '';
$message_status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $sujet = htmlspecialchars(trim($_POST['sujet'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    if (empty($nom) || empty($email) || empty($sujet) || empty($message)) {
        $message_status = "<p class='error-message'>Veuillez remplir tous les champs obligatoires.</p>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message_status = "<p class='error-message'>Veuillez entrer une adresse email valide.</p>";
    } else {
        // Destination de l'email (changez ceci par votre adresse email !)
        $to = "votre_email@example.com"; // REMPLACEZ CETTE ADRESSE !!!
        $headers = "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=utf-8\r\n";

        $email_content = "Nom: " . $nom . "\n";
        $email_content .= "Email: " . $email . "\n";
        $email_content .= "Sujet: " . $sujet . "\n\n";
        $email_content .= "Message:\n" . $message . "\n";

        // Envoi de l'email
        if (mail($to, $sujet, $email_content, $headers)) {
            $message_status = "<p class='success-message'>Votre message a été envoyé avec succès ! Nous vous répondrons bientôt.</p>";
            // Réinitialiser les champs après succès
            $nom = $email = $sujet = $message = '';
        } else {
            $message_status = "<p class='error-message'>Désolé, une erreur est survenue lors de l'envoi de votre message. Veuillez réessayer.</p>";
        }
    }
}
?>

<h1 class="page-title">Contactez-nous</h1>

<section class="contact-section">
    <div class="contact-info">
        <h2>Informations de Contact</h2>
        <p>N'hésitez pas à nous contacter pour toute question, suggestion ou demande de partenariat.</p>
        <ul>
            <li><strong>Email:</strong> info@votresite.com</li>
            <li><strong>Téléphone:</strong> +228 90 12 34 56</li>
            <li><strong>Adresse:</strong> 123 Rue de l'Événement, Lomé, Togo</li>
        </ul>
        <div class="social-media">
            <h3>Suivez-nous</h3>
            <a href="#" class="social-icon"><img src="assets/images/facebook.png" alt="Facebook"></a>
            <a href="#" class="social-icon"><img src="assets/images/instagram.png" alt="Instagram"></a>
            <a href="#" class="social-icon"><img src="assets/images/twitter.png" alt="Twitter"></a>
        </div>
    </div>

    <div class="contact-form-container">
        <h2>Envoyez-nous un message</h2>
        <?php echo $message_status; ?>
        <form action="contact.php" method="POST" class="contact-form">
            <div class="form-group">
                <label for="nom">Votre Nom *</label>
                <input type="text" id="nom" name="nom" required value="<?php echo htmlspecialchars($nom); ?>">
            </div>
            <div class="form-group">
                <label for="email">Votre Email *</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="form-group">
                <label for="sujet">Sujet *</label>
                <input type="text" id="sujet" name="sujet" required value="<?php echo htmlspecialchars($sujet); ?>">
            </div>
            <div class="form-group">
                <label for="message">Votre Message *</label>
                <textarea id="message" name="message" rows="7" required><?php echo htmlspecialchars($message); ?></textarea>
            </div>
            <button type="submit" class="btn btn-submit">Envoyer le message</button>
        </form>
    </div>
</section>

<?php
include_once 'includes/footer.php'; // Inclut le pied de page HTML
?>