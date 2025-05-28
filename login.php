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
    .auth-form input[type="password"] {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 1rem;
        background: #f9fafb;
        transition: border-color 0.2s;
    }

    .auth-form input:focus {
        border-color: #4f8cff;
        outline: none;
        background: #fff;
    }

    .btn.btn-submit {
        width: 100%;
        padding: 12px 0;
        background: linear-gradient(90deg,rgb(143, 35, 168) 0%, #1e90ff 100%);
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        margin-top: 8px;
        margin-bottom: 8px;
    }

    .btn.btn-submit:hover {
        background: linear-gradient(90deg,rgb(10, 35, 79) 0%,rgb(146, 70, 196) 100%);
    }

    .auth-link {
        text-align: center;
        margin-top: 10px;
        font-size: 0.97rem;
    }

    .auth-link a {
        color: #1e90ff;
        text-decoration: none;
        transition: text-decoration 0.2s;
    }

    .auth-link a:hover {
        text-decoration: underline;
    }

    .error-message {
        color: #e74c3c;
        background: #ffeaea;
        border: 1px solid #f5c6cb;
        border-radius: 5px;
        padding: 10px 14px;
        margin-bottom: 18px;
        text-align: center;
        font-size: 1rem;
    }
</style>
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