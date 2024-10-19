<?php
include "bddConnexion.php"; // Assurez-vous que ce fichier inclut la connexion à la base de données
include "../APIBrawlhalla/security.php";

$askClan_id = $_SESSION['brawlhalla_data']['clan_id']; 
$askedClan_ids = isset($_POST['clan_ids']) ? $_POST['clan_ids'] : []; // Retrieve selected clans
$accepted = 0; // Par défaut, la demande n'est pas acceptée

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

    // Retrieve formats and their numbers
    if (isset($_POST['format']) && isset($_POST['format_number']) && isset($_POST['format_order'])) {
        $formats = $_POST['format']; // Array of selected formats
        $format_numbers = $_POST['format_number']; // Array of corresponding numbers
        $format_order = json_decode($_POST['format_order']); // Decode the format order

        // Initialize format order variables
        $crew_battle_format_order = null;
        $two_vs_two_format_order = null;
        $one_vs_one_format_order = null;

        // Determine the order for each format
        foreach ($formats as $index => $format) {
            switch ($format) {
                case 'crew_battle':
                    $crew_battle_format_order = $index + 1; // 1, 2, 3...
                    break;
                case '2v2':
                    $two_vs_two_format_order = $index + 1; // 1, 2, 3...
                    break;
                case '1v1':
                    $one_vs_one_format_order = $index + 1; // 1, 2, 3...
                    break;
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

                    $sql_check_clan = "
                    SELECT * 
                    FROM tournoi_results 
                    WHERE 
                        (id_winner = ? AND id_loser = ? OR id_winner = ? AND id_loser = ?)
                        AND DATEDIFF(NOW(), date_finish) < 3";
                        
                    $stmt_check_clan = $conn->prepare($sql_check_clan);
                    $stmt_check_clan->bind_param("iiii", $askedClan_id, $askClan_id, $askClan_id, $askedClan_id);
                    $stmt_check_clan->execute();
                    $result_date_finish = $stmt_check_clan->get_result();
                    
                    // Check if a result is found
                    if ($result_date_finish->num_rows > 0) {
                        // The tournament exists and the date_finish is less than 3 days ago
                        $_SESSION['notification'] = "Error: Your last encounter against this clan was no more than 3 days ago."; // Translated text
                        $_SESSION['from_treatment'] = true; 
                        header("Location: ../view/ask.php");
                        exit(); 
                    } 
                    else {
                      
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

                            // Prepare the tournament insertion
                            $sql_insert = "INSERT INTO tournoi (id_clan_demandeur, id_clan_receveur, date_rencontre, accepted, crew_battle_format, two_vs_two_format, one_vs_one_format, crew_battle_format_order, two_vs_two_format_order, one_vs_one_format_order) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            $stmt_insert = $conn->prepare($sql_insert);

                            // Initialize the format counts
                            $crew_battle_format = (int)($format_numbers[array_search('crew_battle', $formats)] ?? 0);
                            $two_vs_two_format = (int)($format_numbers[array_search('2v2', $formats)] ?? 0);
                            $one_vs_one_format = (int)($format_numbers[array_search('1v1', $formats)] ?? 0);

                            // Bind parameters to the query
                            $stmt_insert->bind_param("iisssiiiii", $askClan_id, $askedClan_id, $date_rencontre, $accepted, $crew_battle_format, $two_vs_two_format, $one_vs_one_format, $crew_battle_format_order, $two_vs_two_format_order, $one_vs_one_format_order);

                            // Execute the query and check
                            if ($stmt_insert->execute()) {
                                $_SESSION['notification'] = "Tournament request made."; // Translated text
                            } else {
                                echo "Error creating the tournament: " . $stmt_insert->error; // Translated text
                            }

                            // Close the insertion statement
                            $stmt_insert->close();
                            header("Location: ../view/AdminPanel.php");
                            exit(); 
                        } else {
                            echo "The receiving clan does not exist."; // Translated text
                        }

                        // Close the query for checking the receiving clan
                        $stmt_check_clan->close();
                    }
                }

                // Close the query for checking the combination of the two clans
                $stmt_check_combination->close();
            }
        } else {
            $_SESSION['notification'] = "Clan IDs cannot be empty."; // Translated text
            $_SESSION['from_treatment'] = true; 
            header("Location: ../view/ask.php");
            exit();
        }
    } else {
        echo "Format data is missing."; // Translated text
    }
}
?>
