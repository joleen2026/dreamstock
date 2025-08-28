<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// Vérification de l'ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Bon de sortie introuvable.";
    exit;
}

$id = intval($_GET['id']);

// Récupération des détails du bon de sortie
$stmt = $pdo->prepare("
    SELECT so.*, 
           p.nom AS produit_nom, 
           p.reference AS produit_ref, 
           p.unite,
           u.nom AS utilisateur_nom, 
           c.nom AS client_nom
    FROM stock_outputs so
    LEFT JOIN products p ON so.produit_id = p.id
    LEFT JOIN users u ON so.utilisateur_id = u.id
    LEFT JOIN clients c ON so.client_id = c.id
    WHERE so.id = ?
");
$stmt->execute([$id]);
$bon = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bon) {
    echo "Bon de sortie introuvable.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détail du bon de sortie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            🧾 Bon de sortie : <?= htmlspecialchars($bon['numero_bon']) ?>
        </div>
        <div class="card-body">
            <h5 class="card-title">Détails du bon</h5>
            <table class="table table-bordered">
                <tr>
                    <th>Produit</th>
                    <td><?= htmlspecialchars($bon['produit_nom']) ?> (<?= htmlspecialchars($bon['produit_ref']) ?>)</td>
                </tr>
                <tr>
                    <th>Quantité</th>
                    <td><?= $bon['quantite'] . ' ' . htmlspecialchars($bon['unite']) ?></td>
                </tr>
                <tr>
                    <th>Client</th>
                    <td><?= $bon['client_nom'] ?: '—' ?></td>
                </tr>
                <tr>
                    <th>Motif</th>
                    <td><?= htmlspecialchars($bon['motif']) ?></td>
                </tr>
                <tr>
                    <th>Date de sortie</th>
                    <td><?= date('d/m/Y H:i', strtotime($bon['date_sortie'])) ?></td>
                </tr>
                <tr>
                    <th>Utilisateur</th>
                    <td><?= htmlspecialchars($bon['utilisateur_nom']) ?></td>
                </tr>
            </table>

            <a href="historique_bons_sortie.php" class="btn btn-secondary">↩️ Retour</a>
            <a href="modifier_bon_sortie.php?id=<?= $bon['id'] ?>" class="btn btn-primary">✏️ Modifier</a>
            <a href="supprimer_bon_sortie.php?id=<?= $bon['id'] ?>" class="btn btn-danger" onclick="return confirm('Supprimer ce bon ?')">🗑️ Supprimer</a>
            <a href="imprimer_bon_sortie.php?id=<?= $bon['id'] ?>" class="btn btn-success" target="_blank">🖨️ Imprimer</a>
        </div>
    </div>
</div>
</body>
</html>
