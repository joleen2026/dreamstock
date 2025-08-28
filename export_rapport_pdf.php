<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Paramètres
$type = $_GET['type'] ?? '';
$date_debut = $_GET['date_debut'] ?? '';
$date_fin = $_GET['date_fin'] ?? '';

if (!$type || !$date_debut || !$date_fin) {
    die('Paramètres manquants.');
}

// Déterminer la table et la colonne de date
$table = ($type === 'entree') ? 'stock_entries' : 'stock_outputs';
$colonne_date = ($type === 'entree') ? 'date_entree' : 'date_sortie';

// Récupération des données
$stmt = $pdo->prepare("
    SELECT p.nom AS produit, s.quantite, s.$colonne_date AS date_mouvement, s.motif
    FROM $table s
    JOIN products p ON p.id = s.produit_id
    WHERE s.$colonne_date BETWEEN ? AND ?
    ORDER BY s.$colonne_date DESC
");
$stmt->execute([$date_debut, $date_fin]);
$rows = $stmt->fetchAll();

// HTML à afficher dans le PDF
$html = "
<h2 style='text-align:center;'>Rapport de stock (" . ucfirst($type) . ")</h2>
<p><strong>Période :</strong> Du $date_debut au $date_fin</p>
<table border='1' cellpadding='8' cellspacing='0' width='100%'>
    <thead>
        <tr style='background-color:#f0f0f0;'>
            <th>Date</th>
            <th>Produit</th>
            <th>Quantité</th>
            <th>Motif</th>
        </tr>
    </thead>
    <tbody>
";

if ($rows) {
    foreach ($rows as $r) {
        $html .= "<tr>
            <td>{$r['date_mouvement']}</td>
            <td>{$r['produit']}</td>
            <td>{$r['quantite']}</td>
            <td>{$r['motif']}</td>
        </tr>";
    }
} else {
    $html .= "<tr><td colspan='4'>Aucune donnée trouvée pour cette période.</td></tr>";
}

$html .= "</tbody></table>";

// Génération du PDF
$options = new Options();
$options->set('defaultFont', 'DejaVu Sans'); // pour UTF-8
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("rapport_stock_$type.pdf", ['Attachment' => false]);
exit;
