<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

if (!isset($_GET['date'])) {
    die("❌ Date d'inventaire manquante.");
}

$date = $_GET['date'];

// Récupération des données d'inventaire
$stmt = $pdo->prepare("
    SELECT i.*, p.nom AS produit, u.nom AS utilisateur
    FROM inventory i
    LEFT JOIN products p ON i.produit_id = p.id
    LEFT JOIN users u ON i.utilisateur_id = u.id
    WHERE i.date_inventaire = ?
    ORDER BY p.nom
");
$stmt->execute([$date]);
$data = $stmt->fetchAll();

if (empty($data)) {
    die("❌ Aucun inventaire trouvé pour cette date.");
}

// Préparation du fichier CSV
$filename = "inventaire_$date.csv";
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=\"$filename\"");

$output = fopen('php://output', 'w');

// En-tête
fputcsv($output, ['Produit', 'Stock théorique', 'Stock réel', 'Écart', 'Utilisateur']);

// Données
foreach ($data as $ligne) {
    fputcsv($output, [
        $ligne['produit'],
        $ligne['stock_theorique'],
        $ligne['stock_reel'],
        $ligne['ecart'],
        $ligne['utilisateur']
    ]);
}

fclose($output);
exit;
