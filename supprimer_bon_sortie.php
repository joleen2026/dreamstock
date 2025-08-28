<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

if (!isset($_GET['id'])) {
    die("ID du bon manquant.");
}

$id = intval($_GET['id']);

// Récupérer les infos du bon
$stmt = $pdo->prepare("SELECT * FROM stock_outputs WHERE id = ?");
$stmt->execute([$id]);
$bon = $stmt->fetch();

if (!$bon) {
    die("Bon introuvable.");
}

// Réintégrer la quantité au stock
$produit_id = $bon['produit_id'];
$quantite = $bon['quantite'];

$pdo->prepare("UPDATE products SET stock_actuel = stock_actuel + ? WHERE id = ?")
    ->execute([$quantite, $produit_id]);

// Supprimer le bon de sortie
$pdo->prepare("DELETE FROM stock_outputs WHERE id = ?")
    ->execute([$id]);

// Redirection
header("Location: historique_bons_sortie.php?message=supprime");
exit;
