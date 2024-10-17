<?php
include "bddConnexion.php";
include "../APIBrawlhalla/security.php";

$askClan_id = $_SESSION['brawlhalla_data']['clan_id']; 
$askedClan_ids = isset($_POST['clan_ids']) ? $_POST['clan_ids'] : []; // Retrieve selected clans
$format = $_POST['format'];
$accepted = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date_rencontre = $_POST['date_rencontre'];

    if (empty($date_rencontre)) {
        $_SESSION['notification'] = "Error: The meeting date cannot be empty."; // Translated text
        $_SESSION['from_treatment'] = true; 
        header("Location: ../view/ask.php");
        exit(); 
    } else {
        // Validate the date
        $timestamp = strtotime($date_rencontre);
        if ($timestamp === false || $timestamp === 0) {
            $_SESSION['notification'] = "Error: Invalid meeting date."; // Translated text
            $_SESSION['from_treatment'] = true; 
            header("Location: ../view/ask.php");
            exit(); 
        }
    }
}

// Check that at least one clan has been selected
if (!empty($askClan_id) && !empty($askedClan_ids)) {
    foreach ($askedClan_ids as $askedClan_id) {
        // Check if the combination of the two clans already exists in the tournament
        $sql_check_combination = "SELECT * FROM tournoi WHERE id_clan_demandeur = ? AND id_clan_receveur = ?";
        $stmt_check_combination = $conn->prepare($sql_check_combination);
        $stmt_check_combination->bind_param("ii", $askClan_id, $askedClan_id);
        $stmt_check_combination->execute();
        $result_combination = $stmt_check_combination->get_result();

        if ($result_combination->num_rows > 0) {
            // If the combination already exists, do not insert the tournament
            $_SESSION['notification'] = "Error: These two clans are already engaged in a tournament."; // Translated text
            $_SESSION['from_treatment'] = true; 
            header("Location: ../view/ask.php");
            exit(); 
        } else {
            // Check if the receiving clan exists in the database
            $sql_check_clan = "SELECT * FROM clans WHERE id_clan = ?";
            $stmt_check_clan = $conn->prepare($sql_check_clan);
            $stmt_check_clan->bind_param("i", $askedClan_id);
            $stmt_check_clan->execute();
            $result_clan = $stmt_check_clan->get_result();

            if ($result_clan->num_rows > 0) {
                // Check if the clan has already made a tournament request
                $sql_check_previous_request = "SELECT * FROM tournoi WHERE id_clan_demandeur = ?";
                $stmt_check_previous_request = $conn->prepare($sql_check_previous_request);
                $stmt_check_previous_request->bind_param("i", $askClan_id);
                $stmt_check_previous_request->execute();
                $result_previous_request = $stmt_check_previous_request->get_result();

                if ($result_previous_request->num_rows > 0) { 
                    // Tournament requests found for this clan
                    while ($previous_data = $result_previous_request->fetch_assoc()) {
                        $previous_date = new DateTime($previous_data['date_rencontre']);
                        $new_date = new DateTime($date_rencontre);
                        $diff = $new_date->diff($previous_date);  
                        // Check if the new date is at least 1 hour later
                        if ($diff->h < 1 && $diff->days == 0) { // Less than one hour but on the same day
                            $_SESSION['notification'] = "Error: You have already requested a tournament on the same day with less than one hour between."; // Translated text
                            $_SESSION['from_treatment'] = true; 
                            header("Location: ../view/ask.php");
                            exit();
                        }
                    }
                }

                // Prepare the tournament insertion query
                $sql_insert = "INSERT INTO tournoi (id_clan_demandeur, id_clan_receveur, date_rencontre, format, accepted) 
                               VALUES (?, ?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("iissi", $askClan_id, $askedClan_id, $date_rencontre, $format, $accepted);

                // Execute the query and check
                if ($stmt_insert->execute()) {
                    $_SESSION['notification'] = "Tournament request made."; // Translated text
                } else {
                    echo "Error creating the tournament: " . $stmt_insert->error; // Translated text
                }

                // Close the query
                $stmt_insert->close();
            } else {
                echo "The receiving clan does not exist."; // Translated text
            }

            // Close the query for checking the receiving clan
            $stmt_check_clan->close();
        }

        // Close the query for checking the combination of the two clans
        $stmt_check_combination->close();
    }

    header("Location: ../view/AdminPanel.php");
    exit(); 
} else {
    $_SESSION['notification'] = "Clan IDs cannot be empty."; // Translated text
    $_SESSION['from_treatment'] = true; 
    header("Location: ../view/ask.php");
    exit();
}
?>
