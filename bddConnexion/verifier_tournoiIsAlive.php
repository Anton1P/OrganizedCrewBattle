<?php
session_start(); // Assurez-vous que la session est démarrée
include "bddConnexion/bddConnexion.php";

// Initialisation de la réponse
$response = ['isAlive' => false];

if (isset($_POST['id_tournoi'])) {
    $id_tournoi = $_POST['id_tournoi'];

    // Vérifier l'existence du tournoi
    $sql = "SELECT * FROM tournoi WHERE id_tournoi = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_tournoi);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response['isAlive'] = true; // Le tournoi existe
    }
}

// Retourner la réponse en JSON
echo json_encode($response);

// Fermer la connexion
$stmt->close();
$conn->close();
?>
