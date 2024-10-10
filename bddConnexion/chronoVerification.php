<?php
session_start();
include "../bddConnexion/bddConnexion.php";

// Initialisation de la réponse
$response = [
    'status' => 'error',
    'message' => '',
    'match_verified' => false // Ajoutez une clé pour indiquer si le match a été vérifié
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_tournoi = $_POST['id_tournoi'];
    $id_clan_demandeur = $_POST['id_clan_demandeur'];
    $id_clan_receveur = $_POST['id_clan_receveur'];

    // Requête pour récupérer le temps du premier report
    $sql_check_time = "SELECT * FROM verif_report WHERE id_tournoi = ? AND id_clan_demandeur = ? AND id_clan_receveur = ?";
    $stmt_check_time = $conn->prepare($sql_check_time);
    
    if ($stmt_check_time) {
        $stmt_check_time->bind_param("iii", $id_tournoi, $id_clan_demandeur, $id_clan_receveur);
        $stmt_check_time->execute();
        $result = $stmt_check_time->get_result();
        $data = $result->fetch_assoc();

        if (isset($data['report_time'])) {
            // Vérifiez si les résultats des clans sont égaux
            if ($data['clan_demandeur_result'] == $data['clan_receveur_result']) {
                // Rediriger avec un formulaire
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
                $minutes_passed = $diff->i; // Minutes écoulées
                $time = 20;

                if ($minutes_passed >= $time) {
                    $response['status'] = 'success';
                    $response['redirect'] = "../bddConnexion/traitement_addElo.php?id_tournoi=" . $id_tournoi;
                } else {
                    $time_remaining = $time - $minutes_passed;
                    $response['status'] = 'waiting';
                    $response['message'] = "Une personne a déjà reporté la partie. Il vous reste " . $time_remaining . " minutes avant l'auto-report.";
                }
                $response['match_verified'] = true;
            }
        } else {
            $response['status'] = 'no_report';
            $response['message'] = "Nobody reported the match yet."; // Si le premier rapport n'est pas encore effectué
        }
    } else {
        $response['message'] = "Erreur lors de la préparation de la requête.";
    }

    // Renvoyer la réponse en JSON
    echo json_encode($response);
} else {
    // Si la méthode de requête n'est pas POST
    $response['message'] = "Méthode de requête invalide.";
    echo json_encode($response);
}

// Fermer la connexion
$conn->close();
?>
