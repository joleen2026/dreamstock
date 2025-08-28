
<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $reference = $_POST['reference'];
    $code_barres = $_POST['code_barres'];
    $description = $_POST['description'];
    $stock_actuel = $_POST['stock_actuel'];
    $stock_minimum = $_POST['stock_minimum'];
    $unite = $_POST['unite'];
    $prix = $_POST['prix'];
    $categorie_id = $_POST['categorie_id'];

    if (!empty($nom) && !empty($reference)) {
        $stmt = $pdo->prepare("INSERT INTO products (nom, reference, code_barres, description, stock_actuel, stock_minimum, unite, prix, categorie_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $reference, $code_barres, $description, $stock_actuel, $stock_minimum, $unite, $prix, $categorie_id]);

        $success = "✅ Produit ajouté avec succès.";
    } else {
        $error = "❌ Le nom et la référence sont obligatoires.";
    }
}

// Récupérer les catégories pour le menu déroulant
$categories = $pdo->query("SELECT id, nom FROM categories ORDER BY nom")->fetchAll();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Produits</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4">
    <h3 class="mb-4">🛒 Gestion des Produits</h3>

    <!-- ✅ Formulaire d'ajout de produit -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">➕ Ajouter un produit</div>
        <div class="card-body">
            <form action="" method="post">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Nom</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Référence</label>
                        <input type="text" name="reference" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Code-barres</label>
                        <input type="text" name="code_barres" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Stock initial</label>
                        <input type="number" name="stock_actuel" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Stock min</label>
                        <input type="number" name="stock_minimum" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Unité</label>
                        <input type="text" name="unite" class="form-control" placeholder="ex: pièce">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Prix</label>
                        <input type="number" step="0.01" name="prix" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Catégorie</label>
                        <select name="categorie_id" class="form-select" required>
                            <option value="">-- Choisir une catégorie --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-success">✅ Ajouter le produit</button>  
                </div>
            </form>
        </div>
    </div>
<!-- ✅ Liste des produits -->
<div class="card">
    <div class="card-header bg-dark text-white">📋 Liste des produits</div>
    <div class="card-body table-responsive">
        <?php
        $produits = $pdo->query("
            SELECT p.*, c.nom AS categorie
            FROM products p
            LEFT JOIN categories c ON p.categorie_id = c.id
            ORDER BY p.id DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <table class="table table-bordered table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>Nom</th>
                    <th>Réf.</th>
                    <th>Code-barres</th>
                    <th>Stock</th>
                    <th>Min</th>
                    <th>Unité</th>
                    <th>Prix (XAF)</th>
                    <th>Catégorie</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($produits as $prod): ?>
                <tr class="<?= $prod['stock_actuel'] <= $prod['stock_minimum'] ? 'table-danger' : '' ?>">
                    <td><?= htmlspecialchars($prod['nom']) ?></td>
                    <td><?= htmlspecialchars($prod['reference']) ?></td>
                    <td><?= htmlspecialchars($prod['code_barres']) ?></td>
                    <td><?= $prod['stock_actuel'] ?></td>
                    <td><?= $prod['stock_minimum'] ?></td>
                    <td><?= htmlspecialchars($prod['unite']) ?></td>
                    <td><?= number_format($prod['prix'], 0, ',', ' ') ?></td>
                    <td><?= htmlspecialchars($prod['categorie']) ?></td>
                    <td>
                        <a href="modifier_produit.php?id=<?= $prod['id'] ?>" class="btn btn-sm btn-primary">✏️</a>
                        <a href="supprimer_produit.php?id=<?= $prod['id'] ?>" onclick="return confirm('Supprimer ce produit ?')" class="btn btn-sm btn-danger">🗑️</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
