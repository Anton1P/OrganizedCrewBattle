<?php
$servername = "localhost"; // ou l'adresse de ton serveur de base de données
$username = "root"; // ton nom d'utilisateur
$password = ""; // ton mot de passe
$dbname = "organizedcrewbattle"; // le nom de ta base de données

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}



?>