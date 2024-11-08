<?php

//! CRON TASK 1/day
include "../bddConnexion/bddConnexion.php";

// Vérification de la connexion à la base de données
if (!isset($conn) || $conn->connect_error) {
    $connectionFile = '/homez.1951/crewbas/www/bddConnexion/bddConnexion.php';
    include $connectionFile;
}
if (!isset($conn) || $conn->connect_error) {
    $connectionFile = '/home/crewbas/www/bddConnexion/bddConnexion.php';
    include $connectionFile;
}
if (!isset($conn) || $conn->connect_error) {
    $servername = "crewbasantonin.mysql.db";
    $username = "crewbasantonin";
    $password = "Organizedcrewbattle76";
    $dbname = "crewbasantonin";

    // Création de la connexion
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérification de la connexion
    if ($conn->connect_error) {
        die("Échec de la connexion : " . $conn->connect_error);
    }
}

$log_file = 'logs/cron_suppressionTables.log';

// Fonction pour écrire dans le log
function log_message($message, $log_file) {
    $timestamp = date('Y-m-d H:i:s');
    $formatted_message = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($log_file, $formatted_message, FILE_APPEND);
}

// Récupérer la date de fin de la saison actuelle
$sql_saison = "SELECT date_fin FROM saison ORDER BY id_saison DESC LIMIT 1";
$result_saison = $conn->query($sql_saison);

if ($result_saison->num_rows > 0) {
    $row = $result_saison->fetch_assoc();
    $date_limite = $row['date_fin'];
} else {
    log_message("Aucune saison trouvée dans la base de données. Tâche annulée.", $log_file);
    exit(); // Arrêter le script si aucune saison n'est trouvée
}

$date_actuelle = date('Y-m-d');

if ($date_actuelle >= $date_limite) {
    // Récupérer le top 10 des clans basé sur elo_rating
    $sql_top_clans = "SELECT id_clan, elo_rating, elo_peak FROM clans ORDER BY elo_rating DESC LIMIT 10";
    $result_top_clans = $conn->query($sql_top_clans);

    if ($result_top_clans->num_rows > 0) {
        $top_clans = $result_top_clans->fetch_all(MYSQLI_ASSOC);
    
        // Si moins de 10 clans, remplir le reste avec des valeurs NULL
        while (count($top_clans) < 10) {
            $top_clans[] = ['id_clan' => null, 'elo_rating' => null, 'elo_peak' => null];
        }
    
        // Préparer l'insertion dans la table leaderboard pour la nouvelle saison
        $sql_insert_leaderboard = "
            INSERT INTO leaderboard 
            (id_saison, un, best_elo_un, elo_un, deux, best_elo_deux, elo_deux, trois, best_elo_trois, elo_trois, quatre, best_elo_quatre, elo_quatre, 
            cinq, best_elo_cinq, elo_cinq, six, best_elo_six, elo_six, sept, best_elo_sept, elo_sept, huit, best_elo_huit, elo_huit, 
            neufs, best_elo_neufs, elo_neufs, dix, best_elo_dix, elo_dix) 
            VALUES 
            ((SELECT id_saison FROM saison ORDER BY id_saison DESC LIMIT 1), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql_insert_leaderboard);
    
        // Associer les valeurs de chaque position du top 10 des clans, y compris les valeurs NULL si présentes
        $stmt->bind_param("iiiiiiiiiiiiiiiiiiiiiiiiiiiiii", 
            $top_clans[0]['id_clan'], $top_clans[0]['elo_peak'], $top_clans[0]['elo_rating'],
            $top_clans[1]['id_clan'], $top_clans[1]['elo_peak'], $top_clans[1]['elo_rating'],
            $top_clans[2]['id_clan'], $top_clans[2]['elo_peak'], $top_clans[2]['elo_rating'],
            $top_clans[3]['id_clan'], $top_clans[3]['elo_peak'], $top_clans[3]['elo_rating'],
            $top_clans[4]['id_clan'], $top_clans[4]['elo_peak'], $top_clans[4]['elo_rating'],
            $top_clans[5]['id_clan'], $top_clans[5]['elo_peak'], $top_clans[5]['elo_rating'],
            $top_clans[6]['id_clan'], $top_clans[6]['elo_peak'], $top_clans[6]['elo_rating'],
            $top_clans[7]['id_clan'], $top_clans[7]['elo_peak'], $top_clans[7]['elo_rating'],
            $top_clans[8]['id_clan'], $top_clans[8]['elo_peak'], $top_clans[8]['elo_rating'],
            $top_clans[9]['id_clan'], $top_clans[9]['elo_peak'], $top_clans[9]['elo_rating']
        );
    
        if ($stmt->execute()) {
            log_message("Top 10 des clans ajouté au leaderboard avec succès.", $log_file);
        } else {
            log_message("Erreur lors de l'ajout des clans dans le leaderboard : " . $stmt->error, $log_file);
        }
    
        $stmt->close();
    } else {
        log_message("Aucun clan n'a été trouvé pour le leaderboard.", $log_file);
    }

    // SQL pour supprimer le contenu des tables
    $sql = "
    DELETE FROM verif_match;
    DELETE FROM player_tournoi;
    DELETE FROM players;
    DELETE FROM tournoi;
    DELETE FROM checkin;
    DELETE FROM verif_report;
    DELETE FROM tournoi_results;
    ";

    // Exécution de la requête de suppression et gestion des résultats
    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->next_result());

        log_message("Le contenu de toutes les tables a été supprimé avec succès.", $log_file);

        // Créer une nouvelle saison avec la date actuelle et une date de fin dans 3 mois
        $newStartDate = date('Y-m-d'); // Date actuelle
        $newEndDate = date('Y-m-d', strtotime('+3 months')); // Date dans 3 mois

        // Insérer la nouvelle saison dans la table
        $sql_insert_saison = "INSERT INTO saison (date_debut, date_fin) VALUES ('$newStartDate', '$newEndDate')";
        
        if ($conn->query($sql_insert_saison) === TRUE) {
            log_message("Nouvelle saison créée avec succès : Début - $newStartDate, Fin - $newEndDate", $log_file);
        } else {
            log_message("Erreur lors de la création de la nouvelle saison : " . $conn->error, $log_file);
        }
    } else {
        log_message("Erreur lors de la suppression du contenu des tables: " . $conn->error, $log_file);
    }

    $conn->close();
} else {
    log_message("La date actuelle est inférieure à la date limite. Aucune suppression n'a été effectuée.", $log_file);
}

?>
