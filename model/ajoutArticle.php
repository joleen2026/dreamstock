<?php
include 'connexion.php';

if (
    !empty($_POST['nom_article'])
    && !empty($_POST['categorie'])
    && !empty($_POST['quantite'])
    && !empty($_POST['prix_unitaire'])
    ) {
    $sql = "INSERT INTO article(nom_article, categorie, quantite, prix_unitaire) VALUES (?, ?, ?, ?)";
    $req = $connexion->prepare($sql);
    $req->execute(array(
        $_POST['nom_article'],
        $_POST['categorie'],
        $_POST['quantite'],
        $_POST['prix_unitaire']
    ));

        if ($req->rowCount()!=0) {
            $_SESSION['message']['text'] = "L'article a été ajouté avec succès.";
            $_SESSION['message']['type'] = "success";
        }else {
            $_SESSION['message']['text'] = "Erreur lors de l'ajout de l'article.";
            $_SESSION['message']['type'] = "danger";
        }

    }else {
        $_SESSION['message']['text'] = "Veuillez remplir tous les champs.";
        $_SESSION['message']['type'] = "danger";
    }

    header('Location: ../vue/article.php');
