<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

$bons = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = $_POST['numero_bon'] ?? '';
    $date = $_POST['date_sortie'] ?? '';

    $query = "SELECT so.*, p.nom AS produit_nom, u.nom AS utilisateur_nom
              FROM stock_outputs so
              LEFT JOIN products p ON so.produit_id = p.id
              LEFT JOIN users u ON so.utilisateur_id = u.id
              WHERE 1 ";

    $params = [];

    if (!empty($numero)) {
        $query .= " AND so.numero_bon = ? ";
        $params[] = $numero;
    }

    if (!empty($date)) {
        $query .= " AND DATE(so.date_sortie) = ? ";
        $params[] = $date;
    }

    $query .= " ORDER BY so.date_sortie DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $bons = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des bons de sortie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            📜 Historique des bons de sortie
        </div>
        <div class="card-body">
            <form method="POST" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="numero_bon" class="form-label">Numéro de bon</label>
                    <input type="text" name="numero_bon" id="numero_bon" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="date_sortie" class="form-label">Date</label>
                    <input type="date" name="date_sortie" id="date_sortie" class="form-control">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">🔍 Rechercher</button>
                </div>
            </form>

            <?php if (!empty($bons)): ?>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>N° Bon</th>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Date</th>
                            <th>Utilisateur</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bons as $b): ?>
                            <tr>
                                <td><?= htmlspecialchars($b['numero_bon']) ?></td>
                                <td><?= htmlspecialchars($b['produit_nom']) ?></td>
                                <td><?= $b['quantite'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($b['date_sortie'])) ?></td>
                                <td><?= htmlspecialchars($b['utilisateur_nom']) ?></td>
                                <td>
                                    <a href="voir_bon_sortie.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-info">Voir</a>
									 <a href="supprimer_bon_sortie.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-info">🗑️</a>
                                    <a href="imprimer_bon_sortie.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-success">Imprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="alert alert-warning">Aucun bon de sortie trouvé pour les critères donnés.</div>
            <?php endif; ?>
        </div>
    </div>
	
</div>
<a href="bon_sortie.php" class="btn btn-outline-secondary">← Retour</a>
</body>
</html>
