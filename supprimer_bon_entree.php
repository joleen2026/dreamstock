<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// Vérifier que l'ID est présent
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID du bon d'entrée manquant.");
}

$id = intval($_GET['id']);

// Récupérer le bon avant suppression pour corriger le stock
$stmt = $pdo->prepare("SELECT produit_id, quantite FROM stock_entries WHERE id = ?");
$stmt->execute([$id]);
$bon = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bon) {
    die("Bon d'entrée introuvable.");
}

// 1. Réduire le stock du produit
$updateStock = $pdo->prepare("UPDATE products SET stock_actuel = stock_actuel - ? WHERE id = ?");
$updateStock->execute([$bon['quantite'], $bon['produit_id']]);

// 2. Supprimer le bon d’entrée
$delete = $pdo->prepare("DELETE FROM stock_entries WHERE id = ?");
$delete->execute([$id]);

// 3. Redirection avec message
header("Location: bon_entree.php?success=suppression");
exit;
