<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
require_once 'dompdf/autoload.inc.php';



use Dompdf\Dompdf;
use Dompdf\Options;

// Vérification de l’ID du bon
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID du bon manquant.");
}

$id = intval($_GET['id']);

// Récupérer les données du bon de sortie
$stmt = $pdo->prepare("
    SELECT so.*, 
           p.nom AS produit_nom, 
           p.reference AS produit_ref, 
           p.unite,
           u.nom AS utilisateur_nom, 
           c.nom AS client_nom
    FROM stock_outputs so
    LEFT JOIN products p ON so.produit_id = p.id
    LEFT JOIN users u ON so.utilisateur_id = u.id
    LEFT JOIN clients c ON so.client_id = c.id
    WHERE so.id = ?
");
$stmt->execute([$id]);
$bon = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bon) {
    die("Bon introuvable.");
}

// Préparation du contenu HTML
$html = '
<h2 style="text-align:center;">Bon de Sortie - ' . htmlspecialchars($bon["numero_bon"]) . '</h2>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <tr>
        <th align="left">Produit</th>
        <td>' . htmlspecialchars($bon["produit_nom"]) . ' (' . htmlspecialchars($bon["produit_ref"]) . ')</td>
    </tr>
    <tr>
        <th align="left">Quantité</th>
        <td>' . $bon["quantite"] . ' ' . htmlspecialchars($bon["unite"]) . '</td>
    </tr>
    <tr>
        <th align="left">Client</th>
        <td>' . (!empty($bon["client_nom"]) ? htmlspecialchars($bon["client_nom"]) : '—') . '</td>
    </tr>
    <tr>
        <th align="left">Motif</th>
        <td>' . htmlspecialchars($bon["motif"]) . '</td>
    </tr>
    <tr>
        <th align="left">Date</th>
        <td>' . date("d/m/Y H:i", strtotime($bon["date_sortie"])) . '</td>
    </tr>
    <tr>
        <th align="left">Utilisateur</th>
        <td>' . htmlspecialchars($bon["utilisateur_nom"]) . '</td>
    </tr>
</table>
<br><br>
<p style="text-align:right;">Signature : _______________________</p>
';

// Création du PDF avec DomPDF
$options = new Options();
$options->set('defaultFont', 'Helvetica');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Téléchargement ou affichage dans le navigateur
$dompdf->stream("bon_sortie_" . $bon["numero_bon"] . ".pdf", ["Attachment" => false]);
exit;
