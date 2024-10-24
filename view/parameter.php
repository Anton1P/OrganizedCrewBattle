<?php 
include "../bddConnexion/bddConnexion.php";
include "../APIBrawlhalla/security.php";

$id_clan = $_SESSION['brawlhalla_data']['clan_id'];

// Requête pour obtenir les régions déjà sélectionnées pour le clan
$sql = "SELECT * FROM region WHERE id_clan = $id_clan";
$result = $conn->query($sql);

// Initialisation des variables pour chaque région
$regions = [
    'us_e' => 0,
    'eu' => 0,
    'sea' => 0,
    'brz' => 0,
    'aus' => 0,
    'us_w' => 0,
    'jpn' => 0,
    'sa' => 0,
    'me' => 0
];

if ($result->num_rows > 0) {
    // Extraire les résultats
    $row = $result->fetch_assoc();
    foreach ($regions as $region => $value) {
        $regions[$region] = $row[$region]; // Assigner la valeur récupérée pour chaque région
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parameter Management</title>
    <link rel="stylesheet" href="../assets/styles/tournoiReport.css"> <!-- Votre fichier CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<div class="container">
    <h1>Parameter Management</h1>
        <div style="display:flex; justify-content: space-between;">
             <button class="button-home" onclick="window.location.href='AdminPanel.php';">
                <i class="fas fa-home"></i>
            </button>
            <button class="button-refresh" onclick="location.reload();">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    <?php
        if (isset($_SESSION['notification'])) {
            echo '<div class="notification">' . $_SESSION['notification'] . '</div>';
            unset($_SESSION['notification']); // Remove the notification after displaying it
        }
    ?>
    <form action="parameter.php" method="POST">
        <div class="region-container">
            <span >Select region for matchmaking :</span>
            <div class="region-header" onclick="toggleDropdown()">
                <h3>Region</h3>
                <i class="arrow"></i>
            </div>
            <div id="dropdown" class="dropdown-content">
                <label>
                    <input type="checkbox" name="region[us_e]" value="1" <?php if ($regions['us_e']) echo "checked"; ?>> US-East
                </label><br>
                <label>
                    <input type="checkbox" name="region[eu]" value="1" <?php if ($regions['eu']) echo "checked"; ?>> Europe
                </label><br>
                <label>
                    <input type="checkbox" name="region[sea]" value="1" <?php if ($regions['sea']) echo "checked"; ?>> Southeast Asia
                </label><br>
                <label>
                    <input type="checkbox" name="region[brz]" value="1" <?php if ($regions['brz']) echo "checked"; ?>> Brazil
                </label><br>
                <label>
                    <input type="checkbox" name="region[aus]" value="1" <?php if ($regions['aus']) echo "checked"; ?>> Australia
                </label><br>
                <label>
                    <input type="checkbox" name="region[us_w]" value="1" <?php if ($regions['us_w']) echo "checked"; ?>> US-West
                </label><br>
                <label>
                    <input type="checkbox" name="region[jpn]" value="1" <?php if ($regions['jpn']) echo "checked"; ?>> Japan
                </label><br>
                <label>
                    <input type="checkbox" name="region[sa]" value="1" <?php if ($regions['sa']) echo "checked"; ?>> South America
                </label><br>
                <label>
                    <input type="checkbox" name="region[me]" value="1" <?php if ($regions['me']) echo "checked"; ?>> Middle East
                </label><br>
            </div>
        </div>
        <button type="submit" class="checkin-button">Submit</button>
    </form>
</div>

<script>
    function toggleDropdown() {
        var dropdown = document.getElementById("dropdown");
        var arrow = document.querySelector(".arrow");
        dropdown.classList.toggle("show");
        arrow.classList.toggle("rotate");
    }
</script>

</body>
</html>

<?php
// Sauvegarde des paramètres si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifiez si des données existent pour l'ID du clan
    $check_query = "SELECT * FROM region WHERE id_clan = $id_clan";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        // Si des données existent, nous mettons à jour
        $update_query = "UPDATE region SET ";
        foreach ($regions as $region => $value) {
            $checked = isset($_POST['region'][$region]) ? 1 : 0;
            $update_query .= "$region = $checked, ";
        }
        // Retirer la dernière virgule
        $update_query = rtrim($update_query, ', ');
        $update_query .= " WHERE id_clan = $id_clan";

        if ($conn->query($update_query) === TRUE) {
            $_SESSION['notification'] = "Settings updated successfully!";
            header('Location: ../view/AdminPanel.php');
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        // Si aucune donnée n'existe, nous insérons
        $insert_query = "INSERT INTO region (id_clan, us_e, eu, sea, brz, aus, us_w, jpn, sa, me) VALUES ($id_clan, ";
        foreach ($regions as $region => $value) {
            $checked = isset($_POST['region'][$region]) ? 1 : 0;
            $insert_query .= "$checked, ";
        }
        // Retirer la dernière virgule et compléter la requête
        $insert_query = rtrim($insert_query, ', ') . ")";

        if ($conn->query($insert_query) === TRUE) {
            $_SESSION['notification'] = "Settings inserted successfully!";
            header('Location: ../view/AdminPanel.php');
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>