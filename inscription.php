<?php
// inscription.php
session_start();
require_once 'includes/db.php'; // Inclut la connexion à la BDD
include_once 'includes/header.php'; // Inclut l'en-tête HTML

$email = '';
$nom = ''; // Vous pouvez ajouter un champ nom si vous le souhaitez
$message_status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password = htmlspecialchars(trim($_POST['password'] ?? ''));
    $password_confirm = htmlspecialchars(trim($_POST['password_confirm'] ?? ''));
    $nom = htmlspecialchars(trim($_POST['nom'] ?? '')); // Exemple pour un champ nom

    if (empty($email) || empty($password) || empty($password_confirm)) {
        $message_status = "<p class='error-message'>Veuillez remplir tous les champs obligatoires.</p>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message_status = "<p class='error-message'>Veuillez entrer une adresse email valide.</p>";
    } elseif ($password !== $password_confirm) {
        $message_status = "<p class='error-message'>Les mots de passe ne correspondent pas.</p>";
    } elseif (strlen($password) < 6) {
        $message_status = "<p class='error-message'>Le mot de passe doit contenir au moins 6 caractères.</p>";
    } else {
        try {
            // Vérifier si l'email existe déjà
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch()) {
                $message_status = "<p class='error-message'>Cet email est déjà enregistré. Veuillez utiliser un autre email.</p>";
            } else {
                // Hasher le mot de passe avant de l'enregistrer
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                // Insérer le nouvel utilisateur dans la base de données
                $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role) VALUES (:email, :password_hash, 'user')");
                // Note: 'user' est le rôle par défaut. L'admin serait inséré manuellement ou via un panneau d'administration.
                $stmt->execute([
                    ':email' => $email,
                    ':password_hash' => $password_hash
                ]);

                $message_status = "<p class='success-message'>Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.</p>";
                // Réinitialiser les champs après l'inscription réussie
                $email = $nom = '';
            }
        } catch (PDOException $e) {
            $message_status = "<p class='error-message'>Une erreur est survenue lors de l'inscription. Veuillez réessayer plus tard.</p>";
            error_log("Registration Error: " . $e->getMessage());
        }
    }
}
?>

<style>
    body {
        background: linear-gradient(120deg, #f0f4f8 0%, #e0e7ef 100%);
        font-family: 'Segoe UI', Arial, sans-serif;
        margin: 0;
        padding: 0;
        min-height: 100vh;
    }

    .page-title {
        text-align: center;
        margin-top: 40px;
        color: #2c3e50;
        font-size: 2.2rem;
        letter-spacing: 1px;
    }

    section.auth-form-section {
        padding: 0;
        background: none;
        border-radius: 0;
        max-width: none;
        margin: 0;
    }

    .auth-form-container {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(44, 62, 80, 0.08);
        max-width: 400px;
        margin: 40px auto 0 auto;
        padding: 32px 28px 24px 28px;
    }

    .auth-form-container h2 {
        text-align: center;
        color: #34495e;
        margin-bottom: 24px;
        font-size: 1.4rem;
        font-weight: 600;
    }

    .auth-form .form-group {
        margin-bottom: 18px;
    }

    .auth-form label {
        display: block;
        margin-bottom: 6px;
        color: #2c3e50;
        font-weight: 500;
        font-size: 1rem;
    }

    .auth-form input[type="email"],
    .auth-form input[type="password"],
    .auth-form input[type="text"] {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 1rem;
        background: #f9fafb;
        transition: border-color 0.2s;
        }

</style>

<h1 class="page-title">Inscription</h1>

<section class="auth-form-section">
    <div class="auth-form-container">
        <h2>Créez votre compte</h2>
        <?php echo $message_status; ?>
        <form action="inscription.php" method="POST" class="auth-form">
            <?php /* Si vous voulez un champ nom réel:
            <div class="form-group">
                <label for="nom">Votre Nom</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($nom); ?>">
            </div>
            */ ?>
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="form-group">
                <label for="password">Mot de passe *</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirmer le mot de passe *</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            <button type="submit" class="btn btn-submit">S'inscrire</button>
            <p class="auth-link">Déjà un compte ? <a href="login.php">Connectez-vous ici</a>.</p>
        </form>
    </div>
</section>

<?php
include_once 'includes/footer.php'; // Inclut le pied de page HTML
?>