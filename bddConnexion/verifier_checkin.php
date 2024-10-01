<?php
include "../bddConnexion/bddConnexion.php";

if (isset($_POST['id_tournoi'])) {
    $id_tournoi = $_POST['id_tournoi'];

    // Requête pour vérifier le check-in des deux équipes
    $sql = "SELECT clan_demandeur_checkin, clan_receveur_checkin FROM checkin WHERE id_tournoi = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_tournoi);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $checkin_data = $result->fetch_assoc();
        echo json_encode([
            'checkin_demandeur' => $checkin_data['clan_demandeur_checkin'],
            'checkin_receveur' => $checkin_data['clan_receveur_checkin']
        ]);
    } else {
        echo json_encode(['error' => 'Aucun check-in trouvé.']);
    }

    $stmt->close();
}
?>
