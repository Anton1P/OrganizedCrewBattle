<?php
session_start(); // Assurez-vous que la session est démarrée pour accéder à l'ID du clan connecté
include "../bddConnexion/bddConnexion.php";

if (isset($_POST['search'])) {
    $search = $conn->real_escape_string($_POST['search']);
    
    // Obtenir l'ID du clan connecté pour l'exclure de la recherche
    $clan_id = $_SESSION['brawlhalla_data']['clan_id'];
    
    // Requête SQL pour rechercher les clans correspondant au texte tapé, triés par nom_clan, et exclure le clan connecté
    $query = "SELECT id_clan, nom_clan 
              FROM clans 
              WHERE nom_clan LIKE '%$search%' 
              AND id_clan != ? 
              ORDER BY nom_clan ASC 
              LIMIT 10"; // Limiter à 10 résultats et trier par ordre alphabétique

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $clan_id); // Lier l'ID du clan pour l'exclusion
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Afficher les résultats sous forme de liens
        while ($row = $result->fetch_assoc()) {
            // Générer un lien avec les informations du clan en paramètre GET
            echo ' <a href="#" class="clan-link" data-id="' . $row['id_clan'] . '" data-nom="' . $row['nom_clan'] . '">
                    <div>' . $row['nom_clan'] . '</div> </a>';
        }
    } else {
        echo '<div>Aucun clan trouvé.</div>';
    }

    $stmt->close();
}

$conn->close();
?>
