<?php
// Function to sanitize input by replacing unwanted characters with a space
function sanitizeInput($input) {
    // Replace any character that is not a letter, number, or space with a space
    return preg_replace('/[^A-Za-z0-9 ]/', ' ', $input);
}

if ($rank === 'Leader' || $rank === 'Officer') {
    //? Programme qui met les clans du joueur dans la bdd
    // Vérifier si le clan existe déjà
    $sanitized_clan_name = sanitizeInput($clan_name); // Sanitize the clan name
    $sql_check = "SELECT * FROM clans WHERE id_clan = $clan_id";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
        // echo "Le clan avec l'ID $clan_id existe déjà. Aucune insertion effectuée.";
    } else {
        // Préparer la requête d'insertion
        $sql = "INSERT INTO clans (id_clan, nom_clan, wins, loses, elo_rating, elo_peak) VALUES ($clan_id , '$sanitized_clan_name', 0, 0, 1200, 1200)";

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
        $name_bdd = sanitizeInput($clan_member["name"]); // Sanitize the player name

        // Vérifier si le joueur existe déjà dans la base de données
        $sql_check = "SELECT * FROM players WHERE id_player = $brawlhalla_id";
        $result = $conn->query($sql_check);

        if ($result->num_rows > 0) {
            // Le joueur existe, vérifier si son clan a changé
            $row = $result->fetch_assoc();
            $current_clan_id = $row['id_clan'];

            if ($current_clan_id != $clan_id) {
                // Le clan a changé, mettre à jour la valeur de id_clan
                $sql_update = "UPDATE players SET id_clan = $clan_id WHERE id_player = $brawlhalla_id";
                
                if ($conn->query($sql_update) === TRUE) {
                    // echo "Le clan du joueur a été mis à jour avec succès !";
                } else {
                    echo "Erreur lors de la mise à jour du clan : " . $sql_update . "<br>" . $conn->error;
                }
            } else {
                // echo "Le joueur est déjà dans le bon clan.";
            }
        } else {
            // Le joueur n'existe pas, insertion d'un nouveau joueur
            $sql = "INSERT INTO players (id_player, player_name, id_clan) VALUES ($brawlhalla_id, '$name_bdd', $clan_id)";

            if ($conn->query($sql) === TRUE) {
                // echo "Nouveau joueur ajouté avec succès !";
            } else {
                echo "Erreur : " . $sql . "<br>" . $conn->error;
            }
        }
    }
}
?>
