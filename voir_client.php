<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

if (!isset($_GET['id'])) {
    die("ID du client manquant.");
}

$id = intval($_GET['id']);

// Récupérer les infos du client
$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch();

if (!$client) {
    die("Client introuvable.");
}

// Récupérer les bons de sortie du client
$stmt = $pdo->prepare("
    SELECT so.*, p.nom AS produit
    FROM stock_outputs so
    LEFT JOIN products p ON so.produit_id = p.id
    WHERE so.client_id = ?
    ORDER BY so.date_sortie DESC
");
$stmt->execute([$id]);
$bons = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            👤 Détails du client : <?= htmlspecialchars($client['nom']) ?>
        </div>
        <div class="card-body">
            <p><strong>Téléphone :</strong> <?= htmlspecialchars($client['telephone']) ?></p>
            <p><strong>Email :</strong> <?= htmlspecialchars($client['email']) ?></p>
            <p><strong>Adresse :</strong> <?= htmlspecialchars($client['adresse']) ?></p>

            <hr>
            <h5>📦 Historique des bons de sortie</h5>

            <?php if (empty($bons)) : ?>
                <div class="alert alert-warning">Aucun bon de sortie lié à ce client.</div>
            <?php else : ?>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Date de sortie</th>
                            <th>Motif</th>
                            <th>Utilisateur</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bons as $bon) : ?>
                            <tr>
                                <td><?= $bon['id'] ?></td>
                                <td><?= htmlspecialchars($bon['produit']) ?></td>
                                <td><?= $bon['quantite'] ?></td>
                                <td><?= $bon['date_sortie'] ?></td>
                                <td><?= htmlspecialchars($bon['motif']) ?></td>
                                <td><?= htmlspecialchars($bon['utilisateur_id']) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <a href="client.php" class="btn btn-secondary mt-3">↩️ Retour</a>
        </div>
    </div>
</div>
</body>
</html>
