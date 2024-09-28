<?php
//! CRON TASK 1/day
include "bddConnexion.php";
// Requête pour récupérer les clans sans joueurs associés
$sql = "SELECT c.id_clan 
        FROM clans c 
        LEFT JOIN players p ON c.id_clan = p.id_clan 
        WHERE p.id_clan IS NULL";

$result = $conn->query($sql);

// Boucle pour supprimer chaque clan sans joueurs
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $id_clan = $row['id_clan'];
        
        // Suppression du clan
        $delete_sql = "DELETE FROM clans WHERE id_clan = $id_clan";
        if ($conn->query($delete_sql) === TRUE) {
          echo "Clan ID $id_clan supprimé.<br>";
        } else {
            echo "Erreur lors de la suppression du clan ID $id_clan: " . $conn->error . "<br>";
        }
    }
} else {
    echo "Aucun clan sans joueurs trouvé.";
}

$conn->close();
?>
