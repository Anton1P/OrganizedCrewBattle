<?php
    
// Vérifier si le clan existe déjà
$sql_check = "SELECT * FROM clans WHERE id_clan = $clan_id";
$result = $conn->query($sql_check);

if ($result->num_rows > 0) {
    // echo "Le clan avec l'ID $clan_id existe déjà. Aucune insertion effectuée.";
} else {

    // Préparer la requête d'insertion
    $sql = "INSERT INTO clans (id_clan, nom_clan, wins, loses, elo_rating) VALUES ($clan_id , '$clan_name', 0, 0, 1200)";

        if ($conn->query($sql) === TRUE) {
            // echo "Nouveau clan créé avec succès !";
        } else {
            echo "Erreur : " . $sql . "<br>" . $conn->error;
        }
    }
?>  