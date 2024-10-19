<?php

include "bddConnexion.php";
include "../APIBrawlhalla/security.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tournoi = $_POST['id_tournoi'];
    $action = $_POST['action'];

    if ($action === 'Accept') {
        //? Update the tournament status in the database to "accepted"
        $sql_update = "UPDATE tournoi SET accepted = 1 WHERE id_tournoi = $id_tournoi";
        if ($conn->query($sql_update) === TRUE) {
            header("Location: ../bddConnexion/traitement_responseForm.php");
        } else {
            $_SESSION['notification'] = "Error while accepting the tournament.";
        }
    } elseif ($action === 'Deny') {
        //? Update the tournament status in the database to "refused"
        $sql_update = "DELETE FROM tournoi WHERE id_tournoi = $id_tournoi";
        if ($conn->query($sql_update) === TRUE) {
            $_SESSION['notification'] = "The tournament has been refused.";
        } else {
            $_SESSION['notification'] = "Error while refusing the tournament.";
        }

        //? Update the player_tournoi status in the database to "refused"
        $sql_update = "DELETE FROM player_tournoi WHERE id_tournoi = $id_tournoi";
        if ($conn->query($sql_update) === TRUE) {
            header("Location: ../view/AdminPanel.php");
        } else {
            $_SESSION['notification'] = "Error while refusing the tournament.";
        }
    }

    exit();
}
