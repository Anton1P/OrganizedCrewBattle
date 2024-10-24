<?php
//! CRON TASK 1/day
include "../bddConnexion/bddConnexion.php";

// Chemin vers le fichier de log
$log_file = 'logs/cron_suppressionTournois.log';

// Fonction pour écrire dans le log
function log_message($message, $log_file) {
    $timestamp = date('Y-m-d H:i:s'); // Format de timestamp
    $formatted_message = "[$timestamp] $message" . PHP_EOL; // Format du message
    file_put_contents($log_file, $formatted_message, FILE_APPEND); // Écrire dans le fichier de log
}

// Initialiser la date actuelle
$date_actuelle = new DateTime();

// Calculer la limite de 10 heures avant l'heure actuelle
$date_limite = clone $date_actuelle;
$date_limite->modify('-10 hours');

// Formater la date pour la requête SQL
$date_limite_format = $date_limite->format('Y-m-d H:i:s');

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    log_message("Connexion échouée: " . $conn->connect_error, $log_file);
    die("Erreur de connexion à la base de données.");
}

// Préparer la requête SQL pour supprimer les tournois
$sql = "DELETE FROM tournoi
    WHERE date_rencontre <= ?
    AND id_tournoi NOT IN (
        SELECT id_tournoi FROM verif_match
        WHERE demandeur_sendproof = 1 OR receveur_sendproof = 1)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $date_limite_format);

// Exécuter la requête
if ($stmt->execute()) {
    log_message("Les tournois plus anciens que 10 heures ont été supprimés avec succès.", $log_file);
} else {
    log_message("Erreur lors de la suppression des tournois : " . $stmt->error, $log_file);
}

// Fermer la connexion à la base de données
$stmt->close();
$conn->close();
?>
