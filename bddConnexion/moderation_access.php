<?php
session_start();
include "../bddConnexion/bddConnexion.php";

if (!isset($_SESSION['userData']['steam_id'])) {
    header("Location: ../APIBrawlhalla/routes.php");
    exit();
}

$steam_id = $_SESSION['userData']['steam_id'];

// Check if the user is authorized to access the page
$query = "SELECT steam_id FROM moderation_access WHERE steam_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $steam_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../APIBrawlhalla/routes.php");
    exit();
}

// Display the list of folders in assets/images
$dir = "../assets/images/";
$folders = scandir($dir);

echo "<h1>Moderation - List of Tournaments to Report</h1>";
echo "<ul>";

foreach ($folders as $folder) {
    if ($folder != "." && $folder != ".." && is_dir($dir . $folder)) {
        echo "<li><a href='../bddConnexion/moderation_view.php?id_tournoi=" . htmlspecialchars($folder) . "'>Tournament ID: " . htmlspecialchars($folder) . "</a></li>";
    }
}

echo "</ul>";

?>
