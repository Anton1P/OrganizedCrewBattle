<?php
// Calcul du nombre de jeux joués et du pourcentage de victoires
$games_played = $row['wins'] + $row['loses'];
$winrate = ($games_played > 0) ? ($row['wins'] / $games_played) * 100 : 0;

// Choix de l'image du tier en fonction de l'elo_rating
switch (true) {
    case ($row['elo_rating'] >= 2000 || $row['elo_peak'] >= 2000):
        $tier_icon = "../assets/img/rank/diamond.png";
        break;
    case ($row['elo_rating'] >= 1680):
        $tier_icon = "../assets/img/rank/platinum.png";
        break;
    case ($row['elo_rating'] >= 1390):
        $tier_icon = "../assets/img/rank/gold.png";
        break;
    case ($row['elo_rating'] >= 1130):
        $tier_icon = "../assets/img/rank/silver.png";
        break;
    case ($row['elo_rating'] >= 910):
        $tier_icon = "../assets/img/rank/bronze.png";
        break;
    default:
        $tier_icon = "../assets/img/rank/tin.png";
        break;
}
?>
