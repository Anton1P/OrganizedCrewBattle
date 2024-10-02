<?php 
include "../APIBrawlhalla/security.php";
include "../bddConnexion/bddConnexion.php";

$clan_id = $_SESSION['brawlhalla_data']['clan_id'];
$date_actuelle = (new DateTime())->format('Y-m-d\TH:i');

?>
<style>
.notification {
    padding: 10px;
    margin: 10px 0;
    border: 1px solid red;
    background-color: #f8d7da;
    color: #721c24;
}
</style>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Tournoi</title>
    <!-- Inclure une bibliothèque de date picker (flatpickr) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.js"></script>
</head>
<body>

    <?php // Vérifier si une notification existe
    if (isset($_SESSION['notification'])) {
        echo '<div class="notification">' . $_SESSION['notification'] . '</div>';
        unset($_SESSION['notification']); // Supprimer la notification après l'affichage
    }
    ?>

<h2>Remplir le formulaire pour le tournoi</h2>

<form action="../bddConnexion/traitement_askForm.php" method="POST">

    <!-- Liste déroulante pour choisir le clan à affronter -->
    <label for="clan_id">Choisir le clan à affronter:</label>
    <select required name="clan_id" id="clan_id">
        <?php
        // Récupérer les clans depuis la base de données
        $query = "SELECT id_clan, nom_clan FROM clans WHERE id_clan != $clan_id"; 
        $result = $conn->query($query);

        // Si des résultats sont trouvés
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<option value="' . $row['id_clan'] . '">' . $row['nom_clan'] . '</option>';
            }
        } else {
            // Message d'erreur si aucun clan n'est trouvé
            echo '<option value="">Aucun clan trouvé</option>';
        }
        ?>
    </select>
    <br><br>

    <!-- Choisir le format du tournoi -->
    <label for="format">Choisir le format de la clan battle:</label>
    <select required name="format" id="format">
        <option value="1">CrewBattle Bo3</option>
        <option value="2">CrewBattle Bo5</option>
        <option value="3">French CrewBattle</option>
        <option value="4">Deutch CrewBattle</option>
        <option value="5">English CrewBattle</option>
    </select>
    <br><br>

    <!-- Sélection des joueurs du clan -->
    <label for="joueurs">Sélectionner les joueurs :</label><br>
    <?php 
    // Récupérer les joueurs appartenant au clan connecté
    $query = "SELECT id_player, player_name FROM players WHERE id_clan = $clan_id";
    $result = $conn->query($query);

    if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <input type="checkbox" name="joueurs[]" value="<?php echo $row['id_player']; ?>">
            <label for="joueur_<?php echo $row['id_player']; ?>"><?php echo $row['player_name']; ?></label><br>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Aucun joueur trouvé.</p>
    <?php endif; ?>
    <br>

   
    <label for="date_rencontre">Choisir la date et l'heure de la rencontre :</label>
    <input required type="datetime-local" id="date_rencontre" name="date_rencontre" class="date-picker" >
    <br><br>

    <input type="submit" value="Envoyer">

</form>

<script>
   
    flatpickr(".date-picker", {
        enableTime: true,        
        dateFormat: "Y-m-d H:i:S", 
        time_24hr: true,       
        minDate: "<?php echo $date_actuelle; ?>" // Limiter les dates antérieures
    });
</script>

</body>
</html>

<?php
$conn->close();
?>
