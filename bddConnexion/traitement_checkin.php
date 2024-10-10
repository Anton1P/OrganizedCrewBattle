<?php

include "../bddConnexion/bddConnexion.php";


$id_clan = $_SESSION['brawlhalla_data']['clan_id'];

// Initialisation des données de check-in si elles n'existent pas encore
$sql_init = "SELECT * FROM checkin WHERE id_tournoi = ?";
$stmt_init = $conn->prepare($sql_init);
$stmt_init->bind_param("i", $id_tournoi);
$stmt_init->execute();
$result_init = $stmt_init->get_result();

if ($result_init->num_rows == 0) {
    // Insérer l'initialisation pour le check-in si le clan n'a pas encore été enregistré
    $id_clan_demandeur = $_SESSION['id_clan_demandeur']; 
    $id_clan_receveur = $_SESSION['id_clan_receveur'];
    
    $sql_insert = "INSERT INTO checkin (id_tournoi, id_clan_demandeur, id_clan_receveur) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iii", $id_tournoi, $id_clan_demandeur, $id_clan_receveur);
    $stmt_insert->execute();
    $stmt_insert->close();
}

$stmt_init->close();

// Vérifier si le tournoi existe et récupérer les données
$sql = "SELECT * FROM checkin WHERE id_tournoi = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_tournoi);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $checkin_data = $result->fetch_assoc();

    // Vérifier si le clan connecté est le demandeur ou le receveur
    if ($id_clan == $checkin_data['id_clan_demandeur']) {
        $is_checked_in = $checkin_data['clan_demandeur_checkin'];
        $role = 'demandeur';
    } elseif ($id_clan == $checkin_data['id_clan_receveur']) {
        $is_checked_in = $checkin_data['clan_receveur_checkin'];
        $role = 'receveur';
    } else {
        echo "Erreur : votre clan n'est pas impliqué dans ce tournoi.";
        exit();
    }

    // Si le clan n'est pas encore check-in
    if ($is_checked_in == 0) {
        echo "<form action='../view/tournoiReport.php' method='POST'>
                <input type='hidden' name='id_tournoi' value='$id_tournoi'>
                <input type='hidden' name='role' value='$role'>
                <button  class='checkin-button' onclick='activateComponent(\'game1Component\')' type='submit' name='checkin'>Check-in</button>
              </form>";
    }
} else {
    echo "Aucune donnée de check-in trouvée pour ce tournoi.";
}

$stmt->close();
?>


<?php
// Traitement du formulaire de check-in
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkin'])) {
    $id_tournoi = (int)$_POST['id_tournoi'];
    $role = $_POST['role'];

    // Déterminer quelle colonne doit être mise à jour pour le check-in
    if ($role === 'demandeur') {
        $sql = "UPDATE checkin SET clan_demandeur_checkin = 1 WHERE id_tournoi = ?";
    } elseif ($role === 'receveur') {
        $sql = "UPDATE checkin SET clan_receveur_checkin = 1 WHERE id_tournoi = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_tournoi);

    if ($stmt->execute()) {
        echo "Check-in réussi!";

        // Redirection vers la page tournoi après check-in pour le clan actuel
        header("Location: tournoiReport.php?id_tournoi=$id_tournoi");
        exit();
    } else {
        echo "Erreur lors du check-in: " . $conn->error;
    }

    $stmt->close();
}
?>