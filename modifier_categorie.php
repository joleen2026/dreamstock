<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// Vérification de l'ID de la catégorie
if (!isset($_GET['id'])) {
    die("ID de catégorie manquant.");
}

$id = intval($_GET['id']);
$message = "";

// Récupérer les infos de la catégorie
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$categorie = $stmt->fetch();

if (!$categorie) {
    die("Catégorie introuvable.");
}

// Si formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);

    if (!empty($nom)) {
        $stmt = $pdo->prepare("UPDATE categories SET nom = ? WHERE id = ?");
        $stmt->execute([$nom, $id]);
        $message = "✅ Catégorie mise à jour avec succès.";
        // Recharger la catégorie après MAJ
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $categorie = $stmt->fetch();
    } else {
        $message = "❌ Le nom ne peut pas être vide.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une catégorie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-header bg-warning">
            ✏️ Modifier la catégorie
        </div>
        <div class="card-body">

            <?php if (!empty($message)) : ?>
                <div class="alert alert-info"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nom de la catégorie</label>
                    <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($categorie['nom']) ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
                <a href="cathegories.php" class="btn btn-secondary">↩️ Retour</a> 
            </form>

        </div>
    </div>
</div>

</body>
</html>
