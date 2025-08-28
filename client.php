<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

$message = "";

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $telephone = trim($_POST['telephone']);
    $email = trim($_POST['email']);
    $adresse = trim($_POST['adresse']);

    if (!empty($nom)) {
        $stmt = $pdo->prepare("INSERT INTO clients (nom, telephone, email, adresse) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nom, $telephone, $email, $adresse]);
        $message = "✅ Client ajouté avec succès.";
    } else {
        $message = "❌ Le nom du client est requis.";
    }
}

// Récupérer les clients
$stmt = $pdo->query("SELECT * FROM clients ORDER BY nom ASC");
$clients = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des clients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            👥 Gestion des clients
        </div>
        <div class="card-body">

            <?php if (!empty($message)) : ?>
                <div class="alert alert-info"><?= $message ?></div>
            <?php endif; ?>

            <!-- Formulaire d'ajout -->
            <form method="POST" class="row g-3 mb-4">
                <div class="col-md-3">
                    <input type="text" name="nom" class="form-control" placeholder="Nom du client" required>
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

            <!-- Liste des clients -->
            <?php if (empty($clients)) : ?>
                <div class="alert alert-warning">Aucun client enregistré.</div>
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
                        <?php foreach ($clients as $c) : ?>
                            <tr>
                                <td><?= $c['id'] ?></td>
                                <td><?= htmlspecialchars($c['nom']) ?></td>
                                <td><?= htmlspecialchars($c['telephone']) ?></td>
                                <td><?= htmlspecialchars($c['email']) ?></td>
                                <td><?= htmlspecialchars($c['adresse']) ?></td>
                                <td>
									 <a href="voir_client.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-warning">👁️</a>
                                    <a href="modifier_client.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-warning">✏️</a>
                                    <a href="supprimer_client.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce client ?')">🗑️</a>
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
