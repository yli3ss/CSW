<?php
session_start();

// Accès réservé au rôle 1 (tuteur)
if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 1) {
    header("Location: index.php");
    exit;
}

$titre = "Proposition d'un sujet PING";
include('header.inc.php');
include('menu.inc.php');
?>

<div class="container my-4">
  <h1 class="mb-4">Proposition d'un sujet PING</h1>

  <form method="POST" action="tt_envoi_sujet.php" enctype="multipart/form-data" class="row g-3">

    <div class="col-12">
      <label for="titre" class="form-label">Titre du sujet *</label>
      <input type="text" class="form-control" id="titre" name="titre" required placeholder="Titre du sujet">
    </div>

    <div class="col-12">
      <label for="resume" class="form-label">Résumé *</label>
      <textarea class="form-control" id="resume" name="resume" rows="4" required placeholder="Expliquez le contenu en quelques lignes"></textarea>
    </div>

    <div class="col-md-6">
      <label for="image" class="form-label">Image (optionnel)</label>
      <input class="form-control" type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
    </div>

    <div class="col-md-6">
      <label for="pdfs" class="form-label">Fichiers PDF (optionnels)</label>
      <input class="form-control" type="file" id="pdfs" name="pdfs[]" multiple accept=".pdf">
    </div>

    <div class="col-md-6">
      <div class="form-check mt-4">
        <input class="form-check-input" type="checkbox" id="deux_equipes" name="deux_equipes" value="1">
        <label class="form-check-label" for="deux_equipes">Deux équipes possibles sur le sujet ?</label>
      </div>
    </div>

    <div class="col-md-6">
        <div class="form-check mt-4">
        <input class="form-check-input" type="checkbox" id="confidentiel" name="confidentiel" value="1">
        <label class="form-check-label" for="confidentiel">Sujet confidentiel ?</label>
      </div>
    </div>

    <div class="col-12">
      <button type="submit" class="btn btn-primary">Créer le sujet</button>
    </div>

  </form>
</div>

<?php include('footer.inc.php'); ?>
