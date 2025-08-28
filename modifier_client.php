<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

if (!isset($_GET['id'])) {
    die("ID du client manquant.");
}

$id = intval($_GET['id']);

// Récupérer les infos du client
$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch();

if (!$client) {
    die("Client introuvable.");
}

$message = "";

// Traitement de la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $telephone = trim($_POST['telephone']);
    $email = trim($_POST['email']);
    $adresse = trim($_POST['adresse']);

    if (!empty($nom)) {
        $stmt = $pdo->prepare("UPDATE clients SET nom = ?, telephone = ?, email = ?, adresse = ? WHERE id = ?");
        $stmt->execute([$nom, $telephone, $email, $adresse, $id]);
        $message = "✅ Client mis à jour avec succès.";
        // Recharger les données
        $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->execute([$id]);
        $client = $stmt->fetch();
    } else {
        $message = "❌ Le nom est requis.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            ✏️ Modifier le client
        </div>
        <div class="card-body">

            <?php if (!empty($message)) : ?>
                <div class="alert alert-info"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label>Nom</label>
                    <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($client['nom']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Téléphone</label>
                    <input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($client['telephone']) ?>">
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($client['email']) ?>">
                </div>
                <div class="mb-3">
                    <label>Adresse</label>
                    <input type="text" name="adresse" class="form-control" value="<?= htmlspecialchars($client['adresse']) ?>">
                </div>

                <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
                <a href="client.php" class="btn btn-secondary">↩️ Retour</a>
            </form>

        </div>
    </div>
</div>

</body>
</html>
