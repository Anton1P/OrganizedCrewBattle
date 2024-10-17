<script>
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            console.log("The page was loaded via back/forward navigation");
            location.reload();  
        } else {
            console.log("The page was loaded normally");
        }
    });
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-in Page</title>
    <link rel="stylesheet" href="../assets/styles/tournoiReport.css">
</head>
<body>
    <div class="container">


<?php
include "../bddConnexion/bddConnexion.php";
include "../APIBrawlhalla/security.php";
include "../APIBrawlhalla/traductions.php";

$id_clan = $_SESSION['brawlhalla_data']['clan_id'];
$is_checked_in_ennemy = 0;
$is_checked_in = 0;

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

            $tournoi_checkin = false;
            // Vérifier si les deux clans ont fait leur check-in
            $sql_check = "SELECT * FROM checkin WHERE id_tournoi = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("i", $id_tournoi);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

                if ($result_check->num_rows > 0) {
                    $checkin_data = $result_check->fetch_assoc();
                    
                    if ($id_clan == $id_clan_demandeur) {
                        // Le clan connecté est le demandeur
                        $is_checked_in = $checkin_data['clan_demandeur_checkin'];
                        $is_checked_in_ennemy = $checkin_data['clan_receveur_checkin'];
                    } elseif ($id_clan == $id_clan_receveur) {
                        // Le clan connecté est le receveur
                        $is_checked_in = $checkin_data['clan_receveur_checkin'];
                        $is_checked_in_ennemy = $checkin_data['clan_demandeur_checkin'];
                    } else {
                        $is_checked_in = 0; 
                        $is_checked_in_ennemy = 0;
                    }

                    if ($checkin_data['clan_demandeur_checkin'] == 1 && $checkin_data['clan_receveur_checkin'] == 1) {
                        $tournoi_checkin = true;
                    }
                    else{
                        $tournoi_checkin = false;
                        
                    }
                }
        
                // All check message
                if ($tournoi_checkin == false){
                    echo '<div class="component" id="checkinComponent">';
                    echo '<h2>Check in</h2>';
                    echo '<div class="active-content">';
                    echo '<p>It\'s time to check into the match!</p>';
                    echo '<p>1 player from each team must check-in to complete this step.</p>';
                    echo '<div class="player-info">';
                    echo '<img class="avatar" src="' .$avatar.'" alt="">';
                    echo '<div class="player-details">';
                    echo '<p>'. $name .' <strong>'.$rank.'</strong></p>';
                        if($is_checked_in == 0){
                            echo '<p class="not-checked-in">Not checked in</p>';
                        }
                        else{
                            echo '<p class="checked-in">Check in !</p>';
                        }
                    echo '</div>';
                    echo '</div>';
                        if($is_checked_in == 0 && $is_checked_in_ennemy == 0){
                            echo '<p class="remaining-checkin">2 more player must check-in</p>';
                        }
                        elseif($is_checked_in == 0 && $is_checked_in_ennemy == 1 || $is_checked_in == 1 && $is_checked_in_ennemy == 0 ){
                            echo '<p class="remaining-checkin">1 more player must check-in</p>';
                        }
                 
                    echo '<p class="disqualification-time">Automatic disqualification at +15 : <span id="compteur"></span></p>';
                    include "../bddConnexion/traitement_checkin.php";
                    echo '</div>';
                    echo '</div>';
                } 
            // Si le check-in est réussi, activer le composant game1Component
                if ($tournoi_checkin == true ) {
                    echo '<div class="component" id="game1Component">';
                    echo '<h2>Brawlhalla room</h2>';
                    echo '<div class="active-content">';
                    echo '<form action="../view/tournoiReport.php" method="POST">';
                    echo '<label for="brawlhalla_room">Room number (6 digits) : </label>';
                    echo '<input type="number" id="brawlhalla_room" name="brawlhalla_room" required min="100000" max="999999" oninput="validateInput(this)">';
                    echo '<input type="hidden" name="id_tournoi" value="' . $id_tournoi . '">';
                    echo '<br><br>';
                    echo '<input class="checkin-button" type="submit" value="Submit">';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                }

                if (!empty($brawlhalla_room) && $tournoi_checkin == true) {
                    echo '<div class="component" id="game2Component">';
                    echo '<h2>Report the Crewbattle</h2>';
                    echo '<div class="active-content">';
                      // If no results have been reported yet, display the tournament details
                    echo "<h4>Tournament Details :</h4> <br>";
                    echo "<p>Format: " . $tournamentFormats[$format] . "</p>";
                    echo "<p>Brawlhalla Room: #" . htmlspecialchars($brawlhalla_room) . "</p> <br>";
                    echo '<div id="response-container">Nobody reported the match yet</div>';
                    echo '<a class="checkin-button"  href="resultReport.php?id_tournoi=' . $id_tournoi .
                    '&date_rencontre=' . urlencode($date_rencontre->format('Y-m-d H:i:s')) .
                    '&format=' . $format .
                    '&id_clan_demandeur=' . $id_clan_demandeur .
                    '&id_clan_receveur=' . $id_clan_receveur .
                    '&brawlhalla_room=' . $brawlhalla_room .
                    '">Report</a> <br>';
                    echo '</div>';
                    echo '</div>';
                }
            }
            else{
                    header("Location: ../view/AdminPanel.php");
            }
        } else {
            $_SESSION['notification'] = "Cannot access the tournament report page like this.";
            header("Location: ../view/AdminPanel.php");
            exit();
        }
    } else {
        $_SESSION['notification'] = "No tournament found.";
        header("Location: ../view/AdminPanel.php");
        exit();
    }
} else {
    header("Location: ../view/AdminPanel.php");
    exit();
}

     // Mettre à jour la colonne brawlhalla_room dans la base de données
     if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($brawlhalla_room)) {
        if(isset($_POST['id_tournoi']) && isset($_POST['brawlhalla_room'])){
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
    }
?>


</div>


</body>
</html>















<script>
var tournoi_checkin = <?php echo json_encode($tournoi_checkin); ?>;
var brawlhalla_room = <?php echo json_encode($brawlhalla_room); ?>;

document.addEventListener("DOMContentLoaded", function () {
    // Vérification des conditions basées sur les variables PHP
    if (tournoi_checkin === false) {
        // Si le check-in n'est pas fait, afficher le composant de check-in
        activateComponent('checkinComponent');
    } else if (tournoi_checkin === true && brawlhalla_room == 0) {
        // Si le check-in est fait et la salle Brawlhalla n'est pas encore définie
        activateComponent('game1Component');
    } else if (tournoi_checkin === true && brawlhalla_room !== 0) {
        // Si le check-in est fait et que la salle est définie
        activateComponent('game2Component');
    }
});

function activateComponent(componentId) {
    const allComponents = document.querySelectorAll('.component');
    allComponents.forEach(component => {
        component.style.display = 'none'; // Masquer tous les composants
    });

    const activeComponent = document.getElementById(componentId);
    activeComponent.style.display = 'block'; // Afficher le composant activé
}
</script>

<script src="../assets/script/"></script>

<script>
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
        compteurElem.innerText =  signe + String(minutes).padStart(2, '0') + ':' + String(secondes).padStart(2, '0');

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
</script>


<script>
    function validateInput(input) {
    // Supprimer les caractères non numériques
    input.value = input.value.replace(/[^0-9]/g, '');
    
    // Limiter à 6 chiffres
    if (input.value.length > 6) {
        input.value = input.value.slice(0, 6);
    }
}

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
            if(response.checkin_demandeur == 1 || response.checkin_receveur == 1){
                location.reload(); 
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
            alert("The tournament has been deleted because a team did not check in on time.");
            location.reload();  // Recharger la page après la suppression
        }
    };
    xhr.send("id_tournoi=" + tournoiID);
}
</script>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>



