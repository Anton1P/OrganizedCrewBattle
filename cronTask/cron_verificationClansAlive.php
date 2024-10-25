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

$log_file = 'logs/cron_verificationClansAlive.log';

// Fonction pour écrire dans le log
function log_message($message, $log_file) {
    $timestamp = date('Y-m-d H:i:s'); // Format de timestamp
    $formatted_message = "[$timestamp] $message" . PHP_EOL; // Format du message
    file_put_contents($log_file, $formatted_message, FILE_APPEND); // Écrire dans le fichier de log
}

// Vérifier la connexion
if ($conn->connect_error) {
    log_message("Connexion échouée: " . $conn->connect_error, $log_file);
    die("Erreur de connexion à la base de données.");
}

// SQL pour sélectionner les clans à supprimer
$sql_select = "SELECT c.id_clan 
               FROM clans c 
               LEFT JOIN players p ON c.id_clan = p.id_clan 
               WHERE p.id_clan IS NULL";

$result = $conn->query($sql_select);

// Boucle pour supprimer chaque clan sans joueurs
if ($result->num_rows > 0) {
    // Créer un tableau pour stocker les ID des clans à supprimer
    $ids_to_delete = [];
    while ($row = $result->fetch_assoc()) {
        $ids_to_delete[] = $row['id_clan'];
    }

    // Vérifier si des IDs sont à supprimer
    if (!empty($ids_to_delete)) {
        // Préparer la requête de suppression
        $id_list = implode(',', $ids_to_delete);
        $delete_sql = "DELETE FROM clans WHERE id_clan IN ($id_list)";
        
        // Exécution de la requête de suppression
        if ($conn->query($delete_sql) === TRUE) {
            log_message("Clans ID ($id_list) supprimés avec succès.", $log_file);
        } else {
            log_message("Erreur lors de la suppression des clans: " . $conn->error, $log_file);
        }
    }
} else {
    log_message("Aucun clan sans joueurs trouvé.", $log_file);
}

// Fermer la connexion à la base de données
$conn->close();
?>
