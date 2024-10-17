<?php 
include "../bddConnexion/bddConnexion.php"; // Database connection

// Function to calculate new Elo points
function calculateElo($elo_winner, $elo_loser, $k_factor = 30) {
    $expected_winner = 1 / (1 + pow(10, ($elo_loser - $elo_winner) / 400));
    $expected_loser = 1 - $expected_winner;

    $new_elo_winner = $elo_winner + $k_factor * (1 - $expected_winner);
    $new_elo_loser = $elo_loser + $k_factor * (0 - $expected_loser);

    return [$new_elo_winner, $new_elo_loser];
}

if (!isset($_GET['id_tournoi'])) {
    echo "Error: No tournament ID specified."; // Translated text
    exit();
}

$id_tournoi = $_GET['id_tournoi'];

if (!is_numeric($id_tournoi)) {
    echo "Error: Invalid tournament ID.";
    var_dump($id_tournoi);
    exit();
}

// Retrieve tournament information, including clans and results
$sql_tournoi = "SELECT id_clan_demandeur, id_clan_receveur, clan_demandeur_result, clan_receveur_result 
                FROM verif_report 
                WHERE id_tournoi = ?";
$stmt_tournoi = $conn->prepare($sql_tournoi);
$stmt_tournoi->bind_param("i", $id_tournoi);
$stmt_tournoi->execute();
$result_tournoi = $stmt_tournoi->get_result();

// Check if the tournament exists
if ($result_tournoi->num_rows === 0) {
    echo "Error: No tournament found."; // Translated text
    exit();
}

$tournoi_info = $result_tournoi->fetch_assoc();
$id_clan_demandeur = $tournoi_info['id_clan_demandeur'];
$id_clan_receveur = $tournoi_info['id_clan_receveur'];
$resultat_demandeur = $tournoi_info['clan_demandeur_result'];

// Determine the tournament result
if ($resultat_demandeur == 1) {
    $resultat_receveur = 0;
} elseif ($resultat_demandeur == 0) {
    $resultat_receveur = 1;
} else {
    echo "Error in the results."; // Translated text
    exit();
}

// Retrieve the current Elo points of both clans
$sql = "SELECT id_clan, wins, loses, elo_rating FROM clans WHERE id_clan IN (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_clan_demandeur, $id_clan_receveur);
$stmt->execute();
$result = $stmt->get_result();

$clans = [];
while ($row = $result->fetch_assoc()) {
    $clans[$row['id_clan']] = $row;
}

// Calculate new Elo points
if ($resultat_demandeur == 1 && $resultat_receveur == 0) {
    [$new_elo_demandeur, $new_elo_receveur] = calculateElo($clans[$id_clan_demandeur]['elo_rating'], $clans[$id_clan_receveur]['elo_rating']);
    
    $clans[$id_clan_demandeur]['wins'] += 1;
    $clans[$id_clan_receveur]['loses'] += 1;
} elseif ($resultat_demandeur == 0 && $resultat_receveur == 1) {
    [$new_elo_receveur, $new_elo_demandeur] = calculateElo($clans[$id_clan_receveur]['elo_rating'], $clans[$id_clan_demandeur]['elo_rating']);
    
    $clans[$id_clan_receveur]['wins'] += 1;
    $clans[$id_clan_demandeur]['loses'] += 1;
} else {
    echo "Error in the results."; // Translated text
    exit();
}

// Update Elo points, wins, and losses for both clans
$sql_update = "UPDATE clans SET elo_rating = ?, wins = ?, loses = ? WHERE id_clan = ?";
$stmt_update = $conn->prepare($sql_update);

// Update for the requesting clan
$stmt_update->bind_param("iiii", $new_elo_demandeur, $clans[$id_clan_demandeur]['wins'], $clans[$id_clan_demandeur]['loses'], $id_clan_demandeur);
$stmt_update->execute();

// Update for the receiving clan
$stmt_update->bind_param("iiii", $new_elo_receveur, $clans[$id_clan_receveur]['wins'], $clans[$id_clan_receveur]['loses'], $id_clan_receveur);
$stmt_update->execute();

// Delete the tournament after updating scores and Elo
$sql_delete = "DELETE FROM tournoi WHERE id_tournoi = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $id_tournoi);
$stmt_delete->execute();

// Insert results into the tournament_results table
$sql_insert_result = "INSERT INTO tournoi_results (id_tournoi, id_winner, id_loser) VALUES (?, ?, ?)";
$stmt_insert_result = $conn->prepare($sql_insert_result);

// Determine who is the winner and who is the loser, then insert
if ($resultat_demandeur == 1) {
    $id_winner = $id_clan_demandeur;
    $id_loser = $id_clan_receveur;
} else {
    $id_winner = $id_clan_receveur;
    $id_loser = $id_clan_demandeur;
}

$stmt_insert_result->bind_param("iii", $id_tournoi, $id_winner, $id_loser);
$stmt_insert_result->execute();

// Retrieve the current elo_peak of both clans
$sql_peak = "SELECT id_clan, elo_peak FROM clans WHERE id_clan IN (?, ?)";
$stmt_peak = $conn->prepare($sql_peak);
$stmt_peak->bind_param("ii", $id_clan_demandeur, $id_clan_receveur);
$stmt_peak->execute();
$result_peak = $stmt_peak->get_result();

$elo_peaks = [];
while ($row_peak = $result_peak->fetch_assoc()) {
    $elo_peaks[$row_peak['id_clan']] = $row_peak['elo_peak'];
}

// Update the elo_peak for the requesting clan if necessary
if ($new_elo_demandeur > $elo_peaks[$id_clan_demandeur]) {
    $sql_update_peak = "UPDATE clans SET elo_peak = ? WHERE id_clan = ?";
    $stmt_update_peak = $conn->prepare($sql_update_peak);
    $stmt_update_peak->bind_param("ii", $new_elo_demandeur, $id_clan_demandeur);
    $stmt_update_peak->execute();
}

// Update the elo_peak for the receiving clan if necessary
if ($new_elo_receveur > $elo_peaks[$id_clan_receveur]) {
    $sql_update_peak = "UPDATE clans SET elo_peak = ? WHERE id_clan = ?";
    $stmt_update_peak = $conn->prepare($sql_update_peak);
    $stmt_update_peak->bind_param("ii", $new_elo_receveur, $id_clan_receveur);
    $stmt_update_peak->execute();
}

$stmt_peak->close();
$stmt_update_peak->close();
$stmt_update->close();
$stmt_tournoi->close();
$stmt_delete->close();
$stmt_insert_result->close();
$conn->close();

$_SESSION['notification'] = "Tournament processed successfully. Results have been recorded."; // Translated text
header("Location: ../view/AdminPanel.php");
exit();
?>
