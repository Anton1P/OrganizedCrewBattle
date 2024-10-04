<?php
include "../bddConnexion/bddConnexion.php";

// Définir le nombre de clans par page
$clans_per_page = 2;

// Vérifier si la page actuelle est spécifiée dans l'URL, sinon par défaut à 1
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculer l'offset pour la requête SQL
$offset = ($current_page - 1) * $clans_per_page;

// Récupérer le total des clans pour la pagination
$total_query = "SELECT COUNT(*) as total FROM clans";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_clans = $total_row['total'];

// Récupérer les clans triés par ELO
$query = "SELECT nom_clan, elo_rating, wins, loses FROM clans ORDER BY elo_rating DESC LIMIT $clans_per_page OFFSET $offset";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard des Clans</title>
</head>
<body>

<h1>Leaderboard des Clans</h1>

<table border="1">
    <tr>
        <th>Rang</th>
        <th>Nom du Clan</th>
        <th>W - L</th>
        <th>elo_rating</th>
    </tr>
    <?php if ($result->num_rows > 0): ?>
        <?php $rank = $offset + 1; // Rang commence à 1 ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $rank++; ?></td>
                <td><?php echo htmlspecialchars($row['nom_clan']); ?></td>
                <td><?php echo htmlspecialchars($row['wins']) . ' - ' . htmlspecialchars($row['loses']); ?></td>
                <td><?php echo htmlspecialchars($row['elo_rating']); ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="4">Aucun clan trouvé.</td>
        </tr>
    <?php endif; ?>
</table>

<div>
    <?php
    // Calculer le nombre total de pages
    $total_pages = ceil($total_clans / $clans_per_page);

    // Lien vers la page précédente
    if ($current_page > 1): ?>
        <a href="Leaderboard.php?page=<?php echo $current_page - 1; ?>">Précédent</a>
    <?php endif; ?>

    <!-- Affichage des pages -->
    <?php for ($page = 1; $page <= $total_pages; $page++): ?>
        <a href="Leaderboard.php?page=<?php echo $page; ?>"><?php echo $page; ?></a>
    <?php endfor; ?>

    <!-- Lien vers la page suivante -->
    <?php if ($current_page < $total_pages): ?>
        <a href="Leaderboard.php?page=<?php echo $current_page + 1; ?>">Suivant</a>
    <?php endif; ?>
</div>

</body>
</html>

<?php
$conn->close();
?>
