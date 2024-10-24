<?php
//! CRON TASK 1/day
include "../bddConnexion/bddConnexion.php";

// Chemin vers le fichier de log
$log_file = 'logs/cron_suppressionTables.log';

// Fonction pour écrire dans le log
function log_message($message, $log_file) {
    $timestamp = date('Y-m-d H:i:s'); // Format de timestamp
    $formatted_message = "[$timestamp] $message" . PHP_EOL; // Format du message
    file_put_contents($log_file, $formatted_message, FILE_APPEND); // Écrire dans le fichier de log
}

// Définir la date limite de suppression
$date_limite = '2024-12-31';  //! Date de fin de saison
$date_actuelle = date('Y-m-d');  

// Comparer la date limite avec la date actuelle
if ($date_actuelle >= $date_limite) {
    // Création de la connexion
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        log_message("Connexion échouée: " . $conn->connect_error, $log_file);
        die("Erreur de connexion à la base de données.");
    }

    // SQL pour supprimer le contenu des tables
    $sql = "
    DELETE FROM verif_match;
    DELETE FROM player_tournoi;
    DELETE FROM players;
    DELETE FROM tournoi;
    DELETE FROM clans;
    ";

    // Exécution de la requête
    if ($conn->multi_query($sql)) {
        log_message("Le contenu de toutes les tables a été supprimé avec succès.", $log_file);
    } else {
        log_message("Erreur lors de la suppression du contenu des tables: " . $conn->error, $log_file);
    }

    // Fermer la connexion
    $conn->close();
} else {
    log_message("La date actuelle est inférieure à la date limite. Aucune suppression n'a été effectuée.", $log_file);
}
?>
