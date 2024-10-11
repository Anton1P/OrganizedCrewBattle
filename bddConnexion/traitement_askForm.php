<?php
include "bddConnexion.php";
include "../APIBrawlhalla/security.php";

$askClan_id = $_SESSION['brawlhalla_data']['clan_id']; 
$askedClan_ids = isset($_POST['clan_ids']) ? $_POST['clan_ids'] : []; // Récupérer les clans sélectionnés
$format = $_POST['format'];
$accepted = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date_rencontre = $_POST['date_rencontre'];

    if (empty($date_rencontre)) {
        $_SESSION['notification'] = "Erreur : La date de rencontre ne peut pas être vide.";
        $_SESSION['from_treatment'] = true; 
        header("Location: ../view/ask.php");
        exit(); 
    } else {
        // Vérifier que c'est une date valide
        $timestamp = strtotime($date_rencontre);
        if ($timestamp === false || $timestamp === 0) {
            $_SESSION['notification'] = "Erreur : Date de rencontre invalide.";
            $_SESSION['from_treatment'] = true; 
            header("Location: ../view/ask.php");
            exit(); 
        }
    }
}

// Vérifier qu'au moins un clan a été sélectionné
if (!empty($askClan_id) && !empty($askedClan_ids)) {
    foreach ($askedClan_ids as $askedClan_id) {
        // Vérifier si la combinaison des deux clans existe déjà dans le tournoi
        $sql_check_combination = "SELECT * FROM tournoi WHERE id_clan_demandeur = ? AND id_clan_receveur = ?";
        $stmt_check_combination = $conn->prepare($sql_check_combination);
        $stmt_check_combination->bind_param("ii", $askClan_id, $askedClan_id);
        $stmt_check_combination->execute();
        $result_combination = $stmt_check_combination->get_result();

        if ($result_combination->num_rows > 0) {
            // Si la combinaison existe déjà, ne pas insérer le tournoi
            $_SESSION['notification'] = "Erreur : Ces deux clans sont déjà engagés dans un tournoi.";
            $_SESSION['from_treatment'] = true; 
            header("Location: ../view/ask.php");
            exit(); 
        } else {
            // Vérification de l'existence du clan receveur dans la BDD
            $sql_check_clan = "SELECT * FROM clans WHERE id_clan = ?";
            $stmt_check_clan = $conn->prepare($sql_check_clan);
            $stmt_check_clan->bind_param("i", $askedClan_id);
            $stmt_check_clan->execute();
            $result_clan = $stmt_check_clan->get_result();

            if ($result_clan->num_rows > 0) {
                // Vérifier si le clan a déjà fait une demande de tournoi
                $sql_check_previous_request = "SELECT * FROM tournoi WHERE id_clan_demandeur = ?";
                $stmt_check_previous_request = $conn->prepare($sql_check_previous_request);
                $stmt_check_previous_request->bind_param("i", $askClan_id);
                $stmt_check_previous_request->execute();
                $result_previous_request = $stmt_check_previous_request->get_result();

                if ($result_previous_request->num_rows > 0) { 
                    // On a trouvé des demandes de tournoi pour ce clan
                    while ($previous_data = $result_previous_request->fetch_assoc()) {
                        $previous_date = new DateTime($previous_data['date_rencontre']);
                        $new_date = new DateTime($date_rencontre);
                        $diff = $new_date->diff($previous_date);  
                        // Vérifiez si la nouvelle date est d'au moins 1 heure plus tard
                        if ($diff->h < 1 && $diff->days == 0) { // Moins d'une heure, mais le même jour
                            $_SESSION['notification'] = "Erreur : Vous avez déjà demandé un tournoi ayant le même jour avec moins d'heure d'intervalle.";
                            $_SESSION['from_treatment'] = true; 
                            header("Location: ../view/ask.php");
                            exit();
                        }
                    }
                }

                // Préparation de la requête d'insertion du tournoi
                $sql_insert = "INSERT INTO tournoi (id_clan_demandeur, id_clan_receveur, date_rencontre, format, accepted) 
                               VALUES (?, ?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("iissi", $askClan_id, $askedClan_id, $date_rencontre, $format, $accepted);

                // Exécution de la requête et vérification
                if ($stmt_insert->execute()) {
                    $_SESSION['notification'] = "Demande de tournois effectuée.";
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
    }

    header("Location: ../view/AdminPanel.php");
    exit(); 
} else {
    $_SESSION['notification'] = "Les identifiants des clans ne peuvent pas être vides.";
    $_SESSION['from_treatment'] = true; 
    header("Location: ../view/ask.php");
    exit();
}
?>
