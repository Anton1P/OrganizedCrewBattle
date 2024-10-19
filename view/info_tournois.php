<?php

include "../bddConnexion/bddConnexion.php";

//! Security check
if (!isset($_SESSION['brawlhalla_data']['clan_id'])) { 
    header("Location: ../APIBrawlhalla/routes.php");
    exit(); 
}

$id_clan = $_SESSION['brawlhalla_data']['clan_id'];
$date_actuelle = new DateTime();
$date_today = $date_actuelle->format('Y-m-d'); // Format the current date

// SQL to retrieve tournaments scheduled for the connected clan today
$sql_tournaments_today = "SELECT * FROM tournoi WHERE (id_clan_demandeur = ? OR id_clan_receveur = ?) AND DATE(date_rencontre) = ? AND accepted = 1";
$stmt_tournaments = $conn->prepare($sql_tournaments_today);
$stmt_tournaments->bind_param("iis", $id_clan, $id_clan, $date_today);
$stmt_tournaments->execute();
$result_tournaments = $stmt_tournaments->get_result();

// Check if there are tournaments for today
if ($result_tournaments->num_rows > 0) {
  
    $result_tournaments->data_seek(0);

    // Display information for each tournament scheduled for today
    while ($row = $result_tournaments->fetch_assoc()) {
        
        $match_date = new DateTime($row['date_rencontre']);
        $today = new DateTime();

        if ($match_date->format('Y-m-d') === $today->format('Y-m-d')) {
            $formatted_date = "Today at " . $match_date->format('H\hi');
        } else {
            $formatted_date = $match_date->format('d/m/Y at H\hi');
        }
        
        echo "<div class='info-today'> <h3>". $clanTranslations[$row['id_clan_demandeur']] ." Vs ".  $clanTranslations[$row['id_clan_receveur']]  ."</h3>";
        echo  $formatted_date . "<br>";
        echo  $tournamentFormats[$row['id_tournoi']] . "<br>";
        echo "Brawlhalla room: #" . $row['brawlhalla_room'];

        // Check if the tournament is in verif_match table
        $sql_verif_match = "SELECT * FROM verif_match WHERE id_tournoi = ?";
        $stmt_verif_match = $conn->prepare($sql_verif_match);
        $stmt_verif_match->bind_param("i", $row['id_tournoi']);
        $stmt_verif_match->execute();
        $result_verif_match = $stmt_verif_match->get_result();
        
        // If there's an entry in verif_match, add the verification message
        if ($result_verif_match->num_rows > 0) {
            echo "<p style='color: orange;'>Currently under verification</p>";
        }

        echo "<br> </div>";
    }
    
} else {
    echo "<h3>No clan battles today</h3>";
}

?>
