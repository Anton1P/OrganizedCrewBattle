<?php
if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    if (strpos($referer, 'moderation_access.php') !== false || strpos($referer, 'moderation_view.php') !== false) {

        session_start();
        include "../bddConnexion/bddConnexion.php";
        include "../APIBrawlhalla/traductions.php";

        // Vérification si l'utilisateur est autorisé
        if (!isset($_SESSION['userData']['steam_id'])) {
            header("Location: ../APIBrawlhalla/routes.php");
            exit();
        }

        $id_tournoi = $_GET['id_tournoi'] ?? null;

        if (!$id_tournoi) {
            echo "ID du tournoi manquant."; 
            exit();
        }

        // Récupération des informations du tournoi
        $query = "SELECT * FROM tournoi WHERE id_tournoi = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_tournoi);
        $stmt->execute();
        $tournoi = $stmt->get_result()->fetch_assoc();

        if (!$tournoi) {
            echo "Tournoi non trouvé.";
            exit();
        }

        // Récupérer les IDs des clans demandeurs et receveurs
        $id_clan_demandeur = $tournoi['id_clan_demandeur'];
        $id_clan_receveur = $tournoi['id_clan_receveur'];
        $format_tournoi = $tournoi['format']; 

        // Affichage des images du tournoi
        $dir = "../assets/images/" . $id_tournoi . "/";
        $images = scandir($dir);

        echo "<h1>Tournoi ID : " . htmlspecialchars($id_tournoi) . "</h1>";
        echo "<h2>Format du tournoi : " . $tournamentFormats[$format_tournoi] . "</h2>"; // Afficher le format du tournoi
        echo "<h2>Images du tournoi :</h2>";

        echo "<ul style='display:flex; list-style-type: none; '>";
        foreach ($images as $image) {
            if ($image != "." && $image != ".." && is_file($dir . $image)) {
                echo "<li><img src='" . $dir . $image . "' width='500'></li>";
            }
        }
        echo "</ul>";

        // Formulaire pour soumettre le résultat du match
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     
            function deleteDirectory($dir) {
                if (!is_dir($dir)) return;
                $files = scandir($dir);
                foreach ($files as $file) {
                    if ($file != "." && $file != "..") {
                        if (is_dir($dir . "/" . $file)) {
                            deleteDirectory($dir . "/" . $file); // Récursion pour supprimer les sous-dossiers
                        } else {
                            unlink($dir . "/" . $file); // Supprime les fichiers
                        }
                    }
                }
                rmdir($dir); // Supprime le dossier
            }


            if(isset($_POST['winner'])){
                $winner = $_POST['winner'];
       
                // Mettre à jour le résultat du tournoi dans la base de données
                if ($winner == 'demandeur') {
                    $query = "UPDATE verif_report SET clan_demandeur_result = 1, clan_receveur_result = 0 WHERE id_tournoi = ?";
                } else {
                    $query = "UPDATE verif_report SET clan_receveur_result = 1, clan_demandeur_result = 0 WHERE id_tournoi = ?";
                }
    
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $id_tournoi);
                $stmt->execute();

                // Suppression des fichiers et du dossier
                deleteDirectory($dir);
    
                echo "Résultat mis à jour avec succès.";
                header("Location: ../bddConnexion/traitement_addElo.php?id_tournoi=". $id_tournoi);
                exit(); 
    
            }     


            if (isset($_POST['id_tournoi']) && isset($_POST['delete'])) {
                $id_tournoi = $_POST['id_tournoi'];

                // Requête pour supprimer le tournoi
                $sql = "DELETE FROM tournoi WHERE id_tournoi = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id_tournoi);


                if ($stmt->execute()) {   
                    deleteDirectory($dir);
                    $_SESSION['notification'] = "Tournoi supprimé avec succès.";
                    header("Location: ../view/AdminPanel.php");
                    exit(); 
                } else {
                    $_SESSION['notification'] = "Erreur lors de la suppression du tournoi.";
                }

                $stmt->close();
            }


        }
    }
}
?>

<form action="" method="POST">
    <label for="winner">Sélectionnez le gagnant :</label>
    <select name="winner" id="winner">
        <option value="demandeur">
            Clan Demandeur : <?php echo isset($clanTranslations[$id_clan_demandeur]) ? htmlspecialchars($clanTranslations[$id_clan_demandeur]) : "Inconnu"; ?>
        </option>
        <option value="receveur">
            Clan Receveur :  <?php echo isset($clanTranslations[$id_clan_receveur]) ? htmlspecialchars($clanTranslations[$id_clan_receveur]) : "Inconnu"; ?>
        </option>
    </select>
    <button type="submit">Envoyer</button>
</form>

<form action="" method="POST">
    <input type="hidden" name="id_tournoi" value="<?php echo $id_tournoi; ?>">
    <input type="hidden" name="delete" value="<?php echo true; ?>">
    <button type="submit">Delete</button>
</form>