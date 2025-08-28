<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

if (!isset($_GET['id'])) {
    die("ID du fournisseur manquant.");
}

$id = intval($_GET['id']);

// Récupérer les informations du fournisseur
$stmt = $pdo->prepare("SELECT * FROM suppliers WHERE id = ?");
$stmt->execute([$id]);
$fournisseur = $stmt->fetch();

if (!$fournisseur) {
    die("Fournisseur introuvable.");
}

// Récupérer les bons d'entrée liés à ce fournisseur
$stmt = $pdo->prepare("
    SELECT se.*, u.nom AS utilisateur
    FROM stock_entries se
    LEFT JOIN users u ON se.utilisateur_id = u.id
    WHERE se.fournisseur_id = ?
    ORDER BY se.date_entree DESC
");
$stmt->execute([$id]);
$bons = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique du fournisseur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            📄 Historique des livraisons du fournisseur : <strong><?= htmlspecialchars($fournisseur['nom']) ?></strong>
        </div>
        <div class="card-body">

            <?php if (empty($bons)) : ?>
                <div class="alert alert-warning">Aucun bon d’entrée enregistré pour ce fournisseur.</div>
            <?php else : ?>
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Numéro du bon</th>
                            <th>Date</th>
                            <th>Utilisateur</th>
                            <th>Motif</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bons as $bon) : ?>
                            <tr>
                                <td><?= htmlspecialchars($bon['numero_bon']) ?></td>
                                <td><?= date('d/m/Y', strtotime($bon['date_creation'])) ?></td>
                                <td><?= htmlspecialchars($bon['utilisateur']) ?></td>
                                <td><?= htmlspecialchars($bon['motif']) ?></td>
                                <td>
                                    <a href="voir_bon_entree.php?id=<?= $bon['id'] ?>" class="btn btn-sm btn-primary">Voir</a>
                                    <a href="imprimer_bon_entree.php?id=<?= $bon['id'] ?>" class="btn btn-sm btn-secondary">Imprimer</a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <a href="fournisseur.php" class="btn btn-secondary mt-3">↩️ Retour</a>

        </div>
    </div>
</div>

</body>
</html>
