<?php
// Démarrer la session pour accéder aux données du tournoi
include "../bddConnexion/bddConnexion.php";
session_start();

$tournoi_id = $_SESSION['tournoi_id']; 

// Vérifier si l'utilisateur vient de la page AdminPanel.php
if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'AdminPanel.php') !== false) {
    // Si la personne vient bien de AdminPanel.php
    if (isset($tournoi_id)) {
        echo "<h2>Détails du tournoi</h2>";
        echo "<p>Date de la rencontre : " . $_SESSION['date_rencontre'] . "</p>";
        echo "<p>Format : " . $_SESSION['format'] . "</p>";
        echo "<p>Clan Demandeur ID : " . $_SESSION['id_clan_demandeur'] . "</p>";
        echo "<p>Clan Receveur ID : " . $_SESSION['id_clan_receveur'] . "</p>";
    } else {
        echo "Aucun détail de tournoi disponible.";
    }

    // Incrémenter le champ on_page
    $sql = "UPDATE tournoi SET on_page = on_page + 1 WHERE id_tournoi = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tournoi_id);
    $stmt->execute();
    $stmt->close();
} else {
    // Si la personne ne vient pas de AdminPanel.php, rediriger vers la page AdminPanel.php
    header("Location: ../view/AdminPanel.php");
    exit();
}
?>

<script>
window.addEventListener("beforeunload", function () {
    // Faire une requête AJAX pour décrémenter la valeur de on_page
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../bddConnexion/decrement_on_page.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("tournoi_id=<?php echo $tournoi_id; ?>");
});
</script>
