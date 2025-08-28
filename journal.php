<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// Contrôle d'accès : uniquement Admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: tableau_de_bord.php");
    exit;
}

// Récupération des logs
$stmt = $pdo->prepare("
    SELECT l.id, l.action, l.module, l.date_action, u.nom AS utilisateur
    FROM logs l
    JOIN users u ON u.id = l.utilisateur_id
    ORDER BY l.date_action DESC
");
$stmt->execute();
$logs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Journal des actions</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

  <!-- 🔙 Bouton retour -->
  <div class="mb-3">
    <a href="tableau_de_bord.php" class="btn btn-outline-secondary">
      ← Retour au tableau de bord
    </a>
  </div>

  <h3 class="mb-4">📋 Journal des actions</h3>

  <?php if (empty($logs)): ?>
    <div class="alert alert-warning">Aucune action enregistrée.</div>
  <?php else: ?>
    <table class="table table-striped table-bordered bg-white shadow-sm">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Utilisateur</th>
          <th>Action</th>
          <th>Module</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($logs as $log): ?>
          <tr>
            <td><?= $log['id'] ?></td>
            <td><?= htmlspecialchars($log['utilisateur']) ?></td>
            <td><?= htmlspecialchars($log['action']) ?></td>
            <td><?= htmlspecialchars($log['module']) ?></td>
            <td><?= $log['date_action'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

</body>
</html>
