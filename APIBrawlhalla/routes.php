<?php
session_start();
if(!$_SESSION['logged_in']){
    header("location: ../steamConnexion/index.php");
    exit();
}

    include "setup.php";

    if($isAdmin){

        header("Location: ../view/AdminPanel.php"); //vue des Leader
        exit;

    }
    else{

        header("Location: ../view/MemberPanel.php"); //!vue des membre  =    header("Location: ../MemberPanel.php"); 
        exit;

    }

