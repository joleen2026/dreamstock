<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// 1. Produits en rupture
$rupture_stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE stock_actuel <= stock_minimum");
$produits_en_rupture = $rupture_stmt->fetchColumn();

// 2. Produits les plus sortis
$plus_sortis_stmt = $pdo->query("
    SELECT p.nom, SUM(s.quantite) AS total_sortie
    FROM stock_outputs s
    JOIN products p ON p.id = s.produit_id
    GROUP BY s.produit_id
    ORDER BY total_sortie DESC
    LIMIT 5
");
$plus_sortis = $plus_sortis_stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Stock total en valeur
$valeur_stmt = $pdo->query("SELECT SUM(stock_actuel * prix) FROM products");
$stock_total_valeur = $valeur_stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>📈 Tableau de bord graphique</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">

  <h2 class="mb-4">📈 Statistiques de Stock</h2>

  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card text-white bg-danger mb-3">
        <div class="card-body">
          <h5 class="card-title">Produits en rupture</h5>
          <p class="card-text display-6"><?= $produits_en_rupture ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-white bg-success mb-3">
        <div class="card-body">
          <h5 class="card-title">Valeur totale du stock</h5>
          <p class="card-text display-6"><?= number_format($stock_total_valeur, 0, ',', ' ') ?> FCFA</p>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <canvas id="chartBar"></canvas>
    </div>
    <div class="col-md-6">
      <canvas id="chartPie"></canvas>
    </div>
  </div>

</div>

<script>
const labels = <?= json_encode(array_column($plus_sortis, 'nom')) ?>;
const data = <?= json_encode(array_column($plus_sortis, 'total_sortie')) ?>;
 
new Chart(document.getElementById('chartBar'), {
  type: 'bar',
  data: {
    labels: labels,
    datasets: [{
      label: 'Produits les plus sortis',
      data: data,
      backgroundColor: 'rgba(54, 162, 235, 0.7)'
    }]
  }
});

new Chart(document.getElementById('chartPie'), {
  type: 'pie',
  data: {
    labels: labels,
    datasets: [{
      label: 'Répartition des sorties',
      data: data,
      backgroundColor: ['#FF6384','#36A2EB','#FFCE56','#4BC0C0','#9966FF']
    }]
  }
});
</script>
</body>
</html>
