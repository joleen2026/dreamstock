<?php
include 'connexion.php';

function getArticle($id=null) 
{
    if (!empty($id)) {
        $sql= "SELECT * FROM article WHERE id=?";

        $req = $GLOBALS['connexion']->prepare($sql);
        $req->execute(array($id));

        return $req->fetch();
    } else{
        $sql= "SELECT * FROM article";

        $req = $GLOBALS['connexion']->prepare($sql);
        $req->execute();

        return $req->fetchAll();
    }
}

function getClient($id=null) 
{
    if (!empty($id)) {
        $sql= "SELECT * FROM client WHERE id=?";

        $req = $GLOBALS['connexion']->prepare($sql);
        $req->execute(array($id));

        return $req->fetch();
    } else{
        $sql= "SELECT * FROM client";

        $req = $GLOBALS['connexion']->prepare($sql);
        $req->execute();

        return $req->fetchAll();
    }
}

function getVente($id = null) 
{
    if (!empty($id)) {
        $sql = "SELECT v.id, v.id_article, v.id_client, a.nom_article, c.nom, c.prenom, v.quantite, v.prix, v.date_vente
                FROM vente AS v
                JOIN article AS a ON v.id_article = a.id
                JOIN client AS c ON v.id_client = c.id
                WHERE v.id = ?";
        $req = $GLOBALS['connexion']->prepare($sql);
        $req->execute(array($id));
        return $req->fetch();
    } else {
        $sql = "SELECT v.id, a.nom_article, c.nom, c.prenom, v.quantite, v.prix, v.date_vente
                FROM vente AS v
                JOIN article AS a ON v.id_article = a.id
                JOIN client AS c ON v.id_client = c.id
                ORDER BY v.date_vente DESC";
        $req = $GLOBALS['connexion']->prepare($sql);
        $req->execute();
        return $req->fetchAll();
    }
}
