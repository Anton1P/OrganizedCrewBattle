<?php
include "../bddConnexion/bddConnexion.php";
session_start();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tournoi_id = $_POST['tournoi_id'];
    $brawlhalla_room = $_POST['brawlhalla_room'];

    // Mettre à jour la colonne brawlhalla_room dans la base de données
    $sql = "UPDATE tournoi SET brawlhalla_room = ? WHERE id_tournoi = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $brawlhalla_room, $tournoi_id);

    if ($stmt->execute()) {
      
        header("Location: ../view/AdminPanel.php");
        exit();
    } else {
        echo "Erreur lors de la mise à jour de la salle Brawlhalla.";
    }

    $stmt->close();
}

// Fermer la connexion à la base de données
$conn->close();
?>
