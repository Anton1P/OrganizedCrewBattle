<?php
     if (isset($_SESSION['notification']) && !empty($_SESSION['notification'])) {
     echo "<li>" . $_SESSION['notification'] . "</li>";
     } else {
     echo "<li>No new notifications</li>"; // Translated text
     }
?>
