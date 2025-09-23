<?php
  session_start(); // Pour les massages

  // Contenu du formulaire :;
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Connexion :
  require_once("param.inc.php");
  $mysqli = new mysqli($host, $login, $passwd, $dbname);
  if ($mysqli->connect_error) {
    $_SESSION['erreur']="Problème de connexion à la base de données ! &#128557;";
      // die('Erreur de connexion (' . $mysqli->connect_errno . ') '
              // . $mysqli->connect_error);
  } else {
    $requete = "SELECT * FROM `user` WHERE email = '$email'";

    $resultat = $mysqli->query($requete);
    if(!$resultat){
            echo "Erreur SQL : " . $mysqli->error . "<br>";
            echo "Requête exécutée : " . $requete . "<br>";
        $_SESSION['erreur'] = "Problème requete";
    } else {
        if($resultat->num_rows == 0){
            $_SESSION['erreur'] = "Identifiants incorrects";
        } else {
            $tuple = $resultat->fetch_assoc();
            $pass_bdd = $tuple['password'];
            if(password_verify($password,$pass_bdd)){
                $_SESSION['message'] = "Connexion réussi";
                $_SESSION['role']    = $tuple['role'];
                $_SESSION['id']      = $tuple['id'];

            } else {
                $_SESSION['erreur'] = "Identifiants incorrects";
            }
        }
    }
  }
 header('Location: index.php');


?>