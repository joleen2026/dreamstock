<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
if (isset($_GET['message']) && $_GET['message'] === 'supprime') {
    $message = "✅ Fournisseur supprimé avec succès.";
}


if (!isset($_GET['id'])) {
    die("ID du fournisseur manquant.");
}

$id = intval($_GET['id']);

// Vérifier s'il est lié à des bons d'entrée
$stmt = $pdo->prepare("SELECT COUNT(*) FROM stock_entries WHERE fournisseur_id = ?");
$stmt->execute([$id]);
$nb = $stmt->fetchColumn();

if ($nb > 0) {
    die("❌ Impossible de supprimer : ce fournisseur est lié à $nb bon(s) d’entrée.");
}

// Supprimer le fournisseur
$stmt = $pdo->prepare("DELETE FROM suppliers WHERE id = ?");
$stmt->execute([$id]);

// Redirection
header("Location: fournisseur.php?message=supprime");
exit;
