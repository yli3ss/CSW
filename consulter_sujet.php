<?php
session_start();
$titre = "Sujets disponibles";
include('header.inc.php');
include('menu.inc.php');
include('message.inc.php');

require_once("param.inc.php");
$mysqli = new mysqli($host, $login, $passwd, $dbname);
?>

<h1 class="my-4">Sujets de projet disponibles</h1>

<div class="row">
<?php
// Récupération des sujets 
$query = "SELECT s.id, s.nom as titre, s.resume, s.confidentiel, s.deux_equipes, 
                 u.nom, u.prenom 
          FROM sujets s 
          JOIN user u ON s.id_createur = u.id 
          ORDER BY s.id DESC";
          
if ($result = $mysqli->query($query)) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="col-md-6 mb-4">';
            echo '<div class="card h-100">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . htmlspecialchars($row['titre']) . '</h5>';
            echo '<p class="card-text">' . nl2br(htmlspecialchars($row['resume'])) . '</p>';
            echo '<p class="text-muted">Proposé par: ' . htmlspecialchars($row['prenom']) . ' ' . htmlspecialchars($row['nom']) . '</p>';
            echo '<div class="d-flex gap-2">';
            echo '<span class="badge bg-' . ($row['confidentiel'] ? 'warning' : 'success') . '">';
            echo $row['confidentiel'] ? 'Confidentiel' : 'Public';
            echo '</span>';
            echo '<span class="badge bg-' . ($row['deux_equipes'] ? 'primary' : 'secondary') . '">';
            echo $row['deux_equipes'] ? '2 équipes' : '1 équipe';
            echo '</span>';
            echo '</div>';
            
            // Récupération des PDFs associés
            $pdf_query = "SELECT path FROM sujets_pdfs WHERE sujet_id = " . $row['id'];
            $pdf_result = $mysqli->query($pdf_query);
            if ($pdf_result && $pdf_result->num_rows > 0) {
                echo '<div class="mt-2">';
                echo '<small class="text-muted">Documents associés:</small>';
                while ($pdf = $pdf_result->fetch_assoc()) {
                    $filename = basename($pdf['path']);
                    echo '<br><small><a href="' . htmlspecialchars($pdf['path']) . '" target="_blank">📄 ' . htmlspecialchars($filename) . '</a></small>';
                }
                echo '</div>';
            }
            
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        $result->free();
    } else {
        echo '<div class="col-12"><div class="alert alert-info">Aucun sujet disponible pour le moment.</div></div>';
    }
} else {
    echo '<div class="col-12"><div class="alert alert-danger">Erreur lors de la récupération des sujets.</div></div>';
}
$mysqli->close();
?>
</div>

<?php
include('footer.inc.php');
?>