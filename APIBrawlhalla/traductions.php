<?php
  $tournamentId = 0;
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
    $formats = []; // Tableau associatif pour stocker les formats de chaque tournoi

    // Obtenir tous les tournois
    $sql = "SELECT id_tournoi, crew_battle_format, two_vs_two_format, one_vs_one_format, crew_battle_format_order, two_vs_two_format_order, one_vs_one_format_order FROM tournoi";
    $result = $conn->query($sql);

    while ($tournoi = $result->fetch_assoc()) {
        // Tableau pour stocker les formats avec leur ordre
        $orderedFormats = [];

        // Ajouter les formats selon leur ordre
        if ($tournoi['crew_battle_format'] > 0 && $tournoi['crew_battle_format_order'] !== null) {
            $orderedFormats[$tournoi['crew_battle_format_order']] = "Crew Battle Bo" . $tournoi['crew_battle_format'];
        }

        if ($tournoi['two_vs_two_format'] > 0 && $tournoi['two_vs_two_format_order'] !== null) {
            $orderedFormats[$tournoi['two_vs_two_format_order']] = "2v2 Bo" . $tournoi['two_vs_two_format'];
        }

        if ($tournoi['one_vs_one_format'] > 0 && $tournoi['one_vs_one_format_order'] !== null) {
            $orderedFormats[$tournoi['one_vs_one_format_order']] = "1v1 Bo" . $tournoi['one_vs_one_format'];
        }

        // Trier les formats par ordre et construire la chaîne formatée
        ksort($orderedFormats); // Trie par clé (ordre)

        // Joindre les formats avec " -> "
        $formats[$tournoi['id_tournoi']] = implode(' -> ', $orderedFormats);
    }

    return $formats; // Retourner le tableau associatif
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


$clanTranslations = getClanTranslations($conn);
$tournamentFormats = getTournamentFormats($conn);
$playerNames = getPlayerNames($conn);


?>
