<?php
if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    if (strpos($referer, 'moderation_access.php') !== false || strpos($referer, 'moderation_view.php') !== false) {

        session_start();
        include "../bddConnexion/bddConnexion.php";
        include "../APIBrawlhalla/traductions.php";

        // Check if the user is authorized
        if (!isset($_SESSION['userData']['steam_id'])) {
            header("Location: ../APIBrawlhalla/routes.php");
            exit();
        }

        $id_tournoi = $_GET['id_tournoi'] ?? null;

        if (!$id_tournoi) {
            echo "Tournament ID is missing."; 
            exit();
        }

        // Retrieve tournament information
        $query = "SELECT * FROM tournoi WHERE id_tournoi = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_tournoi);
        $stmt->execute();
        $tournoi = $stmt->get_result()->fetch_assoc();

        if (!$tournoi) {
            echo "Tournament not found.";
            exit();
        }

        // Retrieve the IDs of the requesting and receiving clans
        $id_clan_demandeur = $tournoi['id_clan_demandeur'];
        $id_clan_receveur = $tournoi['id_clan_receveur'];
        $format_tournoi_id = $tournoi['id_tournoi']; 

        // Display tournament images
        $dir = "../assets/images/" . $id_tournoi . "/";
        $images = scandir($dir);

        echo "<h1>Tournament ID: " . htmlspecialchars($id_tournoi) . "</h1>";
        echo "<h2>Tournament Format: " . $tournamentFormats[$format_tournoi_id] . "</h2>"; // Display tournament format
        echo "<h2>Tournament Images:</h2>";

        echo "<ul style='display:flex; list-style-type: none; '>";
        foreach ($images as $image) {
            if ($image != "." && $image != ".." && is_file($dir . $image)) {
                echo "<li><img src='" . $dir . $image . "' width='500'></li>";
            }
        }
        echo "</ul>";

        // Form to submit the match result
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     
            function deleteDirectory($dir) {
                if (!is_dir($dir)) return;
                $files = scandir($dir);
                foreach ($files as $file) {
                    if ($file != "." && $file != "..") {
                        if (is_dir($dir . "/" . $file)) {
                            deleteDirectory($dir . "/" . $file); // Recursion to delete subdirectories
                        } else {
                            unlink($dir . "/" . $file); // Delete files
                        }
                    }
                }
                rmdir($dir); // Delete the directory
            }

            if(isset($_POST['winner'])){
                $winner = $_POST['winner'];
       
                // Update the tournament result in the database
                if ($winner == 'demandeur') {
                    $query = "UPDATE verif_report SET clan_demandeur_result = 1, clan_receveur_result = 0 WHERE id_tournoi = ?";
                } else {
                    $query = "UPDATE verif_report SET clan_receveur_result = 1, clan_demandeur_result = 0 WHERE id_tournoi = ?";
                }
    
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $id_tournoi);
                $stmt->execute();

                // Delete files and the directory
                deleteDirectory($dir);
    
                echo "Result successfully updated.";
                header("Location: ../bddConnexion/traitement_addElo.php?id_tournoi=". $id_tournoi);
                exit(); 
            }     

            if (isset($_POST['id_tournoi']) && isset($_POST['delete'])) {
                $id_tournoi = $_POST['id_tournoi'];

                // Query to delete the tournament
                $sql = "DELETE FROM tournoi WHERE id_tournoi = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id_tournoi);

                if ($stmt->execute()) {   
                    deleteDirectory($dir);
                    $_SESSION['notification'] = "Tournament successfully deleted.";
                    header("Location: ../view/AdminPanel.php");
                    exit(); 
                } else {
                    $_SESSION['notification'] = "Error deleting the tournament.";
                }

                $stmt->close();
            }
        }
    }
}
?>

<form action="" method="POST">
    <label for="winner">Select the winner:</label>
    <select name="winner" id="winner">
        <option value="demandeur">
            Requesting Clan: <?php echo isset($clanTranslations[$id_clan_demandeur]) ? htmlspecialchars($clanTranslations[$id_clan_demandeur]) : "Unknown"; ?>
        </option>
        <option value="receveur">
            Receiving Clan:  <?php echo isset($clanTranslations[$id_clan_receveur]) ? htmlspecialchars($clanTranslations[$id_clan_receveur]) : "Unknown"; ?>
        </option>
    </select>
    <button type="submit">Submit</button>
</form>

<form action="" method="POST">
    <input type="hidden" name="id_tournoi" value="<?php echo $id_tournoi; ?>">
    <input type="hidden" name="delete" value="<?php echo true; ?>">
    <button type="submit">Delete</button>
</form>