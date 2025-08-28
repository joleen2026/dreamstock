<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Vérifier l'ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID du bon manquant.");
}

$id = intval($_GET['id']);

// Récupérer les infos du bon
$stmt = $pdo->prepare("
    SELECT se.*, 
           p.nom AS produit_nom, 
           p.reference AS produit_ref,
           p.unite,
           u.nom AS utilisateur_nom,
           f.nom AS fournisseur_nom
    FROM stock_entries se
    LEFT JOIN products p ON se.produit_id = p.id
    LEFT JOIN users u ON se.utilisateur_id = u.id
    LEFT JOIN suppliers f ON se.fournisseur_id = f.id
    WHERE se.id = ?
");
$stmt->execute([$id]);
$bon = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bon) {
    die("Bon introuvable.");
}

// Générer le contenu HTML du PDF
ob_start();
?>

<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    .header { text-align: center; font-weight: bold; font-size: 16px; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 8px; border: 1px solid #000; }
</style>

<div class="header">BON D'ENTRÉE N° <?= htmlspecialchars($bon['numero_bon']) ?></div>

<table>
    <tr>
        <th>Produit</th>
        <td><?= htmlspecialchars($bon['produit_nom']) ?> (<?= htmlspecialchars($bon['produit_ref']) ?>)</td>
    </tr>
    <tr>
        <th>Quantité</th>
        <td><?= $bon['quantite'] . ' ' . htmlspecialchars($bon['unite']) ?></td>
    </tr>
    <tr>
        <th>Prix unitaire</th>
        <td><?= number_format($bon['prix_unitaire'], 0, ',', ' ') ?> XAF</td>
    </tr>
    <tr>
        <th>Fournisseur</th>
        <td><?= $bon['fournisseur_nom'] ?: '—' ?></td>
    </tr>
    <tr>
        <th>Motif</th>
        <td><?= htmlspecialchars($bon['motif']) ?></td>
    </tr>
    <tr>
        <th>Date d’entrée</th>
        <td><?= date('d/m/Y H:i', strtotime($bon['date_entree'])) ?></td>
    </tr>
    <tr>
        <th>Utilisateur</th>
        <td><?= htmlspecialchars($bon['utilisateur_nom']) ?></td>
    </tr>
</table>

<p style="margin-top: 40px;">Signature :</p>
<p>__________________________</p>

<?php
$html = ob_get_clean();

// Créer l'objet DomPDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Affiche dans le navigateur sans téléchargement automatique
$dompdf->stream("Bon_Entree_" . $bon['numero_bon'] . ".pdf", ["Attachment" => false]);
exit;
