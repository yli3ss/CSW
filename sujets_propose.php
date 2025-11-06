<?php
session_start();
if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 1) {
    header("Location: index.php");
    exit;
}

$titre = "Mes sujets proposés";
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

$id_tuteur = $_SESSION['id'];
?>

<div class="container my-4">
  <h1 class="mb-4">Mes sujets proposés</h1>

  <div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
      <thead class="thead-dark">
        <tr>
          <th scope="col">Titre</th>
          <th scope="col">Résumé</th>
          <th scope="col" style="width: 15%;">Options</th>
          <th scope="col" style="width: 15%;">Statut</th>
          <th scope="col" style="width: 15%;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $stmt = $mysqli->prepare("SELECT * FROM sujets WHERE id_createur = ? ORDER BY id DESC");
        $stmt->bind_param("i", $id_tuteur);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0):
        ?>
          <tr>
            <td colspan="5" class="text-center">Vous n'avez encore proposé aucun sujet.</td>
          </tr>
        <?php
        else:
          while ($sujet = $result->fetch_assoc()):
            
            $statut_text = 'En attente';
            $statut_class = 'text-warning';
            if ($sujet['statut'] == 1) {
                $statut_text = 'Validé';
                $statut_class = 'text-success';
            } elseif ($sujet['statut'] == 2) {
                $statut_text = 'Refusé';
                $statut_class = 'text-danger';
            } elseif ($sujet['statut'] == 3) {
                $statut_text = 'Modifications demandées';
                $statut_class = 'text-info';
            }
        ?>
            <tr>
              <td><?php echo htmlspecialchars($sujet['nom']); ?></td>
              <td><?php echo nl2br(htmlspecialchars($sujet['resume'])); ?></td>
              <td>
                <?php echo $sujet['deux_equipes'] ? '2 équipes' : '1 équipe'; ?><br>
                <?php echo $sujet['confidentiel'] ? 'Confidentiel' : 'Public'; ?>
              </td>
              <td class="fw-bold <?php echo $statut_class; ?>">
                <?php echo $statut_text; ?>
                <?php if (($sujet['statut'] == 2 || $sujet['statut'] == 3) && !empty($sujet['message_responsable'])): ?>
                  <small class="d-block text-muted fst-italic">
                    Message: "<?php echo htmlspecialchars($sujet['message_responsable']); ?>"
                  </small>
                <?php endif; ?>
              </td>
              <td>
                <?php
                if ($sujet['statut'] == 0 || $sujet['statut'] == 3):
                ?>
                  <a href="tuteur_modifier_sujet.php?id=<?php echo $sujet['id']; ?>" class="btn btn-sm btn-primary mb-1 w-100">Modifier</a>
                  <a href="tt_supprimer_sujet.php?id=<?php echo $sujet['id']; ?>" class="btn btn-sm btn-danger w-100" onclick="return confirm('Êtes-vous sûr ?');">Supprimer</a>
                <?php else: ?>
                  <small class="text-muted">Verrouillé</small>
                <?php endif; ?>
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