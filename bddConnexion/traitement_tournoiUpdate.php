<?php

include "bddConnexion.php";
//! C'est une sécurité
if (!isset($_SESSION['brawlhalla_data']['clan_id'])) { 
    header("Location: ../view/AdminPanel.php");
    exit(); 
}

$id_clan = $_SESSION['brawlhalla_data']['clan_id'];
$date_actuelle = new DateTime();
$date_aujourdhui = $date_actuelle->format('Y-m-d'); // Formater la date actuelle

//! Définir le tableau associatif pour les formats
$formats = [
    1 => 'Crew Battle 1',
    2 => 'Crew Battle 2',
    3 => 'Crew Battle 3',
    4 => 'Crew Battle 4',
    5 => 'Crew Battle 5'
];







// SQL pour récupérer les tournois prévus pour le clan connecté le même jour
$sql_tournois_hier = "SELECT * FROM tournoi WHERE (id_clan_demandeur = ? OR id_clan_receveur = ?) AND DATE(date_rencontre) = ? AND accepted = 1";
$stmt_tournois = $conn->prepare($sql_tournois_hier);
$stmt_tournois->bind_param("iis", $id_clan, $id_clan, $date_aujourdhui);
$stmt_tournois->execute();
$result_tournois = $stmt_tournois->get_result();

// Vérifier s'il y a des tournois pour aujourd'hui
if ($result_tournois->num_rows > 0) {
    echo "<h3>Vous avez des tournois prévus aujourd'hui !</h3>";
    echo "<button id='btnVoirTournois'>Voir les détails des tournois</button>";
    echo "<div id='tournoiDetails' style='display: none;'>";
    echo "<h4>Détails des tournois :</h4>";
    echo "<ul>";

    // Récupérer les noms des clans à partir de leurs ID
    $clan_names = [];
    $sql_clan_names = "SELECT id_clan, nom_clan FROM clans WHERE id_clan IN (?, ?)";
    $stmt_clan_names = $conn->prepare($sql_clan_names);
    
    // Remplir le tableau des noms de clans
    while ($row = $result_tournois->fetch_assoc()) {
        $stmt_clan_names->bind_param("ii", $row['id_clan_demandeur'], $row['id_clan_receveur']);
        $stmt_clan_names->execute();
        $result_clan_names = $stmt_clan_names->get_result();
        while ($clan_row = $result_clan_names->fetch_assoc()) {
            $clan_names[$clan_row['id_clan']] = $clan_row['nom_clan'];
        }
    }
    $stmt_clan_names->close();

    // Réinitialiser le pointeur du résultat pour l'affichage
    $result_tournois->data_seek(0);

    // Afficher les informations de chaque tournoi prévu aujourd'hui
    while ($row = $result_tournois->fetch_assoc()) {
        echo "<li>";
        
        // Formater la date de rencontre
        $date_rencontre = new DateTime($row['date_rencontre']);
        $aujourdhui = new DateTime();
        if ($date_rencontre->format('Y-m-d') === $aujourdhui->format('Y-m-d')) {
            $formatted_date = "Aujourd'hui à " . $date_rencontre->format('H\hi');
        } else {
            $formatted_date = $date_rencontre->format('d/m/Y à H\hi');
        }
        
        echo "ID Tournoi : " . $row['id_tournoi'] . "<br>";
        echo "Date de Rencontre : " . $formatted_date . "<br>";
        
        // Remplacer le format numérique par son nom
        $format_nom = isset($formats[$row['format']]) ? $formats[$row['format']] : 'Format inconnu';
        echo "Format : " . $format_nom . "<br>";
        
        echo "Clan Demandeur : " . $clan_names[$row['id_clan_demandeur']] . "<br>";
        echo "Clan Receveur : " . $clan_names[$row['id_clan_receveur']] . "<br>";
        echo "Brawlhalla room : #" . $row['brawlhalla_room'] . "<br>";
        echo "</li><br>";
    }
    
    echo "</ul>";
    echo "</div>";

    // Script JavaScript pour afficher/masquer les détails des tournois
    echo "
    <script>
        document.getElementById('btnVoirTournois').addEventListener('click', function() {
            var details = document.getElementById('tournoiDetails');
            if (details.style.display === 'none') {
                details.style.display = 'block';
            } else {
                details.style.display = 'none';
            }
        });
    </script>
    ";
} 












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

        // Vérifier si la date actuelle est passée //! ajouter une condition 
        if ($date_actuelle > $plage_apres && $row['on_page'] == 0) {
            // Suppression du tournoi de la base de données
            $delete_sql = "DELETE FROM tournoi WHERE id_tournoi = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $row['id_tournoi']);
            $delete_stmt->execute();
            $delete_stmt->close();
            continue; // Passer au tournoi suivant
        }

        // Si la date actuelle est dans la plage définie
        if ($date_actuelle >= $plage_avant && $date_actuelle <= $plage_apres) {
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
            echo "<h2>Un tournoi est disponible !</h2>";
            echo "<p id='compteur'></p>";
            echo "<form action='../view/tournoiReport.php' method='post'>";
            echo "<input type='hidden' name='tournoi_id' value='" . $_SESSION['tournoi_id'] . "'>";
            echo "<input type='submit' value='Voir les détails du tournoi'>";
            echo "</form>";

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

                    // Vérifier si le tournoi est déjà passé de plus de 15 minutes
                    if (secondesRestantes <= -900) {
                        clearInterval(intervalle);
                        // Rafraîchir la page pour supprimer le tournoi
                        location.reload(); // Rafraîchir la page
                    }
                }

                let intervalle = setInterval(mettreAJourCompteur, 1000); // Met à jour chaque seconde
                mettreAJourCompteur(); // Appelle immédiatement pour un affichage correct au début
            </script>
            ";

            exit;
        } 
    }
} else {
    echo "Aucun tournoi en attente pour le clan.";
}

// Connexion à la base de données et autres logiques
$stmt->close();
$conn->close();
?>
