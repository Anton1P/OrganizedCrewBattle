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
            // Stocke les résultats et libère la connexion pour les prochaines requêtes
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

    // Fermer la connexion
    $conn->close();
} else {
    log_message("La date actuelle est inférieure à la date limite. Aucune suppression n'a été effectuée.", $log_file);
}
?>
