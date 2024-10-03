<script>
    window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        console.log("La page a été chargée via la navigation arrière/avant");
        location.reload();  
    } else {
        console.log("La page a été chargée normalement");
    }
});
</script>

<?php
include "../bddConnexion/bddConnexion.php";
include "../APIBrawlhalla/security.php";


$id_clan = $_SESSION['brawlhalla_data']['clan_id'];
// SQL pour récupérer les informations du tournoi du clan connecté
$sql = "SELECT * FROM tournoi WHERE (id_clan_demandeur = ? OR id_clan_receveur = ?) AND accepted = 1";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo "Erreur dans la préparation de la requête: " . $conn->error;
    exit();
}

$stmt->bind_param("ii", $id_clan, $id_clan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_tournoi = $row['id_tournoi'];
        $date_rencontre = $row['date_rencontre'];
        $format = $row['format'];
        $id_clan_demandeur = $row['id_clan_demandeur'];
        $id_clan_receveur = $row['id_clan_receveur'];
        $brawlhalla_room = $row['brawlhalla_room'];

        $_SESSION['id_tournoi'] = $id_tournoi;
        $_SESSION['date_rencontre'] = $date_rencontre;
        $_SESSION['format'] = $format ;
        $_SESSION['id_clan_demandeur'] = $id_clan_demandeur;
        $_SESSION['id_clan_receveur'] = $id_clan_receveur ;
        $_SESSION['brawlhalla_room'] = $brawlhalla_room ;
    }
  
    if (isset($id_tournoi)) {
        // Date
        $date_rencontre = new DateTime($date_rencontre);
        $date_actuelle = new DateTime();
        

        //! Securité
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
          if (strpos($referer, '/view/AdminPanel.php') !== false || strpos($referer, 'tournoiReport.php') !== false) {
       
            // Vérifier si les deux clans ont fait leur check-in
            $sql_check = "SELECT clan_demandeur_checkin, clan_receveur_checkin FROM checkin WHERE id_tournoi = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("i", $id_tournoi);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                $checkin_data = $result_check->fetch_assoc();     
                if ($checkin_data['clan_demandeur_checkin'] == 1 && $checkin_data['clan_receveur_checkin'] == 1) {
                    $tournoi_checkin = true;
                }
                else{
                    $tournoi_checkin = false;
                    
                }
            }
      
            // All check message
            if ($tournoi_checkin == false){
                echo "<div style='border: 2px solid red; padding: 10px; margin-top: 20px; background-color: #ffe6e6;'>";
                echo "<h3>Avertissement</h3>";
                echo "<p>Si vous quittez cette page 15 minutes après le début du tournoi, alors le tournoi sera supprimé.</p>";
                echo "<p>Attednez que votre adversaire check-in (DQ in 15m)</p>";
                echo "<p id='compteur'></p>";  
                echo "</div>";
            } 
            else{
                echo "<div style='border: 2px solid green; padding: 10px; margin-top: 20px; background-color: #2fff2f66  ;'>";
                echo "Les deux clans ont check-in, le match est prêt.";
                echo "</div>";
            }
         
                // Détails du tournoi
                echo "<h2>Détails du tournoi</h2>";
                echo "<p>Date de la rencontre : " . $date_rencontre->format('Y-m-d H:i:s') . "</p>";
                echo "<p>Format : " . $format . "</p>";
                echo "<p>Clan Demandeur ID : " . $id_clan_demandeur . "</p>";
                echo "<p>Clan Receveur ID : " . $id_clan_receveur . "</p>";
                
                $sql_joueurs = "SELECT id_player FROM player_tournoi WHERE id_tournoi = ?";
                $stmt_joueurs = $conn->prepare($sql_joueurs);
                $stmt_joueurs->bind_param("i", $id_tournoi);
                $stmt_joueurs->execute();
                $result_joueurs = $stmt_joueurs->get_result();

                echo "<h3>Joueurs Choisis pour le Tournoi :</h3>";
                if ($result_joueurs->num_rows > 0) {
                    echo "<ul>";
                    while ($joueur = $result_joueurs->fetch_assoc()) {
                        echo "<li>" . htmlspecialchars($joueur['id_player']) . "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>Aucun joueur sélectionné.</p>";
                }

                include "../bddConnexion/traitement_checkin.php";

                if($is_checked_in == 1){
                    if ($brawlhalla_room != 0) {
                        echo "<p>Brawlhalla room : #" . $brawlhalla_room . "</p>";
                    } else {
                        echo "<p>Brawlhalla room : Room à setup</p>";
                    }

                    // Si brawlhalla_room est vide, afficher le formulaire
                    if (empty($brawlhalla_room)) {
                        echo "<h3>Ajouter la salle Brawlhalla</h3>";
                        echo '<form action="../view/tournoiReport.php" method="POST">';
                        echo '<label for="brawlhalla_room">Numéro de salle (6 chiffres) :</label>';
                        echo '<input type="number" id="brawlhalla_room" name="brawlhalla_room" required min="100000" max="999999" oninput="validateInput(this)">';
                        echo '<input type="hidden" name="id_tournoi" value="' . $id_tournoi . '">';
                        echo '<br><br>';
                        echo '<input type="submit" value="Enregistrer">';
                        echo '</form>';
                    }
                    elseif($tournoi_checkin == true){

                            echo '<a href="resultReport.php?id_tournoi=' . $id_tournoi .
                                 '&date_rencontre=' . urlencode($date_rencontre->format('Y-m-d H:i:s')) .
                                 '&format=' . $format .
                                 '&id_clan_demandeur=' . $id_clan_demandeur .
                                 '&id_clan_receveur=' . $id_clan_receveur .
                                 '&brawlhalla_room=' . $brawlhalla_room .
                                 '">Report</a> <br>';
                    }
                }
            }
            else{
                header("Location: ../view/AdminPanel.php");
            }
        } else {
            $_SESSION['notification'] = "Impossible de se rendre sur la page report du tournoi comme cela";
            header("Location: ../view/AdminPanel.php");
            exit();
        }
    } else {
        echo "Aucun tournoi trouvé.";
    }
} else {
    header("Location: ../view/AdminPanel.php");
    exit();
}

     // Mettre à jour la colonne brawlhalla_room dans la base de données
     if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($brawlhalla_room)) {
        $id_tournoi = $_POST['id_tournoi'];
        $brawlhalla_room = $_POST['brawlhalla_room'];
    
        $sql = "UPDATE tournoi SET brawlhalla_room = ? WHERE id_tournoi = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $brawlhalla_room, $id_tournoi);
    
        if ($stmt->execute()) {
            header("Location: ../view/tournoiReport.php");
            exit();
        } else {
            echo "Erreur lors de la mise à jour de la salle Brawlhalla: " . $stmt->error;
        }
    
        $stmt->close();
    }


