<?php
session_start(); // Make sure the session is started to access the connected clan's ID
include "../bddConnexion/bddConnexion.php";

if (isset($_POST['search'])) {
    $search = $conn->real_escape_string($_POST['search']);
    
    // Get the connected clan's ID to exclude it from the search
    $clan_id = $_SESSION['brawlhalla_data']['clan_id'];
    
    // SQL query to search for clans matching the input text, sorted by clan name, and exclude the connected clan
    $query = "SELECT id_clan, nom_clan 
              FROM clans 
              WHERE nom_clan LIKE '%$search%' 
              AND id_clan != ? 
              ORDER BY nom_clan ASC 
              LIMIT 10"; // Limit to 10 results and sort alphabetically

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $clan_id); // Bind the clan ID for exclusion
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Display the results as links
        while ($row = $result->fetch_assoc()) {
            // Generate a link with the clan information as a GET parameter
            echo ' <a href="#" class="clan-link" data-id="' . $row['id_clan'] . '" data-nom="' . $row['nom_clan'] . '">
                    <div>' . $row['nom_clan'] . '</div> </a>';
        }
    } else {
        echo '<div>No clan found.</div>'; // Translated text
    }

    $stmt->close();
}

$conn->close();
?>
