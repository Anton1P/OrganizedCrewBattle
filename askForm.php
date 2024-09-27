<?php 
session_start();
$clan_id = $_SESSION['brawlhalla_data']['clan_id'];

include "./bddConnexion/bddConnexion.php";
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Tournoi</title>
    <!-- Inclure une bibliothèque de date picker (comme jQuery UI ou flatpickr) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.js"></script>
</head>
<body>

<h2>Remplir le formulaire pour le tournoi</h2>

<form action="bddConnexion/traitement_askForm.php" method="POST">

    <!-- Liste déroulante (select) -->
    <label for="clan_id">Choisir le clan à affronter:</label>
    <select name="clan_id" id="clan_id">
    <?php
        // Récupérer les clans depuis la BDD
        $query = "SELECT id_clan, nom_clan FROM clans WHERE id_clan != $clan_id"; 
        $result = $conn->query($query);

        // Si des résultats sont trouvés
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Afficher chaque clan dans une option du select
                echo '<option value="' . $row['id_clan'] . '">' . $row['nom_clan'] . '</option>';
            }
        } else {
            // Message d'erreur si aucun clan n'est trouvé
            echo '<option value="">Aucun clan trouvé</option>';
        }
        ?>
    </select>
    <br><br>

    <label for="format">Choisir le format de la clan battle:</label>
    <select name="format" id="format">
        <option value="1">CrewBattle Bo3  </option>
        <option value="2">CrewBattle Bo5</option>
        <option value="3">French CrewBattle</option>
        <option value="4">Deutch CrewBattle</option>
        <option value="5">English CrewBattle</option>
        <!-- Ajouter plus d'options ici ou les récupérer dynamiquement de la base de données -->
    </select>
    <br><br>


    <!-- Liste des cases à cocher avec les joueurs récupérés de la BDD -->
    <label for="joueurs">Sélectionner les joueurs :</label><br>
    <?php 
    $query = "SELECT id_player, player_name, id_clan FROM players WHERE id_clan = $clan_id" ; 
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

    <!-- Date Picker -->
    <label for="date_rencontre">Choisir la date de la rencontre:</label>
    <input type="text" id="date_rencontre" name="date_rencontre" class="date-picker">
    <br><br>

    <input type="submit" value="Envoyer">

</form>

<script>
    // Initialiser le date picker avec l'heure, adapté au format DATETIME (Y-m-d H:i:s)
    flatpickr(".date-picker", {
        enableTime: true,         // Activer la sélection de l'heure
        dateFormat: "Y-m-d H:i:S",  // Format compatible DATETIME (ex: 2024-09-26 14:30:00)
        time_24hr: true           // Utiliser le format 24 heures
    });
</script>

</body>
</html>

<?php
// Fermer la connexion à la base de données
$conn->close();
?>