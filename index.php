<?php 
session_start();

if(isset($_SESSION['brawlhalla_data'])){
    header("Location: view/AdminPanel.php");
}
else{
    header("Location: steamConnexion/index.php");
}




?>