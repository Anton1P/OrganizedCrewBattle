<?php
include "../bddConnexion/bddConnexion.php";

if (isset($_POST['id_tournoi'])) {
    $id_tournoi = $_POST['id_tournoi'];

    // Query to delete the tournament
    $sql = "DELETE FROM tournoi WHERE id_tournoi = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_tournoi);

    if ($stmt->execute()) {
        $_SESSION['notification'] = "Tournament successfully deleted.";
    } else {
        $_SESSION['notification'] = "Error deleting the tournament.";
    }

    $stmt->close();
}

// Redirect to the admin panel
header("Location: ../view/AdminPanel.php");
exit();
?>
