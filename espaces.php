<?php
// espaces.php
require_once 'includes/db.php';
include_once 'includes/header.php';

// Récupération des paramètres de recherche et filtrage
$recherche = $_GET['recherche'] ?? '';
$categorie_id = $_GET['categorie'] ?? '';

// Construction de la requête SQL de base
$sql = "SELECT e.id, e.nom, e.image_principale, e.capacite, e.prix, c.nom_categorie 
        FROM espaces e 
        JOIN categories c ON e.id_categorie = c.id 
        WHERE e.actif = TRUE";
$params = []; // Tableau pour les paramètres des requêtes préparées

if (!empty($recherche)) {
    $sql .= " AND (e.nom LIKE :recherche OR e.description LIKE :recherche OR e.adresse LIKE :recherche)";
    $params[':recherche'] = '%' . $recherche . '%';
}
if (!empty($categorie_id)) {
    $sql .= " AND e.id_categorie = :categorie_id";
    $params[':categorie_id'] = $categorie_id;
}

$sql .= " ORDER BY e.date_ajout DESC"; // Ordre par défaut

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $espaces = $stmt->fetchAll(); // Récupère tous les résultats
} catch (PDOException $e) {
    echo "<p>Erreur lors du chargement des espaces. Veuillez réessayer plus tard.</p>";
    error_log("Erreur chargement espaces: " . $e->getMessage());
    $espaces = []; // Assure que $espaces est un tableau vide en cas d'erreur
}
?>

<h1 class="page-title">Nos Espaces Événementiels</h1>

<section class="filters-section">
    <form action="espaces.php" method="GET" class="filter-form">
        <input type="text" name="recherche" placeholder="Rechercher par nom, description, adresse..." value="<?php echo htmlspecialchars($recherche); ?>">
        <select name="categorie">
            <option value="">Toutes les catégories</option>
            <?php
            try {
                $stmt_cat = $pdo->query("SELECT id, nom_categorie FROM categories ORDER BY nom_categorie ASC");
                while ($cat = $stmt_cat->fetch()) {
                    $selected = ($cat['id'] == $categorie_id) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($cat['id']) . "' $selected>" . htmlspecialchars($cat['nom_categorie']) . "</option>";
                }
            } catch (PDOException $e) {
                error_log("Erreur chargement catégories filtres: " . $e->getMessage());
            }
            ?>
        </select>
        <button type="submit" class="btn btn-filter">Appliquer les filtres</button>
        <a href="espaces.php" class="btn btn-reset">Réinitialiser</a>
    </form>
</section>

<section class="espaces-listing">
    <?php if (!empty($espaces)): ?>
        <div class="espaces-grid">
            <?php foreach ($espaces as $espace): ?>
                <div class="espace-card">
                    <img src="assets/images/<?php echo htmlspecialchars($espace['image_principale']); ?>" alt="<?php echo htmlspecialchars($espace['nom']); ?>" class="espace-image">
                    <div class="espace-info">
                        <h3><?php echo htmlspecialchars($espace['nom']); ?></h3>
                        <p class="category-tag"><?php echo htmlspecialchars($espace['nom_categorie']); ?></p>
                        <p>Capacité : **<?php echo htmlspecialchars($espace['capacite']); ?>** personnes</p>
                        <p class="price"><?php echo !empty($espace['prix']) ? htmlspecialchars($espace['prix']) : 'Sur devis'; ?></p>
                        <a href="detail_espace.php?id=<?php echo htmlspecialchars($espace['id']); ?>" class="btn btn-details">Voir les détails</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="no-results">Aucun espace trouvé pour votre recherche. Essayez d'autres critères !</p>
    <?php endif; ?>
</section>

<?php
include_once 'includes/footer.php';
?>