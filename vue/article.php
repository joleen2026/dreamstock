    <?php
        include 'entete.php';  

        if (!empty($_GET['id'])) {
            $article = getArticle($_GET['id']);
        }
    ?>  
    <div class="home-content">
        <div class="overview-boxes">
            <div class="box">
                <form action="<?= !empty($_GET['id']) ? "../model/modifArticle.php" :"../model/ajoutArticle.php" ?> " method="post">

                    <label for="nom_article">Nom de l'article</label>
                    <input value="<?= !empty($_GET['id']) ? $article['nom_article'] :"" ?>" type="text" name="nom_article" id="nom_article" placeholder="veuillez saisir le nom">
                    <input value="<?= !empty($_GET['id']) ? $article['id'] :"" ?>" type="hidden" name="id" id="id" >

                    <label for="categorie">Categorie</label>
                    <select name="categorie" id="categorie">
                        <option <?= !empty($_GET['id']) &&  $article['categorie']=="surveillance" ? "selected" :"" ?> value="surveillance">Surveillance</option>
                        <option <?= !empty($_GET['id']) &&  $article['categorie']=="accessoire" ? "selected" :"" ?> value="accessoire">Accessoire</option>
                        <option <?= !empty($_GET['id']) &&  $article['categorie']=="Cable" ? "selected" :"" ?> value="Cable">Cable</option>
                        <option <?= !empty($_GET['id']) &&  $article['categorie']=="communication" ? "selected" :"" ?> value="communication">Communication</option>
                    </select>

                    <label for="quantite">Quantite</label>
                    <input value="<?= !empty($_GET['id']) ? $article['quantite'] :"" ?>" type="number" name="quantite" id="quantite" placeholder="veuillez saisir la quantite">

                    <label for="prix_unitaire">Prix unitaire</label>
                    <input value="<?= !empty($_GET['id']) ? $article['prix_unitaire'] :"" ?>" type="number" name="prix_unitaire" id="prix_unitaire" placeholder="veuillez saisir le prix"> 
                    
                    <button type="submit">Valider</button>

                    <?php if (!empty($_SESSION['message']['text'])): ?>
                        <div class="alert alert-<?= $_SESSION['message']['type'] ?>">
                            <?= $_SESSION['message']['text'] ?>
                        </div>
                        <?php unset($_SESSION['message']); // Supprimer le message après affichage ?>
                    <?php endif; ?>

                </form>
            </div>

            <div class="box">
                <table class="mtable">
                    <tr>
                        <th>Nom de l'article</th>
                        <th>Categorie</th>
                        <th>Quantite</th>   
                        <th>Prix unitaire</th>
                        <th>Action</th>
                    </tr>
                    <?php
                        $articles=getArticle();
                        if (!empty($articles) && is_array($articles)) {
                            foreach ($articles as $key => $value) {
                                ?>
                                <tr>
                                    <td><?= $value['nom_article'] ?></td>
                                    <td><?= $value['categorie'] ?></td>
                                    <td><?= $value['quantite'] ?></td>
                                    <td><?= $value['prix_unitaire'] ?> FCFA</td>
                                    <td> <a href="?id=<?= $value['id'] ?>"><i class='bx bx-edit-alt'></i> </a></td>
                                </tr>
                                <?php
                            }
                        }
                    ?>
                </table>
            </div>

        </div>
    </div>
    </section>
      

    <?php
       include 'pied.php';  
    ?>