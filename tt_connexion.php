<?php
  session_start();

  $email = $_POST['email'];
  $password = $_POST['password'];

  require_once("param.inc.php");
  $mysqli = new mysqli($host, $login, $passwd, $dbname);
  if ($mysqli->connect_error) {
    $_SESSION['erreur']="Problème de connexion à la base de données ! &#128557;";
    header('Location: index.php');
    exit();
  }

  $stmt = $mysqli->prepare("SELECT id, prenom, password, role FROM `user` WHERE email = ?");
  if (!$stmt) {
      $_SESSION['erreur'] = "Problème de préparation de la requête.";
      header('Location: connexion.php');
      exit();
  }

  $stmt->bind_param("s", $email);

  if (!$stmt->execute()) {
      $_SESSION['erreur'] = "Problème d'exécution de la requête.";
      header('Location: connexion.php');
      exit();
  }

  $stmt->store_result();

  if ($stmt->num_rows == 0) {
      $_SESSION['erreur'] = "Identifiants incorrects";
  } else {
      $stmt->bind_result($id_bdd, $prenom_bdd, $pass_bdd, $role_bdd);
      $stmt->fetch();

      if (password_verify($password, $pass_bdd)) {
          if ($role_bdd == 0) {
              $_SESSION['erreur'] = "Votre compte n'a pas encore été activé par un responsable PING.";
          } else {
              $_SESSION['message'] = "Connexion réussie";
              $_SESSION['role']    = $role_bdd;
              $_SESSION['id']      = $id_bdd;
              $_SESSION['prenom']  = $prenom_bdd;
          }
      } else {
          $_SESSION['erreur'] = "Identifiants incorrects";
      }
  }
  
  $stmt->close();
  $mysqli->close();
  
 header('Location: index.php');
 exit();
?>