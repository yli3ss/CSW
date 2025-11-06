<?php
  session_start();
  if (!isset($_SESSION['role']) || $_SESSION['role'] != 2):
    header("Location: index.php");
    exit();
  endif;

  $titre = "Gestion PING";
  include('header.inc.php');
  include('menu.inc.php');
  include('message.inc.php');
?>
  <h1 class="my-4">Tableau de bord Responsable PING</h1>
  <p class="lead">Gérez les inscriptions de tuteurs et les propositions de sujets.</p>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Activer les comptes</h5>
                <p class="card-text">Valider les nouveaux comptes tuteurs en attente (rôle 0).</p>
                <a href="responsable_activer_comptes.php" class="btn btn-primary">Gérer les comptes</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Valider les sujets</h5>
                <p class="card-text">Consulter, valider ou refuser les sujets proposés par les tuteurs.</p>
                <a href="responsable_valider_sujets.php" class="btn btn-primary">Valider les sujets</a>
            </div>
        </div>
    </div>
</div>

<?php
  include('footer.inc.php');
?>