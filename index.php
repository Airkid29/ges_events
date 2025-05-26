<?php
// index.php
require_once 'includes/db.php'; // Inclut la connexion à la BDD
include_once 'includes/header.php'; // Inclut l'en-tête HTML
?>

<section class="hero-section">
    <div class="hero-content">
        <h1>Trouvez l'espace parfait pour votre événement</h1>
        <p>Mariages, conférences, anniversaires, ou réunions d'affaires – votre lieu idéal vous attend.</p>

        <form action="espaces.php" method="GET" class="search-bar">
            <input type="text" name="recherche" placeholder="Rechercher un espace (ex: salle de mariage, jardin)..." class="search-input">
            <select name="categorie" class="category-select">
                <option value="">Toutes les catégories</option>
                <?php
                try {
                    $stmt = $pdo->query("SELECT id, nom_categorie FROM categories ORDER BY nom_categorie ASC");
                    while ($row = $stmt->fetch()) {
                        echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nom_categorie']) . "</option>";
                    }
                } catch (PDOException $e) {
                    echo "<option value=''>Erreur de chargement des catégories</option>";
                    error_log("Erreur chargement catégories Index: " . $e->getMessage());
                }
                ?>
            </select>
            <button type="submit" class="search-button">Rechercher</button>
        </form>
    </div>
</section>

<section class="how-it-works-brief">
    <h2>Comment ça marche ?</h2>
    <div class="steps-grid">
        <div class="step-item">
            <img src="assets/images/searchh.png" alt="Rechercher">
            <h3>1. Recherchez</h3>
            <p>Utilisez nos filtres pour trouver l'espace idéal qui correspond à vos besoins.</p>
        </div>
        <div class="step-item">
            <img src="assets/images/call.png" alt="Contacter">
            <h3>2. Contactez</h3>
            <p>Envoyez une demande de devis ou de visite directement au propriétaire.</p>
        </div>
        <div class="step-item">
            <img src="assets/images/reserv.png" alt="Réserver">
            <h3>3. Réservez</h3>
            <p>Confirmez votre réservation et préparez votre événement inoubliable !</p>
        </div>
    </div>
    <div class="button-center">
        <a href="comment_ca_marche.php" class="btn btn-primary">En savoir plus</a>
    </div>
</section>

<section class="categories-section">
    <h2>Découvrez par Catégorie</h2>
    <div class="categories-grid">
        <?php
        try {
            $stmt = $pdo->query("SELECT id, nom_categorie, icone FROM categories ORDER BY nom_categorie ASC");
            while ($category = $stmt->fetch()):
        ?>
            <a href="espaces.php?categorie=<?php echo htmlspecialchars($category['id']); ?>" class="category-card">
                <?php if (!empty($category['icone'])): ?>
                    <img src="assets/images/<?php echo htmlspecialchars($category['icone']); ?>" alt="Icône <?php echo htmlspecialchars($category['nom_categorie']); ?>">
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($category['nom_categorie']); ?></h3>
            </a>
        <?php
            endwhile;
        } catch (PDOException $e) {
            echo "<p>Impossible de charger les catégories pour le moment.</p>";
            error_log("Erreur chargement catégories: " . $e->getMessage());
        }
        ?>
    </div>
</section>

<section class="featured-spaces-section">
    <h2>Nos Espaces Recommandés</h2>
    <div class="espaces-grid">
        <?php
        try {
            // Sélectionne quelques espaces actifs aléatoires
            $stmt = $pdo->query("SELECT id, nom, image_principale, capacite, prix FROM espaces WHERE actif = TRUE ORDER BY RAND() LIMIT 4");
            if ($stmt->rowCount() > 0):
                while ($espace = $stmt->fetch()):
        ?>
            <div class="espace-card">
                <img src="assets/images/<?php echo htmlspecialchars($espace['image_principale']); ?>" alt="<?php echo htmlspecialchars($espace['nom']); ?>" class="espace-image">
                <div class="espace-info">
                    <h3><?php echo htmlspecialchars($espace['nom']); ?></h3>
                    <p>Capacité : <?php echo htmlspecialchars($espace['capacite']); ?> personnes</p>
                    <p class="price"><?php echo !empty($espace['prix']) ? htmlspecialchars($espace['prix']) : 'Sur devis'; ?></p>
                    <a href="detail_espace.php?id=<?php echo htmlspecialchars($espace['id']); ?>" class="btn-details">Voir les détails</a>
                </div>
            </div>
        <?php
                endwhile;
            else:
                echo "<p>Aucun espace recommandé pour le moment. Revenez bientôt !</p>";
            endif;
        } catch (PDOException $e) {
            echo "<p>Impossible de charger les espaces recommandés pour le moment.</p>";
            error_log("Erreur chargement espaces recommandés: " . $e->getMessage());
        }
        ?>
    </div>
    <div class="button-center">
        <a href="espaces.php" class="btn btn-primary">Voir tous les espaces</a>
    </div>
</section>

<?php
include_once 'includes/footer.php'; // Inclut le pied de page HTML
?>