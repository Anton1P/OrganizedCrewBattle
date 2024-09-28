<?php
//! CRON TASK 1/day
include "../bddConnexion/bddConnexion.php";

$date_actuelle = new DateTime();

// Calculer la limite de 10 heures après l'heure actuelle
$date_limite = clone $date_actuelle;
$date_limite->modify('-10 hours');

// Formater la date pour la requête SQL (optionnel selon le format de votre base de données)
$date_limite_format = $date_limite->format('Y-m-d H:i:s');

// Préparer la requête SQL pour supprimer les tournois où la date de rencontre est plus ancienne que 10 heures après la date actuelle
$sql = "DELETE FROM tournoi WHERE date_rencontre <= ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $date_limite_format);

// Exécuter la requête
if ($stmt->execute()) {
    echo "Les tournois plus anciens que 10 heures ont été supprimés avec succès.";
} else {
    echo "Erreur lors de la suppression des tournois : " . $stmt->error;
}

// Fermer la connexion à la base de données
$stmt->close();
$conn->close();
?>
