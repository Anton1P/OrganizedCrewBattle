<?php

include "../bddConnexion/bddConnexion.php";

function getClanTranslations($conn) {
    $sql = "SELECT id_clan, nom_clan FROM clans";
    $result = $conn->query($sql);
    
    $translations = [];
    while ($row = $result->fetch_assoc()) {
        $translations[$row['id_clan']] = $row['nom_clan'];
    }
    
    return $translations;
}

function getTournamentFormats($conn) {
    
    $formats = [
        1 => 'Crew Battle 1',
        2 => 'Crew Battle 2',
        3 => 'Crew Battle 3',
        4 => 'Crew Battle 4',
        5 => 'Crew Battle 5'
    ];
       
    return $formats;
}

function getPlayerNames($conn) {
    $sql = "SELECT id_player, player_name FROM players";
    $result = $conn->query($sql);
    
    $players = [];
    while ($row = $result->fetch_assoc()) {
        $players[$row['id_player']] = $row['player_name'];
    }
    
    return $players;
}

function getTournamentDates($conn) {
    
    function formatDateTime($dateString) {
        $date = strtotime($dateString); // Convertir la chaîne de date en timestamp
        if ($date === false) {
            return "Date invalide"; // Gestion des erreurs
        }
        return date("d/m/Y \à H:i", $date); // Formatage de la date
    }

    $sql = "SELECT id_tournoi, date_rencontre FROM tournoi"; // Modifiez la requête si nécessaire
    $result = $conn->query($sql);
    
    $dates = [];
    while ($row = $result->fetch_assoc()) {
        $dates[$row['id_tournoi']] = formatDateTime($row['date_rencontre']); // Utiliser la fonction de formatage
    }
    
    return $dates;
}


$clanTranslations = getClanTranslations($conn);
$tournamentFormats = getTournamentFormats($conn);
$playerNames = getPlayerNames($conn);
$tournamentDates = getTournamentDates($conn);

?>
