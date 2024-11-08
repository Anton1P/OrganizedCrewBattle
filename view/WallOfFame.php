<?php 
session_start();
include "../bddConnexion/bddConnexion.php";
include "../APIBrawlhalla/setup.php";
include "../APIBrawlhalla/traductions.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8" />
        <title> Wall of Fame - Ranked CrewBattle</title>
        <link rel="icon" href="../assets/img/mini-logo-2.png" type="image/x-icon">
        <link rel="icon" href="../assets/img/mini-logo-2.png" type="image/x-icon">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="../assets/styles/style.css" />
        <link rel="stylesheet" href="../assets/styles/seasonWinner.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body>
        <div class="wrapper">
            <div class="left-side">
                <a href="../view/parameter.php" >
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" role="img" viewBox="0 0 54 54" height="54" width="54" xmlns="http://www.w3.org/2000/svg" ><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <path d="M51.22,21h-5.052c-0.812,0-1.481-0.447-1.792-1.197s-0.153-1.54,0.42-2.114l3.572-3.571 c0.525-0.525,0.814-1.224,0.814-1.966c0-0.743-0.289-1.441-0.814-1.967l-4.553-4.553c-1.05-1.05-2.881-1.052-3.933,0l-3.571,3.571 c-0.574,0.573-1.366,0.733-2.114,0.421C33.447,9.313,33,8.644,33,7.832V2.78C33,1.247,31.753,0,30.22,0H23.78 C22.247,0,21,1.247,21,2.78v5.052c0,0.812-0.447,1.481-1.197,1.792c-0.748,0.313-1.54,0.152-2.114-0.421l-3.571-3.571 c-1.052-1.052-2.883-1.05-3.933,0l-4.553,4.553c-0.525,0.525-0.814,1.224-0.814,1.967c0,0.742,0.289,1.44,0.814,1.966l3.572,3.571 c0.573,0.574,0.73,1.364,0.42,2.114S8.644,21,7.832,21H2.78C1.247,21,0,22.247,0,23.78v6.439C0,31.753,1.247,33,2.78,33h5.052 c0.812,0,1.481,0.447,1.792,1.197s0.153,1.54-0.42,2.114l-3.572,3.571c-0.525,0.525-0.814,1.224-0.814,1.966 c0,0.743,0.289,1.441,0.814,1.967l4.553,4.553c1.051,1.051,2.881,1.053,3.933,0l3.571-3.572c0.574-0.573,1.363-0.731,2.114-0.42 c0.75,0.311,1.197,0.98,1.197,1.792v5.052c0,1.533,1.247,2.78,2.78,2.78h6.439c1.533,0,2.78-1.247,2.78-2.78v-5.052 c0-0.812,0.447-1.481,1.197-1.792c0.751-0.312,1.54-0.153,2.114,0.42l3.571,3.572c1.052,1.052,2.883,1.05,3.933,0l4.553-4.553 c0.525-0.525,0.814-1.224,0.814-1.967c0-0.742-0.289-1.44-0.814-1.966l-3.572-3.571c-0.573-0.574-0.73-1.364-0.42-2.114 S45.356,33,46.168,33h5.052c1.533,0,2.78-1.247,2.78-2.78V23.78C54,22.247,52.753,21,51.22,21z M52,30.22 C52,30.65,51.65,31,51.22,31h-5.052c-1.624,0-3.019,0.932-3.64,2.432c-0.622,1.5-0.295,3.146,0.854,4.294l3.572,3.571 c0.305,0.305,0.305,0.8,0,1.104l-4.553,4.553c-0.304,0.304-0.799,0.306-1.104,0l-3.571-3.572c-1.149-1.149-2.794-1.474-4.294-0.854 c-1.5,0.621-2.432,2.016-2.432,3.64v5.052C31,51.65,30.65,52,30.22,52H23.78C23.35,52,23,51.65,23,51.22v-5.052 c0-1.624-0.932-3.019-2.432-3.64c-0.503-0.209-1.021-0.311-1.533-0.311c-1.014,0-1.997,0.4-2.761,1.164l-3.571,3.572 c-0.306,0.306-0.801,0.304-1.104,0l-4.553-4.553c-0.305-0.305-0.305-0.8,0-1.104l3.572-3.571c1.148-1.148,1.476-2.794,0.854-4.294 C10.851,31.932,9.456,31,7.832,31H2.78C2.35,31,2,30.65,2,30.22V23.78C2,23.35,2.35,23,2.78,23h5.052 c1.624,0,3.019-0.932,3.64-2.432c0.622-1.5,0.295-3.146-0.854-4.294l-3.572-3.571c-0.305-0.305-0.305-0.8,0-1.104l4.553-4.553 c0.304-0.305,0.799-0.305,1.104,0l3.571,3.571c1.147,1.147,2.792,1.476,4.294,0.854C22.068,10.851,23,9.456,23,7.832V2.78 C23,2.35,23.35,2,23.78,2h6.439C30.65,2,31,2.35,31,2.78v5.052c0,1.624,0.932,3.019,2.432,3.64 c1.502,0.622,3.146,0.294,4.294-0.854l3.571-3.571c0.306-0.305,0.801-0.305,1.104,0l4.553,4.553c0.305,0.305,0.305,0.8,0,1.104 l-3.572,3.571c-1.148,1.148-1.476,2.794-0.854,4.294c0.621,1.5,2.016,2.432,3.64,2.432h5.052C51.65,23,52,23.35,52,23.78V30.22z"></path> <path d="M27,18c-4.963,0-9,4.037-9,9s4.037,9,9,9s9-4.037,9-9S31.963,18,27,18z M27,34c-3.859,0-7-3.141-7-7s3.141-7,7-7 s7,3.141,7,7S30.859,34,27,34z"></path> </g> </g></svg>
                </a>
                <a href="https://discord.gg/HqsJNVTkg6" target="_blank">
                  <svg stroke="currentColor" fill="currentColor" stroke-width="0" role="img" viewBox="0 0 24 24" height="54" width="54" xmlns="http://www.w3.org/2000/svg"><path d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0371c-.211.3753-.4447.8648-.6083 1.2495-1.8447-.2762-3.68-.2762-5.4868 0-.1636-.3933-.4058-.8742-.6177-1.2495a.077.077 0 00-.0785-.037 19.7363 19.7363 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8722-.8923a.077.077 0 01-.0076-.1277c.1258-.0943.2517-.1923.3718-.2914a.0743.0743 0 01.0776-.0105c3.9278 1.7933 8.18 1.7933 12.0614 0a.0739.0739 0 01.0785.0095c.1202.099.246.1981.3728.2924a.077.077 0 01-.0066.1276 12.2986 12.2986 0 01-1.873.8914.0766.0766 0 00-.0407.1067c.3604.698.7719 1.3628 1.225 1.9932a.076.076 0 00.0842.0286c1.961-.6067 3.9495-1.5219 6.0023-3.0294a.077.077 0 00.0313-.0552c.5004-5.177-.8382-9.6739-3.5485-13.6604a.061.061 0 00-.0312-.0286zM8.02 15.3312c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9555-2.4189 2.157-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.9555 2.4189-2.1569 2.4189zm7.9748 0c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9554-2.4189 2.1569-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.946 2.4189-2.1568 2.4189Z"></path></svg>
                </a> 
                <a href="https://x.com/BlackneightBH" target="_blank">
                  <svg stroke="currentColor" fill="currentColor" stroke-width="0" role="img" viewBox="0 0 24 24" height="54" width="54" xmlns="http://www.w3.org/2000/svg"><path d="M21.543 7.104c.015.211.015.423.015.636 0 6.507-4.954 14.01-14.01 14.01v-.003A13.94 13.94 0 0 1 0 19.539a9.88 9.88 0 0 0 7.287-2.041 4.93 4.93 0 0 1-4.6-3.42 4.916 4.916 0 0 0 2.223-.084A4.926 4.926 0 0 1 .96 9.167v-.062a4.887 4.887 0 0 0 2.235.616A4.928 4.928 0 0 1 1.67 3.148 13.98 13.98 0 0 0 11.82 8.292a4.929 4.929 0 0 1 8.39-4.49 9.868 9.868 0 0 0 3.128-1.196 4.941 4.941 0 0 1-2.165 2.724A9.828 9.828 0 0 0 24 4.555a10.019 10.019 0 0 1-2.457 2.549z"></path></svg>
                </a> 
                <a href="https://brawlhalla.wiki.gg/wiki/Brawlhalla_Wiki" target="_blank">
                     <svg stroke="currentColor" fill="currentColor" stroke-width="0" role="img"  viewBox="0 0 18 18"  height="54" width="54" xmlns="http://www.w3.org/2000/svg"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path></svg>
                </a> 
                <a href="../steamConnexion/logout.php" >
                    <svg class="svg-icon" style="width: 1.5em; height: 1.5em; vertical-align: middle; fill: currentColor; overflow: hidden;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg">
                        <path d="M768 106V184c97.2 76 160 194.8 160 328 0 229.6-186.4 416-416 416S96 741.6 96 512c0-133.2 62.8-251.6 160-328V106C121.6 190.8 32 341.2 32 512c0 265.2 214.8 480 480 480s480-214.8 480-480c0-170.8-89.6-321.2-224-406z" fill="" />
                        <path d="M512 32c-17.6 0-32 14.4-32 32v448c0 17.6 14.4 32 32 32s32-14.4 32-32V64c0-17.6-14.4-32-32-32z" fill="" />
                    </svg>
                </a>
            </div>
            <div class="main-container">
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
                        <?php echo $rank;?> Panel
                    </a>
                    <a class="header-link" href="Leaderboard.php">
                        <svg fill="#ffffff" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 489.4 489.4" xml:space="preserve">
                            <g>
                                <path d="M369.75,0h-250.2v44.3h-85.6V110c0,47.2,38.4,85.6,85.6,85.6h1.5c7.9,51.3,47,92.2,97.2,103v70.9h-30.7
                                     c-9.5,0-17.1,7.7-17.1,17.1v22.5h-26.2v80.3h200.9v-80.3h-26.2v-22.5c0-9.5-7.7-17.1-17.1-17.1h-30.7v-70.9
                                     c50.3-10.8,89.3-51.8,97.2-103h1.5c47.2,0,85.6-38.4,85.6-85.6V44.3h-85.6V0H369.75z M119.55,152.3c-23.3,0-42.3-19-42.3-42.3V87.6
                                     h42.3V152.3z M301.45,121.7l-25.7,21.7l8,32.7c1.5,6.1-5.2,11-10.6,7.7l-28.5-17.8l-28.6,17.7c-5.4,3.3-12.1-1.5-10.6-7.7l8-32.7
                                     l-25.6-21.6c-4.8-4.1-2.3-12,4-12.4l33.5-2.4l12.8-31.2c2.4-5.9,10.7-5.9,13.1,0l12.7,31.1l33.5,2.4
                                     C303.75,109.7,306.25,117.6,301.45,121.7z M411.95,87.6V110c0,23.3-18.9,42.3-42.2,42.3V87.6H411.95z"/>
                            </g>
                        </svg>
                        Leaderboard
                    </a>
                    <a class="header-link" href="../view/ask.php">
                        <svg viewBox="0 0 24 24" fill="#ffffff" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22 12C22 15.7712 22 17.6569 20.8284 18.8284C19.6569 20 17.7712 20 14 20H10C6.22876 20 4.34315 20 3.17157 18.8284C2 17.6569 2 15.7712 2 12C2 8.22876 2 6.34315 3.17157 5.17157C4.34315 4 6.22876 4 10 4H14C17.7712 4 19.6569 4 20.8284 5.17157C21.4816 5.82475 21.7706 6.69989 21.8985 8" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M18 8L15.8411 9.79908C14.0045 11.3296 13.0861 12.0949 12 12.0949C11.3507 12.0949 10.7614 11.8214 10 11.2744M6 8L6.9 8.75L7.8 9.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        Ask for a clan battle
                    </a>
                    <a class="header-link " href="../view/documentation.html">
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
                    <a class="header-link active" href="../view/walloffame.php">
                        <svg fill="#ffffff" version="1.1" id="Layer_1" viewBox="0 0 64 64"  width="20" height="20">
                            <path id="Diamond" d="M63.6870499,18.5730648L48.7831497,4.278266c-0.1855011-0.1758003-0.4316025-0.4813001-0.6870003-0.4813001
                                H15.9037514c-0.2553005,0-0.5014,0.3054998-0.6870003,0.4813001l-14.9038,14.1908998
                                c-0.374,0.3535004-0.4184,0.9855995-0.1025,1.3917999c0.21,0.2703991,30.8237991,39.7256012,31.0517006,39.9812012
                                c0.1022987,0.1149979,0.2402992,0.2215996,0.3428001,0.266098c0.2763996,0.1206017,0.5077,0.1296997,0.7900982,0.0065002
                                c0.1025009-0.0444984,0.2404022-0.1348991,0.3428001-0.2499008c0.0151024-0.0168991,0.0377007-0.0224991,0.0517006-0.0404968
                                L63.789547,19.9121666C64.1054459,19.5058651,64.0610504,18.9265652,63.6870499,18.5730648z M15.6273508,6.4344659
                                l4.9945002,11.3625011H3.6061509L15.6273508,6.4344659z M24.0795517,17.7969666l7.9203987-11.2617006l7.9204979,11.2617006
                                H24.0795517z M40.7191467,19.7969666l-8.7191963,34.8769989l-8.719099-34.8769989H40.7191467z M33.9257507,5.7969656h12.5423012
                                l-4.8240013,10.9746008L33.9257507,5.7969656z M22.3559513,16.7715664L17.53195,5.7969656h12.5423012L22.3559513,16.7715664z
                                M21.2191505,19.7969666l8.6596012,34.638401L2.975451,19.7969666H21.2191505z M42.7808495,19.7969666h18.2436981
                                l-26.9032974,34.638401L42.7808495,19.7969666z M43.3781471,17.7969666l4.9944992-11.3625011l12.0212021,11.3625011H43.3781471z"/>
                        </svg>
                        Wall of Fame
                    </a>

                    <!-- Button to open mobile menu -->
                    <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">☰</button>
                </div>

                <!-- Hidden mobile menu by default -->
                <nav class="mobile-nav" id="mobileNav">
                    <a href="../view/AdminPanel.php">Panel</a>
                    <a href="../view/Leaderboard.php">Leaderboard</a>
                    <a href="../view/walloffame.php">Wall of Fame</a>
                    <a href="../view/ask.php">Ask for a clan battle</a>
                    <a href="../view/documentation.html">Documentation</a>
                    <a href="../steamConnexion/logout.php">Logout</a>
                </nav>

                <?php
                    // Requête pour récupérer les saisons dont la date de fin est passée
                    $sqlsaison = "SELECT id_saison, date_debut, date_fin FROM saison WHERE date_fin <= CURDATE() ORDER BY date_fin ASC";
                    $result = $conn->query($sqlsaison);

                    // Vérifier si des saisons sont trouvées
                    if ($result->num_rows > 0) {
                        // On prépare la liste des saisons
                        $seasons = [];
                        while ($row = $result->fetch_assoc()) {
                            $seasons[] = $row;
                        }

                    } else {
                        echo "<div style='display: flex; justify-content: center; margin-top:20px;'><p>This season is not ended.</p></div>";
                        exit;
                    }
                ?>

                    <div class="tabs">
                        <?php foreach ($seasons as $season): ?>
                            <a style=" text-decoration: none;" href="?saison_id=<?php echo $season['id_saison']; ?>" class="tab <?php echo (isset($_GET['saison_id']) && $_GET['saison_id'] == $season['id_saison']) ? 'active' : ''; ?>">
                                Season <?php echo $season['id_saison']; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>



                <?php

                    // Vérifier si la saison a été passée dans l'URL
                    if (isset($_GET['saison_id'])) {
                        // Mettre à jour la saison dans la session (facultatif mais recommandé)
                        $_SESSION['saison_id'] = (int)$_GET['saison_id'];
                    }

                    // Utiliser la saison de la session (ou une valeur par défaut si non définie)
                    $saison_id = isset($_SESSION['saison_id']) ? $_SESSION['saison_id'] : 2;  // Saison par défaut = 1

                    // Requête pour récupérer les top 10 clans par Elo pour une saison spécifique
                    $sql = "SELECT id_leaderboard, un, best_elo_un, elo_un, deux, best_elo_deux, elo_deux, trois, best_elo_trois, elo_trois, 
                                quatre, best_elo_quatre, elo_quatre, cinq, best_elo_cinq, elo_cinq, six, best_elo_six, elo_six, sept, 
                                best_elo_sept, elo_sept, huit, best_elo_huit, elo_huit, neufs, best_elo_neufs, elo_neufs, dix, best_elo_dix, elo_dix
                            FROM leaderboard 
                            WHERE id_saison = ? 
                            ORDER BY elo_un DESC 
                            LIMIT 10";

                    // Préparer et exécuter la requête
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('i', $saison_id); // Paramètre pour la saison
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Tableau pour stocker les résultats
                    $topClans = [];
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $topClans[] = $row;
                        }
                    } else {
                        echo "<div style='display: flex;justify-content: center;'><p>This season is not ended.</p></div>";
                    }
                    // Fermer la connexion
                    $conn->close();
                    ?>

                <div class="podium">
                    <?php
                    // Affichage des podiums (Top 1 à 3)
                    for ($i = 0; $i < 3; $i++) {
                        if (isset($topClans[0])) {
                            // Utilisation de la bonne clé du tableau pour chaque place
                            $clan = $topClans[0];
                            $places = ['un', 'deux', 'trois'];  // Positions des clans dans le tableau

                            // Récupérer dynamiquement la position
                            $place = $places[$i];
                            $elo = 'elo_' . $place;
                            $best_elo = 'best_elo_' . $place;

                            // Définir la classe du podium (gold, silver, bronze)
                            $rankClass = $i === 0 ? 'gold' : ($i === 1 ? 'silver' : 'bronze');
                            $img = $i === 0 ? 'gold.png' : ($i === 1 ? 'silver.png' : 'bronze.png');

                            echo "
                                <div class='podium-item $rankClass'>
                                    <img src='../assets/img/$img' alt='Avatar de clan'>
                                     <h2><a  style=' text-decoration: none; ' target='_blank' href='https://corehalla.com/stats/clan/{$clan[$place]}'>{$clanTranslations[$clan[$place]]}</a></h2> 
                                    <p>{$clan[$elo]} Elo</p>
                                    <p>{$clan[$best_elo]} 💎 </p>
                                </div>
                            ";
                        }
                    }
                    ?>
        </div>
        <!-- Countdown timer below the podium -->
        <div class="date-countdown">
          Season <?php echo $saison_id;?>
        </div>

        <!-- Liste des autres clans dans le leaderboard (Top 4 à 10) -->
        <div class="leaderboard-list" id="leaderboard-list">
            <?php
            // Affichage des autres clans dans le leaderboard (Top 4 à 10)
            $places = ['quatre', 'cinq', 'six', 'sept', 'huit', 'neufs', 'dix'];  // Les positions après le top 3
            for ($i = 3; $i < 10 && isset($topClans[0]); $i++) {
                if (isset($topClans[0])) {
                    // Récupérer le clan pour chaque position
                    $clan = $topClans[0];

                    // Récupérer la bonne position et les informations associées
                    $place = $places[$i - 3];  // Décalage pour accéder aux bonnes positions
                    $elo = 'elo_' . $place;
                    $best_elo = 'best_elo_' . $place;
                    $top = $i +1;
                    echo "
                        <div class='leaderboard-item'>
                        <p>TOP $top</p>
                              <p><a  style=' text-decoration: none; ' target='_blank' href='https://corehalla.com/stats/clan/{$clan[$place]}'>{$clanTranslations[$clan[$place]]}</a></p> 
                                <p>{$clan[$elo]} Elo</p>
                                <p>{$clan[$best_elo]} 💎 </p>
                        </div>
                    ";
                }
            }
            ?>
        </div>


<script>
    // Script pour gérer l'activation des tabs (saison 1, saison 2, etc.)
    function switchTab(season_id, tabElement) {
        // Retirer la classe active de toutes les tabs
        const tabs = document.querySelectorAll('.tab');
        tabs.forEach(tab => {
            tab.classList.remove('active');
        });

        // Ajouter la classe active à l'onglet cliqué
        tabElement.classList.add('active');
        console.log('Saison sélectionnée : ' + season_id);

        // Envoi de la saison sélectionnée au backend via AJAX
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'changeSeason.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log('Saison mise à jour avec succès : ' + xhr.responseText);
                // Optionnel : rafraîchir la page ou charger dynamiquement le contenu
                window.location.href = "?saison_id=" + season_id;
            }
        };
        xhr.send('season_id=' + season_id);
    }
</script>