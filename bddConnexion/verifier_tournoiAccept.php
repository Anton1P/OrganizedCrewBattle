<?php

session_start();
include('bddConnexion.php'); 

$connectedClanId = $_SESSION['brawlhalla_data']['clan_id'];

if (!$connectedClanId) {
    echo json_encode(["status" => "error", "message" => "Clan non connecté"]);
    exit;
}

// Préparer la requête SQL pour vérifier s'il y a un tournoi accepté pour ce clan
$sql = "SELECT id_tournoi FROM tournoi WHERE (id_clan_demandeur = ? OR id_clan_receveur = ?) AND accepted = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $connectedClanId, $connectedClanId);
$stmt->execute();
$result = $stmt->get_result();

// Si un tournoi est trouvé
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc(); // Correction pour récupérer l'ID du tournoi
    echo json_encode(["status" => "success", "id_tournoi" => $row['id_tournoi']]);
} else {
    echo json_encode(["status" => "no_tournament"]);
}

$stmt->close();
$conn->close();
?>
