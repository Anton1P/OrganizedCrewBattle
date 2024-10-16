<?php

include "bddConnexion.php";
include "../APIBrawlhalla/traductions.php";

//! C'est une sécurité
if (!isset($_SESSION['brawlhalla_data']['clan_id'])) { 
    header("Location: ../APIBrawlhalla/routes.php");
    exit(); 
}


$clan_id = $_SESSION['brawlhalla_data']['clan_id'];


// Requête pour vérifier les tournois reçus
$sql_check_received = "SELECT * FROM tournoi WHERE id_clan_receveur = $clan_id";
$result_received = $conn->query($sql_check_received);

$tournois_recus = []; 

    if ($result_received->num_rows > 0) {
        $has_pending_tournaments = false; 
        // Récupérer les tournois reçus
        while ($row = $result_received->fetch_assoc()) {
            if ($row['accepted'] == 1) { 
                $has_pending_tournaments = true; 
            }
            $tournois_recus[] = $row; 
        }
        if (!$has_pending_tournaments &&  !isset($_SESSION['notification_sent'])) {
            $_SESSION['notification'] = "Vous avez des tournois en attente !"; 
            $_SESSION['notification_sent'] = true; 
        }
    }



// Requête pour vérifier les tournois demandés
$sql_check_demande = "SELECT * FROM tournoi WHERE id_clan_demandeur = $clan_id";
$result_demande = $conn->query($sql_check_demande);