<script>
    // Fonction pour faire la requête AJAX
    function checkReport() {
    $.ajax({
        url: '../bddConnexion/chronoVerification.php', // Fichier qui vérifie le temps écoulé
        type: 'POST',
        data: {
            id_tournoi: <?php echo $_SESSION['id_tournoi']; ?>,
            id_clan_demandeur: <?php echo $_SESSION['id_clan_demandeur']; ?>,
            id_clan_receveur: <?php echo $_SESSION['id_clan_receveur']; ?>
        },
        success: function(response) {
            const data = JSON.parse(response);
            if (data.status === 'redirect') {
                // Rediriger avec les données de formulaire
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = '../view/matchVerif.php';
                
                for (const key in data.formData) {
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = data.formData[key];
                    form.appendChild(input);
                }
                
                document.body.appendChild(form);
                form.submit();
            } else if (data.status === 'success') {
                window.location.href = data.redirect; // Redirection vers la page de traitement ELO
            } else if (data.status === 'waiting') {
                $('#response-container').html(data.message);
            } else if (data.status === 'no_report') {
                $('#response-container').html(data.message);
            }
        },
        error: function() {
            console.error("Erreur lors de la vérification du report.");
            location.reload();
        }
    });
}

    // Fonction pour démarrer les requêtes AJAX toutes les secondes après le premier appel
    function updateTimerAndCheckReport() {
        // Lancer la première requête immédiatement
        checkReport();

        // Ensuite, exécuter la requête chaque seconde
        setInterval(function() {
            checkReport();
        }, 1000); // Toutes les secondes
    }

    // Démarrer la fonction
    updateTimerAndCheckReport();
</script>


