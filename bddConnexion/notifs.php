<?php

include "bddConnexion.php";
include "../APIBrawlhalla/traductions.php";

//! This is a security check
if (!isset($_SESSION['brawlhalla_data']['clan_id'])) { 
    header("Location: ../APIBrawlhalla/routes.php");
    exit(); 
}

$clan_id = $_SESSION['brawlhalla_data']['clan_id'];

// Query to check received tournaments
$sql_check_received = "SELECT * FROM tournoi WHERE id_clan_receveur = $clan_id";
$result_received = $conn->query($sql_check_received);

$tournois_recus = []; 

if ($result_received->num_rows > 0) {
    $has_pending_tournaments = false; 
    // Retrieve received tournaments
    while ($row = $result_received->fetch_assoc()) {
        if ($row['accepted'] == 1) { 
            $has_pending_tournaments = true; 
        }
        $tournois_recus[] = $row; 
    }
    if ($has_pending_tournaments == false) {
        $_SESSION['notification'] = "You have pending tournaments!"; 
    }
}

// Query to check requested tournaments
$sql_check_demande = "SELECT * FROM tournoi WHERE id_clan_demandeur = $clan_id";
$result_demande = $conn->query($sql_check_demande);

$tournois_demandes = [];
$has_pending_requests = false; 

