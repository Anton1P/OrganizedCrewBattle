<?php
include "bddConnexion.php";

session_start();

// Récupérer l'ID du tournoi
if(isset($_SESSION['tournoi_id'])){
    $tournoi_id = $_SESSION['tournoi_id']; 
}
else{
        $tournoi_id = $_SESSION['tournoi_id'];
}


// Décrémenter le champ on_page
$sql = "UPDATE tournoi SET on_page = on_page - 1 WHERE id_tournoi = ? AND on_page > 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tournoi_id);
$stmt->execute();
$stmt->close();
?>
