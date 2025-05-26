<?php
// admin/admin_login.php
session_start(); // Démarre la session

require_once '../includes/db.php'; // Connexion à la BDD

$message_login = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $mot_de_passe = htmlspecialchars(trim($_POST['mot_de_passe'] ?? ''));

    if (empty($email) || empty($mot_de_passe)) {
        $message_login = "<p class='error-message'>Veuillez remplir tous les champs.</p>";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, nom_utilisateur, mot_de_passe, role FROM utilisateurs WHERE email = :email AND role = 'admin'");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
                // Connexion réussie
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nom_utilisateur'];
                $_SESSION['user_role'] = $user['role'];

                header('Location: dashboard.php'); // Redirige vers le tableau de bord
                exit;
            } else {
                $message_login = "<p class='error-message'>Email ou mot de passe incorrect.</p>";
            }
        } catch (PDOException $e) {
            $message_login = "<p class='error-message'>Une erreur est survenue lors de la connexion. Veuillez réessayer.</p>";
            error_log("Erreur connexion admin: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administrateur</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background-color: var(--primary-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: var(--white);
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container h1 {
            color: var(--primary-color);
            margin-bottom: 30px;
        }
        .login-form .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .login-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        .login-form input[type="email"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 1em;
        }
        .login-form .btn-submit {
            width: 100%;
            padding: 12px;
            font-size: 1.1em;
            margin-top: 20px;
        }
        .login-container .error-message, .login-container .success-message {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Connexion Administrateur</h1>
        <?php echo $message_login; ?>
        <form action="admin_login.php" method="POST" class="login-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
            </div>
            <button type="submit" class="btn btn-submit">Se connecter</button>
        </form>
        <p style="margin-top: 20px;"><a href="../index.php" style="color: var(--primary-color); text-decoration: none;">Retour au site</a></p>
    </div>
</body>
</html>