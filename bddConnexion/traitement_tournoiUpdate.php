<style>
    .go-report {
        <?php 
            if ($hideDiv) {
                echo 'display: none;';
            }
        ?>
    }
</style>

<?php

include "bddConnexion.php";


//! Security check
if (!isset($_SESSION['brawlhalla_data']['clan_id'])) { 
    header("Location: ../APIBrawlhalla/routes.php");
    exit(); 
}

$id_clan = $_SESSION['brawlhalla_data']['clan_id'];
$date_actuelle = new DateTime();
$date_aujourdhui = $date_actuelle->format('Y-m-d'); // Format the current date


//! For keeping the match notification when both have checked in (admin panel)

// SQL to retrieve tournament information for the connected clan
$sql = "SELECT * FROM tournoi WHERE (id_clan_demandeur = ? OR id_clan_receveur = ?) AND accepted = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_clan, $id_clan);
$stmt->execute();
$result = $stmt->get_result();

// Check if there is a corresponding tournament
if ($result->num_rows > 0) {

    if ($result->num_rows > 1) {$num_rows_over = true;}else{$num_rows_over = false;} // pour voir si ya d'autre truc afficher si non on affiche si oui on affiche rien;

    while ($row = $result->fetch_assoc()) {
        $date_rencontre = new DateTime($row['date_rencontre']);

        // Calculate the range of 30 minutes before and 15 minutes after the match
        $plage_avant = clone $date_rencontre;
        $plage_avant->modify('-30 minutes');

        $plage_apres = clone $date_rencontre;
        $plage_apres->modify('+15 minutes');

        // Check if the current date has passed
        if ($date_actuelle > $plage_apres ) {

            // Add a condition to check if both clans have checked in
            $checkin_sql = "SELECT id_checkin FROM checkin WHERE id_tournoi = ? AND clan_demandeur_checkin = 1 AND clan_receveur_checkin = 1";
            $checkin_stmt = $conn->prepare($checkin_sql);
            $checkin_stmt->bind_param("i", $row['id_tournoi']);
            $checkin_stmt->execute();
            $checkin_result = $checkin_stmt->get_result();
        
            // If no check-in found for both clans, delete the tournament
            if ($checkin_result->num_rows == 0) {
                // Delete the tournament from the database
                $delete_sql = "DELETE FROM tournoi WHERE id_tournoi = ?";
                $delete_stmt = $conn->prepare($delete_sql);
                $delete_stmt->bind_param("i", $row['id_tournoi']);
                $delete_stmt->execute();
                $delete_stmt->close();
            }
        
            $checkin_stmt->close();
         
        }

        $checkin_sql = "SELECT id_checkin FROM checkin WHERE id_tournoi = ? AND clan_demandeur_checkin = 1 AND clan_receveur_checkin = 1";
        $checkin_stmt = $conn->prepare($checkin_sql);
        $checkin_stmt->bind_param("i", $row['id_tournoi']);
        $checkin_stmt->execute();
        $checkin_result = $checkin_stmt->get_result();
        // If the current date is within the defined range
        if ($date_actuelle >= $plage_avant || $checkin_result->num_rows > 0) {

            // Check if proof has already been submitted by either clan
            $proof_sql = "SELECT demandeur_sendproof, receveur_sendproof FROM verif_match WHERE id_tournoi = ?";
            $proof_stmt = $conn->prepare($proof_sql);
            $proof_stmt->bind_param("i", $row['id_tournoi']);
            $proof_stmt->execute();
            $proof_result = $proof_stmt->get_result();
            $proof_data = $proof_result->fetch_assoc();

            if (!empty($proof_data) && $proof_data['demandeur_sendproof'] == 1 || !empty($proof_data) &&  $proof_data['receveur_sendproof'] == 1) {
                $hideDiv = true; 
                if ( $hideDiv === true && $num_rows_over === false ) {
                    echo "<h3>One tournament currently under verification...</h3>";
                }
            }
            else {
                // Send tournament information to the session
                $_SESSION['tournoi_id'] = $row['id_tournoi'];
                $_SESSION['date_rencontre'] = $row['date_rencontre'];
                $_SESSION['format'] = $row['format'];
                $_SESSION['id_clan_demandeur'] = $row['id_clan_demandeur'];
                $_SESSION['id_clan_receveur'] = $row['id_clan_receveur'];
                $_SESSION['brawlhalla_room'] = $row['brawlhalla_room'];

                // Calculate remaining or elapsed time to display the counter
                $secondes_restantes = $date_rencontre->getTimestamp() - $date_actuelle->getTimestamp();

                // Store the match timestamp in a JS variable
                $timestamp_rencontre = $date_rencontre->getTimestamp();

                // Display the button with the counter
                echo "<span class='go-report'>";
                echo "<h3>A tournament is available!</h3>";
                echo "<p id='compteur'></p>";
                echo "<form style=' display: flex; justify-content: space-around;' action='../view/tournoiReport.php' method='post'>";
                echo "<input type='hidden' name='tournoi_id' value='" . $_SESSION['tournoi_id'] . "'>";
                echo "<button class='rounded-button' >Check-in</button>";
                echo "</form>";
                echo "</span>";
                // JavaScript to update the counter
                echo "
                <script>
                    let timestampRencontre = $timestamp_rencontre;
                    let dateActuelle = " . $date_actuelle->getTimestamp() . ";
                    let compteurElem = document.getElementById('compteur');

                    function updateCounter() {
                        let now = Math.floor(Date.now() / 1000); // current timestamp
                        let secondsRemaining = timestampRencontre - now;

                        let minutes = Math.floor(Math.abs(secondsRemaining) / 60);
                        let seconds = Math.abs(secondsRemaining) % 60;

                        let counter = (secondsRemaining > 0 ? '-' : '+') + String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
                        compteurElem.innerText = 'Time until the match: ' + counter;
                    }

                    let interval = setInterval(updateCounter, 1000); // Updates every second
                    updateCounter(); // Call immediately for correct initial display
                </script>
                ";
            } 
        }
        else {
            echo "<h3>Check-in available 30m before</h3>";
        }  
    }
    
}else {
    echo "<h3>No pending battles...</h3>";
}

// Database connection and other logic
$stmt->close();
$conn->close();
?>
