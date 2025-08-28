<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// Produits disponibles
$produits = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);

// Fournisseurs
$fournisseurs = $pdo->query("SELECT * FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);

// Générer numéro de bon
$numero_bon = "BE-" . strtoupper(uniqid());

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon d'entrée</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4">
    <h3 class="mb-4">📥 Bon d'entrée de stock</h3>

    <!-- ✅ Formulaire de saisie -->
    <form action="ajouter_entree.php" method="post" class="row g-3">
        <input type="hidden" name="numero_bon" value="<?= $numero_bon ?>">
        <div class="col-md-4">
            <label>Produit</label>
            <select name="produit_id" class="form-select" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($produits as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label>Quantité</label>
            <input type="number" name="quantite" class="form-control" required min="1">
        </div>
        <div class="col-md-2">
            <label>Prix unitaire (XAF)</label>
            <input type="number" name="prix_unitaire" class="form-control" step="0.01">
        </div>
        <div class="col-md-4">
            <label>Fournisseur</label>
            <select name="fournisseur_id" class="form-select">
                <option value="">-- Aucun --</option>
                <?php foreach ($fournisseurs as $f): ?>
                    <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label>Motif</label>
            <input type="text" name="motif" class="form-control" placeholder="Achat, retour client, don, etc.">
        </div>
        <div class="col-md-6">
            <label>Numéro de bon</label>
            <input type="text" class="form-control" value="<?= $numero_bon ?>" readonly>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success">✅ Enregistrer l'entrée</button>
			<a href="tableau_de_bord.php" class="btn btn-outline-secondary">← Retour au tableau de bord</a>
        </div>
		
    
  
    </form>
</div>
<hr class="my-5">

<div class="card">
    <div class="card-header bg-dark text-white">📑 Historique des bons d’entrée</div>
    <div class="card-body table-responsive">
        <?php
        $bons = $pdo->query("
            SELECT se.*, p.nom AS produit, u.nom AS utilisateur, f.nom AS fournisseur
            FROM stock_entries se
            LEFT JOIN products p ON se.produit_id = p.id
            LEFT JOIN users u ON se.utilisateur_id = u.id
            LEFT JOIN suppliers f ON se.fournisseur_id = f.id
            ORDER BY se.date_entree DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <table class="table table-bordered table-hover table-striped">
            <thead class="table-light">
                <tr>
                    <th>Numéro</th>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Fournisseur</th>
                    <th>Date</th>
                    <th>Utilisateur</th>
                    <th>Motif</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bons as $bon): ?>
                <tr>
                    <td><?= htmlspecialchars($bon['numero_bon']) ?></td>
                    <td><?= htmlspecialchars($bon['produit']) ?></td>
                    <td><?= $bon['quantite'] ?></td>
                    <td><?= $bon['fournisseur'] ?: '—' ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($bon['date_entree'])) ?></td>
                    <td><?= htmlspecialchars($bon['utilisateur']) ?></td>
                    <td><?= htmlspecialchars($bon['motif']) ?></td>
                    <td>
                        <a href="voir_bon_entree.php?id=<?= $bon['id'] ?>" class="btn btn-sm btn-info">👁️</a>
                        <a href="modifier_bon_entree.php?id=<?= $bon['id'] ?>" class="btn btn-sm btn-primary">✏️</a>
                        <a href="supprimer_bon_entree.php?id=<?= $bon['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce bon ?')">🗑️</a>
                        <a href="imprimer_bon_entree.php?id=<?= $bon['id'] ?>" class="btn btn-sm btn-success" target="_blank">🖨️</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
