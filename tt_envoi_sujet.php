    <?php
    session_start();
    if (!isset($_SESSION["role"]) || (int) $_SESSION["role"] !== 1) {
        header("Location: index.php");
    }

    require_once "param.inc.php";
    $mysqli = new mysqli($host, $login, $passwd, $dbname);
    if ($mysqli->connect_error) {
        $_SESSION["erreur"] =
            "Problème de connexion à la base de données ! &#128557;";
        header("Location: proposition_sujet.php");
    }

    if (isset($_POST["titre"])) {
        $titre_sujet = trim($_POST["titre"]);
    } else {
        $titre_sujet = "";
    }

    if (isset($_POST["resume"])) {
        $resume = trim($_POST["resume"]);
    } else {
        $resume = "";
    }

    if (isset($_POST["deux_equipes"])) {
        $deux_equipes = 1;
    } else {
        $deux_equipes = 0;
    }
    if (isset($_POST["confidentiel"])) {
        $confidentiel = 1;
    } else {
        $confidentiel = 0;
    }

    if ($titre_sujet === "" || $resume === "") {
        $_SESSION["erreur"] = "Le titre et le résumé sont obligatoires.";
        header("Location: proposition_sujet.php");
    }
    if (mb_strlen($resume) > 300) {
        $_SESSION["erreur"] = "Le résumé ne doit pas dépasser 300 caractères.";
        header("Location: proposition_sujet.php");
    }

    $base = __DIR__ . "/uploads";
    $imgDir = $base . "/images/";
    $pdfDir = $base . "/pdfs/";

    if (!is_dir($imgDir)) {
        mkdir($imgDir, 0775, true);
    }
    if (!is_dir($pdfDir)) {
        mkdir($pdfDir, 0775, true);
    }

    $stmt = $mysqli->prepare(
        "INSERT INTO `sujets` (nom, id_createur, resume, deux_equipes, confidentiel) VALUES (?, ?, ?, ?, ?)"
    );
    if (!$stmt) {
        $_SESSION["erreur"] = "Erreur préparation requête.";
        header("Location: proposition_sujet.php");
        exit();
    }
    $stmt->bind_param("sisii", $titre_sujet,$_SESSION['id'], $resume, $deux_equipes, $confidentiel);
    if (!$stmt->execute()) {
        $_SESSION["erreur"] = "Impossible d'enregistrer le sujet.";
        $stmt->close();
        header("Location: proposition_sujet.php");
        exit;
    }
    $sujetId = $mysqli->insert_id;
    $stmt->close();

    $uploadCheck = 1;
    $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $target_img = $imgDir . "sujet_" . $sujetId . "." . $imageFileType;
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        $uploadCheck = 1;
    } else {
        //erreur
        $uploadCheck = 0;
    }

    if ($_FILES["image"]["size"] > 2 * 1024 * 1024) {
        //erreur
        $uploadCheck = 0;
    }

    if (
        $imageFileType != "jpg" &&
        $imageFileType != "png" &&
        $imageFileType != "jpeg" &&
        $imageFileType != "gif"
    ) {
        //erreur
        $uploadCheck = 0;
    }

    if ($uploadCheck == 1) {
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_img)) {
            $_SESSION["erreur"] = "Échec de l’upload de l’image.";
            header("Location: proposition_sujet.php");
            exit;
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

            // Vérifs
            if ($_FILES["pdfs"]["size"][$i] > 5 * 1024 * 1024) {
                $uploadCheck = 0;
            }
            if ($pdfFileType != "pdf") {
                $uploadCheck = 0;
            }

            if ($uploadCheck == 1) {
                if (!move_uploaded_file($_FILES["pdfs"]["tmp_name"][$i], $target_pdf)) {
                    $_SESSION["erreur"] = "Échec de l’upload d’un PDF.";
                    header("Location: proposition_sujet.php");
                    exit;
                }

                // Stocker le chemin relatif
                $webPath = "uploads/pdfs/" . $filename;
                $pstmt->bind_param("is", $sujetId, $webPath);
                $pstmt->execute();
            }
            $n++;
        }
    }

    $pstmt->close();

    /* ---------- FIN ---------- */

    $_SESSION["message"] = "Sujet enregistré (ID #$sujetId).";
    $mysqli->close();
    header("Location: sujets_propose.php");
    exit();
