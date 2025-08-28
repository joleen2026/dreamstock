<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// 🔐 Autoriser uniquement les admins
if ($_SESSION['role'] !== 'admin') {
    header("Location: tableau_de_bord.php");
    exit;
}

$message = '';

// ⚙️ Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom_entreprise'];
    $email = $_POST['email_contact'];
    $telephone = $_POST['telephone_contact'];
    $adresse = $_POST['adresse'];
    $stock_min = $_POST['stock_minimum_defaut'];

    // Upload du logo
    if (!empty($_FILES['logo']['name'])) {
        $logo = 'uploads/' . basename($_FILES['logo']['name']);
        move_uploaded_file($_FILES['logo']['tmp_name'], $logo);
        $stmt = $pdo->prepare("UPDATE parametres SET nom_entreprise=?, email_contact=?, telephone_contact=?, adresse=?, stock_minimum_defaut=?, logo=? WHERE id=1");
        $stmt->execute([$nom, $email, $telephone, $adresse, $stock_min, $logo]);
    } else {
        $stmt = $pdo->prepare("UPDATE parametres SET nom_entreprise=?, email_contact=?, telephone_contact=?, adresse=?, stock_minimum_defaut=? WHERE id=1");
        $stmt->execute([$nom, $email, $telephone, $adresse, $stock_min]);
    }

    $message = "✅ Paramètres mis à jour.";
}

// 📦 Récupération des paramètres actuels
$stmt = $pdo->query("SELECT * FROM parametres WHERE id = 1");
$param = $stmt->fetch();
if (!$param) {
    // Si aucun paramètre n’est trouvé, on évite l'erreur et on initialise avec des valeurs par défaut
    $param = [
        'nom_entreprise' => '',
        'email_contact' => '',
        'telephone_contact' => '',
        'adresse' => '',
        'stock_minimum_defaut' => 5,
        'logo' => ''
    ];
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Paramètres de l'application</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

  <!-- 🔙 Bouton retour -->
  <div class="mb-4">
    <a href="tableau_de_bord.php" class="btn btn-outline-secondary">← Retour au tableau de bord</a>
  </div>

  <h3 class="mb-4">⚙️ Paramètres de l'application</h3>

  <?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="bg-white p-4 shadow-sm rounded">
    <div class="row mb-3">
      <div class="col-md-6">
        <label>Nom de l’entreprise</label>
        <input type="text" name="nom_entreprise" class="form-control" value="<?= htmlspecialchars($param['nom_entreprise']) ?>" required>
      </div>
      <div class="col-md-6">
        <label>Email contact</label>
        <input type="email" name="email_contact" class="form-control" value="<?= htmlspecialchars($param['email_contact']) ?>" required>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label>Téléphone</label>
        <input type="text" name="telephone_contact" class="form-control" value="<?= htmlspecialchars($param['telephone_contact']) ?>">
      </div>
      <div class="col-md-6">
        <label>Stock minimum par défaut</label>
        <input type="number" name="stock_minimum_defaut" class="form-control" value="<?= $param['stock_minimum_defaut'] ?? 5 ?>">
      </div>
    </div>

    <div class="mb-3">
      <label>Adresse</label>
      <textarea name="adresse" class="form-control" rows="2"><?= htmlspecialchars($param['adresse']) ?></textarea>
    </div>

    <div class="mb-3">
      <label>Logo (optionnel)</label><br>
      <?php if (!empty($param['logo']) && file_exists($param['logo'])): ?>
        <img src="<?= $param['logo'] ?>" alt="Logo actuel" height="80" class="mb-2 d-block">
      <?php endif; ?>
      <input type="file" name="logo" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
  </form>
</div>

</body>
</html>
