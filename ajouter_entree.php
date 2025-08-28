<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produit_id = $_POST['produit_id'];
    $quantite = $_POST['quantite'];
    $prix_unitaire = $_POST['prix_unitaire'] ?? 0;
    $fournisseur_id = $_POST['fournisseur_id'] ?: null;
    $motif = $_POST['motif'] ?? 'Achat';
    $numero_bon = $_POST['numero_bon'];
    $utilisateur_id = $_SESSION['user_id'];

    // 1. Enregistrer l’entrée dans stock_entries
    $stmt = $pdo->prepare("INSERT INTO stock_entries 
        (produit_id, quantite, prix_unitaire, date_entree, motif, utilisateur_id, fournisseur_id, numero_bon)
        VALUES (?, ?, ?, NOW(), ?, ?, ?, ?)");
    $stmt->execute([$produit_id, $quantite, $prix_unitaire, $motif, $utilisateur_id, $fournisseur_id, $numero_bon]);

    // 2. Mettre à jour le stock du produit
    $pdo->prepare("UPDATE products SET stock_actuel = stock_actuel + ? WHERE id = ?")
        ->execute([$quantite, $produit_id]);

    // 3. Rediriger
    header("Location: bon_entree.php");
    exit;
}
