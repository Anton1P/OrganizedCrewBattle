<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat du Tournoi</title>
    <script>
        // Fonction pour afficher une popup de confirmation avant l'envoi du formulaire
        function confirmSubmission(event) {
            event.preventDefault(); // Empêche l'envoi du formulaire immédiatement
            let confirmation = confirm("Êtes-vous sûr de vouloir envoyer ces résultats ?");
            if (confirmation) {
                document.getElementById("resultForm").submit(); // Envoie le formulaire si l'utilisateur confirme
            }
        }
    </script>
</head>
<body>

<?php
include "../bddConnexion/bddConnexion.php";

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    if (strpos($referer, 'tournoiReport.php') !== false || strpos($referer, 'resultReport.php') !== false) {

        // Récupérer les variables depuis l'URL
        $id_tournoi = $_GET['id_tournoi'];
        $date_rencontre = $_GET['date_rencontre'];
        $format = $_GET['format'];
        $id_clan_demandeur = $_GET['id_clan_demandeur'];
        $id_clan_receveur = $_GET['id_clan_receveur'];
        $brawlhalla_room = $_GET['brawlhalla_room'];

        // Détails du tournoi
        echo "<h2>Détails du tournoi</h2>";
        echo "<p>Date de la rencontre : " . htmlspecialchars($date_rencontre) . "</p>";
        echo "<p>Format : " . htmlspecialchars($format) . "</p>";
        echo "<p>Clan Demandeur ID : " . htmlspecialchars($id_clan_demandeur) . "</p>";
        echo "<p>Clan Receveur ID : " . htmlspecialchars($id_clan_receveur) . "</p>";
        echo "<p>Salle Brawlhalla : #" . htmlspecialchars($brawlhalla_room) . "</p>";
    }
} else {

    header("Location: ../view/AdminPanel.php");
    exit();
}
?>


<h2>Soumettre les résultats du tournoi</h2>

<form id="resultForm" action="../bddConnexion/traitement_report.php" method="POST">
    <input type="hidden" name="id_tournoi" value="<?php echo htmlspecialchars($id_tournoi); ?>">
    <input type="hidden" name="date_rencontre" value="<?php echo htmlspecialchars($date_rencontre); ?>">
    <input type="hidden" name="format" value="<?php echo htmlspecialchars($format); ?>">
    <input type="hidden" name="id_clan_demandeur" value="<?php echo htmlspecialchars($id_clan_demandeur); ?>">
    <input type="hidden" name="id_clan_receveur" value="<?php echo htmlspecialchars($id_clan_receveur); ?>">
    <input type="hidden" name="brawlhalla_room" value="<?php echo htmlspecialchars($brawlhalla_room); ?>">
    <input type="hidden" name="verif_report" value="1">

    <label for="resultat">Choisissez le résultat :</label>
    <select name="resultat" id="resultat" required>
        <option value="1">Victoire</option>
        <option value="0">Défaite</option>
    </select>
    <br><br>
    
    <button type="submit" onclick="confirmSubmission(event)">Envoyer</button>
</form>

</body>
</html>
