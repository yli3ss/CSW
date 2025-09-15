<?php
  session_start();
  if (!($_SESSION['role'])==1):
    header("Location: index.php");
  else:

  $titre = "Proposition d'un sujet PING";
  include('header.inc.php');
  include('menu.inc.php');
?>
  <h1>Proposition d'un sujet PING</h1>



<?php
  include('footer.inc.php');

  endif;
?>