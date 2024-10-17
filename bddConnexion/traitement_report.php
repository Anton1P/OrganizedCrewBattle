<?php
include "../bddConnexion/bddConnexion.php";
include "../APIBrawlhalla/security.php";

// Check that the request is a POST submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_tournoi = $_POST['id_tournoi'];
    $resultat = $_POST['resultat'];
    $verif_report = $_POST['verif_report'];
    $id_clan_demandeur = $_POST['id_clan_demandeur'];
    $id_clan_receveur = $_POST['id_clan_receveur'];
   
    // Check that all fields are filled in
    if (empty($id_tournoi)) {
        $_SESSION['notification'] = "Error: All fields must be filled.";
        header("Location: ../view/AdminPanel.php");
        exit();
    }

    // Check if both clans agree on the result
    $sql_verif = "SELECT * FROM verif_report WHERE id_tournoi = ? AND (id_clan_demandeur = ? OR id_clan_receveur = ?)";
    $stmt_verif = $conn->prepare($sql_verif);
    $stmt_verif->bind_param("iii", $id_tournoi, $clan_id, $clan_id);
    $stmt_verif->execute();
    $result_verif = $stmt_verif->get_result();

    if ($result_verif->num_rows > 0) {
        $verif_data = $result_verif->fetch_assoc();
       
        if ($verif_data['clan_demandeur_report'] && $verif_data['clan_receveur_report']) {
            // Proceed here 
        } else {

            if ($verif_data['clan_demandeur_report'] && !$verif_data['clan_receveur_report']) {

                if ($clan_id == $id_clan_demandeur) {
                    $_SESSION['notification'] = "The requesting clan has already reported the result. Waiting for the receiving clan.";
                    header("Location: ../view/AdminPanel.php");
                    exit();
                } elseif ($clan_id == $id_clan_receveur) {
                    if (isset($verif_report)) {
                        $sql_update_verif = "UPDATE verif_report 
                            SET clan_receveur_report = ?, clan_receveur_result = ? 
                            WHERE id_tournoi = ? AND id_clan_demandeur = ? AND id_clan_receveur = ?";
                        $stmt_update_verif = $conn->prepare($sql_update_verif);
                        $stmt_update_verif->bind_param("iiiii", $verif_report, $resultat, $id_tournoi, $id_clan_demandeur, $id_clan_receveur);
                        $stmt_update_verif->execute();
                        $stmt_update_verif->close();
                    }
                }
            
            } elseif (!$verif_data['clan_demandeur_report'] && $verif_data['clan_receveur_report']) {
                if ($clan_id == $id_clan_receveur) {
                    $_SESSION['notification'] = "The receiving clan has already reported the result. Waiting for the requesting clan.";
                    header("Location: ../view/AdminPanel.php");
                    exit();
                } elseif ($clan_id == $id_clan_demandeur) {
                    if (isset($verif_report)) {

                        $sql_update_verif = "UPDATE verif_report 
                                SET clan_demandeur_report = ?, clan_demandeur_result = ? 
                                WHERE id_tournoi = ? AND id_clan_demandeur = ? AND id_clan_receveur = ?";
                        $stmt_update_verif = $conn->prepare($sql_update_verif);
                        $stmt_update_verif->bind_param("iiiii", $verif_report, $resultat, $id_tournoi, $id_clan_demandeur, $id_clan_receveur);
                        $stmt_update_verif->execute();
                        $stmt_update_verif->close();
                    }
                }  
            }
        }
    } elseif ($result_verif->num_rows == 0) {
        if ($clan_id == $id_clan_demandeur) {
            
            if (isset($verif_report)) {
                $sql_insert_verif = "INSERT INTO verif_report (id_tournoi, id_clan_demandeur, clan_demandeur_report, clan_demandeur_result, id_clan_receveur) 
                VALUES (?, ?, ?, ?, ?)";
                $stmt_insert_verif = $conn->prepare($sql_insert_verif);
                $stmt_insert_verif->bind_param("iiiii", $id_tournoi, $id_clan_demandeur, $verif_report, $resultat, $id_clan_receveur);
                $stmt_insert_verif->execute();
                $stmt_insert_verif->close();
                
                $sql_update_time = "UPDATE verif_report 
                SET report_time = NOW() 
                WHERE id_tournoi = ? AND id_clan_demandeur = ? AND id_clan_receveur = ?";
                $stmt_update_time = $conn->prepare($sql_update_time);
                $stmt_update_time->bind_param("iii", $id_tournoi, $id_clan_demandeur, $id_clan_receveur);
                $stmt_update_time->execute();
                $stmt_update_time->close();

                $_SESSION['notification'] = "The requesting clan has just reported the result. Waiting for the receiving clan.";
                header("Location: ../view/AdminPanel.php");
                exit();
            } else {
                echo "Error: empty result";
            }
        } elseif ($clan_id == $id_clan_receveur) {
            if (isset($verif_report)) { 
                $sql_insert_verif = "INSERT INTO verif_report (id_tournoi, id_clan_demandeur, id_clan_receveur, clan_receveur_report, clan_receveur_result) 
                VALUES (?, ?, ?, ?, ?)";
                $stmt_insert_verif = $conn->prepare($sql_insert_verif);
                $stmt_insert_verif->bind_param("iiiii", $id_tournoi, $id_clan_demandeur, $id_clan_receveur, $verif_report, $resultat);
                $stmt_insert_verif->execute();
                $stmt_insert_verif->close();

                $sql_update_time = "UPDATE verif_report 
                SET report_time = NOW() 
                WHERE id_tournoi = ? AND id_clan_demandeur = ? AND id_clan_receveur = ?";
                $stmt_update_time = $conn->prepare($sql_update_time);
                $stmt_update_time->bind_param("iii", $id_tournoi, $id_clan_demandeur, $id_clan_receveur);
                $stmt_update_time->execute();
                $stmt_update_time->close();

                $_SESSION['notification'] = "The receiving clan has just reported the result. Waiting for the requesting clan.";
                header("Location: ../view/AdminPanel.php");
                exit();
            } else {
                echo "Error: empty result";
            }
        }    
    }

    // Check if both clans agree on the result
    $sql_verif = "SELECT * FROM verif_report WHERE id_tournoi = ? AND (id_clan_demandeur = ? OR id_clan_receveur = ?)";
    $stmt_verif = $conn->prepare($sql_verif);
    $stmt_verif->bind_param("iii", $id_tournoi, $clan_id, $clan_id);
    $stmt_verif->execute();
    $result_verif = $stmt_verif->get_result();

    if ($result_verif->num_rows > 0) {
        $verif_data = $result_verif->fetch_assoc();

        if ($verif_data['clan_demandeur_report'] && $verif_data['clan_receveur_report']) {

            if ($verif_data['clan_demandeur_result'] == $verif_data['clan_receveur_result']) {
                // Both clans confirmed the same result
                $_SESSION['notification'] = "Both clans confirmed the same result. Manual verification is required.";
                // Create the form for the POST redirect
                echo '<form id="redirectForm" action="../view/matchVerif.php" method="POST">';
                echo '<input type="hidden" name="id_tournoi" value="' . htmlspecialchars($id_tournoi) . '">';
                echo '<input type="hidden" name="id_clan_demandeur" value="' . htmlspecialchars($id_clan_demandeur) . '">';
                echo '<input type="hidden" name="id_clan_receveur" value="' . htmlspecialchars($id_clan_receveur) . '">';
                echo '</form>';
                echo '<script type="text/javascript">';
                echo 'document.getElementById("redirectForm").submit();';
                echo '</script>';
                exit();
            } else {
                // The results from both clans are different
                $_SESSION['notification'] = "The results submitted by both clans have been correctly reported.";
                header("Location: ../bddConnexion/traitement_addElo.php?id_tournoi=" . $id_tournoi);
                exit();
            }

        }
    }
}
?>
