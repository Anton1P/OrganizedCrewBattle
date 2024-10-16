<?php

include "../bddConnexion/bddConnexion.php";

$id_clan = $_SESSION['brawlhalla_data']['clan_id'];

// Initialize check-in data if it doesn't exist yet
$sql_init = "SELECT * FROM checkin WHERE id_tournoi = ?";
$stmt_init = $conn->prepare($sql_init);
$stmt_init->bind_param("i", $id_tournoi);
$stmt_init->execute();
$result_init = $stmt_init->get_result();

if ($result_init->num_rows == 0) {
    // Insert initialization for check-in if the clan hasn't been recorded yet
    $id_clan_demandeur = $_SESSION['id_clan_demandeur']; 
    $id_clan_receveur = $_SESSION['id_clan_receveur'];
    
    $sql_insert = "INSERT INTO checkin (id_tournoi, id_clan_demandeur, id_clan_receveur) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iii", $id_tournoi, $id_clan_demandeur, $id_clan_receveur);
    $stmt_insert->execute();
    $stmt_insert->close();
}

$stmt_init->close();

// Check if the tournament exists and retrieve the data
$sql = "SELECT * FROM checkin WHERE id_tournoi = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_tournoi);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $checkin_data = $result->fetch_assoc();

    // Check if the connected clan is the requester or the receiver
    if ($id_clan == $checkin_data['id_clan_demandeur']) {
        $is_checked_in = $checkin_data['clan_demandeur_checkin'];
        $role = 'requester'; // Translated text
    } elseif ($id_clan == $checkin_data['id_clan_receveur']) {
        $is_checked_in = $checkin_data['clan_receveur_checkin'];
        $role = 'receiver'; // Translated text
    } else {
        echo "Error: your clan is not involved in this tournament."; // Translated text
        exit();
    }

    // If the clan is not checked in yet
    if ($is_checked_in == 0) {
        echo "<form action='../view/tournoiReport.php' method='POST'>
                <input type='hidden' name='id_tournoi' value='$id_tournoi'>
                <input type='hidden' name='role' value='$role'>
                <button class='checkin-button' onclick='activateComponent(\"game1Component\")' type='submit' name='checkin'>Check-in</button>
              </form>";
    }
} else {
    echo "No check-in data found for this tournament."; // Translated text
}

$stmt->close();
?>

<?php
// Process the check-in form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkin'])) {
    $id_tournoi = (int)$_POST['id_tournoi'];
    $role = $_POST['role'];

    // Determine which column should be updated for the check-in
    if ($role === 'requester') {
        $sql = "UPDATE checkin SET clan_demandeur_checkin = 1 WHERE id_tournoi = ?"; // Translated text
    } elseif ($role === 'receiver') {
        $sql = "UPDATE checkin SET clan_receveur_checkin = 1 WHERE id_tournoi = ?"; // Translated text
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_tournoi);

    if ($stmt->execute()) {
        echo "Check-in successful!"; // Translated text

        // Redirect to the tournament page after check-in for the current clan
        header("Location: tournoiReport.php?id_tournoi=$id_tournoi");
        exit();
    } else {
        echo "Error during check-in: " . $conn->error; // Translated text
    }

    $stmt->close();
}
?>
