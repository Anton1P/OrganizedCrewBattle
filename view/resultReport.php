<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranked CrewBattle - Tournament Result</title>
    <link rel="icon" href="../assets/img/mini-logo-2.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/styles/ask.css">
    <script>
        // Function to display a confirmation popup before submitting the form
        function confirmSubmission(event) {
            event.preventDefault(); // Prevent the form from being submitted immediately
            let confirmation = confirm("Are you sure you want to submit these results?");
            if (confirmation) {
                document.getElementById("resultForm").submit(); // Submit the form if the user confirms
            }
        }
    </script>
</head>
<body>
<div class="container">
<?php
include "../bddConnexion/bddConnexion.php";
include "../APIBrawlhalla/traductions.php";
include "../APIBrawlhalla/security.php";

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    if (strpos($referer, 'tournoiReport.php') !== false || strpos($referer, 'resultReport.php') !== false) {

        // Retrieve variables from the URL
        $id_tournoi = $_GET['id_tournoi'];
        $date_rencontre = $_GET['date_rencontre'];
        $id_clan_demandeur = $_GET['id_clan_demandeur'];
        $id_clan_receveur = $_GET['id_clan_receveur'];
        $brawlhalla_room = $_GET['brawlhalla_room'];
        
        // Check if both clans agree on the result
        $sql_verif = "SELECT * FROM verif_report WHERE id_tournoi = ?";
        $stmt_verif = $conn->prepare($sql_verif);
        $stmt_verif->bind_param("i", $id_tournoi);
        $stmt_verif->execute();
        $result_verif = $stmt_verif->get_result();

        if ($result_verif->num_rows > 0) {
            $verif_data = $result_verif->fetch_assoc();
            
            echo $verif_data['id_tournoi']; // Debugging to see if the data exists
            echo "1"; // Debugging to check the execution flow

            // Check if the connected clan is the demander or the receiver
            if ($clan_id == $id_clan_demandeur) {
                if ($verif_data['clan_demandeur_report'] == 1) {
                    $_SESSION['notification'] = "The requester clan has already reported the result. Waiting for the receiver clan.";
                    header("Location: ../view/AdminPanel.php");
                    exit();
                }
            } elseif ($clan_id == $id_clan_receveur) {
                if ($verif_data['clan_receveur_report'] == 1) {
                    $_SESSION['notification'] = "The receiver clan has already reported the result. Waiting for the requester clan.";
                    header("Location: ../view/AdminPanel.php");
                    exit();
                }
            } else {
                $_SESSION['notification'] = "Error: unrecognized clan.";
                header("Location: ../view/AdminPanel.php");
                exit();
            }
        } 

        // If no results have been reported yet, display the tournament details
        echo "<h2>Tournament Details</h2>";
        echo "<p>Format: " . $tournamentFormats[$id_tournoi] . "</p>";
        echo "<p>Requester Clan: " . $clanTranslations[$id_clan_demandeur] . "</p>";
        echo "<p>Receiver Clan: " . $clanTranslations[$id_clan_receveur] . "</p>";
        echo "<p>Brawlhalla Room: #" . htmlspecialchars($brawlhalla_room) . "</p>";
    }
} else {
    header("Location: ../view/AdminPanel.php");
    exit();
}
?>

<h2>Submit Tournament Results</h2>

<form id="resultForm" action="../bddConnexion/traitement_report.php" method="POST">
    <input type="hidden" name="id_tournoi" value="<?php echo htmlspecialchars($id_tournoi); ?>">
    <input type="hidden" name="date_rencontre" value="<?php echo htmlspecialchars($date_rencontre); ?>">
    <input type="hidden" name="format" value="<?php echo htmlspecialchars($format); ?>">
    <input type="hidden" name="id_clan_demandeur" value="<?php echo htmlspecialchars($id_clan_demandeur); ?>">
    <input type="hidden" name="id_clan_receveur" value="<?php echo htmlspecialchars($id_clan_receveur); ?>">
    <input type="hidden" name="brawlhalla_room" value="<?php echo htmlspecialchars($brawlhalla_room); ?>">
    <input type="hidden" name="verif_report" value="1">

    <label for="resultat">Choose the result:</label>
    <select name="resultat" id="resultat" required>
        <option value="1">Victory</option>
        <option value="0">Defeat</option>
    </select>
    <br><br>
    
    <button type="submit" onclick="confirmSubmission(event)">Submit</button>
</form>
<p>If no evidence is submitted by at least one of the two clans, the match will be automatically canceled in 10 hours.</p>
</div>
</body>
</html>
