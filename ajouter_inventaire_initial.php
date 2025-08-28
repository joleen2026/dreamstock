<?php
// ajouter_inventaire_initial.php

$host = '127.0.0.1';
$db   = 'gestion_stock';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Erreur de connexion BDD : " . $e->getMessage());
}

$message = "";

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produit_id = (int) ($_POST['produit_id'] ?? 0);
    $stock_theorique = (int) ($_POST['stock_theorique'] ?? 0);
    $stock_reel = (int) ($_POST['stock_reel'] ?? 0);
    $ecart = $stock_reel - $stock_theorique;

    if ($produit_id > 0 && $stock_theorique >= 0 && $stock_reel >= 0) {
        $stmt = $pdo->prepare("
            INSERT INTO inventory (produit_id, stock_theorique, stock_reel, ecart, date_inventaire)
            VALUES (:produit_id, :stock_theorique, :stock_reel, :ecart, NOW())
        ");
        $stmt->execute([
            ':produit_id' => $produit_id,
            ':stock_theorique' => $stock_theorique,
            ':stock_reel' => $stock_reel,
            ':ecart' => $ecart,
        ]);
        $message = "Inventaire initial ajouté avec succès pour le produit ID $produit_id.";
    } else {
        $message = "Veuillez saisir des valeurs valides.";
    }
}

// Récupération des produits sans inventaire
$sql = "
SELECT p.id, p.nom, p.reference
FROM products p
LEFT JOIN inventory i ON i.produit_id = p.id
WHERE i.produit_id IS NULL
ORDER BY p.nom
";
$stmt = $pdo->query($sql);
$produits_sans_inventaire = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Ajouter inventaire initial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="p-4">
    <h1>Ajouter un inventaire initial</h1>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (count($produits_sans_inventaire) === 0): ?>
        <div class="alert alert-success">Tous les produits ont déjà un inventaire.</div>
    <?php else: ?>
        <form method="post" class="mb-4">
            <div class="mb-3">
                <label for="produit_id" class="form-label">Produit</label>
                <select name="produit_id" id="produit_id" class="form-select" required>
                    <option value="">-- Choisissez un produit --</option>
                    <?php foreach ($produits_sans_inventaire as $prod): ?>
                        <option value="<?= (int)$prod['id'] ?>">
                            <?= htmlspecialchars($prod['nom']) ?> (Réf: <?= htmlspecialchars($prod['reference']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="stock_theorique" class="form-label">Stock théorique</label>
                <input type="number" min="0" name="stock_theorique" id="stock_theorique" class="form-control" required />
            </div>
            <div class="mb-3">
                <label for="stock_reel" class="form-label">Stock réel</label>
                <input type="number" min="0" name="stock_reel" id="stock_reel" class="form-control" required />
            </div>
            <button type="submit" class="btn btn-primary">Ajouter inventaire</button>
        </form>
    <?php endif; ?>
</body>
</html>
