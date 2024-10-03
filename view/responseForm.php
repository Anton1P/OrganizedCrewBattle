<?php 
include "../APIBrawlhalla/security.php";

$clan_id = $_SESSION['brawlhalla_data']['clan_id'];
include "../bddConnexion/bddConnexion.php";
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

<h2>Remplir le formulaire pour selectionner les joueur de votre clan qui ferra le tournoi</h2>

<form action="../bddConnexion/traitement_responseForm.php" method="POST">


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

    

    <input type="submit" value="Envoyer">

</form>


</body>
</html>

<?php
// Fermer la connexion à la base de données
$conn->close();
?>