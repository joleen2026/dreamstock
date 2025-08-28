<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// Vérification de l'ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID utilisateur manquant.");
}

$id = $_GET['id'];

// Sécurité : Empêcher la suppression de soi-même
if ($_SESSION['id'] == $id) {
    die("Vous ne pouvez pas supprimer votre propre compte.");
}

// Supprimer l'utilisateur
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
if ($stmt->execute([$id])) {
    header("Location: utilisateur.php?msg=deleted");
    exit;
} else {
    echo "Erreur lors de la suppression.";
}
