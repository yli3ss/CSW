<nav class="mb-2 navbar navbar-expand-md bg-dark border-bottom border-body" data-bs-theme="dark">
  <div class="container-fluid">

    <!-- Partie gauche de la barre -->
    <a class="navbar-brand" href="index.php">Esigelec</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarText">
      
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="index.php">Accueil</a>
        </li>
       <?php if(isset($_SESSION['role'])):
        if (($_SESSION['role'])==1):?>
        <li class="nav-item">
          <a class="nav-link" href="sujets.php">Gestion des sujets</a>
        </li>
        <?php endif;
        endif;?>
        <li class="nav-item">
          <a class="nav-link" href="autre.php">Autre page</a>
        </li>
      </ul>

      <!-- Partie droite -->
      <ul class="navbar-nav">
               <?php if(!isset($_SESSION['role'])):
       ?>
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="inscription.php">Inscription</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="connexion.php">Connexion</a>
          </li>
                <?php else: ?>
                   <li class="nav-item">
            <a class="nav-link" href="deconnexion.php">Déconnexion</a>
          </li> 

      <?php endif;?>
      </ul>
    </div>
  </div>
</nav>

<div class="container">