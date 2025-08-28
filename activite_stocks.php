<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// Récupération des totaux
$totalEntrees = $pdo->query("SELECT SUM(quantite) FROM stock_entries")->fetchColumn() ?: 0;
$totalSorties = $pdo->query("SELECT SUM(quantite) FROM stock_outputs")->fetchColumn() ?: 0;

// Derniers mouvements
$mouvements = $pdo->query("
    SELECT 'Entrée' AS type, produit_id, quantite, date_entree AS date_mvt 
    FROM stock_entries
    UNION ALL
    SELECT 'Sortie' AS type, produit_id, quantite, date_sortie AS date_mvt
    FROM stock_outputs
    ORDER BY date_mvt DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Activité des Stocks</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<div class="container mt-4">
    <h3>📊 Activité des Stocks</h3>
    <canvas id="stockChart" height="100"></canvas>

    <h4 class="mt-5">Derniers Mouvements</h4>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Type</th>
                <th>ID Produit</th>
                <th>Quantité</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mouvements as $mvt): ?>
            <tr>
                <td><?= $mvt['type'] ?></td>
                <td><?= $mvt['produit_id'] ?></td>
                <td><?= $mvt['quantite'] ?></td>
                <td><?= $mvt['date_mvt'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
const ctx = document.getElementById('stockChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Entrées', 'Sorties'],
        datasets: [{
            label: 'Mouvements de stock',
            data: [<?= $totalEntrees ?>, <?= $totalSorties ?>],
            backgroundColor: ['#198754', '#dc3545']
        }]
    },
    options: { responsive: true }
});
</script>
</body>
</html>
