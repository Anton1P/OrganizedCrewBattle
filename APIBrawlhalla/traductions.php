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

// Récupérer toutes les traductions
$clanTranslations = getClanTranslations($conn);
$tournamentFormats = getTournamentFormats($conn);
$playerNames = getPlayerNames($conn);

// Vous pouvez maintenant utiliser ces traductions dans d'autres fichiers
?>
