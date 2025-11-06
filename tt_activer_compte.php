<?php
session_start();
if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 2) {
    header("Location: index.php");
    exit;
}

if (!isset($_POST['id_user'])) {
    $_SESSION['erreur'] = "Aucun utilisateur sélectionné.";
    header('Location: responsable_activer_comptes.php');
    exit();
}

require_once("param.inc.php");
$mysqli = new mysqli($host, $login, $passwd, $dbname);
if ($mysqli->connect_error) {
    $_SESSION['erreur'] = "Problème de connexion BDD.";
    header('Location: responsable_activer_comptes.php');
    exit();
}

$id_user = $_POST['id_user'];
$nouveau_role = 1; 

$stmt = $mysqli->prepare("UPDATE user SET role = ? WHERE id = ? AND role = 0");
$stmt->bind_param("ii", $nouveau_role, $id_user);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "Le compte a été activé avec succès.";
    } else {
        $_SESSION['erreur'] = "Ce compte n'a pas pu être activé (peut-être déjà actif ?).";
    }
} else {
    $_SESSION['erreur'] = "Erreur SQL: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
header('Location: responsable_activer_comptes.php');
exit();
?>