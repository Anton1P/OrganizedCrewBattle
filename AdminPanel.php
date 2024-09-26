<?php   
    session_start();

     include "APIBrawlhalla/setup.php";

     echo $name;
     echo $brawlhalla_id;
     echo $clan_name;
     echo $clan_id;

     include "./bddConnexion/bddConnexion.php";
     include "./bddConnexion/loadData.php";

?>

<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="./styles/output.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="flex items-center justify-center h-screen bg-steam-lightGray text-white flex-col">
        <div class="text-2xl">Welcome Asakai <?php echo $rank;?> </div>
        <div class="text-4xl mt-3 flex items-center font-medium">
            <img src='<?php echo $avatar;?>' class="rounded-full w-12 h-12 mr-3"/>
            <?php echo $name;?>   
        </div>
        <a href="logout.php" class="text-sm mt-5">Logout</a>
    </div>
        
    <?php 
     include "./bddConnexion/notifs.php";
    ?>

            <?php
                    // Fonction pour formater la date depuis un timestamp Unix
                    function formaterDate($timestamp) {
                        return date("d-m-Y", $timestamp);
                    }

                    // Début de la table HTML
                    echo "<table border='1' cellpadding='10' cellspacing='0'>";
                    echo "<thead><tr>";

                    // Affichage des en-têtes (les clés du premier élément)
                    foreach(array_keys($clan_members[0]) as $entete) {
                        echo "<th>" . htmlspecialchars($entete) . "</th>";
                    }
                    echo "</tr></thead>";

                    // Affichage des lignes du tableau
                    echo "<tbody>";
                    foreach($clan_members as $ligne) {
                        echo "<tr>";
                        foreach($ligne as $cle => $valeur) {
                            // Si la clé est 'join_date', formater la date
                            if ($cle === 'join_date') {
                                echo "<td>" . htmlspecialchars(formaterDate($valeur)) . "</td>";
                            } else {
                                echo "<td>" . htmlspecialchars($valeur) . "</td>";
                            }
                        }
                        echo "</tr>";

                    }
                    echo "</tbody>";
                    echo "</table>";

                    
                ?>
   
                    <a href="http://localhost/OrganizedCrewBattle/askForm.php">askForm</a>
 



</body>
</html>
