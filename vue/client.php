    <?php
        include 'entete.php';  

        if (!empty($_GET['id'])) {
            $client = getClient($_GET['id']);
        }
    ?>  
    <div class="home-content">
        <div class="overview-boxes">
            <div class="box">
                <form action="<?= !empty($_GET['id']) ? "../model/modifClient.php" :"../model/ajoutClient.php" ?> " method="post">

                    <label for="nom">Nom </label>
                    <input value="<?= !empty($_GET['id']) ? $client['nom'] :"" ?>" type="text" name="nom" id="nom" placeholder="veuillez saisir le nom">
                    <input value="<?= !empty($_GET['id']) ? $client['id'] :"" ?>" type="hidden" name="id" id="id" >

                    <label for="prenom">Prenom </label>
                    <input value="<?= !empty($_GET['id']) ? $client['prenom'] :"" ?>" type="text" name="prenom" id="prenom" placeholder="veuillez saisir le prenom">

                    <label for="telephone">N* de telephone</label>
                    <input value="<?= !empty($_GET['id']) ? $client['telephone'] :"" ?>" type="text" name="telephone" id="telephone" placeholder="veuillez saisir le numero de telephone">

                    <label for="adresse">Adresse</label>
                    <input value="<?= !empty($_GET['id']) ? $client['adresse'] :"" ?>" type="text" name="adresse" id="adresse" placeholder="veuillez saisir l'adresse">
                    
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
                        <th>Nom</th>
                        <th>Prenom</th>
                        <th>Telephone</th>   
                        <th>Adresse</th>
                        <th>Action</th>
                    </tr>
                    <?php
                        $client=getClient();
                        if (!empty($client) && is_array($client)) {
                            foreach ($client as $key => $value) {
                                ?>
                                <tr>
                                    <td><?= $value['nom'] ?></td>
                                    <td><?= $value['prenom'] ?></td>
                                    <td><?= $value['telephone'] ?></td>
                                    <td><?= $value['adresse'] ?></td>
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