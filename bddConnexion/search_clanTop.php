<?php
include "../bddConnexion/bddConnexion.php"; 

$sql = "SELECT top FROM clans WHERE id_clan = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $clan_id);
$stmt->execute();
$result = $stmt->get_result();

$data_clan_top = $result->fetch_assoc();

$conn->close();
?>
