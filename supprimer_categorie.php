<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

if (!isset($_GET['id'])) {
    die("ID de la catégorie manquant.");
}

$id = intval($_GET['id']);

// Vérifier si des produits sont liés à cette catégorie
$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE categorie_id = ?");
$stmt->execute([$id]);
$nbProduits = $stmt->fetchColumn();

if ($nbProduits > 0) {
    die("❌ Impossible de supprimer : cette catégorie est liée à des produits.");
}

// Supprimer la catégorie
$stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
$stmt->execute([$id]);

// Redirection
header("Location: cathegories.php?message=supprimee");
exit;
