<?php

if (isset($_SESSION['notification'])) {
    echo '<div class="notification">' . $_SESSION['notification'] . '</div>';
    unset($_SESSION['notification']); 
    }
    
    ?>