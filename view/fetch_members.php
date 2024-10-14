<?php
session_start();
include "../APIBrawlhalla/setup.php";
header('Content-Type: application/json');

// Obtenir les membres du clan via l'API
$url = "https://brawlhalla.fly.dev/v1/utils/clan?clan_id=".$clan_id;
$data = file_get_contents($url);
$result = json_decode($data, true);

// Vérifier si la requête a réussi
if ($result['statusCode'] == 200) {
    echo json_encode($result); // Renvoie les données de l'API en tant que JSON
} else {
    // Si quelque chose ne va pas, renvoyez une erreur
    echo json_encode(['error' => 'Unable to fetch data']);
}
