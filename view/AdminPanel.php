<?php   
include "../APIBrawlhalla/security.php";
include "../bddConnexion/bddConnexion.php";
include "../bddConnexion/loadData.php";
?>

<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="../styles/output.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="flex items-center justify-center h-screen bg-steam-lightGray text-white flex-col">
        <div class="text-2xl">Welcome <?php echo $clan_name." ".$rank;?> </div>
        <div class="text-4xl mt-3 flex items-center font-medium">
            <img src='<?php echo $avatar;?>' class="rounded-full w-12 h-12 mr-3"/>
            <?php echo $name; ?>   
        </div>
        <a href="../steamConnexion/logout.php" class="text-sm mt-5">Logout</a>
    </div>
        
    <?php include "../bddConnexion/researchNotifications.php";?><br>
    
     <?php include "../bddConnexion/notifs.php";?><br>
     <?php include "../bddConnexion/traitement_tournoiUpdate.php"; ?><br>
     <?php include "tableauMembresClan.php";?> <br>    
   
    <a href="http://localhost/OrganizedCrewBattle/view/askForm.php">askForm</a>

    <?php
      include "../bddConnexion/bddConnexion.php";
    if (isset($_SESSION['userData']['steam_id'])) {
        $steam_id = $_SESSION['userData']['steam_id'];
        
        $query = "SELECT steam_id FROM moderation_access WHERE steam_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $steam_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<a href='../bddConnexion/moderation_access.php'>Accéder à la modération</a>";
        }
    }   
    
    ?>
 



</body>
</html>
