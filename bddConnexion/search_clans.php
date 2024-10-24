<?php
session_start(); // Make sure the session is started to access the connected clan's ID
include "../bddConnexion/bddConnexion.php";

if (isset($_POST['search'])) {
    $search = $conn->real_escape_string($_POST['search']);
    
    // Get the connected clan's ID to exclude it from the search
    $clan_id = $_SESSION['brawlhalla_data']['clan_id'];

    // Retrieve the region of the connected clan
    $sql_region = "SELECT us_e, eu, sea, brz, aus, us_w, jpn, sa, me 
                   FROM region 
                   WHERE id_clan = ?";
    $stmt_region = $conn->prepare($sql_region);
    $stmt_region->bind_param('i', $clan_id);
    $stmt_region->execute();
    $result_region = $stmt_region->get_result();

    $region = null;
    if ($result_region->num_rows > 0) {
        $region_data = $result_region->fetch_assoc();
        // Check which region the connected clan belongs to
        foreach ($region_data as $key => $value) {
            if ($value == 1) {
                $region = $key; // Store the region key
                break;
            }
        }
    }

    // SQL query to search for clans matching the input text, sorted by clan name, and exclude the connected clan
    $query = "SELECT id_clan, nom_clan 
              FROM clans 
              WHERE nom_clan LIKE '%$search%' 
              AND id_clan != ? 
              AND EXISTS (
                  SELECT 1 
                  FROM region r 
                  WHERE r.id_clan = clans.id_clan 
                  AND r.$region = 1
              )
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
    $stmt_region->close();
}

$conn->close();
?>
