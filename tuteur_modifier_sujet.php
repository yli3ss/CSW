<?php
session_start();
if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 1) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['erreur'] = "Aucun sujet sélectionné.";
    header('Location: sujets_propose.php');
    exit();
}

$titre = "Modifier un sujet PING";
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
$id_tuteur = (int)$_SESSION['id'];

$stmt = $mysqli->prepare("SELECT * FROM sujets WHERE id = ? AND id_createur = ? AND (statut = 0 OR statut = 3)");
$stmt->bind_param("ii", $id_sujet, $id_tuteur);
$stmt->execute();
$sujet = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$sujet) {
    $_SESSION['erreur'] = "Ce sujet ne peut pas être modifié (introuvable, pas le vôtre, ou déjà validé).";
    header('Location: sujets_propose.php');
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
  <h1 class="mb-4">Modifier : <?php echo htmlspecialchars($sujet['nom']); ?></h1>

  <form method="POST" action="tt_modifier_sujet.php" enctype="multipart/form-data" class="row g-3">
    <input type="hidden" name="id_sujet" value="<?php echo $sujet['id']; ?>">

    <div class="col-12">
      <label for="titre" class="form-label">Titre du sujet *</label>
      <input type="text" class="form-control" id="titre" name="titre" required value="<?php echo htmlspecialchars($sujet['nom']); ?>">
    </div>

    <div class="col-12">
      <label for="resume" class="form-label">Résumé *</label>
      <textarea class="form-control" id="resume" name="resume" rows="4" required><?php echo htmlspecialchars($sujet['resume']); ?></textarea>
    </div>

    <div class="col-md-6">
      <label for="image" class="form-label">Changer l'image (optionnel)</label>
      <input class="form-control" type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
      <?php if ($sujet['image']): ?>
        <small class="form-text d-block mt-1">Fichier actuel : <?php echo basename($sujet['image']); ?>.</small>
        <div class="form-check mt-2">
          <input class="form-check-input" type="checkbox" name="delete_image" value="1" id="delete_image">
          <label class="form-check-label" for="delete_image">Supprimer l'image actuelle</label>
        </div>
      <?php endif; ?>
    </div>

    <div class="col-md-6">
      <label class="form-label">Gérer les fichiers PDF</label>
      <?php if ($fichiers->num_rows > 0): ?>
        <p><small>Fichiers actuels (cochez pour supprimer) :</small></p>
        <div class_id="list-group list-group-flush mb-2">
          <?php while($pdf = $fichiers->fetch_assoc()): ?>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="delete_pdf[]" value="<?php echo $pdf['id']; ?>" id="pdf_<?php echo $pdf['id']; ?>">
              <label class="form-check-label" for="pdf_<?php echo $pdf['id']; ?>">
                <?php echo htmlspecialchars(basename($pdf['path'])); ?>
              </label>
            </div>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <p><small>Aucun PDF n'est actuellement joint.</small></p>
      <?php endif; ?>
      
      <label for="pdfs" class="form-label">Ajouter de nouveaux PDF (optionnels)</label>
      <input class="form-control" type="file" id="pdfs" name="pdfs[]" multiple accept=".pdf">
    </div>

    <div class="col-md-6">
      <div class="form-check mt-4">
        <input class="form-check-input" type="checkbox" id="deux_equipes" name="deux_equipes" value="1" <?php if ($sujet['deux_equipes']) echo 'checked'; ?>>
        <label class="form-check-label" for="deux_equipes">Deux équipes possibles sur le sujet ?</label>
      </div>
    </div>

    <div class="col-md-6">
        <div class="form-check mt-4">
        <input class="form-check-input" type="checkbox" id="confidentiel" name="confidentiel" value="1" <?php if ($sujet['confidentiel']) echo 'checked'; ?>>
        <label class="form-check-label" for="confidentiel">Sujet confidentiel ?</label>
      </div>
    </div>

    <div class="col-12">
      <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
      <a href="sujets_propose.php" class="btn btn-secondary">Annuler</a>
    </div>

  </form>
</div>

<?php include('footer.inc.php'); ?>