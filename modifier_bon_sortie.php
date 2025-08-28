<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

if (!isset($_GET['id'])) {
    die("ID du bon de sortie manquant.");
}

$id = intval($_GET['id']);
$erreur = '';
$success = false;

// Récupérer les données actuelles
$stmt = $pdo->prepare("SELECT * FROM stock_outputs WHERE id = ?");
$stmt->execute([$id]);
$bon = $stmt->fetch();

if (!$bon) {
    die("Bon de sortie introuvable.");
}

$produit_id = $bon['produit_id'];
$ancienne_quantite = $bon['quantite'];

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouvelle_quantite = intval($_POST['quantite']);
    $motif = $_POST['motif'];
    $client_id = !empty($_POST['client_id']) ? $_POST['client_id'] : null;

    $ecart = $nouvelle_quantite - $ancienne_quantite;

    // Vérifier stock disponible
    $produit = $pdo->prepare("SELECT stock_actuel FROM products WHERE id = ?");
    $produit->execute([$produit_id]);
    $stock_actuel = $produit->fetchColumn();

    if ($ecart > 0 && $ecart > $stock_actuel) {
        $erreur = "Stock insuffisant pour augmenter la quantité. Stock disponible : $stock_actuel";
    } else {
        // Mise à jour du bon
        $stmt = $pdo->prepare("UPDATE stock_outputs SET quantite = ?, motif = ?, client_id = ? WHERE id = ?");
        $stmt->execute([$nouvelle_quantite, $motif, $client_id, $id]);

        // Mise à jour du stock produit
        $pdo->prepare("UPDATE products SET stock_actuel = stock_actuel - ? WHERE id = ?")
            ->execute([$ecart, $produit_id]);

        $success = true;

        // Rafraîchir les données
        $stmt = $pdo->prepare("SELECT * FROM stock_outputs WHERE id = ?");
        $stmt->execute([$id]);
        $bon = $stmt->fetch();
    }
}

// Récupération des clients
$clients = $pdo->query("SELECT id, nom FROM clients")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le bon de sortie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-warning">
            ✏️ Modifier le bon de sortie : <?= htmlspecialchars($bon['numero_bon']) ?>
        </div>
        <div class="card-body">
            <?php if ($erreur): ?>
                <div class="alert alert-danger"><?= $erreur ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success">Modifications enregistrées ✅</div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Quantité</label>
                    <input type="number" name="quantite" value="<?= $bon['quantite'] ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Motif</label>
                    <input type="text" name="motif" value="<?= htmlspecialchars($bon['motif']) ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Client (optionnel)</label>
                    <select name="client_id" class="form-select">
                        <option value="">-- Aucun --</option>
                        <?php foreach ($clients as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $c['id'] == $bon['client_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
                <a href="voir_bon_sortie.php?id=<?= $bon['id'] ?>" class="btn btn-secondary">↩️ Retour</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
