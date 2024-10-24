<?php
//! CRON TASK 1/day
include "../bddConnexion/bddConnexion.php";

// Augmenter la limite de mémoire si nécessaire
ini_set('memory_limit', '256M'); // Ajustez selon vos besoins

// Chemin vers le fichier de log
$log_file = 'logs/cron_verificationClansAlive.log';

// Fonction pour écrire dans le log
function log_message($message, $log_file) {
    $timestamp = date('Y-m-d H:i:s'); // Format de timestamp
    $formatted_message = "[$timestamp] $message" . PHP_EOL; // Format du message
    file_put_contents($log_file, $formatted_message, FILE_APPEND); // Écrire dans le fichier de log
}

// Requête pour supprimer les clans sans joueurs associés
$delete_sql = "DELETE FROM clans 
                WHERE id_clan IN (
                    SELECT c.id_clan 
                    FROM clans c 
                    LEFT JOIN players p ON c.id_clan = p.id_clan 
                    WHERE p.id_clan IS NULL
                )";

// Exécution de la requête de suppression
if ($conn->query($delete_sql) === TRUE) {
    log_message("Clans sans joueurs supprimés avec succès.", $log_file);
} else {
    log_message("Erreur lors de la suppression des clans: " . $conn->error, $log_file);
}

// Fermer la connexion à la base de données
$conn->close();
?>
