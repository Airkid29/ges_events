<?php
// login.php
session_start();
require_once 'includes/db.php'; // Inclut la connexion à la BDD

$email = '';
$message_status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password = htmlspecialchars(trim($_POST['password'] ?? ''));

    if (empty($email) || empty($password)) {
        $message_status = "<p class='error-message'>Veuillez entrer votre email et votre mot de passe.</p>";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, password_hash, role FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Mot de passe correct, démarrer la session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $email; // Stocker l'email si besoin

                // Redirection après connexion réussie
                // Ces lignes DOIVENT être exécutées AVANT TOUT ENVOI DE HTML
                if ($user['role'] === 'admin') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: profile.php'); // Ou index.php, ou un tableau de bord utilisateur
                }
                exit; // TRES IMPORTANT : Toujours appeler exit() après une redirection
            } else {
                $message_status = "<p class='error-message'>Email ou mot de passe incorrect.</p>";
            }
        } catch (PDOException $e) {
            $message_status = "<p class='error-message'>Une erreur est survenue lors de la connexion. Veuillez réessayer plus tard.</p>";
            error_log("Login Error: " . $e->getMessage()); // Pour les logs serveur
        }
    }
}

// Inclure l'en-tête HTML SEULEMENT APRÈS que toutes les redirections possibles ont été traitées.
include_once 'includes/header.php';
?>

<h1 class="page-title">Connexion</h1>

<section class="auth-form-section">
    <div class="auth-form-container">
        <h2>Connectez-vous à votre compte</h2>
        <?php echo $message_status; ?>
        <form action="login.php" method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="form-group">
                <label for="password">Mot de passe *</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-submit">Se connecter</button>
            <p class="auth-link">Pas encore de compte ? <a href="inscription.php">Inscrivez-vous ici</a>.</p>
            <p class="auth-link"><a href="#">Mot de passe oublié ?</a></p>
        </form>
    </div>
</section>

<?php
include_once 'includes/footer.php'; // Inclut le pied de page HTML
?>