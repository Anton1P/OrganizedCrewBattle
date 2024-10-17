<?php

function displayEloChange($elo_change, $clan_type) {
    $elo_change = round($elo_change);
    
    $elo_history = ($elo_change > 0 ? '+' : '') . $elo_change;

    $color = $elo_change > 0 ? 'limegreen' : 'red';
    echo "<p style='color:$color;'>$elo_history</p>";
}

$elo_change_demandeur = isset($_GET['elo_change_demandeur']) ? $_GET['elo_change_demandeur'] : null;
$elo_change_receveur = isset($_GET['elo_change_receveur']) ? $_GET['elo_change_receveur'] : null;

include "../bddConnexion/bddConnexion.php"; 
$clan_id = '2161882';
$id_tournoi = isset($_GET['id_tournoi']) ? $_GET['id_tournoi'] : null;
$id_clan_demandeur = isset($_GET['id_clan_demandeur']) ? $_GET['id_clan_demandeur'] : null;
$id_clan_receveur = isset($_GET['id_clan_receveur']) ? $_GET['id_clan_receveur'] : null;

if ($id_tournoi !== null && $clan_id !== null) {
    // Requête SQL pour obtenir les résultats du tournoi
    $sql = "SELECT id_winner, id_loser FROM tournoi_results WHERE id_tournoi = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_tournoi);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérifier si des résultats existent
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_winner = $row['id_winner'];
        $id_loser = $row['id_loser'];

        // Afficher le changement d'Elo pour le clan demandeur
        if ($clan_id === $id_clan_demandeur) {
            if ($elo_change_demandeur !== null) {
                displayEloChange($elo_change_demandeur, 'Demandeur');
            }
        }

        // Afficher le changement d'Elo pour le clan receveur
        if ($clan_id === $id_clan_receveur) {
            if ($elo_change_receveur !== null) {
                displayEloChange($elo_change_receveur, 'Receveur');
            }
        }
    } 
    $stmt->close();
} 

$conn->close();

?>
