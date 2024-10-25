<?php

//! CRON TASK 1/day
include "../bddConnexion/bddConnexion.php";

if (!isset($conn) || $conn->connect_error) {
    $connectionFile = '/homez.1951/crewbas/www/bddConnexion/bddConnexion.php';
    include $connectionFile; // Inclure la connexion
}
if (!isset($conn) || $conn->connect_error) {
    $connectionFile = '/home/crewbas/www/bddConnexion/bddConnexion.php';
    include $connectionFile; // Inclure la connexion
}
if (!isset($conn) || $conn->connect_error) {
    $servername = "crewbasantonin.mysql.db"; // ou l'adresse de ton serveur de base de données
    $username = "crewbasantonin"; // ton nom d'utilisateur
    $password = "Organizedcrewbattle76"; // ton mot de passe
    $dbname = "crewbasantonin"; // le nom de ta base de données
    
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
    $timestamp = date('Y-m-d H:i:s'); // Format de timestamp
    $formatted_message = "[$timestamp] $message" . PHP_EOL; // Format du message
    file_put_contents($log_file, $formatted_message, FILE_APPEND); // Écrire dans le fichier de log
}

// Définir la date limite de suppression
$date_limite = '2024-12-31';  //! Date de fin de saison
$date_actuelle = date('Y-m-d');  

if ($date_actuelle >= $date_limite) {
   

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
