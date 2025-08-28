<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// Récupération produit
if (!isset($_GET['id'])) {
    header('Location: produits.php');
    exit;
}
$id = $_GET['id'];
$produit = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$produit->execute([$id]);
$p = $produit->fetch();

if (!$p) {
    echo "Produit introuvable.";
    exit;
}

// Traitement modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE products SET nom = ?, reference = ?, code_barres = ?, description = ?, stock_actuel = ?, stock_minimum = ?, unite = ?, prix = ?, categorie_id = ? WHERE id = ?");
    $stmt->execute([
        $_POST['nom'], $_POST['reference'], $_POST['code_barres'], $_POST['description'],
        $_POST['stock_actuel'], $_POST['stock_minimum'], $_POST['unite'], $_POST['prix'], $_POST['categorie_id'], $id
    ]);
    header("Location: produits.php");
    exit;
}

// Récupération catégories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un produit</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4">
    <h3>✏️ Modifier le produit</h3>

    <form method="post" class="row g-3">
        <div class="col-md-4">
            <label>Nom</label>
            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($p['nom']) ?>" required>
        </div>
        <div class="col-md-4">
            <label>Référence</label>
            <input type="text" name="reference" class="form-control" value="<?= htmlspecialchars($p['reference']) ?>" required>
        </div>
        <div class="col-md-4">
            <label>Code-barres</label>
            <input type="text" name="code_barres" class="form-control" value="<?= $p['code_barres'] ?>">
        </div>
        <div class="col-md-6">
            <label>Description</label>
            <input type="text" name="description" class="form-control" value="<?= $p['description'] ?>">
        </div>
        <div class="col-md-2">
            <label>Stock actuel</label>
            <input type="number" name="stock_actuel" class="form-control" value="<?= $p['stock_actuel'] ?>">
        </div>
        <div class="col-md-2">
            <label>Stock min</label>
            <input type="number" name="stock_minimum" class="form-control" value="<?= $p['stock_minimum'] ?>">
        </div>
        <div class="col-md-2">
            <label>Unité</label>
            <input type="text" name="unite" class="form-control" value="<?= $p['unite'] ?>">
        </div>
        <div class="col-md-2">
            <label>Prix</label>
            <input type="number" step="0.01" name="prix" class="form-control" value="<?= $p['prix'] ?>">
        </div>
        <div class="col-md-4">
            <label>Catégorie</label>
            <select name="categorie_id" class="form-select">
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $p['categorie_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12 mt-3">
            <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
            <a href="produits.php" class="btn btn-secondary">↩️ Retour</a>
        </div>
    </form>
</div>
</body>
</html>
