<?php
require 'includes/db.php';

$nom = "Admin";
$email = "admin@mail.com";
$mot_de_passe = password_hash("admin123", PASSWORD_DEFAULT);
$role = "admin";

$stmt = $pdo->prepare("INSERT INTO users (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
$stmt->execute([$nom, $email, $mot_de_passe, $role]);

echo "Admin ajouté.";
?>
