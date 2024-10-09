<?php
include "../bddConnexion/bddConnexion.php";
include "../APIBrawlhalla/security.php";

// Définir le nombre de clans par page
$clans_per_page = 25;

// Vérifier si la page actuelle est spécifiée dans l'URL, sinon par défaut à 1
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculer l'offset pour la requête SQL
$offset = ($current_page - 1) * $clans_per_page;

// Récupérer le total des clans pour la pagination
$total_query = "SELECT COUNT(*) as total FROM clans";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_clans = $total_row['total'];

// Récupérer les clans triés par ELO
$query = "SELECT nom_clan, id_clan, elo_rating, elo_peak, wins, loses FROM clans ORDER BY elo_rating DESC LIMIT $clans_per_page OFFSET $offset";
$result = $conn->query($query);
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
            <a href="../steamConnexion/logout.php">
                <svg class="svg-icon" style="width: 1.5em; height: 1.5em; vertical-align: middle; fill: currentColor; overflow: hidden;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg">
                    <path d="M768 106V184c97.2 76 160 194.8 160 328 0 229.6-186.4 416-416 416S96 741.6 96 512c0-133.2 62.8-251.6 160-328V106C121.6 190.8 32 341.2 32 512c0 265.2 214.8 480 480 480s480-214.8 480-480c0-170.8-89.6-321.2-224-406z" fill="" />
                    <path d="M512 32c-17.6 0-32 14.4-32 32v448c0 17.6 14.4 32 32 32s32-14.4 32-32V64c0-17.6-14.4-32-32-32z" fill="" />
                </svg>
            </a>
        </div>

        <div class="main-container" >
            <div class="header">
                <div class="logo">
                    <a href="AdminPanel.php"><img style="height: 80px;" src="../assets/img/mini-logo-2.png" alt=""></a>
                </div>
                <a class="header-link" href="AdminPanel.php">
                              <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#ffffff">
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
                         <a class="header-link active" href="Leaderboard.php">
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
                         <?php
                         if ( $rank === 'Leader' || $rank  === 'Officer') {
                            echo' <a class="header-link" href="http://localhost/OrganizedCrewBattle/view/askForm.php">
                              <svg viewBox="0 0 24 24" fill="#ffffff" xmlns="http://www.w3.org/2000/svg">
                                   <path d="M22 12C22 15.7712 22 17.6569 20.8284 18.8284C19.6569 20 17.7712 20 14 20H10C6.22876 20 4.34315 20 3.17157 18.8284C2 17.6569 2 15.7712 2 12C2 8.22876 2 6.34315 3.17157 5.17157C4.34315 4 6.22876 4 10 4H14C17.7712 4 19.6569 4 20.8284 5.17157C21.4816 5.82475 21.7706 6.69989 21.8985 8" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
                                   <path d="M18 8L15.8411 9.79908C14.0045 11.3296 13.0861 12.0949 12 12.0949C11.3507 12.0949 10.7614 11.8214 10 11.2744M6 8L6.9 8.75L7.8 9.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
                              </svg>
                              Ask for a clan battle
                         </a>';
                         }
                         ?>
                         <div class="notification-wrapper">
                              <div class="notification-icon" id="notificationIcon">
                                   <!-- Icône de cloche blanche en SVG -->
                                   <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32">
                                        <path d="M12 2C10.9 2 10 2.9 10 4C10 5.1 10.9 6 12 6C13.1 6 14 5.1 14 4C14 2.9 13.1 2 12 2zM18 8V11C18 12.5 18.8 13.8 20 14.5V17H4V14.5C5.2 13.8 6 12.5 6 11V8C6 5.2 8.2 3 11 3H13C15.8 3 18 5.2 18 8ZM12 22C13.1 22 14 21.1 14 20H10C10 21.1 10.9 22 12 22Z" />
                                   </svg>
                                   <div class="ping" id="notificationPing">1</div>
                              </div>

                              <div class="notification-list" id="notificationList">
                                   <ul>
                                        <!-- Les notifications seront générées ici -->
                                        <?php include "../bddConnexion/researchNotifications.php";?>
                                   </ul>
                              </div>
                         </div>

            </div>

            <!-- Classement des Clans -->
            <h1>Leaderboard des Clans</h1>
            <div class="pagination-controls" >
                <?php
                $total_pages = ceil($total_clans / $clans_per_page);
                
                // Bouton précédent
                if ($current_page > 1): ?>
                    <button onclick="location.href='Leaderboard.php?page=<?php echo $current_page - 1; ?>'">Précédent</button>
                <?php endif; ?>
                
                <!-- Boutons de pagination pour chaque page -->
                <?php for ($page = 1; $page <= $total_pages; $page++): ?>
                    <button 
                        onclick="location.href='Leaderboard.php?page=<?php echo $page; ?>'" 
                        <?php if ($page == $current_page) echo 'class="active-page"'; ?>>
                        <?php echo $page; ?>
                    </button>
                <?php endfor; ?>
                
                <!-- Bouton suivant -->
                <?php if ($current_page < $total_pages): ?>
                    <button onclick="location.href='Leaderboard.php?page=<?php echo $current_page + 1; ?>'">Suivant</button>
                <?php endif; ?>
            </div>

            
            <!-- Tableau des clans -->
            <table border="1">
                <tr>
                    <th>Rang</th>
                    <th>Tier</th> 
                    <th>Nom du Clan</th>
                    <th>Games</th>
                    <th>W/L</th>
                    <th>Winrate</th>
                    <th>Elo</th>
                    <th>Peak elo</th>
                </tr>
                <?php if ($result->num_rows > 0): ?>
                    <?php $rank = $offset + 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                            include '../bddConnexion/calcul_leaderboard.php';
                        ?>
                        <tr>
                            <td><?php echo $rank; ?></td>
                            <td><img src="<?php echo $tier_icon; ?>" alt="Tier Icon"></td> <!-- Affichage de l'image du tier -->
                            <td><a target="_blank" href="https://corehalla.com/stats/clan/<?php echo $row['id_clan']; ?>"><?php echo htmlspecialchars($row['nom_clan']); ?></a></td>
                            <td><?php echo $games_played; ?></td>
                            <td>
                                <?php echo $row['wins'] . ' - ' . $row['loses']; ?>
                                <div class="progress-bar">
                                    <div class="progress-win" style="width: <?php echo $winrate; ?>%;"></div>
                                    <div class="progress-lose" style="width: <?php echo (100 - $winrate); ?>%;"></div>
                                </div>
                            </td>
                            <td class="winrate-cell"><?php echo number_format($winrate, 2); ?>%</td> <!-- Ajouter la classe pour le winrate -->
                            <td style="color:#ffffff;" ><strong><?php echo $row['elo_rating']; ?></strong></td>
                            <td style="color:#ffffff;" ><?php echo $row['elo_peak']; ?></td>
                        </tr>
                        <?php $rank++; ?>
                    <?php endwhile; ?>
                <?php endif; ?>
            </table>
           
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>



