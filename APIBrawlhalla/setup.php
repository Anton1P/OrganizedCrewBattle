<?php

$username = $_SESSION['userData']['name'];
$avatar = $_SESSION['userData']['avatar'];
$steam_id = $_SESSION['userData']['steam_id'];

// Vérifier si les informations sont déjà stockées en session
if (!isset($_SESSION['brawlhalla_data']['name'])) {

    // Si les infos ne sont pas en session, on appelle l'API
    $url = "https://brawlhalla.fly.dev/v1/ranked/steamid?steam_id=".$_SESSION['userData']['steam_id'];
    $data = file_get_contents($url);
    $result = json_decode($data, true);

    $name = $result["data"]["name"];
    $brawlhalla_id = $result["data"]["brawlhalla_id"];
    echo $name;
    echo $brawlhalla_id;
    $url = "https://brawlhalla.fly.dev/v1/stats/id?brawlhalla_id=".$brawlhalla_id;
    $data = file_get_contents($url);
    $result = json_decode($data, true);

    $clan_name = $result["data"]["clan"]["clan_name"];
    $clan_id = $result["data"]["clan"]["clan_id"];

    $url = "https://brawlhalla.fly.dev/v1/utils/clan?clan_id=".$clan_id;
    $data = file_get_contents($url);
    $result = json_decode($data, true);

    $clan_members = $result["data"]["clan"];

    // Stocker les infos dans la session pour ne pas recharger l'API à chaque fois
    $_SESSION['brawlhalla_data'] = [
        'name' => $name,
        'brawlhalla_id' => $brawlhalla_id,
        'clan_name' => $clan_name,
        'clan_id' => $clan_id,
        'clan_members' => $clan_members
    ];
} else {
    // Récupérer les infos depuis la session
    $name = $_SESSION['brawlhalla_data']['name'];
    $brawlhalla_id = $_SESSION['brawlhalla_data']['brawlhalla_id'];
    $clan_name = $_SESSION['brawlhalla_data']['clan_name'];
    $clan_id = $_SESSION['brawlhalla_data']['clan_id'];
    $clan_members = $_SESSION['brawlhalla_data']['clan_members'];
}


include "ClanPermission.php";
?>
