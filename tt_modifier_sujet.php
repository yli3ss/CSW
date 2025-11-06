<?php
session_start();
if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 1) {
    header("Location: index.php");
    exit;
}

if (!isset($_POST['id_sujet'])) {
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

$id_sujet = (int)$_POST['id_sujet'];
$id_tuteur = (int)$_SESSION['id'];

$titre_sujet = trim($_POST["titre"] ?? "");
$resume = trim($_POST["resume"] ?? "");
$deux_equipes = isset($_POST["deux_equipes"]) ? 1 : 0;
$confidentiel = isset($_POST["confidentiel"]) ? 1 : 0;
$statut_sujet = 0;

$stmt_check = $mysqli->prepare("SELECT image FROM sujets WHERE id = ? AND id_createur = ?");
$stmt_check->bind_param("ii", $id_sujet, $id_tuteur);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
if ($result_check->num_rows == 0) {
    $_SESSION['erreur'] = "Opération non autorisée.";
    header('Location: sujets_propose.php');
    exit();
}
$sujet = $result_check->fetch_assoc();
$stmt_check->close();

$base = __DIR__ . "/uploads";
$imgDir = $base . "/images/";
$pdfDir = $base . "/pdfs/";
$image_path_in_db = $sujet['image'];

if (isset($_POST['delete_image']) && $_POST['delete_image'] == '1') {
    if (!empty($image_path_in_db) && file_exists($base . "/../" . $image_path_in_db)) {
        unlink($base . "/../" . $image_path_in_db);
    }
    $image_path_in_db = NULL;
} elseif (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
    if (!empty($image_path_in_db) && file_exists($base . "/../" . $image_path_in_db)) {
        unlink($base . "/../" . $image_path_in_db);
    }
    
    $uploadCheck = 1;
    $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) $uploadCheck = 0;
    if ($_FILES["image"]["size"] > 2 * 1024 * 1024) $uploadCheck = 0;
    if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'webp'])) $uploadCheck = 0;

    if ($uploadCheck == 1) {
        $new_image_name = "sujet_" . $id_sujet . "." . $imageFileType;
        $new_image_path = "uploads/images/" . $new_image_name;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $base . "/images/" . $new_image_name)) {
            $image_path_in_db = $new_image_path;
        }
    }
}

$stmt = $mysqli->prepare("UPDATE `sujets` SET nom = ?, resume = ?, deux_equipes = ?, confidentiel = ?, statut = ?, image = ? WHERE id = ? AND id_createur = ?");
$stmt->bind_param("ssiiisii", $titre_sujet, $resume, $deux_equipes, $confidentiel, $statut_sujet, $image_path_in_db, $id_sujet, $id_tuteur);
$stmt->execute();
$stmt->close();

if (isset($_POST['delete_pdf'])) {
    foreach ($_POST['delete_pdf'] as $pdf_id_to_delete) {
        $pdf_id = (int)$pdf_id_to_delete;
        
        $stmt_get_pdf = $mysqli->prepare("SELECT f.path FROM sujets_pdfs f JOIN sujets s ON f.sujet_id = s.id WHERE f.id = ? AND s.id_createur = ?");
        $stmt_get_pdf->bind_param("ii", $pdf_id, $id_tuteur);
        $stmt_get_pdf->execute();
        $pdf_file = $stmt_get_pdf->get_result()->fetch_assoc();
        $stmt_get_pdf->close();

        if ($pdf_file) {
            if (file_exists($base . "/../" . $pdf_file['path'])) {
                unlink($base . "/../" . $pdf_file['path']);
            }
            $stmt_delete_pdf = $mysqli->prepare("DELETE FROM sujets_pdfs WHERE id = ?");
            $stmt_delete_pdf->bind_param("i", $pdf_id);
            $stmt_delete_pdf->execute();
            $stmt_delete_pdf->close();
        }
    }
}

if (isset($_FILES['pdfs']) && !empty($_FILES['pdfs']['name'][0])) {
    $pstmt = $mysqli->prepare("INSERT INTO `sujets_pdfs` (sujet_id, path) VALUES (?, ?)");
    if (!$pstmt) {
        $_SESSION["erreur"] = "Erreur préparation insertion PDF.";
        header("Location: sujets_propose.php"); exit;
    }
    
    $n = 1;
    foreach ($_FILES['pdfs']['name'] as $i => $name) {
        $uploadCheck = 1;
        $pdfFileType = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $filename    = "sujet_" . $id_sujet . "_" . time() . "_" . $n . "." . $pdfFileType;
        $target_pdf  = $pdfDir . $filename;

        if ($_FILES["pdfs"]["size"][$i] > 5 * 1024 * 1024) $uploadCheck = 0;
        if ($pdfFileType != "pdf") $uploadCheck = 0;

        if ($uploadCheck == 1) {
            if (move_uploaded_file($_FILES["pdfs"]["tmp_name"][$i], $target_pdf)) {
                $webPath = "uploads/pdfs/" . $filename;
                $pstmt->bind_param("is", $id_sujet, $webPath);
                $pstmt->execute();
            } else {
                $_SESSION["erreur"] = "Échec de l’upload d’un PDF.";
            }
        }
        $n++;
    }
    $pstmt->close();
}

$mysqli->close();
$_SESSION['message'] = "Sujet modifié avec succès et soumis à nouveau pour validation.";
header('Location: sujets_propose.php');
exit();
?>