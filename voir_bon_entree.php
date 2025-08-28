<?php
// Activer les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/session.php';
require_once 'includes/db.php';

// Vérifier que l'ID est passé
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Bon d'entrée introuvable.";
    exit;
}

$id = $_GET['id'];

// Récupérer les détails du bon d'entrée
$stmt = $pdo->prepare("
    SELECT se.*, 
           p.nom AS produit_nom, 
           p.reference AS produit_ref, 
           u.nom AS utilisateur_nom, 
           f.nom AS fournisseur_nom
    FROM stock_entries se
    LEFT JOIN products p ON se.produit_id = p.id
    LEFT JOIN users u ON se.utilisateur_id = u.id
    LEFT JOIN suppliers f ON se.fournisseur_id = f.id
    WHERE se.id = ?
");
$stmt->execute([$id]);
$bon = $stmt->fetch(PDO::FETCH_ASSOC);

// Si rien trouvé
if (!$bon) {
    echo "Bon d'entrée introuvable.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détail du bon d’entrée</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            📋 Bon d’entrée : <?= htmlspecialchars($bon['numero_bon']) ?>
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
                    <td><?= $bon['quantite'] ?></td>
                </tr>
                <tr>
                    <th>Prix unitaire</th>
                    <td><?= number_format($bon['prix_unitaire'], 0, ',', ' ') ?> XAF</td>
                </tr>
                <tr>
                    <th>Fournisseur</th>
                    <td><?= $bon['fournisseur_nom'] ?: '—' ?></td>
                </tr>
                <tr>
                    <th>Motif</th>
                    <td><?= htmlspecialchars($bon['motif']) ?></td>
                </tr>
                <tr>
                    <th>Date d’entrée</th>
                    <td><?= date('d/m/Y H:i', strtotime($bon['date_entree'])) ?></td>
                </tr>
                <tr>
                    <th>Utilisateur</th>
                    <td><?= htmlspecialchars($bon['utilisateur_nom']) ?></td>
                </tr>
            </table>

            <a href="bon_entree.php" class="btn btn-secondary">↩️ Retour</a>
            <a href="modifier_bon_entree.php?id=<?= $bon['id'] ?>" class="btn btn-primary">✏️ Modifier</a>
            <a href="supprimer_bon_entree.php?id=<?= $bon['id'] ?>" class="btn btn-danger" onclick="return confirm('Supprimer ce bon ?')">🗑️ Supprimer</a>
            <a href="imprimer_bon_entree.php?id=<?= $bon['id'] ?>" class="btn btn-success" target="_blank">🖨️ Imprimer</a>
        </div>
    </div>
</div>
</body>
</html>

