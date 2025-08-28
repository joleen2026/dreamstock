<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

if (!isset($_GET['date'])) {
    die("❌ Date d'inventaire manquante.");
}

$date = $_GET['date'];

// Récupérer l'utilisateur
$stmt = $pdo->prepare("
    SELECT u.nom AS utilisateur
    FROM inventory i
    LEFT JOIN users u ON i.utilisateur_id = u.id
    WHERE i.date_inventaire = ?
    LIMIT 1
");
$stmt->execute([$date]);
$inventaire_info = $stmt->fetch();

if (!$inventaire_info) {
    die("📅 Aucun inventaire trouvé pour cette date.");
}

// Récupérer les lignes d’inventaire
$stmt = $pdo->prepare("
    SELECT i.*, p.nom AS produit
    FROM inventory i
    LEFT JOIN products p ON i.produit_id = p.id
    WHERE i.date_inventaire = ?
    ORDER BY p.nom
");
$stmt->execute([$date]);
$lignes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détail de l'inventaire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            📦 Détail de l'inventaire du <?= htmlspecialchars($date) ?> (par <?= htmlspecialchars($inventaire_info['utilisateur']) ?>)
        </div>
        <div class="card-body">
            <?php if (empty($lignes)): ?>
                <div class="alert alert-warning">Aucune donnée pour cette date.</div>
            <?php else: ?>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Produit</th>
                            <th>Stock théorique</th>
                            <th>Stock réel</th>
                            <th>Écart</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lignes as $ligne): ?>
                            <tr>
                                <td><?= htmlspecialchars($ligne['produit']) ?></td>
                                <td><?= $ligne['stock_theorique'] ?></td>
                                <td><?= $ligne['stock_reel'] ?></td>
                                <td class="<?= $ligne['ecart'] < 0 ? 'text-danger' : ($ligne['ecart'] > 0 ? 'text-success' : '') ?>">
                                    <?= $ligne['ecart'] ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>

                <a href="export_inventaire_pdf.php?date=<?= urlencode($date) ?>" class="btn btn-outline-primary">
                    📄 Exporter en PDF
                </a>
                <a href="export_inventaire_csv.php?date=<?= urlencode($date) ?>" class="btn btn-outline-success">
                    📊 Exporter en csv
                </a>
            <?php endif ?>
            <a href="liste_inventaires.php" class="btn btn-secondary mt-3">↩️ Retour</a>
        </div>
    </div>
</div>

</body>
</html>
