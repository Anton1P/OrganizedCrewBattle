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
    }
  
    if (isset($id_tournoi)) {
        // Date
        $date_rencontre = new DateTime($date_rencontre);
        $date_actuelle = new DateTime();

        // Incrémenter le champ on_page
        $sql = "UPDATE tournoi SET on_page = on_page + 1 WHERE id_tournoi = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_tournoi);
        $stmt->execute();
        $stmt->close();

        //! Securité
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
          if (strpos($referer, '/view/AdminPanel.php') !== false || strpos($referer, 'tournoiReport.php') !== false) {
                // Détails du tournoi
                echo "<div style='border: 2px solid red; padding: 10px; margin-top: 20px; background-color: #ffe6e6;'>";
                echo "<h3>Avertissement</h3>";
                echo "<p>Si vous quittez cette page 15 minutes après le début du tournoi, alors le tournoi sera supprimé.</p>";
                echo "<p id='compteur'></p>";  // Conteneur pour le compteur
                echo "</div>";

                echo "<h2>Détails du tournoi</h2>";
                echo "<p>Date de la rencontre : " . $date_rencontre->format('Y-m-d H:i:s') . "</p>";
                echo "<p>Format : " . $format . "</p>";
                echo "<p>Clan Demandeur ID : " . $id_clan_demandeur . "</p>";
                echo "<p>Clan Receveur ID : " . $id_clan_receveur . "</p>";
                
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
                }
            }
        } else {
            echo "Aucun détail de tournoi disponible.";
        }
    } else {
        echo "Aucun tournoi trouvé.";
    }
} else {
    header("Location: ../view/AdminPanel.php");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($brawlhalla_room)) {
    $id_tournoi = $_POST['id_tournoi'];
    $brawlhalla_room = $_POST['brawlhalla_room'];

    // Mettre à jour la colonne brawlhalla_room dans la base de données
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
elseif(!empty($brawlhalla_room)){
    echo "La room Brawlhalla vien juste d'être remplis à l'instant ";
}
?>

<script>
// Initialiser le compte à rebours pour le tournoi
let timestampRencontre = <?php echo $date_rencontre->getTimestamp(); ?>;  // Timestamp de la rencontre
let compteurElem = document.getElementById('compteur');

function mettreAJourCompteur() {
    let maintenant = Math.floor(Date.now() / 1000);  // Timestamp actuel en secondes
    let secondesRestantes = timestampRencontre - maintenant;  // Secondes restantes jusqu'à la rencontre

    let minutes = Math.floor(Math.abs(secondesRestantes) / 60);
    let secondes = Math.abs(secondesRestantes) % 60;

    let signe = secondesRestantes > 0 ? '-' : '+';  // Affiche '-' avant le début et '+' après
    compteurElem.innerText = 'Temps jusqu\'à la rencontre : ' + signe + String(minutes).padStart(2, '0') + ':' + String(secondes).padStart(2, '0');
}

// Mettre à jour le compteur chaque seconde
let intervalle = setInterval(mettreAJourCompteur, 1000);
mettreAJourCompteur();  // Appel immédiat au chargement de la page

function validateInput(input) {
    // Supprimer les caractères non numériques
    input.value = input.value.replace(/[^0-9]/g, '');
    
    // Limiter à 6 chiffres
    if (input.value.length > 6) {
        input.value = input.value.slice(0, 6);
    }
}
</script>
