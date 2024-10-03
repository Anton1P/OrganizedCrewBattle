<?php
include "../bddConnexion/bddConnexion.php";

if (isset($_POST['id_tournoi'])) {
    $id_tournoi = $_POST['id_tournoi'];

    // Requête pour supprimer le tournoi
    $sql = "DELETE FROM tournoi WHERE id_tournoi = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_tournoi);

    if ($stmt->execute()) {
        $_SESSION['notification'] = "Tournoi supprimé avec succès.";
    } else {
        $_SESSION['notification'] = "Erreur lors de la suppression du tournoi.";
    }

    $stmt->close();
}

// Redirection vers le panneau d'administration
header("Location: ../view/AdminPanel.php");
exit();
?>