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

if (!isset($_SESSION['notification_sent'])) {
    if ($result_received->num_rows > 0) {
        $has_pending_tournaments = false; 
        // Récupérer les tournois reçus
        while ($row = $result_received->fetch_assoc()) {
            if ($row['accepted'] == 1) { 
                $has_pending_tournaments = true; 
            }
            $tournois_recus[] = $row; 
        }
        if ($has_pending_tournaments) {
            $_SESSION['notification'] = "Vous avez des tournois en attente !"; 
            $_SESSION['notification_sent'] = true; 
        }
    }
}


// Requête pour vérifier les tournois demandés
$sql_check_demande = "SELECT * FROM tournoi WHERE id_clan_demandeur = $clan_id";
$result_demande = $conn->query($sql_check_demande);

$tournois_demandes = [];
$has_pending_requests = false; 


if (!isset($_SESSION['notification_sent'])) {
    // Si aucune notification n'a encore été envoyée, vérifier les tournois acceptés
    if ($result_demande->num_rows > 0) {
        // Récupérer les tournois demandés
        while ($row = $result_demande->fetch_assoc()) {
            if ($row['accepted'] == 1) { 
                $has_pending_requests = true; 
            }
            $tournois_demandes[] = $row; 
        }

        if ($has_pending_requests) {
            $_SESSION['notification'] = "Vous avez des tournois demandés acceptés !";
            $_SESSION['notification_sent'] = true; 
        }
    }
}


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tournois</title>
    <style>
        .tournament-list {
            display: none; /* cacher la liste par défaut */
            border: 1px solid #ccc;
            padding: 10px;
            margin-top: 10px;
        }
    </style>
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
<!-- Liste des tournois reçus -->
<button onclick="toggleTournamentList()">Show Tournois Reçus</button>

<div id="tournament-list" class="tournament-list">
    <h2>Liste des Tournois Reçus</h2>
    <ul>
        <?php
        if (!empty($tournois_recus)) {
            foreach ($tournois_recus as $tournoi) {
                if ($tournoi['accepted'] == 1) {
                    echo "Tournois déjà accepté avec le clan " . $clanTranslations[$tournoi['id_clan_demandeur']];
                } else {
                    echo "<li>Id du clan demandeur: " . $clanTranslations[$tournoi['id_clan_demandeur']] . ",<br> Date: " . htmlspecialchars($tournoi['date_rencontre']) . ", <br> Format: " . $tournamentFormats[$tournoi['format']]. "</li>";
                    
                    // Formulaire pour accepter le tournoi
                    echo "<form action='../bddConnexion/traitement_tournoisConfirme.php' method='post' style='display:inline;'>";
                    echo "<input type='hidden' name='id_tournoi' value='" . $tournoi['id_tournoi'] . "'>";
                    echo "<input type='submit' name='action' value='Accepter' style='color: green;' onclick=\"showPlayerSelection(" . $tournoi['id_tournoi'] . ")\">";
                    echo "</form>";
                    
                    echo "<form action='../bddConnexion/traitement_tournoisConfirme.php' method='post' style='display:inline;'>";
                    echo "<input type='hidden' name='id_tournoi' value='" . $tournoi['id_tournoi'] . "'>";
                    echo "<input type='submit' name='action' value='Refuser' style='color: red;'>";
                    echo "</form>";  
                }
                
                // Vérifier si le tournoi peut être supprimé
                $date_rencontre = new DateTime($tournoi['date_rencontre']);
                $now = new DateTime();
                $interval = $now->diff($date_rencontre);
                if ($interval->h >= 24 || $interval->d > 0) { // Si la date du tournoi est dans plus de 24 heures
                    echo "<br> <form action='../bddConnexion/delete_tournoi.php' method='post' style='display:inline;' onsubmit=\"return confirm('Êtes-vous sûr de vouloir supprimer ce tournoi ?');\">";
                    echo "<input type='hidden' name='id_tournoi' value='" . $tournoi['id_tournoi'] . "'>";
                    echo "<input type='submit' name='action' value='Supprimer' style='color: red;'>";
                    echo "</form>";
                }
            }
        } else {
            echo "<li>Aucun tournoi trouvé.</li>";
        }
        ?>
    </ul>
</div>

<!-- Liste des tournois demandés -->
<button onclick="toggleDemandedTournamentList()">Show Tournois Demandés</button>

<div id="demanded-tournament-list" class="tournament-list">
    <h2>Liste des Tournois Demandés</h2>
    <ul>
        <?php
        if (!empty($tournois_demandes)) {
            foreach ($tournois_demandes as $tournoi) {
                $accepted = $tournoi['accepted'] ? "Accepter" : "En attente de confirmation";
                echo "<li>Id du clan receveur: " . $clanTranslations[$tournoi['id_clan_receveur']] . ",<br> Date: " . htmlspecialchars($tournoi['date_rencontre']) . ", <br> Statut: " . $accepted . "</li>";
                
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
            }
        } else {
            echo "<li>Aucun tournoi trouvé.</li>";
        }
        ?>
    </ul>
</div>

</body>
</html>
