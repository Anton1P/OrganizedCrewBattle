<?php
include "../bddConnexion/bddConnexion.php";

if (isset($_POST['search'])) {
    $search = $conn->real_escape_string($_POST['search']);
    
    // Requête SQL pour rechercher les clans correspondant au texte tapé, triés par nom_clan
    $query = "SELECT id_clan, nom_clan FROM clans WHERE nom_clan LIKE '%$search%' ORDER BY nom_clan ASC LIMIT 10"; // Limiter à 10 résultats et trier par ordre alphabétique
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Afficher les résultats sous forme de liens
        while ($row = $result->fetch_assoc()) {
            // Générer un lien avec les informations du clan en paramètre GET
            echo ' <a href="#" class="clan-link" data-id="' . $row['id_clan'] . '" data-nom="' . $row['nom_clan'] . '">
                    <div>
                   ' . $row['nom_clan'] . '</div> </a>';
               
        }
    } else {
        echo '<div>Aucun clan trouvé.</div>';
    }
}

$conn->close();
?>
