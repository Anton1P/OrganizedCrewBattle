<?php

include "bddConnexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tournoi = $_POST['id_tournoi'];
    $action = $_POST['action'];

    if ($action === 'Accepter') {
        // Mettre à jour le statut du tournoi dans la base de données pour "accepté"
        $sql_update = "UPDATE tournoi SET accepted = 1 WHERE id_tournoi = $id_tournoi";
        if ($conn->query($sql_update) === TRUE) {
            $_SESSION['notification'] = "Le tournoi a été accepté.";
        } else {
            $_SESSION['notification'] = "Erreur lors de l'acceptation du tournoi.";
        }
    } elseif ($action === 'Refuser') {
        // Mettre à jour le statut du tournoi dans la base de données pour "refusé"
        $sql_update = "DELETE FROM tournoi WHERE id_tournoi = $id_tournoi";
        if ($conn->query($sql_update) === TRUE) {
            $_SESSION['notification'] = "Le tournoi a été refusé.";
        } else {
            $_SESSION['notification'] = "Erreur lors du refus du tournoi.";
        }
    }
   // Rediriger vers la page des tournois
   header("Location: ../AdminPanel.php");
   exit();
}