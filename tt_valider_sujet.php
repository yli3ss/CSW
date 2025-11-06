<?php
session_start();
if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 2) {
    header("Location: index.php");
    exit;
}

if (!isset($_POST['id_sujet']) || !isset($_POST['action'])) {
    $_SESSION['erreur'] = "Action non valide.";
    header('Location: responsable_valider_sujets.php');
    exit();
}

require_once("param.inc.php");
$mysqli = new mysqli($host, $login, $passwd, $dbname);
if ($mysqli->connect_error) {
    $_SESSION['erreur'] = "Problème de connexion BDD.";
    header('Location: responsable_valider_sujets.php');
    exit();
}

$id_sujet = (int)$_POST['id_sujet'];
$action = $_POST['action'];
$message = trim($_POST['message_responsable'] ?? '');

$nouveau_statut = 0;
$message_session = "";

switch ($action) {
    case 'valider':
        $nouveau_statut = 1; // 1 = Validé
        $message_session = "Sujet validé.";
        $message = ""; // On efface l'ancien message de justification
        break;
    case 'modifier':
        if (empty($message)) {
             $_SESSION['erreur'] = "Une justification est requise pour demander une modification.";
             header('Location: responsable_details_sujet.php?id=' . $id_sujet);
             exit();
        }
        $nouveau_statut = 3; // 3 = Modifications demandées
        $message_session = "Demande de modification envoyée au tuteur.";
        break;
    case 'refuser':
        if (empty($message)) {
             $_SESSION['erreur'] = "Une justification est requise pour refuser un sujet.";
             header('Location: responsable_details_sujet.php?id=' . $id_sujet);
             exit();
        }
        $nouveau_statut = 2; // 2 = Refusé
        $message_session = "Sujet refusé.";
        break;
    default:
        $_SESSION['erreur'] = "Action inconnue.";
        header('Location: responsable_valider_sujets.php');
        exit();
}

$stmt = $mysqli->prepare("UPDATE sujets SET statut = ?, message_responsable = ? WHERE id = ?");
$stmt->bind_param("isi", $nouveau_statut, $message, $id_sujet);

if ($stmt->execute()) {
    $_SESSION['message'] = $message_session;
} else {
    $_SESSION['erreur'] = "Erreur SQL : " . $stmt->error;
}

$stmt->close();
$mysqli->close();
header('Location: responsable_valider_sujets.php');
exit();
?>