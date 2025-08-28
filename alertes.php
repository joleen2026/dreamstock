<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// Sélection des produits en alerte (stock critique)
$stmt = $pdo->query("
    SELECT * FROM products
    WHERE stock_actuel <= stock_minimum
    ORDER BY stock_actuel ASC
");
$produits = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>🔔 Produits en alerte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            ⚠️ Produits en alerte de stock
        </div>
        <div class="card-body">
            <?php if (empty($produits)): ?>
                <div class="alert alert-success">
                    ✅ Aucun produit n’est actuellement en alerte.
                </div>
            <?php else: ?>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Nom</th>
                            <th>Stock actuel</th>
                            <th>Stock minimum</th>
                            <th>Unité</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produits as $p): ?>
                        <tr class="table-warning">
                            <td><?= htmlspecialchars($p['nom']) ?></td>
                            <td><?= $p['stock_actuel'] ?></td>
                            <td><?= $p['stock_minimum'] ?></td>
                            <td><?= htmlspecialchars($p['unite']) ?></td>
                            <td>
                                <a href="modifier_produit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary">🔧 Modifier</a>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif ?>
            <a href="tableau_de_bord.php" class="btn btn-secondary mt-3">↩️ Retour au tableau de bord</a>
        </div>
    </div>
</div>

</body>
</html>

