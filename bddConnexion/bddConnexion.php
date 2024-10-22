<?php
$servername = "crewbasantonin.mysql.db"; // ou l'adresse de ton serveur de base de données
$username = "crewbasantonin"; // ton nom d'utilisateur
$password = "Organizedcrewbattle76"; // ton mot de passe
$dbname = "crewbasantonin"; // le nom de ta base de données

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

?>

