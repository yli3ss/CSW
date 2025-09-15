<?php
session_start();
$_SESSION = [];
$_SESSION['message'] = "Déconnexion réussie !";
 header('Location: index.php');
?>