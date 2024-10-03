<?php

$isAdmin = false;  
$rank = 'no clan'; 

// Vérifier si le joueur a un clan et des membres de clan
if (!empty($clan_members)) {
    foreach ($clan_members as $joueur) {
        if ($joueur['brawlhalla_id'] == $brawlhalla_id) {
            // Si le joueur est trouvé, on vérifie son rang
            if ($joueur['rank'] === 'Leader' || $joueur['rank'] === 'Officer') {
                $rank = $joueur['rank'];
                $isAdmin = true;  
            } else {
                $rank = $joueur['rank'];
                $isAdmin = false; 
            }
            break; 
        }
    }
} else {
    $isAdmin = false;
}

if($_SESSION['userData']['steam_id'] == 76561198877699338){
    $isAdmin = true;
}

?>
