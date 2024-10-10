<?php
include "../bddConnexion/bddConnexion.php";

$sql_check_time = "SELECT * FROM clans WHERE id_clan = ?";
$stmt_check_time = $conn->prepare($sql_check_time);
$stmt_check_time->bind_param("i", $clan_id);
$stmt_check_time->execute();
$result = $stmt_check_time->get_result();

$data = $result->fetch_assoc();
  
$conn->close();
?>
