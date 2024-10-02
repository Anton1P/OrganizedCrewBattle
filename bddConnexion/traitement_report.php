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

    // $elo_change = $_POST['elo_change']; //! Changement de points ELO

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
            elseif(!$verif_data['clan_demandeur_report'] && !$verif_data['clan_receveur_report']){
                if($clan_id == $id_clan_demandeur){
                    
                    if (isset($verif_report)){
                        $sql_insert_verif = "INSERT INTO verif_report (id_tournoi, id_clan_demandeur, clan_demandeur_report, clan_demandeur_result, id_clan_receveur) 
                        VALUES (?, ?, ?, ?, ?)";
                        $stmt_insert_verif = $conn->prepare($sql_insert_verif);
                        $stmt_insert_verif->bind_param("iiiii", $id_tournoi, $id_clan_demandeur, $verif_report, $resultat,$id_clan_receveur);
                        $stmt_insert_verif->execute();
                        $stmt_insert_verif->close();
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
                    }
                    else{
                    echo "Erreur : resultat vide ";
                    }
                }    
            }
        }
    }

// Vérifier si les deux clans sont d'accord sur le résultat
$sql_verif = "SELECT * FROM verif_report WHERE id_tournoi = ? AND (id_clan_demandeur = ? OR id_clan_receveur = ?)";
$stmt_verif = $conn->prepare($sql_verif);
$stmt_verif->bind_param("iii", $id_tournoi, $clan_id, $clan_id);
$stmt_verif->execute();
$result_verif = $stmt_verif->get_result();
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
            header("Location: ../bddConnexion/traitement_addElo.php");
            exit();
        }

}
}















//     // Si la vérification passe, procéder à la mise à jour des résultats
//     $conn->autocommit(FALSE); // Démarrer une transaction

//     try {
//         // Mettre à jour les victoires et les défaites
//         $sql_update_winner = "UPDATE clan SET win = win + 1, elo = elo + ? WHERE id_clan = ?";
//         $stmt_update_winner = $conn->prepare($sql_update_winner);
//         $stmt_update_winner->bind_param("ii", $elo_change, $winner_id);
//         $stmt_update_winner->execute();

//         $sql_update_loser = "UPDATE clan SET losses = losses + 1, elo = elo - ? WHERE id_clan = ?";
//         $stmt_update_loser = $conn->prepare($sql_update_loser);
//         $stmt_update_loser->bind_param("ii", $elo_change, $loser_id);
//         $stmt_update_loser->execute();

//         // Enregistrer le résultat du match dans la table des résultats (si vous en avez une)
//         $sql_insert_result = "INSERT INTO results (id_tournoi, winner_id, loser_id) VALUES (?, ?, ?)";
//         $stmt_insert_result = $conn->prepare($sql_insert_result);
//         $stmt_insert_result->bind_param("iii", $id_tournoi, $winner_id, $loser_id);
//         $stmt_insert_result->execute();

//         // Commit la transaction
//         $conn->commit();
        
//         // Notification de succès
//         $_SESSION['notification'] = "Résultat du match enregistré avec succès.";
//         header("Location: ../view/AdminPanel.php");
//     } catch (Exception $e) {
//         $conn->rollback(); // Annuler la transaction en cas d'erreur
//         $_SESSION['notification'] = "Erreur lors de l'enregistrement du résultat : " . $e->getMessage();
//         // header("Location: ../view/resultReport.php?id_tournoi=" . $id_tournoi);//!efezezfezfefzezfzfe
//     } finally {
//         // Fermer les requêtes
//         $stmt_update_winner->close();
//         $stmt_update_loser->close();
//         $stmt_insert_result->close();
//         $stmt_verif->close();
//         $conn->close();
//     }
// } else {
//     header("Location: ../view/AdminPanel.php");
//     exit();
// }
?>