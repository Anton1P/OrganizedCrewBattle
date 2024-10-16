<?php  
session_start();
include "../APIBrawlhalla/setup.php";
include "../bddConnexion/bddConnexion.php";
include "../bddConnexion/loadData.php";
?>

<!DOCTYPE html>
<html lang="en">
     <head>
          <meta charset="UTF-8" />
          <title>Ranked CrewBattle - Admin Panel</title>
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css" />
          <link rel="stylesheet" href="../assets/styles/style.css" />
               <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                    <div class="header ">
                         <div class="logo">
                              <a href="../APIBrawlhalla/routes.php"><img style="height: 80px;" src="../assets/img/mini-logo-2.png" alt=""></a>
                         </div>
                         <a class="header-link header-link-member active" href="../APIBrawlhalla/routes.php">
                              <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#ffffff">
                                   <path d="M10 13a2 2 0 110-4 2 2 0 010 4zm0-2.5a.5.5 0 100 1 .5.5 0 000-1z" />
                                   <path d="M20.3 11.8h-8.8a.8.8 0 010-1.6h8.8a.8.8 0 010 1.6zM8.5 11.8H3.7a.8.8 0 010-1.6h4.8a.8.8 0 010 1.6zM15 19a2 2 0 110-4 2 2 0 010 4zm0-2.5a.5.5 0 100 1 .5.5 0 000-1z" />
                                   <path d="M20.3 17.8h-3.8a.8.8 0 010-1.6h3.8a.8.8 0 010 1.6zM13.5 17.8H3.7a.8.8 0 010-1.6h9.8a.8.8 0 010 1.6z" />
                                   <path
                                        d="M21.3 23H2.6A2.8 2.8 0 010 20.2V3.9C0 2.1 1.2 1 2.8 1h18.4C22.9 1 24 2.2 24 3.8v16.4c0 1.6-1.2 2.8-2.8 2.8zM2.6 2.5c-.6 0-1.2.6-1.2 1.3v16.4c0 .7.6 1.3 1.3 1.3h18.4c.7 0 1.3-.6 1.3-1.3V3.9c0-.7-.6-1.3-1.3-1.3z"
                                   />
                                   <path d="M23.3 6H.6a.8.8 0 010-1.5h22.6a.8.8 0 010 1.5z" />
                              </svg>
                         <?php echo $rank;?> Panel
                         </a>
                         <a class="header-link header-link-member" href="Leaderboard.php">
                              <svg fill="#ffffff"  version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 489.4 489.4" xml:space="preserve">
                                   <g>
                                        <path d="M369.75,0h-250.2v44.3h-85.6V110c0,47.2,38.4,85.6,85.6,85.6h1.5c7.9,51.3,47,92.2,97.2,103v70.9h-30.7
                                             c-9.5,0-17.1,7.7-17.1,17.1v22.5h-26.2v80.3h200.9v-80.3h-26.2v-22.5c0-9.5-7.7-17.1-17.1-17.1h-30.7v-70.9
                                             c50.3-10.8,89.3-51.8,97.2-103h1.5c47.2,0,85.6-38.4,85.6-85.6V44.3h-85.6V0H369.75z M119.55,152.3c-23.3,0-42.3-19-42.3-42.3V87.6
                                             h42.3V152.3z M301.45,121.7l-25.7,21.7l8,32.7c1.5,6.1-5.2,11-10.6,7.7l-28.5-17.8l-28.6,17.7c-5.4,3.3-12.1-1.5-10.6-7.7l8-32.7
                                             l-25.6-21.6c-4.8-4.1-2.3-12,4-12.4l33.5-2.4l12.8-31.2c2.4-5.9,10.7-5.9,13.1,0l12.7,31.1l33.5,2.4
                                             C303.75,109.7,306.25,117.6,301.45,121.7z M411.95,87.6V110c0,23.3-18.9,42.3-42.2,42.3V87.6H411.95z"/>
                                   </g>
                              </svg>
                              Classement
                         </a>
                         <a class="header-link header-link-member" href="../view/documentation.html">
                                   <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="20" height="20">
                                        <rect x="8" y="4" width="40" height="56" fill="none" stroke="WHITE" stroke-width="2"/>
                                        <polyline points="8,4 32,4 48,20 48,60 8,60" fill="none" stroke="WHITE" stroke-width="2"/>
                                        <line x1="16" y1="12" x2="32" y2="12" stroke="WHITE" stroke-width="2"/>
                                        <line x1="16" y1="20" x2="40" y2="20" stroke="WHITE" stroke-width="2"/>
                                        <line x1="16" y1="28" x2="40" y2="28" stroke="WHITE" stroke-width="2"/>
                                        <line x1="16" y1="36" x2="40" y2="36" stroke="WHITE" stroke-width="2"/>
                                        <line x1="16" y1="44" x2="40" y2="44" stroke="WHITE" stroke-width="2"/>
                                   </svg>
                                   Documentation
                         </a>
                    </div>
                  
                    <div class="user-box first-box first-box-member">
                         <div class="account-wrapper member-profile" style="--delay: 0.2s;">
                              <div class="account-profile ">
                                   <img src="<?php echo $avatar;?>" alt="" />

                                   <div class="account-name"> <?php echo $name; ?>  </div>
                                   <div class="account-title"><?php echo $clan_name." ".$rank;?></div>
                              </div>
                         </div>
                       
                    </div>
                    <div class="user-box second-box">
                         <div class="cards-wrapper" style="--delay: 1s;">
                              <div class="cards-header" style="padding: 10px 20px;">
                                   <div class="cards-header-date" >
                                       <h3>Clan Asakai</h3> 
                                   </div>
                              </div>
                              <div class="cards card">
                                   <div id="div-myChart" >
                                        
                                        <div id="table-container">
                                             <table id="clan-members">
                                                  <thead>
                                                       <tr>
                                                            <th class="title-th" data-sort="name">Pseudo</th>
                                                            <th class="title-th" data-sort="rank">Rank</th>
                                                            <th class="title-th" data-sort="xp">XP</th>
                                                            <th class="title-th" data-sort="join_date">Date d'arrivée</th>
                                                       </tr>
                                                  </thead>
                                                  <tbody>
                                                       <!-- Les membres seront chargés ici via AJAX -->
                                                  </tbody>
                                             </table>
                                             <div id="pagination">
                                                  <svg id="prev"  onclick="loadMembers(currentPage - 1)" class="pagination-svg" aria-label="Page précédente" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" viewBox="0 0 24 24" style="cursor: pointer;">
                                                       <path d="M15 18l-6-6 6-6v12z"/>
                                                  </svg>
                                                  <span id="page-info">Page 1</span>
                                                  <svg id="next"  onclick="loadMembers(currentPage + 1)"  class="pagination-svg" aria-label="Page suivante" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" viewBox="0 0 24 24" style="cursor: pointer;">
                                                       <path d="M9 6l6 6-6 6V6z"/>
                                                  </svg>
                                             </div>
                                        </div>

                                        <div style="max-width: 350px;">
                                             <canvas id="myChart" width="350" height="350"></canvas> <!-- Réduction de la taille du canvas -->
                                        </div>
                                   </div>      
                              </div>
                         </div>

                         <div class="card transection" style="--delay: 1.2s;">
                            <?php include "../view/info_tournois.php";?>          
                         </div>
                         
                    </div>
                    <div class="card transection hey hey-member" style="display:none;">
                            <?php include "../view/info_tournois.php";?>
                         </div>
               </div>
          </div>
     </body>
</html>

<style>
    .user-box {
        justify-content: center;
}
</style>



<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script src="../assets/script/script.js"></script>

<?php include "../bddConnexion/data_clan.php";?>

<script>
const ctx = document.getElementById('myChart').getContext('2d');

new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: [
      'Wins',
      'Loses'
    ],
    datasets: [{
      data: [<?php echo json_encode($data["wins"]); ?>, <?php echo json_encode($data["loses"]); ?>],
      backgroundColor: [
        '#325dd9',
        '#c32c1d'
      ],
      hoverOffset: 4
    }]
  },
  options: {
    responsive: false,
    maintainAspectRatio: false, // Maintient la taille définie
    cutout: '70%', // Contrôle l'épaisseur du cercle (plus la valeur est grande, plus le trou est large)
    plugins: {
      legend: {
        display: true 
      },
      datalabels: {
        formatter: (value, context) => {
          // Calculer le pourcentage
          let total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
          let percentage = (value / total * 100).toFixed(2) + '%';
          return percentage; // Retourner le pourcentage à afficher
        },
        color: '#fff', // Couleur des labels
        font: {
          weight: 'bold',
          size: 12
        }
      }
    }
  },
  plugins: [ChartDataLabels] // Active le plugin de labels
});

</script>
