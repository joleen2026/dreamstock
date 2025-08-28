<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// 🔐 Sécurité : vérifier l'ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID invalide.");
}
$id = intval($_GET['id']);

// 🔁 Traitement du formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produit_id = $_POST['produit_id'];
    $quantite = $_POST['quantite'];
    $prix_unitaire = $_POST['prix_unitaire'];
    $fournisseur_id = $_POST['fournisseur_id'] ?: null;
    $motif = $_POST['motif'];
    $numero_bon = $_POST['numero_bon'];

    // Récupérer l’ancienne quantité pour corriger le stock
    $ancien = $pdo->prepare("SELECT quantite FROM stock_entries WHERE id = ?");
    $ancien->execute([$id]);
    $ancien_quantite = $ancien->fetchColumn();

    $diff = $quantite - $ancien_quantite;

    // 1. Mise à jour du bon
    $stmt = $pdo->prepare("UPDATE stock_entries SET 
        produit_id = ?, quantite = ?, prix_unitaire = ?, 
        motif = ?, fournisseur_id = ?, numero_bon = ?
        WHERE id = ?");
    $stmt->execute([$produit_id, $quantite, $prix_unitaire, $motif, $fournisseur_id, $numero_bon, $id]);

    // 2. Ajuster le stock du produit
    $pdo->prepare("UPDATE products SET stock_actuel = stock_actuel + ? WHERE id = ?")
        ->execute([$diff, $produit_id]);

    header("Location: voir_bon_entree.php?id=$id");
    exit;
}

// 📦 Récupérer les données du bon
$stmt = $pdo->prepare("SELECT * FROM stock_entries WHERE id = ?");
$stmt->execute([$id]);
$bon = $stmt->fetch();

if (!$bon) {
    die("Bon introuvable.");
}

// Récupérer produits et fournisseurs
$produits = $pdo->query("SELECT id, nom FROM products")->fetchAll();
$fournisseurs = $pdo->query("SELECT id, nom FROM suppliers")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le bon d’entrée</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card">
        <div class="card-header bg-primary text-white">
            ✏️ Modifier le bon d’entrée : <?= htmlspecialchars($bon['numero_bon']) ?>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="numero_bon" class="form-label">Numéro de bon</label>
                    <input type="text" name="numero_bon" id="numero_bon" class="form-control" required value="<?= htmlspecialchars($bon['numero_bon']) ?>">
                </div>

                <div class="mb-3">
                    <label for="produit_id" class="form-label">Produit</label>
                    <select name="produit_id" id="produit_id" class="form-select" required>
                        <?php foreach ($produits as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $p['id'] == $bon['produit_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="quantite" class="form-label">Quantité</label>
                    <input type="number" name="quantite" id="quantite" class="form-control" required value="<?= $bon['quantite'] ?>">
                </div>

                <div class="mb-3">
                    <label for="prix_unitaire" class="form-label">Prix unitaire</label>
                    <input type="number" step="0.01" name="prix_unitaire" id="prix_unitaire" class="form-control" value="<?= $bon['prix_unitaire'] ?>">
                </div>

                <div class="mb-3">
                    <label for="fournisseur_id" class="form-label">Fournisseur</label>
                    <select name="fournisseur_id" id="fournisseur_id" class="form-select">
                        <option value="">— Aucun —</option>
                        <?php foreach ($fournisseurs as $f): ?>
                            <option value="<?= $f['id'] ?>" <?= $f['id'] == $bon['fournisseur_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($f['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="motif" class="form-label">Motif</label>
                    <input type="text" name="motif" id="motif" class="form-control" value="<?= htmlspecialchars($bon['motif']) ?>">
                </div>

                <button type="submit" class="btn btn-primary">💾 Enregistrer les modifications</button>
                <a href="voir_bon_entree.php?id=<?= $bon['id'] ?>" class="btn btn-secondary">↩️ Annuler</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
