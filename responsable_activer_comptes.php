<?php
session_start();
if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 2) {
    header("Location: index.php");
    exit;
}

$titre = "Activer les comptes Tuteurs";
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
?>

<div class="container my-4">
  <h1 class="mb-4">Activer les comptes Tuteurs</h1>
  <p>Liste des utilisateurs inscrits en tant que tuteur et en attente de validation (Rôle 0).</p>

  <div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
      <thead class="thead-dark">
        <tr>
          <th scope="col">Nom</th>
          <th scope="col">Prénom</th>
          <th scope="col">Email</th>
          <th scope="col" style="width: 15%;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $stmt = $mysqli->prepare("SELECT id, nom, prenom, email FROM user WHERE role = 0 ORDER BY nom ASC");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0):
        ?>
          <tr>
            <td colspan="4" class="text-center">Aucun compte n'est en attente d'activation.</td>
          </tr>
        <?php
        else:
          while ($user = $result->fetch_assoc()):
        ?>
            <tr>
              <td><?php echo htmlspecialchars($user['nom']); ?></td>
              <td><?php echo htmlspecialchars($user['prenom']); ?></td>
              <td><?php echo htmlspecialchars($user['email']); ?></td>
              <td>
                <form action="tt_activer_compte.php" method="POST" style="padding:0; box-shadow:none; background:none;">
                    <input type="hidden" name="id_user" value="<?php echo $user['id']; ?>">
                    <button type="submit" class="btn btn-sm btn-success w-100">Activer</button>
                </form>
              </td>
            </tr>
        <?php
          endwhile;
        endif;
        $stmt->close();
        $mysqli->close();
        ?>
      </tbody>
    </table>
  </div>
</div>

<?php include('footer.inc.php'); ?>