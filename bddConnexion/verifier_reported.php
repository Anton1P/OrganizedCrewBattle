<?php
session_start();
include('../bddConnexion/bddConnexion.php');

// Récupérer l'id du clan connecté depuis la session
$clan_id = $_SESSION['brawlhalla_data']['clan_id'];

// Requête pour vérifier si le clan connecté apparaît dans la table verif_match
$match_query = "SELECT * FROM verif_match WHERE id_clan_demandeur = '$clan_id' OR id_clan_receveur = '$clan_id' LIMIT 1";
$match_result = mysqli_query($conn, $match_query);

// Vérifier si le clan est impliqué dans un match
if (mysqli_num_rows($match_result) > 0) {
    $match_row = mysqli_fetch_assoc($match_result);
    
    // Vérifier si le clan est demandeur ou receveur
    if ($match_row['id_clan_demandeur'] == $clan_id) {
        // Si le clan est demandeur, vérifier l'état de demandeur_sendproof
        if ($match_row['demandeur_sendproof'] == 1) {
            // Arrêter la requête AJAX
            echo json_encode([
                'status' => 'stop',
                'message' => 'You have already submitted proof as the demander.'
            ]);
            exit; // Sortir du script
        }
    } else {
        // Si le clan est receveur, vérifier l'état de receveur_sendproof
        if ($match_row['receveur_sendproof'] == 1) {
            // Arrêter la requête AJAX
            echo json_encode([
                'status' => 'stop',
                'message' => 'You have already submitted proof as the receiver.'
            ]);
            exit; // Sortir du script
        }
    }
}

// Requête pour récupérer les informations dans la table verif_report
$query = "SELECT * FROM verif_report WHERE id_clan_demandeur = '$clan_id' OR id_clan_receveur = '$clan_id' LIMIT 1";
$result = mysqli_query($conn, $query);

// Si on trouve une ligne correspondant à ce clan
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    
    // Récupération des valeurs de la ligne
    $id_tournoi = $row['id_tournoi'];
    $id_clan_demandeur = $row['id_clan_demandeur'];
    $id_clan_receveur = $row['id_clan_receveur'];
    $clan_demandeur_report = $row['clan_demandeur_report'];
    $clan_receveur_report = $row['clan_receveur_report'];
    $clan_demandeur_result = $row['clan_demandeur_result'];
    $clan_receveur_result = $row['clan_receveur_result'];

    // Si les deux rapports sont soumis
    if ($clan_demandeur_report == 1 && $clan_receveur_report == 1) {
        // Si les résultats ne sont pas identiques, on redirige vers matchVerif.php avec les informations du tournoi
        if ($clan_demandeur_result == $clan_receveur_result) {
            // Ajouter un message à la session
            $_SESSION['notification'] = "The opposing clan has reported the same result as you. Please submit your match result proof in accordance with the site rules.";
            
            // Retourner les informations avec un statut de redirection
            echo json_encode([
                'status' => 'redirect',
                'url' => "matchVerif.php?message=" . urlencode($_SESSION['notification']) . "&id_tournoi=$id_tournoi&id_clan_demandeur=$id_clan_demandeur&id_clan_receveur=$id_clan_receveur"
            ]);
        } else {
            // Si les résultats sont identiques, recharger la page
            echo json_encode([
                'status' => 'reload'
            ]);
        }
    } else {
        // Aucun rapport soumis, continuer
        echo json_encode([
            'status' => 'continue'
        ]);
    }
} else {
    // Aucun rapport trouvé pour ce clan
    echo json_encode([
        'status' => 'stop'
    ]);
}
?>
