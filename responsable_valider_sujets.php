<?php
session_start();
if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 2) {
    header("Location: index.php");
    exit;
}

$titre = "Sujets à valider";
include('header.inc.php');
include('menu.inc.php');
include('message.inc.php');

require_once("param.inc.php");
$mysqli = new mysqli($host, $login, $passwd, $dbname);
if ($mysqli->connect_error) {
    echo "<div class='alert alert-danger'>Erreur de connexion BDD.</div>";
    include('footer.inc.php');
    exit();
}
?>

<div class="container my-4">
  <h1 class="mb-4">Sujets en attente de validation</h1>
  <p>Liste des sujets proposés par les tuteurs qui nécessitent une action (statut "En attente" ou "Modifications demandées").</p>
  
  <div class="list-group">
    <?php
    $stmt = $mysqli->prepare("SELECT s.id, s.nom, s.resume, s.statut, u.prenom, u.nom as user_nom 
                             FROM sujets s 
                             JOIN user u ON s.id_createur = u.id
                             WHERE s.statut = 0 OR s.statut = 3 
                             ORDER BY s.statut DESC, s.id ASC");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0):
    ?>
      <div class="alert alert-success">Aucun sujet n'est en attente de validation.</div>
    <?php
    else:
      while ($sujet = $result->fetch_assoc()):
        
        $statut_text = 'En attente';
        $statut_class = 'text-warning';
        if ($sujet['statut'] == 3) {
            $statut_text = 'Modifications demandées';
            $statut_class = 'text-info';
        }
    ?>
        <a href="responsable_details_sujet.php?id=<?php echo $sujet['id']; ?>" class="list-group-item list-group-item-action">
          <div class="d-flex w-100 justify-content-between">
            <h5 class="mb-1"><?php echo htmlspecialchars($sujet['nom']); ?></h5>
            <small class="fw-bold <?php echo $statut_class; ?>"><?php echo $statut_text; ?></small>
          </div>
          <p class="mb-1"><?php echo htmlspecialchars(substr($sujet['resume'], 0, 150)); ?>...</p>
          <small class="text-muted">Proposé par <?php echo htmlspecialchars($sujet['prenom'] . ' ' . $sujet['user_nom']); ?></small>
        </a>
    <?php
      endwhile;
    endif;
    $stmt->close();
    $mysqli->close();
    ?>
  </div>
</div>

<?php include('footer.inc.php'); ?>