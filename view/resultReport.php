<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat du Tournoi</title>
    <link rel="stylesheet" href="../assets/styles/ask.css">
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
<div class="container">
<?php
include "../bddConnexion/bddConnexion.php";
include "../APIBrawlhalla/traductions.php";
include "../APIBrawlhalla/security.php";

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
        
        // Vérifier si les deux clans sont d'accord sur le résultat
        $sql_verif = "SELECT * FROM verif_report WHERE id_tournoi = ?";
        $stmt_verif = $conn->prepare($sql_verif);
        $stmt_verif->bind_param("i", $id_tournoi);
        $stmt_verif->execute();
        $result_verif = $stmt_verif->get_result();

        if ($result_verif->num_rows > 0) {
            $verif_data = $result_verif->fetch_assoc();
            
            echo $verif_data['id_tournoi']; // Debugging pour voir si les données existent
            echo "1"; // Debugging pour vérifier le flux d'exécution

            // Vérifier si le clan connecté est le demandeur ou le receveur
            if ($clan_id == $id_clan_demandeur) {
                if ($verif_data['clan_demandeur_report'] == 1) {
                    $_SESSION['notification'] = "Le clan demandeur a déjà reporté le résultat. En attente du clan receveur.";
                    header("Location: ../view/AdminPanel.php");
                    exit();
                }
            } elseif ($clan_id == $id_clan_receveur) {
                if ($verif_data['clan_receveur_report'] == 1) {
                    $_SESSION['notification'] = "Le clan receveur a déjà reporté le résultat. En attente du clan demandeur.";
                    header("Location: ../view/AdminPanel.php");
                    exit();
                }
            } else {
                $_SESSION['notification'] = "Erreur : clan non reconnu.";
                    header("Location: ../view/AdminPanel.php");
                    exit();
            }
        } 

        // Si aucun résultat n'a encore été reporté, afficher les détails du tournoi
        echo "<h2>Détails du tournoi</h2>";
        echo "<p>Format : " . $tournamentFormats[$format] . "</p>";
        echo "<p>Clan Demandeur : " . $clanTranslations[$id_clan_demandeur] . "</p>";
        echo "<p>Clan Receveur : " . $clanTranslations[$id_clan_receveur] . "</p>";
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
</div>
</body>
</html>
