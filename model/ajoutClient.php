<?php
include 'connexion.php';

if (
    !empty($_POST['nom'])
    && !empty($_POST['prenom'])
    && !empty($_POST['telephone'])
    && !empty($_POST['adresse'])
    ) {
    $sql = "INSERT INTO client(nom, prenom, telephone, adresse) VALUES (?, ?, ?, ?)";
    $req = $connexion->prepare($sql);
    $req->execute(array(
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['telephone'],
        $_POST['adresse']
    ));

        if ($req->rowCount()!=0) {
            $_SESSION['message']['text'] = "Le client a été ajouté avec succès.";
            $_SESSION['message']['type'] = "success";
        }else {
            $_SESSION['message']['text'] = "Erreur lors de l'ajout du client.";
            $_SESSION['message']['type'] = "danger";
        }

    }else {
        $_SESSION['message']['text'] = "Veuillez remplir tous les champs.";
        $_SESSION['message']['type'] = "danger";
    }

    header('Location: ../vue/client.php');
