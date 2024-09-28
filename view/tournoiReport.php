<?php
// Démarrer la session pour accéder aux données du tournoi
include "../bddConnexion/bddConnexion.php";
session_start();

$tournoi_id = $_SESSION['tournoi_id']; 
$date_rencontre = new DateTime($_SESSION['date_rencontre']);
$date_actuelle = new DateTime();

// Vérifier si l'utilisateur vient de la page AdminPanel.php
if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'AdminPanel.php') !== false) {
    // Si la personne vient bien de AdminPanel.php
    if (isset($tournoi_id)) {

        // Avertissement avec Chronomètre
        echo "<div style='border: 2px solid red; padding: 10px; margin-top: 20px; background-color: #ffe6e6;'>";
        echo "<h3>Avertissement</h3>";
        echo "<p>Si vous quittez cette page 15 minutes après le début du tournoi, alors le tournoi sera supprimé.</p>";
        echo "<p id='compteur'></p>";  // Conteneur pour le compteur
        echo "</div>";

        echo "<h2>Détails du tournoi</h2>";
        echo "<p>Date de la rencontre : " . $_SESSION['date_rencontre'] . "</p>";
        echo "<p>Format : " . $_SESSION['format'] . "</p>";
        echo "<p>Clan Demandeur ID : " . $_SESSION['id_clan_demandeur'] . "</p>";
        echo "<p>Clan Receveur ID : " . $_SESSION['id_clan_receveur'] . "</p>";

    } else {
        echo "Aucun détail de tournoi disponible.";
    }

    // Incrémenter le champ on_page
    $sql = "UPDATE tournoi SET on_page = on_page + 1 WHERE id_tournoi = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tournoi_id);
    $stmt->execute();
    $stmt->close();
} else {
    // Si la personne ne vient pas de AdminPanel.php, rediriger vers la page AdminPanel.php
    header("Location: ../view/AdminPanel.php");
    exit();
}
?>

<script>
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
</script>
