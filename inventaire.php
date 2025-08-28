<?php
// inventaire.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

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

// Requête avec gestion COALESCE sur stock_reel
$sql = "
SELECT p.id, p.nom, p.reference,
    COALESCE(SUM(se.quantite), 0) AS total_entrees,
    COALESCE(SUM(so.quantite), 0) AS total_sorties,
    (COALESCE(SUM(se.quantite), 0) - COALESCE(SUM(so.quantite), 0)) AS stock_theorique,
    COALESCE((
        SELECT i.stock_reel
        FROM inventory i
        WHERE i.produit_id = p.id
        ORDER BY i.date_inventaire DESC
        LIMIT 1
    ), 0) AS stock_reel,
    COALESCE((
        SELECT i.stock_reel
        FROM inventory i
        WHERE i.produit_id = p.id
        ORDER BY i.date_inventaire DESC
        LIMIT 1
    ), 0) - (COALESCE(SUM(se.quantite), 0) - COALESCE(SUM(so.quantite), 0)) AS ecart
FROM products p
LEFT JOIN stock_entries se ON se.produit_id = p.id
LEFT JOIN stock_outputs so ON so.produit_id = p.id
GROUP BY p.id, p.nom, p.reference
ORDER BY p.nom;
";

$stmt = $pdo->query($sql);
$produits = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Inventaire des produits</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="p-4">
    <h1 class="mb-4">Inventaire des produits</h1>
    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>Produit</th>
                <th>Référence</th>
                <th>Entrées</th>
                <th>Sorties</th>
                <th>Stock théorique</th>
                <th>Stock réel</th>
                <th>Écart</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produits as $prod): ?>
            <tr>
                <td><?= htmlspecialchars($prod['nom']) ?></td>
                <td><?= htmlspecialchars($prod['reference']) ?></td>
                <td><?= (int)$prod['total_entrees'] ?></td>
                <td><?= (int)$prod['total_sorties'] ?></td>
                <td><?= (int)$prod['stock_theorique'] ?></td>
                <td><?= (int)$prod['stock_reel'] ?></td>
                <td><?= (int)$prod['ecart'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>


