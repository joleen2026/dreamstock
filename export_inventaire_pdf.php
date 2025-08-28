<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

if (!isset($_GET['date'])) {
    die("❌ Date d'inventaire manquante.");
}

$date = $_GET['date'];

// Récupérer l'utilisateur
$stmt = $pdo->prepare("
    SELECT u.nom AS utilisateur
    FROM inventory i
    LEFT JOIN users u ON i.utilisateur_id = u.id
    WHERE i.date_inventaire = ?
    LIMIT 1
");
$stmt->execute([$date]);
$inventaire_info = $stmt->fetch();

// Récupérer les lignes d’inventaire
$stmt = $pdo->prepare("
    SELECT i.*, p.nom AS produit
    FROM inventory i
    LEFT JOIN products p ON i.produit_id = p.id
    WHERE i.date_inventaire = ?
    ORDER BY p.nom
");
$stmt->execute([$date]);
$lignes = $stmt->fetchAll();

ob_start();
?>

<h2>Inventaire du <?= htmlspecialchars($date) ?> (<?= htmlspecialchars($inventaire_info['utilisateur']) ?>)</h2>

<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th>Produit</th>
            <th>Stock théorique</th>
            <th>Stock réel</th>
            <th>Écart</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($lignes as $ligne): ?>
        <tr>
            <td><?= htmlspecialchars($ligne['produit']) ?></td>
            <td align="center"><?= $ligne['stock_theorique'] ?></td>
            <td align="center"><?= $ligne['stock_reel'] ?></td>
            <td align="center"><?= $ligne['ecart'] ?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?php
$html = ob_get_clean();

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("inventaire_$date.pdf", ["Attachment" => false]);
exit;
