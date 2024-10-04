<style>
        .go-report {
            <?php 
                if ($hideDiv) {
                    echo 'display: none;';
                }
            ?>
        }
</style>

<?php

include "bddConnexion.php";


//! C'est une sécurité
if (!isset($_SESSION['brawlhalla_data']['clan_id'])) { 
    header("Location: ../APIBrawlhalla/routes.php");
    exit(); 
}

$id_clan = $_SESSION['brawlhalla_data']['clan_id'];
$date_actuelle = new DateTime();
$date_aujourdhui = $date_actuelle->format('Y-m-d'); // Formater la date actuelle



//! pour laisse la notif du match lorsque les 2 ont checkin (admin panel)


// SQL pour récupérer les informations du tournoi du clan connecté
$sql = "SELECT * FROM tournoi WHERE (id_clan_demandeur = ? OR id_clan_receveur = ?) AND accepted = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_clan, $id_clan);
$stmt->execute();
$result = $stmt->get_result();

// Vérifier s'il y a un tournoi correspondant
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $date_rencontre = new DateTime($row['date_rencontre']);

        // Calcul de la plage de 30 minutes avant et 15 minutes après la rencontre
        $plage_avant = clone $date_rencontre;
        $plage_avant->modify('-30 minutes');

        $plage_apres = clone $date_rencontre;
        $plage_apres->modify('+15 minutes');

        // Vérifier si la date actuelle est passée 
        if ($date_actuelle > $plage_apres ) {

                // Ajouter une condition pour vérifier si les deux clans ont fait le check-in
                $checkin_sql = "SELECT id_checkin FROM checkin WHERE id_tournoi = ? AND clan_demandeur_checkin = 1 AND clan_receveur_checkin = 1";
                $checkin_stmt = $conn->prepare($checkin_sql);
                $checkin_stmt->bind_param("i", $row['id_tournoi']);
                $checkin_stmt->execute();
                $checkin_result = $checkin_stmt->get_result();
            
                // Si aucun check-in trouvé pour les deux clans, supprimer le tournoi
                if ($checkin_result->num_rows == 0) {
                    // Suppression du tournoi de la base de données
                    $delete_sql = "DELETE FROM tournoi WHERE id_tournoi = ?";
                    $delete_stmt = $conn->prepare($delete_sql);
                    $delete_stmt->bind_param("i", $row['id_tournoi']);
                    $delete_stmt->execute();
                    $delete_stmt->close();
                }
            
                $checkin_stmt->close();
             
        }

                    $checkin_sql = "SELECT id_checkin FROM checkin WHERE id_tournoi = ? AND clan_demandeur_checkin = 1 AND clan_receveur_checkin = 1";
                    $checkin_stmt = $conn->prepare($checkin_sql);
                    $checkin_stmt->bind_param("i", $row['id_tournoi']);
                    $checkin_stmt->execute();
                    $checkin_result = $checkin_stmt->get_result();
                    // Si la date actuelle est dans la plage définie
                    if ($date_actuelle >= $plage_avant || $checkin_result->num_rows > 0) {



                         // Vérifier si une preuve a déjà été soumise par l'un des clans
                        $proof_sql = "SELECT demandeur_sendproof, receveur_sendproof FROM verif_match WHERE id_tournoi = ?";
                        $proof_stmt = $conn->prepare($proof_sql);
                        $proof_stmt->bind_param("i", $row['id_tournoi']);
                        $proof_stmt->execute();
                        $proof_result = $proof_stmt->get_result();
                        $proof_data = $proof_result->fetch_assoc();

                        
                        if (!empty( $proof_data) && $proof_data['demandeur_sendproof'] == 1 || !empty( $proof_data) &&  $proof_data['receveur_sendproof'] == 1 ) {
                            $hideDiv = true; 
                        }
                        else{
                            // Envoyer les informations du tournoi à la session
                            $_SESSION['tournoi_id'] = $row['id_tournoi'];
                            $_SESSION['date_rencontre'] = $row['date_rencontre'];
                            $_SESSION['format'] = $row['format'];
                            $_SESSION['id_clan_demandeur'] = $row['id_clan_demandeur'];
                            $_SESSION['id_clan_receveur'] = $row['id_clan_receveur'];
                            $_SESSION['brawlhalla_room'] = $row['brawlhalla_room'];

                            // Calcul du temps restant ou écoulé pour afficher le compteur
                            $secondes_restantes = $date_rencontre->getTimestamp() - $date_actuelle->getTimestamp();

                            // Stocker le timestamp de la rencontre dans une variable JS
                            $timestamp_rencontre = $date_rencontre->getTimestamp();

                            // Affichage du bouton avec le compteur
                            echo "<span class='go-report'>";
                            echo "<h3>Un tournoi est disponible !</h3>";
                            echo "<p id='compteur'></p>";
                            echo "<form style=' display: flex; justify-content: space-around;' action='../view/tournoiReport.php' method='post'>";
                            echo "<input type='hidden' name='tournoi_id' value='" . $_SESSION['tournoi_id'] . "'>";
                            echo "<button class='rounded-button' >Check-in</button>";
                            echo "</form>";
                            echo "</span>";
                            // Script JavaScript pour mettre à jour le compteur
                            echo "
                            <script>
                                let timestampRencontre = $timestamp_rencontre;
                                let dateActuelle = " . $date_actuelle->getTimestamp() . ";
                                let compteurElem = document.getElementById('compteur');

                                function mettreAJourCompteur() {
                                    let maintenant = Math.floor(Date.now() / 1000); // timestamp actuel
                                    let secondesRestantes = timestampRencontre - maintenant;

                                    let minutes = Math.floor(Math.abs(secondesRestantes) / 60);
                                    let secondes = Math.abs(secondesRestantes) % 60;

                                    let compteur = (secondesRestantes > 0 ? '-' : '+') + String(minutes).padStart(2, '0') + ':' + String(secondes).padStart(2, '0');
                                    compteurElem.innerText = 'Temps jusqu\'à la rencontre : ' + compteur;


                                
                                }

                                let intervalle = setInterval(mettreAJourCompteur, 1000); // Met à jour chaque seconde
                                mettreAJourCompteur(); // Appelle immédiatement pour un affichage correct au début
                            </script>
                            ";
            } 
        }
        else{
            echo "<h3>Check-in available 30m before</h3>";
           }  
   }
} else {
    echo "<h3>Aucun tournoi en attente...</h3>";
}

// Connexion à la base de données et autres logiques
$stmt->close();
$conn->close();
?>
