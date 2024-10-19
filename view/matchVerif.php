<?php

include "../bddConnexion/bddConnexion.php";
include "../APIBrawlhalla/security.php";

$id_tournoi = $_POST['id_tournoi'] ?? null;
$id_clan_demandeur = $_POST['id_clan_demandeur'] ?? null;
$id_clan_receveur = $_POST['id_clan_receveur'] ?? null;

if (empty($id_clan_demandeur) && empty($id_clan_receveur) && empty($id_tournoi)) {
    $id_clan_demandeur =  $_GET['id_clan_demandeur'];
    $id_clan_receveur =  $_GET['id_clan_receveur'];
    $id_tournoi =  $_GET['id_tournoi'];
}
if (empty($id_clan_demandeur) && empty($id_clan_receveur) && empty($id_tournoi)) {
    $_SESSION['notification'] = "You can't do this.";
    header("Location: ../view/AdminPanel.php");
    exit();
}


// Maintenant, nous récupérons les informations mises à jour du tournoi
$query = "SELECT * FROM verif_match WHERE id_tournoi = '$id_tournoi'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    $insertQuery = "INSERT INTO verif_match (id_tournoi, id_clan_demandeur, id_clan_receveur, demandeur_sendproof, receveur_sendproof) 
                    VALUES ('$id_tournoi', '$id_clan_demandeur', '$id_clan_receveur', 0, 0)";
    mysqli_query($conn, $insertQuery);   
}

$query = "SELECT * FROM verif_match WHERE id_tournoi = '$id_tournoi'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Vérifier si l'utilisateur connecté est le demandeur ou le receveur
$is_demandeur = ($row['id_clan_demandeur'] == $clan_id);
$is_receveur = ($row['id_clan_receveur'] == $clan_id);

// Si l'utilisateur a déjà envoyé des preuves, rediriger vers l'admin panel
if (($is_demandeur && $row['demandeur_sendproof'] == 1) || 
    ($is_receveur && $row['receveur_sendproof'] == 1)) {
    $_SESSION['notification'] = "You have already submitted proof for this tournament.";
    header("Location: ../view/AdminPanel.php");
    exit();
}

// Si la méthode est POST, traiter le téléchargement des fichiers
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Vérifiez si des fichiers ont été téléchargés
    if (isset($_FILES['images'])) {
        // Limiter à 5 fichiers maximum
        if (count($_FILES['images']['name']) > 21) {
            $_SESSION['notification'] = "Error: You can only upload a maximum of 21 images.";
            header("Location: ../view/AdminPanel.php");
            exit();
        }

        // Créer le dossier pour stocker les images si non existant
        $targetDir = "../assets/images/" . $id_tournoi . "/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Traiter chaque image téléchargée
        foreach ($_FILES['images']['name'] as $key => $name) {
            $fileTmpPath = $_FILES['images']['tmp_name'][$key];
            $fileName = $_FILES['images']['name'][$key];
            $fileSize = $_FILES['images']['size'][$key];
            $fileType = $_FILES['images']['type'][$key];

            // Vérifier le type de fichier
            $allowedFileTypes = ['image/jpeg', 'image/png'];
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));


            if (preg_match("/[<>\"'\/]/", $fileName)) {
                $_SESSION['notification'] = "Error: The file name " . htmlspecialchars($fileName) . " contains unauthorized characters.";
                $_SESSION['from_treatment'] = true;
                header("Location: ../view/matchVerif.php?id_clan_demandeur=$id_clan_demandeur&id_clan_receveur=$id_clan_receveur&id_tournoi=$id_tournoi");
                exit();
            }
        
            if (!in_array($fileType, $allowedFileTypes) || !in_array($fileExtension, $allowedExtensions)) {
                $_SESSION['notification'] = "Error: Unauthorized file type for " . $fileName . ". Only png, jpeg, and jpg files are allowed.";
                $_SESSION['from_treatment'] = true;
                header("Location: ../view/matchVerif.php?id_clan_demandeur=$id_clan_demandeur&id_clan_receveur=$id_clan_receveur&id_tournoi=$id_tournoi");
                exit();
            }

            // Vérifier la taille du fichier (max 5 Mo)
            if ($fileSize > 5 * 1024 * 1024) {
                $_SESSION['notification'] = "Error: The file " . $fileName . " is too large. Max: 5 MB.";
                $_SESSION['from_treatment'] = true;
               header("Location: ../view/matchVerif.php?id_clan_demandeur=$id_clan_demandeur&id_clan_receveur=$id_clan_receveur&id_tournoi=$id_tournoi");
                exit();
            }

            // Déplacer le fichier vers le répertoire cible
            $targetFilePath = $targetDir . basename($fileName);
            if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                $_SESSION['notification'] = "The image " . $fileName . " has been uploaded successfully.";
            } else {
                $_SESSION['notification'] = "Error while uploading the image " . $fileName . ".";
            }
        }

        // Mettre à jour la base de données en fonction du rôle de l'utilisateur (demandeur ou receveur)
        if ($is_demandeur) {
            $updateQuery = "UPDATE verif_match 
                            SET demandeur_sendproof = 1 
                            WHERE id_tournoi = '$id_tournoi'";
        } elseif ($is_receveur) {
            $updateQuery = "UPDATE verif_match 
                            SET receveur_sendproof = 1 
                            WHERE id_tournoi = '$id_tournoi'";
        }

        mysqli_query($conn, $updateQuery);

        // Redirection après soumission
        $_SESSION['notification'] = "Proofs sent successfully.";
        header("Location: ../view/AdminPanel.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Match Verification</title>
    <link rel="stylesheet" href="../assets/styles/ask.css" />
</head>
<body>
<div class="container">
<?php 
        if (isset($_SESSION['notification']) && isset($_SESSION['from_treatment'])) {
            echo '<div class="notification">' . $_SESSION['notification'] . '</div>';
            unset($_SESSION['notification']); // Remove the notification after displaying it
        }
    ?>
    
    <title>Match Verification</title>

    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_tournoi" value="<?php echo htmlspecialchars($id_tournoi); ?>">
        <input type="file" name="images[]" multiple accept=".png, .jpg, .jpeg" required>
        <button type="submit">Submit</button>
    </form>
    <p>If no proof is submitted by at least one of the two clans, the match will be automatically canceled in 10 hours.</p>
</div>
</body>
</html>
