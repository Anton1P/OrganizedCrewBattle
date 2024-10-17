<?php
session_start();
include "../bddConnexion/bddConnexion.php";

// Initialize the response
$response = [
    'status' => 'error',
    'message' => '',
    'match_verified' => false // Add a key to indicate if the match has been verified
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_tournoi = $_POST['id_tournoi'];
    $id_clan_demandeur = $_POST['id_clan_demandeur'];
    $id_clan_receveur = $_POST['id_clan_receveur'];

    // Query to retrieve the time of the first report
    $sql_check_time = "SELECT * FROM verif_report WHERE id_tournoi = ? AND id_clan_demandeur = ? AND id_clan_receveur = ?";
    $stmt_check_time = $conn->prepare($sql_check_time);
    
    if ($stmt_check_time) {
        $stmt_check_time->bind_param("iii", $id_tournoi, $id_clan_demandeur, $id_clan_receveur);
        $stmt_check_time->execute();
        $result = $stmt_check_time->get_result();
        $data = $result->fetch_assoc();

        if (isset($data['report_time'])) {
            // Check if the results of the clans are equal
            if ($data['clan_demandeur_result'] == $data['clan_receveur_result'] && $data['clan_demandeur_report'] === 1 && $data['clan_receveur_report'] === 1) {
                // Redirect with a form
                $response['status'] = 'redirect';
                $response['formData'] = [
                    'id_tournoi' => $id_tournoi,
                    'id_clan_demandeur' => $id_clan_demandeur,
                    'id_clan_receveur' => $id_clan_receveur
                ];
                $response['match_verified'] = true; 
            } else {
                $report_time = new DateTime($data['report_time']);
                $now = new DateTime();
                $diff = $now->diff($report_time);
                $minutes_passed = $diff->i; // Minutes passed
                $time = 20;

                if ($minutes_passed >= $time) {
                    $response['status'] = 'success';
                    $response['redirect'] = "../bddConnexion/traitement_addElo.php?id_tournoi=" . $id_tournoi;
                } else {
                    $time_remaining = $time - $minutes_passed;
                    $response['status'] = 'waiting';
                    $response['message'] = "Someone has already reported the match. You have " . $time_remaining . " minutes left before auto-report.";
                }
                $response['match_verified'] = true;
            }
        } else {
            $response['status'] = 'no_report';
            $response['message'] = "Nobody reported the match yet."; // If the first report has not yet been made
        }
    } else {
        $response['message'] = "Error preparing the query.";
    }

    // Return the response as JSON
    echo json_encode($response);
} else {
    // If the request method is not POST
    $response['message'] = "Invalid request method.";
    echo json_encode($response);
}

// Close the connection
$conn->close();
?>
