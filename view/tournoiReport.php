<?php
// Démarrer la session pour accéder aux données du tournoi
session_start();

if (isset($_SESSION['tournoi_id'])) {
    echo "<h2>Détails du tournoi</h2>";
    echo "<p>Date de la rencontre : " . $_SESSION['date_rencontre'] . "</p>";
    echo "<p>Format : " . $_SESSION['format'] . "</p>";
    echo "<p>Clan Demandeur ID : " . $_SESSION['id_clan_demandeur'] . "</p>";
    echo "<p>Clan Receveur ID : " . $_SESSION['id_clan_receveur'] . "</p>";
} else {
    echo "Aucun détail de tournoi disponible.";
}
?>