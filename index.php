<?php 
session_start();

if(isset($_SESSION['userData']['name'])){
    header("Location: view/AdminPanel.php");
}
else{
    header("Location: steamConnexion/index.php");
}




?>