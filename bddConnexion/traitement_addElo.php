<?php 
include "../bddConnexion/bddConnexion.php"; // Connexion à la base de données

// Fonction pour calculer les nouveaux points Elo
function calculateElo($elo_winner, $elo_loser, $k_factor = 30) {
    $expected_winner = 1 / (1 + pow(10, ($elo_loser - $elo_winner) / 400));
    $expected_loser = 1 - $expected_winner;

    $new_elo_winner = $elo_winner + $k_factor * (1 - $expected_winner);
    $new_elo_loser = $elo_loser + $k_factor * (0 - $expected_loser);

    return [$new_elo_winner, $new_elo_loser];
}

if (!isset($_GET['id_tournoi'])) {
    echo "Erreur : Aucun ID de tournoi spécifié.";
    exit();
}

$id_tournoi = $_GET['id_tournoi'];

// Récupérer les informations sur le tournoi, y compris les clans et les résultats
$sql_tournoi = "SELECT id_clan_demandeur, id_clan_receveur, clan_demandeur_result, clan_receveur_result 
                FROM verif_report 
                WHERE id_tournoi = ?";
$stmt_tournoi = $conn->prepare($sql_tournoi);
$stmt_tournoi->bind_param("i", $id_tournoi);
$stmt_tournoi->execute();
$result_tournoi = $stmt_tournoi->get_result();

// Vérifiez si le tournoi existe
if ($result_tournoi->num_rows === 0) {
    echo "Erreur : Aucun tournoi trouvé.";
    exit();
}

$tournoi_info = $result_tournoi->fetch_assoc();
$id_clan_demandeur = $tournoi_info['id_clan_demandeur'];
$id_clan_receveur = $tournoi_info['id_clan_receveur'];
$resultat_demandeur = $tournoi_info['clan_demandeur_result'];

// Déterminer le résultat du tournoi
if ($resultat_demandeur == 1) {
    $resultat_receveur = 0;
} elseif ($resultat_demandeur == 0) {
    $resultat_receveur = 1;
} else {
    echo "Erreur dans les résultats.";
    exit();
}

// Récupérer les points Elo actuels des deux clans
$sql = "SELECT id_clan, wins, loses, elo_rating FROM clans WHERE id_clan IN (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_clan_demandeur, $id_clan_receveur);
$stmt->execute();
$result = $stmt->get_result();

$clans = [];
while ($row = $result->fetch_assoc()) {
    $clans[$row['id_clan']] = $row;
}

// Calculer les nouveaux points Elo
if ($resultat_demandeur == 1 && $resultat_receveur == 0) {
    [$new_elo_demandeur, $new_elo_receveur] = calculateElo($clans[$id_clan_demandeur]['elo_rating'], $clans[$id_clan_receveur]['elo_rating']);
    
    $clans[$id_clan_demandeur]['wins'] += 1;
    $clans[$id_clan_receveur]['loses'] += 1;
} elseif ($resultat_demandeur == 0 && $resultat_receveur == 1) {
    [$new_elo_receveur, $new_elo_demandeur] = calculateElo($clans[$id_clan_receveur]['elo_rating'], $clans[$id_clan_demandeur]['elo_rating']);
    
    $clans[$id_clan_receveur]['wins'] += 1;
    $clans[$id_clan_demandeur]['loses'] += 1;
} else {
    echo "Erreur dans les résultats.";
    exit();
}

// Mise à jour des points Elo, victoires et défaites pour les deux clans
$sql_update = "UPDATE clans SET elo_rating = ?, wins = ?, loses = ? WHERE id_clan = ?";
$stmt_update = $conn->prepare($sql_update);

// Mise à jour pour le clan demandeur
$stmt_update->bind_param("diii", $new_elo_demandeur, $clans[$id_clan_demandeur]['wins'], $clans[$id_clan_demandeur]['loses'], $id_clan_demandeur);
$stmt_update->execute();

// Mise à jour pour le clan receveur
$stmt_update->bind_param("diii", $new_elo_receveur, $clans[$id_clan_receveur]['wins'], $clans[$id_clan_receveur]['loses'], $id_clan_receveur);
$stmt_update->execute();

// Suppression du tournoi après mise à jour des scores et Elo
$sql_delete = "DELETE FROM tournoi WHERE id_tournoi = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $id_tournoi);
$stmt_delete->execute();

// Insertion des résultats dans la table tournoi_results
$sql_insert_result = "INSERT INTO tournoi_results (id_tournoi, id_winner, id_loser) VALUES (?, ?, ?)";
$stmt_insert_result = $conn->prepare($sql_insert_result);

// Déterminer qui est gagnant et qui est perdant, puis insérer
if ($resultat_demandeur == 1) {
    $id_winner = $id_clan_demandeur;
    $id_loser = $id_clan_receveur;
} else {
    $id_winner = $id_clan_receveur;
    $id_loser = $id_clan_demandeur;
}

$stmt_insert_result->bind_param("iii", $id_tournoi, $id_winner, $id_loser);
$stmt_insert_result->execute();

$stmt_update->close();
$stmt_tournoi->close();
$stmt_delete->close();
$stmt_insert_result->close();
$conn->close();

$_SESSION['notification'] = "Tournoi traité avec succès. Les résultats ont été enregistrés.";
header("Location: ../view/AdminPanel.php");
exit();
?>
