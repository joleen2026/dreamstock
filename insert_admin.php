<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

try {
   

    // Infos de l'admin par défaut
    $nom = "Admin";
    $email = "admin@dreamstock.com";
    $motdepasse = "admin123"; // Mot de passe en clair (sera hashé)
    $role = "admin";

    // Hachage sécurisé du mot de passe
    $hash = password_hash($motdepasse, PASSWORD_DEFAULT);

    // Insertion
    $sql = "INSERT INTO users (nom, email, password, role) 
            VALUES (:nom, :email, :password, :role)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":nom" => $nom,
        ":email" => $email,
        ":password" => $hash,
        ":role" => $role
    ]);

    echo "✅ Utilisateur admin inséré avec succès !";
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
