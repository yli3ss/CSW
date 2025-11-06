<?php
session_start();

// On supprime les variables de session (méthode vue en cours)
unset($_SESSION['role']);
unset($_SESSION['id']);
unset($_SESSION['prenom']);

// On laisse un message pour la page d'accueil
$_SESSION['message'] = "Déconnexion réussie !";
header('Location: index.php');
exit();
?>