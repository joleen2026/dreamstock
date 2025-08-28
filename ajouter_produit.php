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
    <title>Ajouter un produit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            🆕 Ajouter un produit
        </div>
        <div class="card-body">
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Nom du produit *</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Référence *</label>
                        <input type="text" name="reference" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Code-barres</label>
                    <input type="text" name="code_barres" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Stock actuel *</label>
                        <input type="number" name="stock_actuel" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Stock minimum *</label>
                        <input type="number" name="stock_minimum" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Unité *</label>
                        <input type="text" name="unite" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Prix unitaire *</label>
                        <input type="number" step="0.01" name="prix" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Catégorie *</label>
                        <select name="categorie_id" class="form-control" required>
                            <option value="">-- Sélectionner une catégorie --</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nom']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">💾 Enregistrer</button>
                <a href="produit.php" class="btn btn-secondary">↩️ Retour</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
