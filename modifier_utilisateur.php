<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

if (!isset($_GET['id'])) {
    die("ID utilisateur manquant.");
}

$id = $_GET['id'];

// Récupérer les infos actuelles
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$utilisateur = $stmt->fetch();

if (!$utilisateur) {
    die("Utilisateur non trouvé.");
}

// Mise à jour
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $mot_de_passe = $_POST['mot_de_passe'];

    if (!empty($mot_de_passe)) {
        $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET nom = ?, email = ?, mot_de_passe = ?, role = ? WHERE id = ?");
        $stmt->execute([$nom, $email, $hash, $role, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET nom = ?, email = ?, role = ? WHERE id = ?");
        $stmt->execute([$nom, $email, $role, $id]);
    }

    $message = "Utilisateur mis à jour avec succès.";
    // Recharge les nouvelles infos
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $utilisateur = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier utilisateur</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <h3 class="mb-4">🛠 Modifier un utilisateur</h3>

  <?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
  <?php endif; ?>

  <form method="post" class="bg-white p-4 rounded shadow-sm">
    <div class="mb-3">
      <label>Nom :</label>
      <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($utilisateur['nom']) ?>" required>
    </div>
    <div class="mb-3">
      <label>Email :</label>
      <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($utilisateur['email']) ?>" required>
    </div>
    <div class="mb-3">
      <label>Nouveau mot de passe (laisser vide si inchangé) :</label>
      <input type="password" name="mot_de_passe" class="form-control">
    </div>
    <div class="mb-3">
      <label>Rôle :</label>
      <select name="role" class="form-select" required>
        <option value="admin" <?= $utilisateur['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="magasinier" <?= $utilisateur['role'] === 'magasinier' ? 'selected' : '' ?>>Magasinier</option>
        <option value="visualiseur" <?= $utilisateur['role'] === 'visualiseur' ? 'selected' : '' ?>>Visualiseur</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
    <a href="utilisateur.php" class="btn btn-secondary">↩ Retour</a>
  </form>
</div>

</body>
</html>
