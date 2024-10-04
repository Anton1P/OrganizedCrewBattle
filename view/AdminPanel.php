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

        
    <?php include "../bddConnexion/researchNotifications.php";?><br>
    
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
<!DOCTYPE html>
<html lang="en">
     <head>
          <meta charset="UTF-8" />
          <title>CodePen - juif Management UI</title>
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css" />
          <link rel="stylesheet" href="../assets/styles/style.css" />
     </head>
     <body>
          <div class="wrapper">
               <div class="left-side">
                <a href="../steamConnexion/logout.php" >
                    <svg class="svg-icon" style="width: 1.5em; height: 1.5em; vertical-align: middle; fill: currentColor; overflow: hidden;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                <path d="M768 106V184c97.2 76 160 194.8 160 328 0 229.6-186.4 416-416 416S96 741.6 96 512c0-133.2 62.8-251.6 160-328V106C121.6 190.8 32 341.2 32 512c0 265.2 214.8 480 480 480s480-214.8 480-480c0-170.8-89.6-321.2-224-406z" fill="" />
                                <path d="M512 32c-17.6 0-32 14.4-32 32v448c0 17.6 14.4 32 32 32s32-14.4 32-32V64c0-17.6-14.4-32-32-32z" fill="" />
                            </svg>
                    </a>
               </div>
               <div class="main-container">
                    <div class="header">
                         <div class="logo">LOGO</div>
                         <a class="header-link active" href="#">
                              <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                                   <path d="M10 13a2 2 0 110-4 2 2 0 010 4zm0-2.5a.5.5 0 100 1 .5.5 0 000-1z" />
                                   <path d="M20.3 11.8h-8.8a.8.8 0 010-1.6h8.8a.8.8 0 010 1.6zM8.5 11.8H3.7a.8.8 0 010-1.6h4.8a.8.8 0 010 1.6zM15 19a2 2 0 110-4 2 2 0 010 4zm0-2.5a.5.5 0 100 1 .5.5 0 000-1z" />
                                   <path d="M20.3 17.8h-3.8a.8.8 0 010-1.6h3.8a.8.8 0 010 1.6zM13.5 17.8H3.7a.8.8 0 010-1.6h9.8a.8.8 0 010 1.6z" />
                                   <path
                                        d="M21.3 23H2.6A2.8 2.8 0 010 20.2V3.9C0 2.1 1.2 1 2.8 1h18.4C22.9 1 24 2.2 24 3.8v16.4c0 1.6-1.2 2.8-2.8 2.8zM2.6 2.5c-.6 0-1.2.6-1.2 1.3v16.4c0 .7.6 1.3 1.3 1.3h18.4c.7 0 1.3-.6 1.3-1.3V3.9c0-.7-.6-1.3-1.3-1.3z"
                                   />
                                   <path d="M23.3 6H.6a.8.8 0 010-1.5h22.6a.8.8 0 010 1.5z" />
                              </svg>
                              Admin Panel
                         </a>
                         <a class="header-link" href="#">
                              <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                                   <path
                                        d="M500 113.3C326.1 78.7 337.4 80.5 333.4 81.2L281 91.7A111.2 111.2 0 00176 17c-48.6 0-90 31.3-105 74.8L18 81.3A15 15 0 000 96v352a15 15 0 0012 14.7l162.2 32.2c3.6.4-7.6 2.3 161.8-31.6l158 31.4a15 15 0 0018-14.7V128a15 15 0 00-12-14.7zM176 47a81 81 0 0181 81c0 37.7-60.3 133.3-81 165-20.7-31.6-81-127.3-81-165a81 81 0 0181-81zM30 114.2l35.2 7a112 112 0 00-.2 6.8c0 25 16.4 65.4 50 123.4 19.7 33.9 39 63 46 73.2v137.1l-131-26zm161 210.4c7-10.2 26.3-39.3 46-73.2 33.6-58 50-98.4 50-123.4 0-2.3 0-4.6-.2-6.9l34.2-6.8v321.4l-130 26zm291 137.1l-131-26V114.3l131 26z"
                                   />
                                   <path d="M176 175a47 47 0 10-.1-94.1 47 47 0 00.1 94zm0-64a17 17 0 110 34 17 17 0 010-34z" />
                              </svg>
                              Classement
                         </a>
                    </div>
                    <div class="user-box first-box">

                         <div class="account-wrapper" style="--delay: 0.2s;">
                              <div class="account-profile">
                                   <img src="<?php echo $avatar;?>" alt="" />

                                   <div class="account-name"> <?php echo $name; ?>  </div>
                                   <div class="account-title"><?php echo $clan_name." ".$rank;?></div>
                              </div>
                         </div>
                         <?php include "../bddConnexion/notifs.php";?>

                        
                         <div class="activity card" style="--delay: 0.5s;">
                              <h3>Un tournoi est disponible !</h3>
                              <p>Temps jusqu'à la rencontre : +10:34</p>
                              <button class="rounded-button">Check-in</button>
                         </div>
                    </div>
                    <div class="user-box second-box">
                         <div class="cards-wrapper" style="--delay: 1s;">
                              <div class="cards-header">
                                   <div class="cards-header-date">
                                        <div class="title">Clan Asakai</div>   
                                   </div>
                              </div>
                              <div class="cards card">
                                 Image avec fleche pour indiqué le procédé 
                              </div>
                         </div>

                         <div class="card transection" style="--delay: 1.2s;">
                         
                              <h3>Asakai Vs lol </h3>
                              Battle today at 22h50 UTC+2 <br>
                              Format : Crew Battle 1  <br>
                              Brawlhalla room : #0  <br>
                           
                              <h3>Asakai Vs Annoying </h3>
                              Battle today at 22h50 UTC+2 <br>
                              Format : Crew Battle 1  <br>
                              Brawlhalla room : #0  <br>

                              
                         </div>
                         
                    </div>
               </div>
               <!-- partial -->
               <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
               <script src="./assets/script/script.js"></script>
          </div>
     </body>
</html>
