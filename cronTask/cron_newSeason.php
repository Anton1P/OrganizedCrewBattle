<?php
//! CRON TASK 1/day
include "../bddConnexion/bddConnexion.php";


$date_limite = '2024-12-31';  //! Date de fin de saison
$date_actuelle = date('Y-m-d');  

// Comparer la date limite avec la date actuelle
if ($date_actuelle >= $date_limite) {
    // Création de la connexion
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Connexion échouée: " . $conn->connect_error);
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
    if ($conn->multi_query($sql) === TRUE) {
        echo "Le contenu de toutes les tables a été supprimé avec succès.";
    } else {
        echo "Erreur lors de la suppression du contenu des tables: " . $conn->error;
    }

    // Fermer la connexion
    $conn->close();
} else {
    echo "La date actuelle est inférieure à la date limite. Aucune suppression n'a été effectuée.";
}
?>
