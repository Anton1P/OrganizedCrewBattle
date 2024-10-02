<?php
include "../bddConnexion/bddConnexion.php";
include "../APIBrawlhalla/security.php";

// Vérification que la requête est une soumission POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_tournoi = $_POST['id_tournoi'];
    $resultat = $_POST['resultat'];
    $verif_report = $_POST['verif_report'];
    $id_clan_demandeur = $_POST['id_clan_demandeur'];
    $id_clan_receveur = $_POST['id_clan_receveur'];
   
    // Vérification que tous les champs sont remplis
    if (empty($id_tournoi)) {
        $_SESSION['notification'] = "Erreur : Tous les champs doivent être remplis.";
        header("Location: ../view/AdminPanel.php");
        exit();
    }

    
    // Vérifier si les deux clans sont d'accord sur le résultat
    $sql_verif = "SELECT * FROM verif_report WHERE id_tournoi = ? AND (id_clan_demandeur = ? OR id_clan_receveur = ?)";
    $stmt_verif = $conn->prepare($sql_verif);
    $stmt_verif->bind_param("iii", $id_tournoi, $clan_id, $clan_id);
    $stmt_verif->execute();
    $result_verif = $stmt_verif->get_result();

    if ($result_verif->num_rows > 0) {
        $verif_data = $result_verif->fetch_assoc();
       
        if ($verif_data['clan_demandeur_report'] && $verif_data['clan_receveur_report']) {
            //r ce passe 
        }
        else {

            if ($verif_data['clan_demandeur_report'] && !$verif_data['clan_receveur_report']) {

                if($clan_id == $id_clan_demandeur){
                    $_SESSION['notification'] = "Le clan demandeur vien ou à déjà reporté le résultat. En attente du clan receveur.";
                    header("Location: ../view/AdminPanel.php");
                    exit();
                }
                elseif($clan_id == $id_clan_receveur){
                    if (isset($verif_report)){
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
                if($clan_id == $id_clan_receveur){
                    $_SESSION['notification'] = "Le clan receveur vien ou à déjà reporté le résultat. En attente du clan demandeur.";
                    header("Location: ../view/AdminPanel.php");
                    exit();
                }
                elseif($clan_id == $id_clan_demandeur){
                    if (isset($verif_report)){

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
    }
    elseif($result_verif->num_rows == 0){
        if($clan_id == $id_clan_demandeur){
            
            if (isset($verif_report)){
                $sql_insert_verif = "INSERT INTO verif_report (id_tournoi, id_clan_demandeur, clan_demandeur_report, clan_demandeur_result, id_clan_receveur) 
                VALUES (?, ?, ?, ?, ?)";
                $stmt_insert_verif = $conn->prepare($sql_insert_verif);
                $stmt_insert_verif->bind_param("iiiii", $id_tournoi, $id_clan_demandeur, $verif_report, $resultat,$id_clan_receveur);
                $stmt_insert_verif->execute();
                $stmt_insert_verif->close();
                
                $sql_update_time = "UPDATE verif_report 
                SET report_time = NOW() 
                WHERE id_tournoi = ? AND id_clan_demandeur = ? AND id_clan_receveur = ?";
                $stmt_update_time = $conn->prepare($sql_update_time);
                $stmt_update_time->bind_param("iii", $id_tournoi, $id_clan_demandeur, $id_clan_receveur);
                $stmt_update_time->execute();
                $stmt_update_time->close();

                $_SESSION['notification'] = "Le clan demandeur vien ou à déjà reporté le résultat. En attente du clan receveur.";
                header("Location: ../view/AdminPanel.php");
                exit();
            }
            else{
            echo "Erreur : resultat vide ";
            }
        }
        elseif($clan_id == $id_clan_receveur){
            if (isset($verif_report)){ 
                $sql_insert_verif = "INSERT INTO verif_report (id_tournoi, id_clan_demandeur, id_clan_receveur, clan_receveur_report ,clan_receveur_result) 
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

                $_SESSION['notification'] = "Le clan receveur vien ou à déjà reporté le résultat. En attente du clan demandeur.";
                header("Location: ../view/AdminPanel.php");
                exit();
            }
            else{
            echo "Erreur : resultat vide ";
            }
        }    
    }

    // Vérifier si les deux clans sont d'accord sur le résultat
    $sql_verif = "SELECT * FROM verif_report WHERE id_tournoi = ? AND (id_clan_demandeur = ? OR id_clan_receveur = ?)";
    $stmt_verif = $conn->prepare($sql_verif);
    $stmt_verif->bind_param("iii", $id_tournoi, $clan_id, $clan_id);
    $stmt_verif->execute();
    $result_verif = $stmt_verif->get_result();

    if ($result_verif->num_rows > 0) {
    $verif_data = $result_verif->fetch_assoc();

        if ($verif_data['clan_demandeur_report'] && $verif_data['clan_receveur_report']) {

                if ($verif_data['clan_demandeur_result'] == $verif_data['clan_receveur_result']) {
                    //! Les résultats des deux clans sont identiques
                    $_SESSION['notification'] = "Les deux clans ont confirmé le même résultat. Une vérification manuelle est nécessaire.";
                    header("Location: ../view/matchVerif.php");
                    exit();
                } else {
                    // Les résultats des deux clans sont différents
                    $_SESSION['notification'] = "Les résultats soumis par les deux clans ont était correctement reporté. ";
                    header("Location: ../bddConnexion/traitement_addElo.php?id_tournoi=". $id_tournoi);
                    exit();
                }

        }
    }
}
?>