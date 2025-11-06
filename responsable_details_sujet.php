<?php
session_start();
if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 2) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['erreur'] = "Aucun sujet sélectionné.";
    header('Location: responsable_valider_sujets.php');
    exit();
}

$titre = "Détails du sujet";
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

$id_sujet = (int)$_GET['id'];

$stmt = $mysqli->prepare("SELECT s.*, u.nom as user_nom, u.prenom, u.email 
                         FROM sujets s 
                         JOIN user u ON s.id_createur = u.id 
                         WHERE s.id = ?");
$stmt->bind_param("i", $id_sujet);
$stmt->execute();
$sujet = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$sujet) {
    $_SESSION['erreur'] = "Sujet introuvable.";
    header('Location: responsable_valider_sujets.php');
    exit();
}

$stmt_pdf = $mysqli->prepare("SELECT * FROM sujets_pdfs WHERE sujet_id = ?");
$stmt_pdf->bind_param("i", $id_sujet);
$stmt_pdf->execute();
$fichiers = $stmt_pdf->get_result();
$stmt_pdf->close();
$mysqli->close();
?>

<div class="container my-4">
  <h1 class="mb-1"><?php echo htmlspecialchars($sujet['nom']); ?></h1>
  <p class="lead">Proposé par <?php echo htmlspecialchars($sujet['prenom'] . ' ' . $sujet['user_nom'] . ' (' . $sujet['email'] . ')'); ?></p>

  <div class="row">
    <div class="col-md-8">
      <div class="card mb-4">
        <div class="card-header">Détails du sujet</div>
        <div class="card-body">
          <p><strong>Résumé :</strong></p>
          <p><?php echo nl2br(htmlspecialchars($sujet['resume'])); ?></p>
          <hr>
          <p><strong>Options :</strong></p>
          <ul>
            <li><?php echo $sujet['deux_equipes'] ? 'Deux équipes autorisées' : 'Une seule équipe'; ?></li>
            <li><?php echo $sujet['confidentiel'] ? 'Sujet confidentiel' : 'Sujet public'; ?></li>
          </ul>

          <?php if ($sujet['image']): ?>
            <hr>
            <p><strong>Image d'illustration :</strong></p>
            <img src="<?php echo htmlspecialchars($sujet['image']); ?>" class="img-fluid rounded" alt="Illustration">
          <?php endif; ?>

          <?php if ($fichiers->num_rows > 0): ?>
            <hr>
            <p><strong>Fichiers PDF :</strong></p>
            <ul>
              <?php while ($pdf = $fichiers->fetch_assoc()): ?>
                <li><a href="<?php echo htmlspecialchars($pdf['path']); ?>" target="_blank"><?php echo htmlspecialchars(basename($pdf['path'])); ?></a></li>
              <?php endwhile; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <form class="card" action="tt_valider_sujet.php" method="POST" style="background-color: #f8f9fa;">
        <div class="card-header">Action</div>
        <div class="card-body">
          <input type="hidden" name="id_sujet" value="<?php echo $sujet['id']; ?>">
          
          <div class="mb-3">
            <label for="message_responsable" class="form-label">Justification (Requis si refus ou modification)</label>
            <textarea class="form-control" id="message_responsable" name="message_responsable" rows="5"><?php echo htmlspecialchars($sujet['message_responsable'] ?? ''); ?></textarea>
          </div>
          
          <div class="d-grid gap-2">
            <button type="submit" name="action" value="valider" class="btn btn-success">Valider le sujet</button>
            <button type="submit" name="action" value="modifier" class="btn btn-warning">Demander des modifications</button>
            <button type="submit" name="action" value="refuser" class="btn btn-danger">Refuser le sujet</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include('footer.inc.php'); ?>