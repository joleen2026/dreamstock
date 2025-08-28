<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

$message = "";

// Traitement du formulaire d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    
    if (!empty($nom)) {
        $stmt = $pdo->prepare("INSERT INTO categories (nom) VALUES (?)");
        $stmt->execute([$nom]);
        $message = "✅ Catégorie ajoutée avec succès.";
    } else {
        $message = "❌ Le nom de la catégorie est requis.";
    }
}

// Récupérer toutes les catégories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY nom ASC");
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des catégories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            🗂️ Gestion des catégories
        </div>
        <div class="card-body">

            <?php if (!empty($message)) : ?>
                <div class="alert alert-info"><?= $message ?></div>
            <?php endif; ?>

            <!-- Formulaire d'ajout -->
            <form method="POST" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-10">
                        <input type="text" name="nom" class="form-control" placeholder="Nom de la catégorie" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100">➕ Ajouter</button>
                    </div>
                </div>
            </form>

            <!-- Liste des catégories -->
            <?php if (empty($categories)) : ?>
                <div class="alert alert-warning">Aucune catégorie enregistrée.</div>
            <?php else : ?>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $categorie): ?>
                            <tr>
                                <td><?= $categorie['id'] ?></td>
                                <td><?= htmlspecialchars($categorie['nom']) ?></td>
                                <td>
                                    <a href="modifier_categorie.php?id=<?= $categorie['id'] ?>" class="btn btn-warning btn-sm">✏️</a>
                                    <a href="supprimer_categorie.php?id=<?= $categorie['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Confirmer la suppression ?')">🗑️</a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif; ?>

        </div>
    </div>
	 <div class="mb-4">
    <a href="tableau_de_bord.php" class="btn btn-outline-secondary">← Retour au tableau de bord</a>
  </div>
</div>

</body>
</html>