$tournois_demandes = [];
$has_pending_requests = false; 

    if ($result_demande->num_rows > 0) {
        // Récupérer les tournois demandés
        while ($row = $result_demande->fetch_assoc()) {
            if ($row['accepted'] == 1) { 
                $has_pending_requests = true; 
            }
            $tournois_demandes[] = $row; 
        }
    
        if (!$has_pending_requests && !isset($_SESSION['notification_sent']) ) {
            $_SESSION['notification'] = "Vous avez des tournois demandés acceptés !";
            $_SESSION['notification_sent'] = true; 
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tournois</title>
    <script>
        function toggleTournamentList() {
            var tournamentList = document.getElementById('tournament-list');
            if (tournamentList.style.display === 'none') {
                tournamentList.style.display = 'block'; // afficher
            } else {
                tournamentList.style.display = 'none'; // masquer
            }
        }

        function toggleDemandedTournamentList() {
            var demandedTournamentList = document.getElementById('demanded-tournament-list');
            if (demandedTournamentList.style.display === 'none') {
                demandedTournamentList.style.display = 'block'; // afficher
            } else {
                demandedTournamentList.style.display = 'none'; // masquer
            }
        }
    </script>
</head>
<body>
<div class="activity card" style="--delay: 0.5s;">
    <div class="card-title">
        <h3>Tournois reçus</h3>      
        <button class="rounded-button" onclick="toggleTournamentList()">Show</button>
    </div>
    <div id="tournament-list" class="tournament-list" style="display: none; width: 65%;">
        <ul>
            <?php
            if (!empty($tournois_recus)) {
                foreach ($tournois_recus as $tournoi) {
                    echo "<li>";
                    if ($tournoi['accepted'] == 1) {
                        echo "Tournois déjà accepté avec le clan " .$clanTranslations[$tournoi['id_clan_demandeur']];
                    } else {
                        echo "<a style='color: #4255d3; text-decoration: none;' target='_blank' href='https://corehalla.com/stats/clan/" . $tournoi['id_clan_demandeur'] . "'>" . $clanTranslations[$tournoi['id_clan_demandeur']] . "</a> invites you<br>
                              Date : " . htmlspecialchars($tournoi['date_rencontre']) . ", <br>  
                              Format : " . $tournamentFormats[$tournoi['format']] . " <br>";
                           
                        // Formulaire pour accepter le tournoi
                        echo "<form action='../bddConnexion/traitement_tournoisConfirme.php' method='post' style='display:inline;' id='form-accept-" . $tournoi['id_tournoi'] . "'>";
                        echo "<input type='hidden' name='id_tournoi' value='" . $tournoi['id_tournoi'] . "'>";
                        echo "<input type='hidden' name='action' value='Accepter'>";
                        echo "</form>";
                        echo "<a href='#' style='color: green; text-decoration: none;' onclick=\"document.getElementById('form-accept-" . $tournoi['id_tournoi'] . "').submit(); return false;\">Accept</a>";
                        
                        echo "<form action='../bddConnexion/traitement_tournoisConfirme.php' method='post' style='display:inline;' id='form-refuse-" . $tournoi['id_tournoi'] . "'>";
                        echo "<input type='hidden' name='id_tournoi' value='" . $tournoi['id_tournoi'] . "'>";
                        echo "<input type='hidden' name='action' value='Refuser'>";
                        echo "</form>";
                        echo " | <a href='#' style='color: red; text-decoration: none;' onclick=\"document.getElementById('form-refuse-" . $tournoi['id_tournoi'] . "').submit(); return false;\">Denied</a>";
                    }
                    
                    // Vérifier si le tournoi peut être supprimé
                    $date_rencontre = new DateTime($tournoi['date_rencontre']);
                    $now = new DateTime();
                    $interval = $now->diff($date_rencontre);
                    if ($interval->h >= 24 || $interval->d > 0) { // Si la date du tournoi est dans plus de 24 heures
                        echo "<form action='../bddConnexion/delete_tournoi.php' method='post' style='display:inline;' onsubmit=\"return confirm('Êtes-vous sûr de vouloir supprimer ce tournoi ?');\">";
                        echo "<input type='hidden' name='id_tournoi' value='" . $tournoi['id_tournoi'] . "'>";
                        echo "<input type='submit' name='action' value='Supprimer' style='color: red;'>";
                        echo "</form>";
                    }
                    echo "</li>"; // Fermeture de la balise <li>
                }
            } else {
                echo "<li>Aucun tournoi trouvé.</li>";
            }
            ?>
        </ul>
    </div>
</div>


<div class="activity card" style="--delay: 0.5s;">
    <div class="card-title">
        <h3>Tournois demandés</h3>      
        <button class="rounded-button" onclick="toggleDemandedTournamentList()">Show</button>
    </div>

    <div id="demanded-tournament-list" class="tournament-list" style="display: none; width: 65%;">
        <ul>
            <?php
            if (!empty($tournois_demandes)) {
                foreach ($tournois_demandes as $tournoi) {
                    $accepted = $tournoi['accepted'] ? "Accepted" : "Waiting for a response.";
                    echo "<li>Clan :  <a style='color: #4255d3; text-decoration: none;' target='_blank' href='https://corehalla.com/stats/clan/" . $tournoi['id_clan_receveur'] . "'>" . $clanTranslations[$tournoi['id_clan_receveur']] . "</a><br> 
                          Date: " . htmlspecialchars($tournoi['date_rencontre']) . "<br> 
                          Statut: " . $accepted . "</li>";
                    
                    // Vérifier si le tournoi peut être supprimé
                    $date_rencontre = new DateTime($tournoi['date_rencontre']);
                    $now = new DateTime();
                    $interval = $now->diff($date_rencontre);
                    if ($interval->h >= 24 || $interval->d > 0) { // Si la date du tournoi est dans plus de 24 heures
                        echo "<form action='../bddConnexion/delete_tournoi.php' method='post' style='display:inline;' onsubmit=\"return confirm('Êtes-vous sûr de vouloir supprimer ce tournoi ?');\">";
                        echo "<input type='hidden' name='id_tournoi' value='" . $tournoi['id_tournoi'] . "'>";
                        echo "<a href='#' style='color: red; text-decoration: none;' onclick=\"if(confirm('Êtes-vous sûr de vouloir supprimer ce tournoi ?')) { this.closest('form').submit(); } return false;\">Supprimer</a>";
                        echo "</form>";
                    }
                }
            } else {
                echo "<li>Aucun tournoi trouvé.</li>";
            }
            ?>
        </ul>
    </div>
</div>


</body>
</html>
