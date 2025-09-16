<?php
  session_start();
  if (!($_SESSION['role'])==0):
    header("Location: index.php");
  else:

  $titre = "Consultaion des sujets du PING";
  include('header.inc.php');
  include('menu.inc.php');
?>
  <h1>Consultaion des sujets du PING</h1>



<?php
  include('footer.inc.php');

  endif;
?>