<?php

include "bddConnexion.php";

$clan_id = $_SESSION['brawlhalla_data']['clan_id']; 

$sql_check = "SELECT * FROM tournoi WHERE id_clan_receveur = $clan_id";
$result = $conn->query($sql_check);

$tournois = []; // tableau pour stocker les tournois

if ($result->num_rows > 0) {
    $_SESSION['notification'] = "Vous avez des tournois en attente !";
    
    // Récupérer les tournois
    while ($row = $result->fetch_assoc()) {
        $tournois[] = $row; // ajouter chaque tournoi au tableau
    }
} 
?>

<head>
 
    <style>
        .tournament-list {
            display: none; /* cacher la liste par défaut */
            border: 1px solid #ccc;
            padding: 10px;
            margin-top: 10px;
        }
    </style>
    <script>
        function toggleTournamentList() {
            var tournamentList = document.getElementById('tournament-list');
            if (tournamentList.style.display === 'none') {
                tournamentList.style.display = 'block'; // afficher
            } else {
                tournamentList.style.display = 'none'; // masquer
            }
        }
    </script>
</head>
<body>
    <h1>Bienvenue</h1>

    <?php
    // Afficher la notification
    if (isset($_SESSION['notification'])) {
        echo "<div style='background-color: #dff0d8; color: #3c763d; padding: 10px; border: 1px solid #d6e9c6; margin-bottom: 20px;'>";
        echo $_SESSION['notification'];
        echo "</div>";

        unset($_SESSION['notification']); // Effacer la notification après affichage
    }
    ?>

    <button onclick="toggleTournamentList()">Show Tournois</button>

    <div id="tournament-list" class="tournament-list">
        <h2>Liste des Tournois</h2>
        <ul>
            <?php
            if (!empty($tournois)) {
                foreach ($tournois as $tournoi) {
                    echo "<li>Nom: " . htmlspecialchars($tournoi['id_clan_demandeur']) . ", Date: " . htmlspecialchars($tournoi['date_rencontre']) . ", Format: " . htmlspecialchars($tournoi['format']) . "</li>";
                }
            } else {
                echo "<li>Aucun tournoi trouvé.</li>";
            }
            ?>
        </ul>
    </div>
</body>
</html>
