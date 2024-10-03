<?php

include "../bddConnexion/bddConnexion.php";
include "../APIBrawlhalla/security.php";

$id_tournoi = $_POST['id_tournoi'] ?? null;
$id_clan_demandeur = $_POST['id_clan_demandeur'];
$id_clan_receveur = $_POST['id_clan_receveur'];


if (empty($id_tournoi)) {
    echo "Erreur : ID du tournoi manquant.";
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
    $_SESSION['notification'] = "Vous avez déjà soumis des preuves pour ce tournoi.";
    header("Location: ../view/AdminPanel.php");
    exit();
}

// Si la méthode est POST, traiter le téléchargement des fichiers
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Vérifiez si des fichiers ont été téléchargés
    if (isset($_FILES['images'])) {
        // Limiter à 5 fichiers maximum
        if (count($_FILES['images']['name']) > 5) {
            $_SESSION['notification'] = "Erreur : Vous ne pouvez télécharger que 5 images maximum.";
            header("Location: ../view/matchVerif.php?id_tournoi=" . $id_tournoi);
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

            if (!in_array($fileType, $allowedFileTypes) || !in_array($fileExtension, $allowedExtensions)) {
                $_SESSION['notification'] = "Erreur : Type de fichier non autorisé pour " . $fileName ." Only png, jpeg, jpg files.";
                continue; 
            }

            // Vérifier la taille du fichier (max 5 Mo)
            if ($fileSize > 5 * 1024 * 1024) {
                $_SESSION['notification'] = "Erreur : La taille du fichier " . $fileName . " est trop grande. Max: 5 Mo.";
                continue;
            }

            // Déplacer le fichier vers le répertoire cible
            $targetFilePath = $targetDir . basename($fileName);
            if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                $_SESSION['notification'] = "L'image " . $fileName . " a été téléchargée avec succès.";
            } else {
                $_SESSION['notification'] = "Erreur lors du téléchargement de l'image " . $fileName . ".";
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
        $_SESSION['notification'] = "Preuves envoyées avec succès.";
        header("Location: ../view/AdminPanel.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification de Match</title>
</head>
<body>
    <h1>Envoyer des preuves d'image</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_tournoi" value="<?php echo htmlspecialchars($id_tournoi); ?>">
        <input type="file" name="images[]" multiple accept=".png, .jpg, .jpeg" required>
        <button type="submit">Envoyer</button>
    </form>
</body>
</html>
