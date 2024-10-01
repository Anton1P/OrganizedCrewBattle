<?php
include "../bddConnexion/bddConnexion.php";

if (isset($_POST['id_tournoi'])) {
    $id_tournoi = $_POST['id_tournoi'];

    // Requête pour supprimer le tournoi
    $sql = "DELETE FROM tournoi WHERE id_tournoi = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_tournoi);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }

    $stmt->close();
}
?>