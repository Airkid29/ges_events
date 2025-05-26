<?php
// modifier_profil.php
session_start(); // Démarre la session

require_once 'includes/db.php'; // Inclut la connexion à la BDD

// Vérifie si l'utilisateur est connecté et n'est pas un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] === 'admin') {
    header('Location: login.php'); // Redirige vers la page de connexion
    exit;
}

$user_id = $_SESSION['user_id'];
$message_status = '';
$email_current = '';
$nom_current = ''; // Supposons que vous avez un champ 'nom'

// Récupérer les données actuelles de l'utilisateur pour pré-remplir le formulaire
try {
    $stmt = $pdo->prepare("SELECT email, nom FROM users WHERE id = :user_id LIMIT 1");
    $stmt->execute([':user_id' => $user_id]);
    $user_data = $stmt->fetch();

    if ($user_data) {
        $email_current = htmlspecialchars($user_data['email']);
        $nom_current = htmlspecialchars($user_data['nom']); // Si vous avez un champ 'nom'
    } else {
        $message_status = "<p class='error-message'>Erreur : Impossible de charger les données du profil.</p>";
    }
} catch (PDOException $e) {
    $message_status = "<p class='error-message'>Une erreur est survenue lors de la récupération des données.</p>";
    error_log("Error fetching user data for profile edit: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $new_nom = htmlspecialchars(trim($_POST['nom'] ?? '')); // Si vous avez un champ 'nom'
    $old_password = htmlspecialchars(trim($_POST['old_password'] ?? ''));
    $new_password = htmlspecialchars(trim($_POST['new_password'] ?? ''));
    $confirm_new_password = htmlspecialchars(trim($_POST['confirm_new_password'] ?? ''));

    $errors = [];

    // Validation de l'email
    if (empty($new_email) || !filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Veuillez entrer une adresse email valide.";
    } elseif ($new_email !== $email_current) {
        // Vérifier si le nouvel email est déjà pris par un autre utilisateur
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :user_id LIMIT 1");
        $stmt->execute([':email' => $new_email, ':user_id' => $user_id]);
        if ($stmt->fetch()) {
            $errors[] = "Cet email est déjà utilisé par un autre compte.";
        }
    }

    // Validation du mot de passe
    if (!empty($new_password)) {
        if (empty($old_password)) {
            $errors[] = "Veuillez entrer votre ancien mot de passe pour le modifier.";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "Le nouveau mot de passe doit contenir au moins 6 caractères.";
        } elseif ($new_password !== $confirm_new_password) {
            $errors[] = "Le nouveau mot de passe et sa confirmation ne correspondent pas.";
        } else {
            // Vérifier l'ancien mot de passe
            $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = :user_id LIMIT 1");
            $stmt->execute([':user_id' => $user_id]);
            $user_pass_data = $stmt->fetch();

            if (!$user_pass_data || !password_verify($old_password, $user_pass_data['password_hash'])) {
                $errors[] = "L'ancien mot de passe est incorrect.";
            }
        }
    }

    if (empty($errors)) {
        try {
            $update_fields = [];
            $update_params = [];

            // Mettre à jour l'email si différent
            if ($new_email !== $email_current) {
                $update_fields[] = "email = :email";
                $update_params[':email'] = $new_email;
                $_SESSION['user_email'] = $new_email; // Mettre à jour la session
            }
            // Mettre à jour le nom si différent (si le champ existe)
            if ($new_nom !== $nom_current) {
                 $update_fields[] = "nom = :nom";
                 $update_params[':nom'] = $new_nom;
            }

            // Mettre à jour le mot de passe si un nouveau est fourni
            if (!empty($new_password) && empty($errors)) { // S'assurer qu'il n'y a pas eu d'erreurs sur le mot de passe
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_fields[] = "password_hash = :password_hash";
                $update_params[':password_hash'] = $new_password_hash;
            }

            if (!empty($update_fields)) {
                $sql = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id = :user_id";
                $update_params[':user_id'] = $user_id;

                $stmt = $pdo->prepare($sql);
                $stmt->execute($update_params);

                $message_status = "<p class='success-message'>Votre profil a été mis à jour avec succès !</p>";
                // Recharger les données courantes après la mise à jour
                $email_current = $new_email;
                $nom_current = $new_nom;
            } else {
                $message_status = "<p class='info-message'>Aucune modification détectée.</p>";
            }

        } catch (PDOException $e) {
            $message_status = "<p class='error-message'>Une erreur est survenue lors de la mise à jour du profil. Veuillez réessayer.</p>";
            error_log("Profile update error: " . $e->getMessage());
        }
    } else {
        $message_status = implode('<br>', array_map(function($err) { return "<p class='error-message'>$err</p>"; }, $errors));
    }
}

// Inclure l'en-tête HTML après la logique de redirection/traitement
include_once 'includes/header.php';
?>

<h1 class="page-title">Modifier Mon Profil</h1>

<section class="profile-edit-section">
    <div class="container">
        <h2>Mettre à jour vos informations</h2>
        <?php echo $message_status; ?>

        <form action="modifier_profil.php" method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required value="<?php echo $email_current; ?>">
            </div>

            <div class="form-group">
                <label for="nom">Nom / Prénom</label>
                <input type="text" id="nom" name="nom" value="<?php echo $nom_current; ?>">
            </div>

            <hr>
            <h3>Changer le mot de passe (optionnel)</h3>
            <div class="form-group">
                <label for="old_password">Ancien mot de passe</label>
                <input type="password" id="old_password" name="old_password">
                <small>Requis si vous souhaitez changer votre mot de passe.</small>
            </div>
            <div class="form-group">
                <label for="new_password">Nouveau mot de passe</label>
                <input type="password" id="new_password" name="new_password">
                <small>Minimum 6 caractères.</small>
            </div>
            <div class="form-group">
                <label for="confirm_new_password">Confirmer le nouveau mot de passe</label>
                <input type="password" id="confirm_new_password" name="confirm_new_password">
            </div>

            <button type="submit" class="btn btn-submit">Enregistrer les modifications</button>
            <p class="auth-link"><a href="profil.php">Retour au profil</a></p>
        </form>
    </div>
</section>

<?php
include_once 'includes/footer.php';
?>