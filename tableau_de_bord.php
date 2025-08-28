<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// Statistiques
$totalProduits = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalEntrees = $pdo->query("SELECT SUM(quantite) FROM stock_entries")->fetchColumn() ?: 0;
$totalSorties = $pdo->query("SELECT SUM(quantite) FROM stock_outputs")->fetchColumn() ?: 0;
$totalUtilisateurs = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { background-color: #f2f2f2; padding-top: 70px; }
        .card { border-radius: 10px; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
        .menu-card { transition: transform 0.2s; }
        .menu-card:hover { transform: scale(1.05); }
        .navbar { box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<!-- HEADER FIXE -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="tableau_de_bord.php">
            <i class="fas fa-boxes"></i> DreamStock
        </a>
        <div class="ms-auto">
            <a href="activite_stocks.php" class="btn btn-warning me-2">
                <i class="fas fa-chart-pie"></i> Activité global de stocks
            </a>
            <a href="logout.php" class="btn btn-danger">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="alert alert-primary">
        <strong>Bienvenue, <?= htmlspecialchars($_SESSION['nom']) ?> 👋</strong>  
        (Rôle : <?= htmlspecialchars($_SESSION['role']) ?>)
    </div>

    <!-- Statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="card text-white bg-primary"><div class="card-body"><h5>📦 Produits</h5><p class="fs-4"><?= $totalProduits ?></p></div></div></div>
        <div class="col-md-3"><div class="card text-white bg-success"><div class="card-body"><h5>📥 Entrées</h5><p class="fs-4"><?= $totalEntrees ?></p></div></div></div>
        <div class="col-md-3"><div class="card text-white bg-danger"><div class="card-body"><h5>📤 Sorties</h5><p class="fs-4"><?= $totalSorties ?></p></div></div></div>
        <div class="col-md-3"><div class="card text-white bg-dark"><div class="card-body"><h5>👤 Utilisateurs</h5><p class="fs-4"><?= $totalUtilisateurs ?></p></div></div></div>
    </div>

    <!-- Boutons de navigation -->
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php
        $pages = [
            ['produits.php', '🛒 Gestion des produits'],
            ['cathegories.php', '🗂️ Catégories de produits'],
            ['fournisseur.php', '🚚 Gestion des fournisseurs'],
            ['client.php', '👥 Gestion des clients'],
            ['bon_entree.php', '➕ Entrées de stock'],
            ['bon_sortie.php', '➖ Sorties de stock'],
            ['dashboard_graphique.php', '📊 Statistiques graphiques'],
            ['inventaire.php', '📊 Inventaire'],
            ['alertes.php', '🚨 Alertes de seuil'],
            ['rapport.php', '📈 Rapports & Export'],
            ['utilisateur.php', '🔐 Gestion des utilisateurs'],
            ['journal.php', '📝 Journal des actions'],
            ['parametres.php', '⚙️ Paramètres'],
            ['logout.php', '🔓 Déconnexion']
        ];

        foreach ($pages as [$link, $title]) {
            echo "
            <div class='col'>
                <div class='card menu-card shadow text-center'>
                    <div class='card-body'>
                        <h5 class='card-title'>$title</h5>
                        <a href='$link' class='btn btn-outline-primary w-100 mt-2'>Accéder</a>
                    </div>
                </div>
            </div>";
        }
        ?>
    </div>

    <!-- Graphique -->
    <div class="mt-5">
        <h5>Graphique : Mouvements</h5>
        <canvas id="stockChart" height="100"></canvas>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
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
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            title: { display: true, text: 'Activité Globale de Stock' }
        }
    }
});
</script>
</body>
</html>



