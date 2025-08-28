<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// Contrôle d'accès : uniquement admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: tableau_de_bord.php");
    exit;
}

// Ajout d'un utilisateur
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO users (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$nom, $email, $mot_de_passe, $role])) {
        $message = "Utilisateur ajouté avec succès.";
    } else {
        $message = "Erreur lors de l'ajout.";
    }
}

// Liste des utilisateurs
$utilisateurs = $pdo->query("SELECT * FROM users ORDER BY date_creation DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des utilisateurs</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container py-5">

  <!-- 🔙 Bouton retour -->
  <div class="mb-4">
    <a href="tableau_de_bord.php" class="btn btn-outline-secondary">
      ← Retour au tableau de bord
    </a>
  </div>

  <h2 class="mb-4">👥 Gestion des utilisateurs</h2>

  <?php if ($message): ?>
    <div class="alert alert-info"><?= $message ?></div>
  <?php endif; ?>

  <!-- 🔧 Formulaire ajout utilisateur -->
  <form method="post" class="border p-4 bg-white rounded shadow-sm mb-5">
    <h5 class="mb-3">➕ Ajouter un utilisateur</h5>
    <div class="row mb-3">
      <div class="col">
        <input type="text" name="nom" class="form-control" placeholder="Nom complet" required>
      </div>
      <div class="col">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
      </div>
      <div class="col">
        <input type="password" name="mot_de_passe" class="form-control" placeholder="Mot de passe" required>
      </div>
      <div class="col">
        <select name="role" class="form-select" required>
          <option value="admin">Admin</option>
          <option value="magasinier">Magasinier</option>
          <option value="visualiseur">Visualiseur</option>
        </select>
      </div>
      <div class="col">
        <button type="submit" class="btn btn-primary">Ajouter</button>
      </div>
    </div>
  </form>

  <!-- 📋 Liste utilisateurs -->
  <h5 class="mb-3">📋 Liste des utilisateurs</h5>
  <table class="table table-bordered table-hover bg-white">
    <thead class="table-light">
      <tr>
        <th>#</th>
        <th>Nom</th>
        <th>Email</th>
        <th>Rôle</th>
        <th>Date création</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($utilisateurs as $u): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><?= htmlspecialchars($u['nom']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= ucfirst($u['role']) ?></td>
          <td><?= $u['date_creation'] ?></td>
          <td>
            <a href="modifier_utilisateur.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
            <a href="supprimer_utilisateur.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</body>
</html>

