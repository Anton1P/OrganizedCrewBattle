<?php

include "bddConnexion.php";
include "../APIBrawlhalla/security.php";

$clan_id = $_SESSION['brawlhalla_data']['clan_id'];
$players = isset($_POST['joueurs']) ? $_POST['joueurs'] : []; // Tableau de joueurs


$sql_check_received = "SELECT id_tournoi, accepted FROM tournoi WHERE id_clan_receveur = $clan_id";
$result_received = $conn->query($sql_check_received);

$tournois_recus = []; // Tableau pour stocker les tournois reçus

if ($result_received->num_rows > 0) {
    $_SESSION['notification'] = "Vous avez des tournois en attente !";
    
    // Récupérer les tournois reçus
    while ($row = $result_received->fetch_assoc()) {
        $tournois_recus[] = $row;
    }
}

if (!empty($tournois_recus)) {
    foreach ($tournois_recus as $tournoi) {
        if ($tournoi['accepted'] == 1) {
            if(isset($players )) {
                foreach ($players as $player_id) {
                    $sql_insert_player = "INSERT INTO player_tournoi (id_player, id_tournoi) VALUES (?, ?)";
                    $stmt_insert_player = $conn->prepare($sql_insert_player);
                    $stmt_insert_player->bind_param("ii", $player_id, $tournoi['id_tournoi']);
                    $stmt_insert_player->execute();
                    $stmt_insert_player->close();
                }
            }
        } 
    }
} else {
    echo "<li>Aucun tournoi trouvé.</li>";
}



?>
