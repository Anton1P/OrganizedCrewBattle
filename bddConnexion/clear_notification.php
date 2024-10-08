<?php
session_start();
unset($_SESSION['notification']); // Supprime la notification de la session
header("Location: " . $_SERVER['HTTP_REFERER']); // Redirige vers la page précédente
exit();
?>
