<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

$message = "";

// Traitement du formulaire d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $telephone = trim($_POST['telephone']);
    $email = trim($_POST['email']);
    $adresse = trim($_POST['adresse']);

    if (!empty($nom)) {
        $stmt = $pdo->prepare("INSERT INTO suppliers (nom, telephone, email, adresse) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nom, $telephone, $email, $adresse]);
        $message = "✅ Fournisseur ajouté avec succès.";
    } else {
        $message = "❌ Le nom du fournisseur est obligatoire.";
    }
}

// Récupérer la liste des fournisseurs
$stmt = $pdo->query("SELECT * FROM suppliers ORDER BY nom ASC");
$fournisseurs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des fournisseurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            🧾 Gestion des fournisseurs
        </div>
        <div class="card-body">

            <?php if (!empty($message)) : ?>
                <div class="alert alert-info"><?= $message ?></div>
            <?php endif; ?>

            <!-- Formulaire d'ajout -->
            <form method="POST" class="row g-3 mb-4">
                <div class="col-md-3">
                    <input type="text" name="nom" class="form-control" placeholder="Nom" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="telephone" class="form-control" placeholder="Téléphone">
                </div>
                <div class="col-md-3">
                    <input type="email" name="email" class="form-control" placeholder="Email">
                </div>
                <div class="col-md-3">
                    <input type="text" name="adresse" class="form-control" placeholder="Adresse">
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-success">Ajouter</button>
                </div>
            </form>

            <!-- Liste des fournisseurs -->
            <?php if (empty($fournisseurs)) : ?>
                <div class="alert alert-warning">Aucun fournisseur trouvé.</div>
            <?php else : ?>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Téléphone</th>
                            <th>Email</th>
                            <th>Adresse</th>
                            <th style="width:150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fournisseurs as $f) : ?>
                            <tr>
                                <td><?= $f['id'] ?></td>
                                <td><?= htmlspecialchars($f['nom']) ?></td>
                                <td><?= htmlspecialchars($f['telephone']) ?></td>
                                <td><?= htmlspecialchars($f['email']) ?></td>
                                <td><?= htmlspecialchars($f['adresse']) ?></td>
                                <td><a href="voir_fournisseur.php?id=<?= $f['id'] ?>" class="btn btn-warning btn-sm">👁️</a>
                                    <a href="modifier_fournisseur.php?id=<?= $f['id'] ?>" class="btn btn-warning btn-sm">✏️</a>
									
                                    <a href="supprimer_fournisseur.php?id=<?= $f['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce fournisseur ?')">🗑️</a>
									
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif; ?>

        </div>
    </div>
	<a href="tableau_de_bord.php" class="btn btn-outline-secondary">← Retour au tableau de bord</a>
</div>

</body>
</html>
