<?php
$host = 'localhost';
$dbname = 'gestion_stock';
$user = 'root'; // à adapter si ton MySQL a un autre identifiant
$pass = '';     // à adapter si ton MySQL a un mot de passe

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    // Options de PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connexion échouée : " . $e->getMessage());
}
?>
