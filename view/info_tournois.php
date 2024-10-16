<?php

include "../bddConnexion/bddConnexion.php";


//! C'est une sécurité
if (!isset($_SESSION['brawlhalla_data']['clan_id'])) { 
    header("Location: ../APIBrawlhalla/routes.php");
    exit(); 
}

$id_clan = $_SESSION['brawlhalla_data']['clan_id'];
$date_actuelle = new DateTime();
$date_aujourdhui = $date_actuelle->format('Y-m-d'); // Formater la date actuelle


// SQL pour récupérer les tournois prévus pour le clan connecté le même jour
$sql_tournois_hier = "SELECT * FROM tournoi WHERE (id_clan_demandeur = ? OR id_clan_receveur = ?) AND DATE(date_rencontre) = ? AND accepted = 1";
$stmt_tournois = $conn->prepare($sql_tournois_hier);
$stmt_tournois->bind_param("iis", $id_clan, $id_clan, $date_aujourdhui);
$stmt_tournois->execute();
$result_tournois = $stmt_tournois->get_result();

// Vérifier s'il y a des tournois pour aujourd'hui
if ($result_tournois->num_rows > 0) {
  
    $result_tournois->data_seek(0);

    // Afficher les informations de chaque tournoi prévu aujourd'hui
    while ($row = $result_tournois->fetch_assoc()) {
        
        $date_rencontre = new DateTime($row['date_rencontre']);
        $aujourdhui = new DateTime();

        if ($date_rencontre->format('Y-m-d') === $aujourdhui->format('Y-m-d')) {
            $formatted_date = "Aujourd'hui à " . $date_rencontre->format('H\hi');
        } else {
            $formatted_date = $date_rencontre->format('d/m/Y à H\hi');
        }
        echo " <div class='info-today'> <h3>". $clanTranslations[$row['id_clan_demandeur']] ." Vs ".  $clanTranslations[$row['id_clan_receveur']]  ."</h3>";
        echo  $formatted_date . "<br>";
        echo  $tournamentFormats[$row['format']] . "<br>";
        echo "Brawlhalla room : #" . $row['brawlhalla_room'] . "<br>";
        echo "<br> </div>";
    }
    
}
else{
    echo "<h3>No clan battles today</h3>";
}



