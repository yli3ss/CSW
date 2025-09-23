<?php
  session_start();
  if (!($_SESSION['role'])==2):
    header("Location: index.php");
  else:

  $titre = "Page responsable PING";
  include('header.inc.php');
  include('menu.inc.php');
?>
  <h1>Page responsable PING</h1>



<?php
  include('footer.inc.php');

  endif;
?>



<div class="row my-4">
    <div class="col-md-8 mx-auto text-center">
        <div class="jumbotron bg-light p-5 rounded">
            <h1 class="display-4">Bienvenue sur la page responsable PING</h1>
            <p class="lead">Gérer les différents sujets envoyés/p>
            <hr class="my-4">
             <?php if (isset $_SESSION['role'] == 2): ?>
                <a class="btn btn-primary btn-lg me-2" href="inscription.php" role="button">Créer un compte</a> 
                <a class="btn btn-outline-primary btn-lg" href="connexion.php" role="button">Se connecter</a>
            <?php endif; ?>
        </div>
    </div>
</div>


// Creer un fichier Consulter sujet et tout les trucs qu'un respo peut faire et mettre les buttons correspondants