if ($result_demande->num_rows > 0) {
    // Retrieve requested tournaments
    while ($row = $result_demande->fetch_assoc()) {
        if ($row['accepted'] == 1) { 
            $has_pending_requests = true; 
        }
        $tournois_demandes[] = $row; 
    }

    if (!$has_pending_requests && !isset($_SESSION['notification_sent'])) {
        $_SESSION['notification'] = "You have accepted requested tournaments!";
        $_SESSION['notification_sent'] = true; 
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tournaments</title>
    <script>
        function toggleTournamentList() {
            var tournamentList = document.getElementById('tournament-list');
            if (tournamentList.style.display === 'none') {
                tournamentList.style.display = 'block'; // show
            } else {
                tournamentList.style.display = 'none'; // hide
            }
        }

        function toggleDemandedTournamentList() {
            var demandedTournamentList = document.getElementById('demanded-tournament-list');
            if (demandedTournamentList.style.display === 'none') {
                demandedTournamentList.style.display = 'block'; // show
            } else {
                demandedTournamentList.style.display = 'none'; // hide
            }
        }
    </script>
</head>
<body>
<div class="activity card" style="--delay: 0.5s;">
    <div class="card-title">
        <h3>Received battles</h3>      
        <button class="rounded-button" onclick="toggleTournamentList()">Show</button>
    </div>  <?php
            if (!empty($tournois_recus)) {echo "You have received somes requests." ;}
                ?>
    <div id="tournament-list" class="tournament-list" style="display: none; width: 65%;">
        <ul>
            <?php
            if (!empty($tournois_recus)) {
                foreach ($tournois_recus as $tournoi) {
                    $date_rencontre = $tournoi['date_rencontre'];
                    $date_formatee = date("d/m/Y H:i", strtotime($date_rencontre));
                    
                    echo "<li>";
                    if ($tournoi['accepted'] == 1) {
                        echo "Tournament already accepted with clan " . $clanTranslations[$tournoi['id_clan_demandeur']];
                    } else {
                        echo "<a style='color: #4255d3; text-decoration: none;' target='_blank' href='https://corehalla.com/stats/clan/" . $tournoi['id_clan_demandeur'] . "'>" . $clanTranslations[$tournoi['id_clan_demandeur']] . "</a> invites you<br>
                              Date: " . $date_formatee . ", <br>  
                              Format: " . $tournamentFormats[$tournoi['id_tournoi']] . " <br>";
                       
                        // Form to accept the tournament
                        echo "<form action='../bddConnexion/traitement_tournoisConfirme.php' method='post' style='display:inline;' id='form-accept-" . $tournoi['id_tournoi'] . "'>";
                        echo "<input type='hidden' name='id_tournoi' value='" . $tournoi['id_tournoi'] . "'>";
                        echo "<input type='hidden' name='action' value='Accept'>";
                        echo "</form>";
                        echo "<a href='#' style='color: green; text-decoration: none;' onclick=\"document.getElementById('form-accept-" . $tournoi['id_tournoi'] . "').submit(); return false;\">Accept</a>";
                        
                        echo "<form action='../bddConnexion/traitement_tournoisConfirme.php' method='post' style='display:inline;' id='form-refuse-" . $tournoi['id_tournoi'] . "'>";
                        echo "<input type='hidden' name='id_tournoi' value='" . $tournoi['id_tournoi'] . "'>";
                        echo "<input type='hidden' name='action' value='Deny'>";
                        echo "</form>";
                        echo " | <a href='#' style='color: red; text-decoration: none;' onclick=\"document.getElementById('form-refuse-" . $tournoi['id_tournoi'] . "').submit(); return false;\">Deny</a>";
                    }
                    
                    // Check if the tournament can be deleted
                    $date_rencontre = new DateTime($tournoi['date_rencontre']);
                    $now = new DateTime();
                    $interval = $now->diff($date_rencontre);
                    if ($interval->h >= 24 || $interval->d > 0) { // If the tournament date is more than 24 hours away
                        echo "<form action='../bddConnexion/delete_tournoi.php' method='post' style='display:inline;' onsubmit=\"return confirm('Are you sure you want to delete this tournament?');\">";
                        echo "<input type='hidden' name='id_tournoi' value='" . $tournoi['id_tournoi'] . "'>";
                        echo "<input type='submit' name='action' value='Delete' style='color: red;'>";
                        echo "</form>";
                    }
                    echo "</li>"; // Close the <li> tag
                }
            } else {
                echo "<li>No tournaments found.</li>";
            }
            ?>
        </ul>
    </div>
</div>

<div class="activity card" style="--delay: 0.5s;">
    <div class="card-title">
        <h3>Requested battles</h3>      
        <button class="rounded-button" onclick="toggleDemandedTournamentList()">Show</button>
    </div>
    <?php
            if (!empty($tournois_demandes)) {echo "You have sent somes requests." ;}
                ?>
    <div id="demanded-tournament-list" class="tournament-list" style="display: none; width: 65%;">
        <ul>
            <?php
            if (!empty($tournois_demandes)) {
                foreach ($tournois_demandes as $tournoi) {
                    $date_rencontre = $tournoi['date_rencontre'];
                    $date_formatee = date("d/m/Y H:i", strtotime($date_rencontre));

                    $accepted = $tournoi['accepted'] ? "Accepted" : "Waiting for a response.";
                    echo "<li>Clan: <a style='color: #4255d3; text-decoration: none;' target='_blank' href='https://corehalla.com/stats/clan/" . $tournoi['id_clan_receveur'] . "'>" . $clanTranslations[$tournoi['id_clan_receveur']] . "</a><br> 
                          Date: " .$date_formatee. "<br> 
                          Status: " . $accepted . "</li>";
                    
                    // Check if the tournament can be deleted
                    $date_rencontre = new DateTime($tournoi['date_rencontre']);
                    $now = new DateTime();
                    $interval = $now->diff($date_rencontre);
                    if ($interval->h >= 24 || $interval->d > 0) { // If the tournament date is more than 24 hours away
                        echo "<form action='../bddConnexion/delete_tournoi.php' method='post' style='display:inline;' onsubmit=\"return confirm('Are you sure you want to delete this tournament?');\">";
                        echo "<input type='hidden' name='id_tournoi' value='" . $tournoi['id_tournoi'] . "'>";
                        echo "<a href='#' style='color: red; text-decoration: none;' onclick=\"if(confirm('Are you sure you want to delete this tournament?')) { this.closest('form').submit(); } return false;\">Delete</a>";
                        echo "</form>";
                    }
                }
            } else {
                echo "<li>No battles found.</li>";
            }
            ?>
        </ul>
    </div>
</div>

</body>
</html>
