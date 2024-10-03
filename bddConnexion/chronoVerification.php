<?php
include "../bddConnexion/bddConnexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_tournoi = $_POST['id_tournoi'];
    $id_clan_demandeur = $_POST['id_clan_demandeur'];
    $id_clan_receveur = $_POST['id_clan_receveur'];

    // Requête pour récupérer le temps du premier report
    $sql_check_time = "SELECT * FROM verif_report WHERE id_tournoi = ? AND id_clan_demandeur = ? AND id_clan_receveur = ?";
    $stmt_check_time = $conn->prepare($sql_check_time);
    $stmt_check_time->bind_param("iii", $id_tournoi, $id_clan_demandeur, $id_clan_receveur);
    $stmt_check_time->execute();
    $result = $stmt_check_time->get_result();
    $data = $result->fetch_assoc();
    
    if (isset($data['report_time'])) {
        
       if($data['clan_demandeur_result'] == $data['clan_receveur_result'] ){

        echo '<form id="redirectForm" action="../view/matchVerif.php" method="POST">';
        echo '<input type="hidden" name="id_tournoi" value="' . htmlspecialchars($id_tournoi) . '">';
        echo '<input type="hidden" name="id_clan_demandeur" value="' . htmlspecialchars($id_clan_demandeur) . '">';
        echo '<input type="hidden" name="id_clan_receveur" value="' . htmlspecialchars($id_clan_receveur) . '">';
        echo '</form>';
        echo '<script type="text/javascript">';
        echo 'document.getElementById("redirectForm").submit();';
        echo '</script>';
        exit();
       }
       else{
            $report_time = new DateTime($data['report_time']);
            $now = new DateTime();
            $diff = $now->diff($report_time);

            $minutes_passed = $diff->i; // Minutes écoulées
            $time = 20;

            if ($minutes_passed >= $time) { 
                header("Location: ../bddConnexion/traitement_addElo.php?id_tournoi=". $id_tournoi);
                exit();
            } else {
            $time_remaining = $time - $minutes_passed;
                echo "Une personne à déjà report la partie. il vous reste " . $time_remaining . " minutes avant l'auto report" ;  // Sinon, continuer à attendre
            }
       }
    } else {
        echo "Personne n'a encore report."; // Si le premier rapport n'est pas encore effectué
    }
}
?>
