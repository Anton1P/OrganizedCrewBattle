<?php
//! Remettre admin false 
$isAdmin = true;  // Par défaut, le joueur n'est pas admin
if(!isset($clan_members)){
    foreach ($clan_members as $joueur) {
        if ($joueur['brawlhalla_id'] == $brawlhalla_id) {
            // Si le joueur est trouvé, on vérifie son rang
            if ($joueur['rank'] === 'Leader' || $joueur['rank'] === 'Officer') {
                $rank = $joueur['rank'];
                $isAdmin = true;  // Il est admin si son rang est Leader ou Officer
            } else {
                $rank = $joueur['rank'];
                $isAdmin = true; // Sinon, il n'est pas admin//! Remettre admin false 
    
            }
            break;  // On sort de la boucle dès qu'on a trouvé le joueur
        }
    }
}
else{
    $isAdmin = false;
}



?>


