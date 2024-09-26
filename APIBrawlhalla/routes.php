<?php
session_start();
if(!$_SESSION['logged_in']){
    header("location: ../error.php");
    exit();
}

    include "setup.php";

    if($isAdmin){

        header("Location: ../AdminPanel.php"); //vue des Leader
        exit;

    }
    else{

        header("Location: ../AdminPanel.php"); //vue des membre  =    header("Location: ../MemberPanel.php"); 
        exit;

    }

