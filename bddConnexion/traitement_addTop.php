<?php 
include "../bddConnexion/bddConnexion.php"; // Database connection
// Calculate ranking and update the `top` column for all clans
$sql_rankings = "SELECT id_clan, elo_rating FROM clans ORDER BY elo_rating DESC";
$result_rankings = $conn->query($sql_rankings);

$position = 1;
while ($row = $result_rankings->fetch_assoc()) {
    $id_clan = $row['id_clan'];
    $sql_update_top = "UPDATE clans SET top = ? WHERE id_clan = ?";
    $stmt_update_top = $conn->prepare($sql_update_top);
    $stmt_update_top->bind_param("ii", $position, $id_clan);
    $stmt_update_top->execute();
    $position++;
}

$stmt_update_top->close();

