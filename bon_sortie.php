<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// Générer un numéro de bon unique
function genererNumeroBonSortie() {
    return 'BS' . date('YmdHis') . rand(100, 999);
}

$erreur = '';
$success = false;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produit_id = $_POST['produit_id'];
    $quantite = intval($_POST['quantite']);
    $motif = $_POST['motif'];
    $client_id = !empty($_POST['client_id']) ? $_POST['client_id'] : null;
    $utilisateur_id = $_SESSION['user_id'];
    $numero_bon = genererNumeroBonSortie();

    // Vérifier le stock disponible
    $stmt = $pdo->prepare("SELECT stock_actuel FROM products WHERE id = ?");
    $stmt->execute([$produit_id]);
    $stock_dispo = $stmt->fetchColumn();

    if ($quantite > $stock_dispo) {
        $erreur = "Stock insuffisant. Stock actuel : $stock_dispo.";
    } else {
        // Insérer dans la table des sorties
        $stmt = $pdo->prepare("INSERT INTO stock_outputs (produit_id, quantite, motif, utilisateur_id, client_id, date_sortie, numero_bon) 
                               VALUES (?, ?, ?, ?, ?, NOW(), ?)");
        $stmt->execute([$produit_id, $quantite, $motif, $utilisateur_id, $client_id, $numero_bon]);

        // Mettre à jour le stock
        $pdo->prepare("UPDATE products SET stock_actuel = stock_actuel - ? WHERE id = ?")
            ->execute([$quantite, $produit_id]);

        $success = true;
    }
}

// Récupérer les produits
$produits = $pdo->query("SELECT id, nom FROM products")->fetchAll();
$clients = $pdo->query("SELECT id, nom FROM clients")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Sortie de stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            ➖ Bon de sortie
        </div>
        <div class="card-body">
            <?php if (!empty($erreur)): ?>
                <div class="alert alert-danger"><?= $erreur ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success">Bon de sortie enregistré avec succès ✅</div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="produit_id" class="form-label">Produit</label>
                    <select name="produit_id" id="produit_id" class="form-select" required>
                        <option value="">-- Choisir --</option>
                        <?php foreach ($produits as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="quantite" class="form-label">Quantité</label>
                    <input type="number" name="quantite" id="quantite" class="form-control" required min="1">
                </div>

                <div class="mb-3">
                    <label for="motif" class="form-label">Motif de sortie</label>
                    <input type="text" name="motif" id="motif" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="client_id" class="form-label">Client (optionnel)</label>
                    <select name="client_id" id="client_id" class="form-select">
                        <option value="">-- Aucun --</option>
                        <?php foreach ($clients as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-danger">➖ Valider la sortie</button>
                <a href="tableau_de_bord.php" class="btn btn-secondary">↩️ Retour</a>
				 <a href=" historique_bons_sortie.php" class="btn btn-secondary">Historique</a>

            </form>
        </div>
    </div>
</div>

</body>
</html>
