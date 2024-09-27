<?php
    session_start();
    include "../APIBrawlhalla/setup.php";
    if(!$isAdmin){
        header("Location: ../APIBrawlhalla/routes.php"); 
    }
    ?>