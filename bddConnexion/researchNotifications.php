<?php

if (isset($_SESSION['notification'])) {
    echo '<div style="background-color: #dff0d8; color: #3c763d; padding: 10px; border: 1px solid #d6e9c6; margin-bottom: 20px;" class="notification">' . $_SESSION['notification'] . '</div>';
    unset($_SESSION['notification']); 
    }
    
    ?>