<style>

.main-container {

    align-items: center;
}
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.table-container {
    width: 90%;
    margin: 80px auto;
    position: relative;
}


.pagination-controls button {
    padding: 10px 20px;
    margin: 0 5px;
    background-color: #333;
    color: #fff;
    border: none;
    cursor: pointer;
}

.pagination-controls button:hover {
    background-color: #444;
}

table {
    width: 90%;
    border-collapse: collapse;
    background-color: #131630;
}

thead {
    background-color: #292929;
}

th, td {
    padding: 4px;
    text-align: left;
}

td {
    border-bottom: 1px solid #333;
}

td img {
    width: 30px;
    height: 30px;
}

tbody tr:nth-child(even) {
    background-color: #191d43;
}

tbody tr:hover {
    background-color: #333;
}


        /* Style pour la barre de progression */
        .progress-bar {
            width: 100%;
            background-color: #f5f5f5;
            border-radius: 5px;
            overflow: hidden;
            height: 5px;
            margin-top: 5px;
            background-color: #ed7853;
        }
.progress-bar div {
            height: 100%;
            
        }
.progress-win {
            background-color: #24cd7c;
        }
.pagination-controls {
    text-align: center;
    margin: 20px 0;
}

.pagination-controls button {
    padding: 10px 15px;
    margin: 0 5px;
    background-color: #333;
    color: #fff;
    border: none;
    cursor: pointer;
    border-radius: 5px;
}

.pagination-controls button:hover {
    background-color: #444;
}

.pagination-controls button.active-page {
    background-color: #24cd7c; /* Couleur pour la page active */
    font-weight: bold;
}

</style>

<script>
    const notificationIcon = document.getElementById('notificationIcon');
    const notificationList = document.getElementById('notificationList');
    const notificationPing = document.getElementById('notificationPing');

    // Afficher/Masquer la liste des notifications
    notificationIcon.addEventListener('click', () => {
        if (notificationList.style.display === 'none' || notificationList.style.display === '') {
            notificationList.style.display = 'block';
        } else {
            notificationList.style.display = 'none';
        }
    });

    // Afficher la pastille rouge si il y a des notifications
    <?php if (!empty($_SESSION['notification'])): ?>
        notificationPing.style.display = 'block';
    <?php else: ?>
        notificationPing.style.display = 'none';
    <?php endif; ?>

    // Supprimer la notification lorsque l'utilisateur clique dessus
    document.querySelectorAll('#notificationIcon').forEach(notification => {
        notification.addEventListener('click', () => {
            fetch('../bddConnexion/clear_notification.php') // Appelle le script pour supprimer la notification
                .then(response => {
                    if (response.ok) {
                        notificationPing.style.display = 'none'; // Cache la pastille rouge
                    } else {
                        console.error('Erreur lors de la suppression de la notification');
                    }
                })
                .catch(error => {
                    console.error('Erreur :', error);
                });
        });
    });
</script>
<script>
        document.querySelectorAll('.winrate-cell').forEach(function(cell) {
            const winrate = parseFloat(cell.textContent); // Obtenir le winrate sous forme de nombre
            const green = Math.min(255, Math.floor((winrate / 100) * 255)); // Plus le winrate est élevé, plus il y a de vert
            const red = 255 - green; // L'inverse pour rendre les faibles winrates plus rouges
            cell.style.color = `rgb(${red}, ${green}, 0)`; // Appliquer la couleur calculée
        });
    </script>