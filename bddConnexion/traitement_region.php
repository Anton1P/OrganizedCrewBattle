<?php
include "../bddConnexion/bddConnexion.php";

$id_clan =  $_SESSION['brawlhalla_data']['clan_id'];

$sql = "SELECT * FROM region WHERE id_clan = $id_clan";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    $_SESSION['notification'] = "During the registration process, you must select the regions where your clan is able to compete. <br> This information is crucial for matchmaking and ensuring that your clan is assigned to battles in the appropriate regions.";
    header('Location: ../view/parameter.php');
    exit();
}

$conn->close();
?>
