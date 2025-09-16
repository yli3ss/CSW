<?php
  session_start();
  if (!($_SESSION['role'])==2):
    header("Location: index.php");
  else:

  $titre = "Consultation des sujets du PING";
  include('header.inc.php');
  include('menu.inc.php');
?>
  <h1>Consultation des sujets du PING</h1>



<?php
  include('footer.inc.php');

  endif;
?>