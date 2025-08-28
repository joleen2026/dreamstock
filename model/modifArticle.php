<?php
include 'connexion.php';

if (
    !empty($_POST['nom_article'])
    && !empty($_POST['categorie'])
    && !empty($_POST['quantite'])
    && !empty($_POST['prix_unitaire'])
    && !empty($_POST['id'])
    ) {
    $sql = "UPDATE article SET nom_article=?, categorie=?, quantite=?, prix_unitaire=? WHERE id=?";
    $req = $connexion->prepare($sql);
    $req->execute(array(
        $_POST['nom_article'],
        $_POST['categorie'],
        $_POST['quantite'],
        $_POST['prix_unitaire'],
        $_POST['id']
    ));

        if ($req->rowCount()!=0) {
            $_SESSION['message']['text'] = "L'article a été modifié avec succès.";
            $_SESSION['message']['type'] = "success";
        }else {
            $_SESSION['message']['text'] = "Rien n'a ete modifié.";
            $_SESSION['message']['type'] = "warning";
        }

    }else {
        $_SESSION['message']['text'] = "Veuillez remplir tous les champs.";
        $_SESSION['message']['type'] = "danger";
    }

    header('Location: ../vue/article.php');