?>

<div id="response-container">Personne n'a encore report.</div>









<script>
    function validateInput(input) {
    // Supprimer les caractères non numériques
    input.value = input.value.replace(/[^0-9]/g, '');
    
    // Limiter à 6 chiffres
    if (input.value.length > 6) {
        input.value = input.value.slice(0, 6);
    }
}
// Initialiser le compte à rebours pour le tournoi
let timestampRencontre = <?php echo $date_rencontre->getTimestamp(); ?>;  // Timestamp de la rencontre
let compteurElem = document.getElementById('compteur');
let tournoiID = <?php echo $id_tournoi; ?>;

function mettreAJourCompteur() {
    if(compteurElem !== null){
        let maintenant = Math.floor(Date.now() / 1000);  // Timestamp actuel en secondes
        let secondesRestantes = timestampRencontre - maintenant;  // Secondes restantes jusqu'à la rencontre

        let minutes = Math.floor(Math.abs(secondesRestantes) / 60);
        let secondes = Math.abs(secondesRestantes) % 60;
        
        let signe = secondesRestantes > 0 ? '-' : '+';  // Affiche '-' avant le début et '+' après
        compteurElem.innerText = 'Temps jusqu\'à la rencontre : ' + signe + String(minutes).padStart(2, '0') + ':' + String(secondes).padStart(2, '0');

        // Vérification si 15 minutes sont écoulées après le début du tournoi
        if (secondesRestantes < -900) { // 15 minutes (900 secondes) après la rencontre
            verifierCheckin(tournoiID);
            clearInterval(intervalle); // Stopper le compte à rebours
        }
    }
}

// Mettre à jour le compteur chaque seconde
let intervalle = setInterval(mettreAJourCompteur, 1000);
mettreAJourCompteur();  

function verifierCheckin(tournoiID) {
    // Appel AJAX pour vérifier si les deux équipes ont fait leur check-in
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "../bddConnexion/verifier_checkin.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let response = JSON.parse(xhr.responseText);
            if (response.checkin_demandeur == 0 || response.checkin_receveur == 0) {
                // Si l'une des équipes n'a pas check-in, supprimer le tournoi
                supprimerTournoi(tournoiID);
            }
        }
    };
    xhr.send("id_tournoi=" + tournoiID);
}

function supprimerTournoi(tournoiID) {
    // Appel AJAX pour supprimer le tournoi
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "../bddConnexion/delete_tournoi.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert("Le tournoi a été supprimé car une équipe n'a pas check-in à temps.");
            location.reload();  // Recharger la page après la suppression
        }
    };
    xhr.send("id_tournoi=" + tournoiID);
}
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>

        // Fonction pour mettre à jour le chronomètre et faire une requête AJAX toutes les 20 secondes
        function updateTimerAndCheckReport() {
            // Incrémenter le chronomètre chaque seconde
            setInterval(function() {
        
                    $.ajax({
                        url: '../bddConnexion/chronoVerification.php',  // Fichier qui vérifie le temps écoulé
                        type: 'POST',
                        data: {
                            id_tournoi: <?php echo $id_tournoi; ?>, // Vous devez passer ici l'ID du tournoi
                            id_clan_demandeur: <?php echo $id_clan_demandeur; ?>, // ID du clan demandeur
                            id_clan_receveur: <?php echo $id_clan_receveur; ?>  // ID du clan receveur
                        },
                        success: function(response) {
                            // Afficher la réponse dans le conteneur
                            $('#response-container').html(response);
                        },
                        error: function() {
                            window.location.href = '../view/AdminPanel.php'
                        }
                    });
                
            }, 1000); // Mettre à jour le chronomètre chaque seconde
        }

        // Lancer la fonction pour démarrer le chronomètre et les requêtes AJAX
        updateTimerAndCheckReport();
    </script>


