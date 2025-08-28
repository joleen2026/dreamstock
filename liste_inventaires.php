<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// Récupération de tous les inventaires groupés par date et utilisateur
$stmt = $pdo->query("
    SELECT i.date_inventaire, u.nom AS utilisateur, COUNT(*) AS nb_lignes
    FROM inventory i
    LEFT JOIN users u ON i.utilisateur_id = u.id
    GROUP BY i.date_inventaire, i.utilisateur_id
    ORDER BY i.date_inventaire DESC
");
$inventaires = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des inventaires</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            📋 Historique des inventaires
        </div>
        <div class="card-body">
            <?php if (empty($inventaires)): ?>
                <div class="alert alert-warning">Aucun inventaire trouvé.</div>
            <?php else: ?>
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Utilisateur</th>
                            <th>Nombre de produits</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventaires as $inv): ?>
                            <tr>
                                <td><?= $inv['date_inventaire'] ?></td>
                                <td><?= htmlspecialchars($inv['utilisateur']) ?></td>
                                <td><?= $inv['nb_lignes'] ?></td>
                                <td>
                                    <a href="voir_inventaire.php?date=<?= urlencode($inv['date_inventaire']) ?>" class="btn btn-sm btn-info">
                                        🔍 Voir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif ?>
            <a href="inventaire.php" class="btn btn-secondary mt-3">↩️ Retour à l'inventaire</a>
        </div>
    </div>
</div>

</body>
</html>
