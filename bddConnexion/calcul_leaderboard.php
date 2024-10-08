<?php
// Calcul du nombre de jeux joués et du pourcentage de victoires
$games_played = $row['wins'] + $row['loses'];
$winrate = ($games_played > 0) ? ($row['wins'] / $games_played) * 100 : 0;
                        
// Choix de l'image du tier en fonction de l'elo_rating
    if ($row['elo_rating'] >= 2000) {
      $tier_icon = "../assets/img/Diamond.webp";
    } elseif ($row['elo_rating'] >= 1936 ) {
     $tier_icon = "../assets/img/Platinum 5.webp";
    } elseif ($row['elo_rating'] >= 1872  ) {
        $tier_icon = "../assets/img/Platinum 4.webp";
       }elseif ($row['elo_rating'] >= 1808 ) {
        $tier_icon = "../assets/img/Platinum 3.webp";
       }elseif ($row['elo_rating'] >= 1744) {
        $tier_icon = "../assets/img/Platinum 2.webp";
       }elseif ($row['elo_rating'] >= 1680) {
        $tier_icon = "../assets/img/Platinum 1.webp";
       }elseif ($row['elo_rating'] >= 1622 ) {
        $tier_icon = "../assets/img/Gold 5.webp";
       }elseif ($row['elo_rating'] >= 1564 ) {
        $tier_icon = "../assets/img/Gold 4.webp";
       }elseif ($row['elo_rating'] >= 1506 ) {
        $tier_icon = "../assets/img/Gold 3.webp";
       }elseif ($row['elo_rating'] >= 1448 ) {
        $tier_icon = "../assets/img/Gold 2.webp";
       }elseif ($row['elo_rating'] >= 1390 ) {
        $tier_icon = "../assets/img/Gold 1.webp";
       }
    else {
      $tier_icon = "../assets/img/Diamond.webp";
    }
    
?>