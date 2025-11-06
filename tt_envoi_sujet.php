<?php
    session_start();
    if (!isset($_SESSION["role"]) || (int) $_SESSION["role"] !== 1) {
        header("Location: index.php");
        exit;
    }

    require_once "param.inc.php";
    $mysqli = new mysqli($host, $login, $passwd, $dbname);
    if ($mysqli->connect_error) {
        $_SESSION["erreur"] =
            "Problème de connexion à la base de données ! &#128557;";
        header("Location: proposition_sujet.php");
        exit;
    }

    $titre_sujet = trim($_POST["titre"] ?? "");
    $resume = trim($_POST["resume"] ?? "");
    $deux_equipes = isset($_POST["deux_equipes"]) ? 1 : 0;
    $confidentiel = isset($_POST["confidentiel"]) ? 1 : 0;
    $statut_sujet = 0;

    if ($titre_sujet === "" || $resume === "") {
        $_SESSION["erreur"] = "Le titre et le résumé sont obligatoires.";
        header("Location: proposition_sujet.php");
        exit;
    }
    if (mb_strlen($resume) > 300) {
        $_SESSION["erreur"] = "Le résumé ne doit pas dépasser 300 caractères.";
        header("Location: proposition_sujet.php");
        exit;
    }

    $base = __DIR__ . "/uploads";
    $imgDir = $base . "/images/";
    $pdfDir = $base . "/pdfs/";

    if (!is_dir($imgDir)) mkdir($imgDir, 0775, true);
    if (!is_dir($pdfDir)) mkdir($pdfDir, 0775, true);

    $image_path_in_db = NULL;
    $sujetId = null;

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $uploadCheck = 1;
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        
        if ($check === false) $uploadCheck = 0;
        if ($_FILES["image"]["size"] > 2 * 1024 * 1024) $uploadCheck = 0;
        if (
            $imageFileType != "jpg" && $imageFileType != "png" &&
            $imageFileType != "jpeg" && $imageFileType != "webp"
        ) $uploadCheck = 0;

        if ($uploadCheck == 1) {
            $temp_name = "sujet_temp_" . time() . "." . $imageFileType;
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $imgDir . $temp_name)) {
                $_SESSION["erreur"] = "Échec de l’upload de l’image.";
                $image_path_in_db = NULL;
            } else {
                $image_path_in_db = "uploads/images/" . $temp_name;
            }
        }
    }

    $stmt = $mysqli->prepare(
        "INSERT INTO `sujets` (nom, id_createur, resume, deux_equipes, confidentiel, statut, image) VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    if (!$stmt) {
        $_SESSION["erreur"] = "Erreur préparation requête.";
        header("Location: proposition_sujet.php");
        exit();
    }
    $stmt->bind_param("sisiiss", $titre_sujet, $_SESSION['id'], $resume, $deux_equipes, $confidentiel, $statut_sujet, $image_path_in_db);
    
    if (!$stmt->execute()) {
        $_SESSION["erreur"] = "Impossible d'enregistrer le sujet.";
        $stmt->close();
        header("Location: proposition_sujet.php");
        exit;
    }
    $sujetId = $mysqli->insert_id;
    $stmt->close();

    if ($image_path_in_db !== NULL && $sujetId !== null) {
        $new_image_name = "sujet_" . $sujetId . "." . strtolower(pathinfo($image_path_in_db, PATHINFO_EXTENSION));
        $new_image_path = "uploads/images/" . $new_image_name;
        if (rename($base . "/images/" . basename($image_path_in_db), $base . "/images/" . $new_image_name)) {
            $stmt_img = $mysqli->prepare("UPDATE `sujets` SET image = ? WHERE id = ?");
            $stmt_img->bind_param("si", $new_image_path, $sujetId);
            $stmt_img->execute();
            $stmt_img->close();
        }
    }

    $pstmt = $mysqli->prepare("INSERT INTO `sujets_pdfs` (sujet_id, path) VALUES (?, ?)");
    if (!$pstmt) {
        $_SESSION["erreur"] = "Erreur préparation insertion PDF.";
        header("Location: proposition_sujet.php"); exit;
    }

    if (!empty($_FILES['pdfs']['name'][0])) {
        $n = 1;
        foreach ($_FILES['pdfs']['name'] as $i => $name) {
            $uploadCheck = 1;

            $pdfFileType = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $filename    = "sujet_" . $sujetId . "_" . $n . "." . $pdfFileType;
            $target_pdf  = $pdfDir . $filename;

            if ($_FILES["pdfs"]["size"][$i] > 5 * 1024 * 1024) $uploadCheck = 0;
            if ($pdfFileType != "pdf") $uploadCheck = 0;

            if ($uploadCheck == 1) {
                if (move_uploaded_file($_FILES["pdfs"]["tmp_name"][$i], $target_pdf)) {
                    $webPath = "uploads/pdfs/" . $filename;
                    $pstmt->bind_param("is", $sujetId, $webPath);
                    $pstmt->execute();
                } else {
                    $_SESSION["erreur"] = "Échec de l’upload d’un PDF.";
                }
            }
            $n++;
        }
    }
    $pstmt->close();

    $_SESSION["message"] = "Sujet enregistré (ID #$sujetId). Il est en attente de validation.";
    $mysqli->close();
    header("Location: sujets_propose.php");
    exit();