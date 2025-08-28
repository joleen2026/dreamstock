

<?php
// login.php - version corrigée et robuste
session_start();
require_once 'includes/db.php'; // doit définir $pdo (PDO)

// Si déjà connecté, rediriger
if (isset($_SESSION['user'])) {
    header('Location: tableau_de_bord.php');
    exit;
}

$error = '';
$identifier = '';
// On accepte plusieurs noms de champs pour compatibilité
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['email'] ?? $_POST['username'] ?? $_POST['identifiant'] ?? '');
    // accepter mot_de_passe ou password
    $password = $_POST['password'] ?? $_POST['mot_de_passe'] ?? '';

    if ($identifier === '' || $password === '') {
        $error = 'Veuillez renseigner votre identifiant et mot de passe.';
    } else {
        try {
            // Recherche par email
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :ident LIMIT 1");
            $stmt->execute([':ident' => $identifier]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Si pas trouvé, recherche par nom (username)
            if (!$user) {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE nom = :ident LIMIT 1");
                $stmt->execute([':ident' => $identifier]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            if ($user) {
                // Accepter colonne 'mot_de_passe' ou 'password' selon ta table
                if (isset($user['mot_de_passe'])) {
                    $hash = $user['mot_de_passe'];
                } elseif (isset($user['password'])) {
                    $hash = $user['password'];
                } else {
                    $hash = null;
                }

                if ($hash && password_verify($password, $hash)) {
                    // Connexion OK : initialiser session (compatibilité projet)
                    $_SESSION['user'] = [
                        'id'    => $user['id'],
                        'nom'   => $user['nom'] ?? '',
                        'email' => $user['email'] ?? '',
                        'role'  => $user['role'] ?? 'visualiseur'
                    ];
                    // variables auxiliaires utilisées ailleurs
                    $_SESSION['id']   = $user['id'];
                    $_SESSION['role'] = $user['role'] ?? 'visualiseur';
                    $_SESSION['nom']  = $user['nom'] ?? '';

                    header('Location: tableau_de_bord.php');
                    exit;
                } else {
                    $error = 'Identifiants incorrects.';
                }
            } else {
                $error = 'Identifiants incorrects.';
            }
        } catch (Exception $e) {
            // Ne pas afficher d'erreur brute en production
            $error = 'Erreur serveur : ' . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Connexion - DreamStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body { background:#f5f7fb; }
      .login-card { max-width:420px; margin:60px auto; }
    </style>
</head>
<body>
  <div class="container login-card">
    <div class="card shadow">
      <div class="card-body">
        <h4 class="card-title mb-3 text-center">Connexion</h4>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" novalidate>
          <div class="mb-3">
            <label class="form-label">Email ou nom d'utilisateur</label>
            <input type="text" name="email" class="form-control" value="<?= htmlspecialchars($identifier) ?>" placeholder="email@exemple.com ou utilisateur" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control" required>
          </div>

          <div class="d-grid">
            <button class="btn btn-primary">Se connecter</button>
          </div>
        </form>

        <div class="mt-3 text-muted small">
          Si tu rencontres encore des erreurs, vérifie :<br>
          - le fichier <code>includes/db.php</code> existe et définit <code>$pdo</code><br>
          - la table <code>users</code> contient les colonnes <code>email</code>, <code>nom</code>, et <code>mot_de_passe</code> (ou <code>password</code>).
        </div>
      </div>
    </div>
  </div>
</body>
</html>
