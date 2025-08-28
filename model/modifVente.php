<?php
session_start();  // Toujours commencer par démarrer la session si tu utilises $_SESSION

include 'connexion.php';

if (
    !empty($_POST['id']) &&
    !empty($_POST['id_article']) &&
    !empty($_POST['id_client']) &&
    !empty($_POST['quantite']) &&
    !empty($_POST['prix'])
) {
    $sql = "UPDATE vente SET id_article = ?, id_client = ?, quantite = ?, prix = ? WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute(array(
        $_POST['id_article'],
        $_POST['id_client'],
        $_POST['quantite'],
        $_POST['prix'],
        $_POST['id']
    ));

    if ($req->rowCount() != 0) {
        $_SESSION['message']['text'] = "La vente a été modifiée avec succès.";
        $_SESSION['message']['type'] = "success";
    } else {
        $_SESSION['message']['text'] = "Rien n'a été modifié.";
        $_SESSION['message']['type'] = "warning";
    }
} else {
    $_SESSION['message']['text'] = "Veuillez remplir tous les champs.";
    $_SESSION['message']['type'] = "danger";
}

header('Location: ../vue/vente.php');
exit;  // Toujours mettre exit après header location
