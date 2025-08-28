<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

if (!isset($_GET['id'])) {
    die("ID du client manquant.");
}

$id = intval($_GET['id']);

// 🔒 OPTIONNEL : vérifier si ce client a des bons de sortie
$stmt = $pdo->prepare("SELECT COUNT(*) FROM stock_outputs WHERE client_id = ?");
$stmt->execute([$id]);
$nb_bons = $stmt->fetchColumn();

if ($nb_bons > 0) {
    die("❌ Ce client ne peut pas être supprimé car des bons de sortie lui sont liés.");
}

// Supprimer le client
$stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
$stmt->execute([$id]);

// Redirection
header("Location: client.php?msg=supprimé");
exit;
?>