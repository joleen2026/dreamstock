<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

$resultats = [];
$type = $_GET['type'] ?? '';
$date_debut = $_GET['date_debut'] ?? '';
$date_fin = $_GET['date_fin'] ?? '';

// Validation simple des dates
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

if ($type && $date_debut && $date_fin && validateDate($date_debut) && validateDate($date_fin)) {
    if ($date_debut > $date_fin) {
        $error = "La date de début doit être antérieure ou égale à la date de fin.";
    } else {
        // Valider les valeurs de $type pour éviter injection SQL
        if ($type === 'entree') {
            $table = 'stock_entries';
            $colonne_date = 'date_entree';
        } elseif ($type === 'sortie') {
            $table = 'stock_outputs';
            $colonne_date = 'date_sortie';
        } else {
            $error = "Type de mouvement invalide.";
        }

        if (!isset($error)) {
            $sql = "
                SELECT p.nom AS produit, s.quantite, s.$colonne_date AS date_mouvement, s.motif
                FROM $table s
                JOIN products p ON s.produit_id = p.id
                WHERE s.$colonne_date BETWEEN :date_debut AND :date_fin
                ORDER BY s.$colonne_date DESC
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':date_debut' => $date_debut . " 00:00:00",
                ':date_fin' => $date_fin . " 23:59:59"
            ]);
            $resultats = $stmt->fetchAll();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>📊 Rapport de stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            📊 Génération de rapport (entrées / sorties)
        </div>
        <div class="card-body">

            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label for="type" class="form-label">Type de mouvement</label>
                    <select name="type" id="type" class="form-select" required>
                        <option value="">-- Choisir --</option>
                        <option value="entree" <?= $type === 'entree' ? 'selected' : '' ?>>Entrée</option>
                        <option value="sortie" <?= $type === 'sortie' ? 'selected' : '' ?>>Sortie</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_debut" class="form-label">Début</label>
                    <input type="date" id="date_debut" name="date_debut" class="form-control" value="<?= htmlspecialchars($date_debut) ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="date_fin" class="form-label">Fin</label>
                    <input type="date" id="date_fin" name="date_fin" class="form-control" value="<?= htmlspecialchars($date_fin) ?>" required>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">🔍 Générer</button>
                </div>
            </form>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (!empty($resultats)): ?>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Motif</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultats as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['date_mouvement']) ?></td>
                            <td><?= htmlspecialchars($r['produit']) ?></td>
                            <td><?= (int)$r['quantite'] ?></td>
                            <td><?= htmlspecialchars($r['motif']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <a class="btn btn-danger mt-3" target="_blank"
                   href="export_rapport_pdf.php?type=<?= urlencode($type) ?>&date_debut=<?= urlencode($date_debut) ?>&date_fin=<?= urlencode($date_fin) ?>">
                    🖨️ Exporter en PDF
                </a>

            <?php elseif ($type && $date_debut && $date_fin && !isset($error)): ?>
                <div class="alert alert-info">Aucune donnée trouvée pour la période sélectionnée.</div>
            <?php endif; ?>

            <a href="tableau_de_bord.php" class="btn btn-secondary mt-3">↩️ Retour au tableau de bord</a>

        </div>
    </div>
</div>

</body>
</html>


