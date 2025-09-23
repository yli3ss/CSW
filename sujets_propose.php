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
include('message.inc.php');
?>

<div class="container my-4">
  <h1 class="mb-4">Proposition d'un sujet PING</h1>
  <div class="table-responsive">
  <table class="table table-bordered">
  <thead class="thead-dark">
    <tr>
      <th scope="col">Numéro de sujet</th>
      <th scope="col">Nom du sujet</th>
      <th scope="col">Résumé du sujet</th>
      <th scope="col">Fichier PDF</th>
      <th scope="col">Possibilité d'avoir deux équipes ?</th>
      <th scope="col">Sujet confidentiel ?</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">blablabla</th>
      <td>Mblablablaark</td>
      <td>Otblablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablablato</td>
      <td>@mdo</td>
    </tr>
    <tr>
      <th scope="row">2</th>
      <td>Jacob</td>
      <td>Thornton</td>
      <td>@fat</td>
    </tr>
    <tr>
      <th scope="row">3</th>
      <td>Larry</td>
      <td>the Bird</td>
      <td>@twitter</td>
    </tr>
  </tbody>
</table>
</div>

</div>

<?php include('footer.inc.php'); ?>
