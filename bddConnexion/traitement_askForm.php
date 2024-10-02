<?php

include "bddConnexion.php";
include "../APIBrawlhalla/security.php";

$askClan_id = $_SESSION['brawlhalla_data']['clan_id']; 
$askedClan_id = $_POST['clan_id'];
$format = $_POST['format'];
$players = isset($_POST['joueurs']) ? $_POST['joueurs'] : []; // Tableau de joueurs
$accepted = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date_rencontre = $_POST['date_rencontre'];

    if (empty($date_rencontre)) {
        $_SESSION['notification'] = "Erreur : La date de rencontre ne peut pas être vide."; // Correction ici
        header("Location: ../view/askForm.php");
        exit(); 
    } else {
        // Vérifier que c'est une date valide
        $timestamp = strtotime($date_rencontre);
        if ($timestamp === false || $timestamp === 0) {
            $_SESSION['notification'] = "Erreur : Date de rencontre invalide.";
            header("Location: ../view/askForm.php");
            exit(); 
        } else {
            // Traiter la date valide ici
            // Pas besoin de faire un echo ici, juste continuer avec le traitement
        }
    }
}


// Programme qui met les infos du formulaire dans la bdd
$sql_check = "SELECT * FROM clans WHERE id_clan = $askedClan_id";
$result = $conn->query($sql_check);

if (!empty($askClan_id) && !empty($askedClan_id)) {
    
    // Vérifier si la combinaison des deux clans existe déjà dans le tournoi
    $sql_check_combination = "SELECT * FROM tournoi WHERE id_clan_demandeur = ? AND id_clan_receveur = ?";
    $stmt_check_combination = $conn->prepare($sql_check_combination);
    $stmt_check_combination->bind_param("ii", $askClan_id, $askedClan_id);
    $stmt_check_combination->execute();
    $result_combination = $stmt_check_combination->get_result();

    if ($result_combination->num_rows > 0) {
        // Si la combinaison existe déjà, ne pas insérer le tournoi
        $_SESSION['notification'] = "Erreur : Ces deux clans sont déjà engagés dans un tournoi.";
        header("Location: ../view/askForm.php");
        exit(); 
    } else {
        // Vérification de l'existence du clan receveur dans la BDD
        $sql_check_clan = "SELECT * FROM clans WHERE id_clan = ?";
        $stmt_check_clan = $conn->prepare($sql_check_clan);
        $stmt_check_clan->bind_param("i", $askedClan_id);
        $stmt_check_clan->execute();
        $result_clan = $stmt_check_clan->get_result();
    
        if ($result_clan->num_rows > 0) {
            // Préparation de la requête d'insertion du tournoi
            $sql_insert = "INSERT INTO tournoi (id_clan_demandeur, id_clan_receveur, date_rencontre, format, accepted) 
                           VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            // Utilisation de "ss" pour les strings (format et date) et "ii" pour les IDs (clans et accepted)
            $stmt_insert->bind_param("iissi", $askClan_id, $askedClan_id, $date_rencontre, $format, $accepted);

            // Exécution de la requête et vérification
            if ($stmt_insert->execute()) {
                $tournoi_id = $stmt_insert->insert_id; // Récupérer l'ID du tournoi inséré
                // Insérer chaque joueur sélectionné dans la table player_tournoi
                foreach ($players as $player_id) {
                    $sql_insert_player = "INSERT INTO player_tournoi (id_player, id_tournoi) VALUES (?, ?)";
                    $stmt_insert_player = $conn->prepare($sql_insert_player);
                    $stmt_insert_player->bind_param("ii", $player_id, $tournoi_id);
                    $stmt_insert_player->execute();
                    $stmt_insert_player->close();
                }
                $_SESSION['notification'] = "Demande de tournois effectué";
                header("Location: ../view/AdminPanel.php");
                exit(); 
            } else {
                echo "Erreur lors de la création du tournoi : " . $stmt_insert->error;
            }

            // Fermeture de la requête
            $stmt_insert->close();
        } else {
            echo "Le clan receveur n'existe pas.";
        }

        // Fermeture de la requête de vérification du clan receveur
        $stmt_check_clan->close();
    }

    // Fermeture de la requête de vérification de la combinaison des deux clans
    $stmt_check_combination->close();

} else {
    echo "Erreur : Les identifiants des clans ne peuvent pas être vides.";
}



?>
