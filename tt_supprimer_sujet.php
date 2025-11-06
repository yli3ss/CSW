<?php
session_start();
if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 1) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['erreur'] = "Aucun sujet sélectionné.";
    header('Location: sujets_propose.php');
    exit();
}

require_once("param.inc.php");
$mysqli = new mysqli($host, $login, $passwd, $dbname);
if ($mysqli->connect_error) {
    $_SESSION['erreur'] = "Problème de connexion BDD.";
    header('Location: sujets_propose.php');
    exit();
}

$id_sujet = (int)$_GET['id'];
$id_tuteur = (int)$_SESSION['id'];
$base = __DIR__;

$stmt_check = $mysqli->prepare("SELECT image FROM sujets WHERE id = ? AND id_createur = ? AND (statut = 0 OR statut = 3)");
$stmt_check->bind_param("ii", $id_sujet, $id_tuteur);
$stmt_check->execute();
$sujet = $stmt_check->get_result()->fetch_assoc();
$stmt_check->close();

if (!$sujet) {
    $_SESSION['erreur'] = "Impossible de supprimer ce sujet (introuvable, pas le vôtre, ou déjà validé).";
    header('Location: sujets_propose.php');
    exit();
}

$stmt_pdf = $mysqli->prepare("SELECT path FROM sujets_pdfs WHERE sujet_id = ?");
$stmt_pdf->bind_param("i", $id_sujet);
$stmt_pdf->execute();
$fichiers = $stmt_pdf->get_result();
$stmt_pdf->close();

if ($sujet['image'] && file_exists($base . "/" . $sujet['image'])) {
    unlink($base . "/" . $sujet['image']);
}

while ($pdf = $fichiers->fetch_assoc()) {
    if (file_exists($base . "/" . $pdf['path'])) {
        unlink($base . "/" . $pdf['path']);
    }
}

$stmt_delete_pdfs = $mysqli->prepare("DELETE FROM sujets_pdfs WHERE sujet_id = ?");
$stmt_delete_pdfs->bind_param("i", $id_sujet);
$stmt_delete_pdfs->execute();
$stmt_delete_pdfs->close();

$stmt_delete_sujet = $mysqli->prepare("DELETE FROM sujets WHERE id = ?");
$stmt_delete_sujet->bind_param("i", $id_sujet);

if ($stmt_delete_sujet->execute()) {
    $_SESSION['message'] = "Sujet supprimé avec succès.";
} else {
    $_SESSION['erreur'] = "Erreur lors de la suppression du sujet.";
}
$stmt_delete_sujet->close();

$mysqli->close();
header('Location: sujets_propose.php');
exit();
?>