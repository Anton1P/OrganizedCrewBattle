<?php

//? Programme qui met les clans du joueur dans la bdd
// Vérifier si le clan existe déjà
$sql_check = "SELECT * FROM clans WHERE id_clan = $clan_id";
$result = $conn->query($sql_check);

if ($result->num_rows > 0) {
    // echo "Le clan avec l'ID $clan_id existe déjà. Aucune insertion effectuée.";
} else {

    // Préparer la requête d'insertion
    $sql = "INSERT INTO clans (id_clan, nom_clan, wins, loses, elo_rating, elo_peak) VALUES ($clan_id , '$clan_name', 0, 0, 1200, 1200)";

        if ($conn->query($sql) === TRUE) {
            // echo "Nouveau clan créé avec succès !";
        } else {
            echo "Erreur : " . $sql . "<br>" . $conn->error;
        }
    }


//? Programme qui met les joueurs du clan dans la bdd
// Préparez la requête d'insertion
$stmt = $conn->prepare("INSERT INTO players (id_player, player_name, id_clan) VALUES (?, ?, ?)");

// Vérifiez si la préparation a échoué
if (!$stmt) {
    die("Preparation failed: " . $conn->error);
}

foreach ($clan_members as $clan_member) {
    $brawlhalla_id = $clan_member["brawlhalla_id"];
    $name_bdd = $clan_member["name"];

    $sql_check = "SELECT * FROM players WHERE id_player = $brawlhalla_id";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
           // echo "Le joueur avec l'ID $bralwlhalla_id existe déjà. Aucune insertion effectuée.";
    } else{
        
        
        $sql = "INSERT INTO players (id_player, player_name, id_clan) VALUES ( $brawlhalla_id, '$name_bdd', $clan_id )";

        if ($conn->query($sql) === TRUE) {
            // echo "Nouveau clan créé avec succès !";
        } else {
            echo "Erreur : " . $sql . "<br>" . $conn->error;
        }
        
    }
}



