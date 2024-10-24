<?php
session_start();
include '../bddConnexion/bddConnexion.php'; // Votre fichier de connexion MySQLi

// Obtenir l'ID du clan connecté (à ajuster selon votre logique)
$clan_id = $_SESSION['brawlhalla_data']['clan_id'];

// Fonction pour récupérer la région du clan connecté
function getClanRegion($conn, $clan_id) {
    $regions = [];
    $query = "SELECT us_e, eu, sea, brz, aus, us_w, jpn, sa, me FROM region WHERE id_clan = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $clan_id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            // Vérifie quelles régions sont sélectionnées
            foreach ($row as $key => $value) {
                if ($value == 1) {
                    $regions[] = $key; // Ajouter le nom de la région à la liste
                }
            }
        }
    }
    $stmt->close();
    
    return $regions;
}

// Fonction pour récupérer les suggestions de clans en fonction du critère
function getSuggestedClans($conn, $clan_id, $criteria = 'elo') {
    $clans = [];
    $regions = getClanRegion($conn, $clan_id); // Obtenir les régions du clan connecté
    $regionCondition = !empty($regions) ? "AND r." . implode(" = 1 OR r.", $regions) . " = 1" : ""; // Créer des conditions de région

    // Requêtes SQL selon le critère sélectionné
    switch ($criteria) {
        case 'elo':
            // Clans dans la même gamme d'Elo (+/- 100 Elo)
            $query = "SELECT c.id_clan, c.nom_clan, c.elo_rating 
                      FROM clans c
                      JOIN region r ON c.id_clan = r.id_clan
                      WHERE ABS(c.elo_rating - (SELECT elo_rating FROM clans WHERE id_clan = ?)) <= 100 
                      AND c.id_clan != ? $regionCondition
                      ORDER BY c.elo_rating DESC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ii', $clan_id, $clan_id);
            break;

        case 'elo_higher':
            // Clans avec environ 100 Elo de plus
            $query = "SELECT c.id_clan, c.nom_clan, c.elo_rating 
                      FROM clans c
                      JOIN region r ON c.id_clan = r.id_clan
                      WHERE c.elo_rating > (SELECT elo_rating FROM clans WHERE id_clan = ?) + 100
                      AND c.id_clan != ? $regionCondition
                      ORDER BY c.elo_rating DESC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ii', $clan_id, $clan_id);
            break;

        case 'elo_lower':
            // Clans avec environ 100 Elo de moins
            $query = "SELECT c.id_clan, c.nom_clan, c.elo_rating 
                      FROM clans c
                      JOIN region r ON c.id_clan = r.id_clan
                      WHERE c.elo_rating < (SELECT elo_rating FROM clans WHERE id_clan = ?) - 100
                      AND c.id_clan != ? $regionCondition
                      ORDER BY c.elo_rating DESC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ii', $clan_id, $clan_id);
            break;

        default:
            return [];
    }

    // Exécution de la requête pour les critères Elo
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $clans[] = $row;
        }
    }
    $stmt->close();
    
    // Mélanger aléatoirement les clans
    if (!empty($clans)) {
        shuffle($clans); // Mélange aléatoire de la liste des clans
    }

    // Limiter le nombre de résultats à 10 après randomisation
    $clans = array_slice($clans, 0, 5);

    // Retourner les clans mélangés et limités
    return $clans;
}

if (isset($_POST['criteria'])) {
    $criteria = $_POST['criteria'];
    $clans = getSuggestedClans($conn, $clan_id, $criteria);

    if (empty($clans)) {
        echo "None clan found.";
        exit; // Sortir si aucun clan trouvé
    }

    // Générer la liste pour les suggestions
    foreach ($clans as $clan) {
        echo "<li>{$clan['nom_clan']} (Elo: {$clan['elo_rating']})</li>";
    }
}

// Fermer la connexion à la base de données
$conn->close();
?>
