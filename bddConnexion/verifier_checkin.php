<?php
include "../bddConnexion/bddConnexion.php";

if (isset($_POST['id_tournoi'])) {
    $id_tournoi = $_POST['id_tournoi'];

    // Requête pour vérifier les check-ins et récupérer brawlhalla_room depuis la table tournoi
    $sql = "
        SELECT 
            c.id_checkin, 
            c.clan_demandeur_checkin, 
            c.clan_receveur_checkin, 
            c.id_clan_demandeur, 
            c.id_clan_receveur,
            t.brawlhalla_room  
        FROM 
            checkin c
        INNER JOIN 
            tournoi t ON c.id_tournoi = t.id_tournoi 
        WHERE 
            c.id_tournoi = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_tournoi);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $checkin_data = $result->fetch_assoc();
        echo json_encode([
            'id_checkin' => $checkin_data['id_checkin'],
            'checkin_demandeur' => $checkin_data['clan_demandeur_checkin'],
            'checkin_receveur' => $checkin_data['clan_receveur_checkin'],
            'id_clan_demandeur' => $checkin_data['id_clan_demandeur'], // ID du clan demandeur
            'id_clan_receveur' => $checkin_data['id_clan_receveur'], // ID du clan receveur
            'brawlhalla_room' => $checkin_data['brawlhalla_room'] // Récupération de la colonne brawlhalla_room
        ]);
    } else {
        echo json_encode(['error' => 'Aucun check-in trouvé.']);
    }

    $stmt->close();
}
?>
