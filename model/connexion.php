<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nom_serveur="localhost";
$nom_base_de_donnee="dreamstock";
$utilisateur="root";
$mot_passe="";

try {
    $connexion = new PDO("mysql:host=$nom_serveur;dbname=$nom_base_de_donnee", $utilisateur, $mot_passe);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $connexion;
} catch (Exception $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
