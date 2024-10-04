<?php
session_start();
include "../bddConnexion/bddConnexion.php";

if (!isset($_SESSION['userData']['steam_id'])) {
    header("Location: ../APIBrawlhalla/routes.php");
    exit();
}

$steam_id = $_SESSION['userData']['steam_id'];

// Vérification si l'utilisateur est autorisé à accéder à la page
$query = "SELECT steam_id FROM moderation_access WHERE steam_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $steam_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../APIBrawlhalla/routes.php");
    exit();
}

// Affichage de la liste des dossiers dans assets/images
$dir = "../assets/images/";
$folders = scandir($dir);

echo "<h1>Modération - Liste des tournois à report</h1>";
echo "<ul>";

foreach ($folders as $folder) {
    if ($folder != "." && $folder != ".." && is_dir($dir . $folder)) {
        echo "<li><a href='../bddConnexion/moderation_view.php?id_tournoi=" . htmlspecialchars($folder) . "'>Tournoi ID : " . htmlspecialchars($folder) . "</a></li>";
    }
}

echo "</ul>";

?>
