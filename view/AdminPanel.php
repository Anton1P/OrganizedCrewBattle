<?php   
include "../APIBrawlhalla/security.php";
include "../bddConnexion/bddConnexion.php";
include "../bddConnexion/loadData.php";
include "../bddConnexion/data_clan.php";
?>
<!-- https://www.youtube.com/watch?v=fmttOmYMm6Q&list=RDcr5kbp7Fu3w&index=13 Masterclass ahky -->
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
                <div class="header">
                    <div class="logo">
                        <a href="AdminPanel.php"><img style="height: 80px;" src="../assets/img/mini-logo-2.png" alt=""></a>
                    </div>
                    <a class="header-link active" href="#">
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
                    <a class="header-link" href="../view/documentation.html">
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
                                echo "<a class='header-link' href='../bddConnexion/moderation_access.php'>
                                    <svg viewBox='0 0 24 24' fill='#ffffff'>
                                        <path d='M20.4329 14.1733C20.4772 13.7615 20.1792 13.3917 19.7674 13.3474C19.3556 13.3031 18.9858 13.601 18.9415 14.0129L20.4329 14.1733ZM4.3128 14.0931L3.5671 14.1733L4.3128 14.0931ZM4.12945 12.3884L4.87514 12.3082L4.12945 12.3884ZM8.76006 10.934L9.41507 11.2993L8.76006 10.934ZM10.5495 7.7254L9.89453 7.36008L10.5495 7.7254ZM13.4505 7.7254L12.7954 8.09071L13.4505 7.7254ZM15.2399 10.934L15.895 10.5686V10.5686L15.2399 10.934ZM16.0038 11.9592L15.7015 12.6456L15.7015 12.6456L16.0038 11.9592ZM17.4705 11.2451L16.9412 10.7138L17.4705 11.2451ZM16.4533 12.0219L16.3506 11.2789L16.3506 11.2789L16.4533 12.0219ZM6.5295 11.2451L6.0002 11.7765L6.5295 11.2451ZM7.5467 12.0219L7.64943 11.2789L7.64943 11.2789L7.5467 12.0219ZM7.99621 11.9592L8.29846 12.6456L8.29846 12.6456L7.99621 11.9592ZM5.71208 20.1532L6.21228 19.5943H6.21228L5.71208 20.1532ZM18.2879 20.1532L17.7877 19.5943L18.2879 20.1532ZM19.9823 10.4999L19.9736 11.2498L19.9823 10.4999ZM18.8645 9.98013L19.432 9.48982L18.8645 9.98013ZM12.9077 6.78265L12.5668 6.11457L12.9077 6.78265ZM11.0923 6.78265L11.4332 6.11457L11.0923 6.78265ZM13.0879 20.25H10.9121V21.75H13.0879V20.25ZM5.0585 14.0129L4.87514 12.3082L3.38375 12.4686L3.5671 14.1733L5.0585 14.0129ZM9.41507 11.2993L11.2046 8.09072L9.89453 7.36008L8.10504 10.5686L9.41507 11.2993ZM12.7954 8.09071L14.5849 11.2993L15.895 10.5686L14.1055 7.36008L12.7954 8.09071ZM14.5849 11.2993C14.7467 11.5893 14.8956 11.8582 15.0399 12.0638C15.1885 12.2753 15.3911 12.5089 15.7015 12.6456L16.306 11.2728C16.3619 11.2973 16.3524 11.3226 16.2675 11.2018C16.1784 11.0749 16.0727 10.8873 15.895 10.5686L14.5849 11.2993ZM16.9412 10.7138C16.6825 10.9715 16.529 11.1231 16.4082 11.2208C16.2931 11.3139 16.2906 11.2872 16.3506 11.2789L16.556 12.7648C16.8918 12.7184 17.1507 12.5495 17.3517 12.3869C17.547 12.2289 17.7642 12.0112 17.9998 11.7765L16.9412 10.7138ZM15.7015 12.6456C15.9698 12.7637 16.2657 12.8049 16.556 12.7648L16.3506 11.2789C16.3353 11.281 16.3199 11.2789 16.306 11.2728L15.7015 12.6456ZM6.0002 11.7765C6.23578 12.0112 6.453 12.2289 6.64834 12.3869C6.84933 12.5495 7.10824 12.7184 7.44397 12.7648L7.64943 11.2789C7.70944 11.2872 7.7069 11.3139 7.5918 11.2208C7.47104 11.1231 7.31753 10.9715 7.05879 10.7138L6.0002 11.7765ZM8.10504 10.5686C7.92732 10.8873 7.82158 11.0749 7.7325 11.2018C7.64765 11.3226 7.63814 11.2973 7.69395 11.2728L8.29846 12.6456C8.60887 12.5089 8.81155 12.2753 8.96009 12.0638C9.10441 11.8583 9.2533 11.5893 9.41507 11.2993L8.10504 10.5686ZM7.44397 12.7648C7.73429 12.8049 8.03016 12.7637 8.29846 12.6456L7.69395 11.2728C7.68011 11.2789 7.66466 11.281 7.64943 11.2789L7.44397 12.7648ZM10.9121 20.25C9.47421 20.25 8.46719 20.2486 7.69857 20.1502C6.9509 20.0545 6.52851 19.8774 6.21228 19.5943L5.21187 20.712C5.84173 21.2758 6.60137 21.522 7.50819 21.6381C8.39406 21.7514 9.51399 21.75 10.9121 21.75V20.25ZM3.5671 14.1733C3.71526 15.5507 3.83282 16.8999 4.03322 17.994C4.1343 18.5459 4.26178 19.0659 4.43833 19.5172C4.61339 19.9648 4.8549 20.3925 5.21187 20.712L6.21228 19.5943C6.0962 19.4904 5.96405 19.3 5.83525 18.9708C5.70795 18.6454 5.60138 18.2299 5.50868 17.7238C5.32149 16.7018 5.21246 15.4443 5.0585 14.0129L3.5671 14.1733ZM18.9415 14.0129C18.7875 15.4443 18.6785 16.7018 18.4913 17.7238C18.3986 18.2299 18.292 18.6454 18.1647 18.9708C18.036 19.3 17.9038 19.4904 17.7877 19.5943L18.7881 20.712C19.1451 20.3925 19.3866 19.9648 19.5617 19.5172C19.7382 19.0659 19.8657 18.5459 19.9668 17.994C20.1672 16.8999 20.2847 15.5507 20.4329 14.1733L18.9415 14.0129ZM13.0879 21.75C14.486 21.75 15.6059 21.7514 16.4918 21.6381C17.3986 21.522 18.1583 21.2758 18.7881 20.712L17.7877 19.5943C17.4715 19.8774 17.0491 20.0545 16.3014 20.1502C15.5328 20.2486 14.5258 20.25 13.0879 20.25V21.75ZM10.75 5C10.75 4.30964 11.3096 3.75 12 3.75V2.25C10.4812 2.25 9.25 3.48122 9.25 5H10.75ZM12 3.75C12.6904 3.75 13.25 4.30964 13.25 5H14.75C14.75 3.48122 13.5188 2.25 12 2.25V3.75ZM20.75 9C20.75 9.41421 20.4142 9.75 20 9.75V11.25C21.2426 11.25 22.25 10.2426 22.25 9H20.75ZM19.25 9C19.25 8.58579 19.5858 8.25 20 8.25V6.75C18.7574 6.75 17.75 7.75736 17.75 9H19.25ZM20 8.25C20.4142 8.25 20.75 8.58579 20.75 9H22.25C22.25 7.75736 21.2426 6.75 20 6.75V8.25ZM4 9.75C3.58579 9.75 3.25 9.41421 3.25 9H1.75C1.75 10.2426 2.75736 11.25 4 11.25V9.75ZM3.25 9C3.25 8.58579 3.58579 8.25 4 8.25V6.75C2.75736 6.75 1.75 7.75736 1.75 9H3.25ZM4 8.25C4.41421 8.25 4.75 8.58579 4.75 9H6.25C6.25 7.75736 5.24264 6.75 4 6.75V8.25ZM20 9.75C19.997 9.75 19.994 9.74998 19.991 9.74995L19.9736 11.2498C19.9824 11.2499 19.9912 11.25 20 11.25V9.75ZM19.991 9.74995C19.7681 9.74737 19.5689 9.64827 19.432 9.48982L18.2969 10.4704C18.703 10.9405 19.3032 11.2421 19.9736 11.2498L19.991 9.74995ZM19.432 9.48982C19.3181 9.35799 19.25 9.18789 19.25 9H17.75C17.75 9.56143 17.9566 10.0765 18.2969 10.4704L19.432 9.48982ZM17.9998 11.7765C18.6773 11.1017 19.0262 10.7616 19.2584 10.6183L18.4705 9.34191C18.0506 9.60109 17.547 10.1103 16.9412 10.7138L17.9998 11.7765ZM4.75 9C4.75 9.18789 4.68188 9.35799 4.56799 9.48982L5.70307 10.4704C6.0434 10.0765 6.25 9.56143 6.25 9H4.75ZM7.05879 10.7138C6.45296 10.1103 5.94936 9.60109 5.52946 9.34191L4.7416 10.6183C4.97377 10.7616 5.32273 11.1017 6.0002 11.7765L7.05879 10.7138ZM4.56799 9.48982C4.4311 9.64827 4.23192 9.74737 4.00904 9.74995L4.02639 11.2498C4.69676 11.2421 5.29701 10.9405 5.70307 10.4704L4.56799 9.48982ZM4.00904 9.74995C4.00602 9.74998 4.00301 9.75 4 9.75V11.25C4.00881 11.25 4.01761 11.2499 4.02639 11.2498L4.00904 9.74995ZM4.87514 12.3082C4.82571 11.8486 4.78687 11.4865 4.76601 11.192C4.74467 10.8908 4.74636 10.7093 4.76107 10.5995L3.27435 10.4003C3.23837 10.6689 3.24701 10.9769 3.26976 11.298C3.29298 11.6258 3.33535 12.0187 3.38375 12.4686L4.87514 12.3082ZM13.25 5C13.25 5.48504 12.9739 5.90689 12.5668 6.11457L13.2485 7.45073C14.1381 6.99685 14.75 6.07053 14.75 5H13.25ZM12.5668 6.11457C12.3975 6.20095 12.2056 6.25 12 6.25V7.75C12.448 7.75 12.873 7.6423 13.2485 7.45073L12.5668 6.11457ZM14.1055 7.36008C13.8992 6.9902 13.7138 6.65746 13.5437 6.3852L12.2716 7.1801C12.4176 7.41372 12.5828 7.70948 12.7954 8.09071L14.1055 7.36008ZM12 6.25C11.7944 6.25 11.6025 6.20095 11.4332 6.11457L10.7515 7.45073C11.127 7.6423 11.552 7.75 12 7.75V6.25ZM11.4332 6.11457C11.0261 5.90689 10.75 5.48504 10.75 5H9.25C9.25 6.07053 9.86186 6.99685 10.7515 7.45073L11.4332 6.11457ZM11.2046 8.09072C11.4172 7.70948 11.5824 7.41372 11.7284 7.1801L10.4563 6.3852C10.2862 6.65746 10.1008 6.9902 9.89453 7.36008L11.2046 8.09072Z' fill='#ffffff'/>
                                        <path d='M5 17.5H19' stroke='#ffffff' stroke-width='1.5' stroke-linecap='round'/>
                                    </svg>
                                    Access Moderation
                                </a>";
                            }
                        }   
                    ?>
                    <div class="notification-wrapper">
                        <div class="notification-icon" id="notificationIcon">
                            <!-- White bell icon in SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32">
                                <path d="M12 2C10.9 2 10 2.9 10 4C10 5.1 10.9 6 12 6C13.1 6 14 5.1 14 4C14 2.9 13.1 2 12 2zM18 8V11C18 12.5 18.8 13.8 20 14.5V17H4V14.5C5.2 13.8 6 12.5 6 11V8C6 5.2 8.2 3 11 3H13C15.8 3 18 5.2 18 8ZM12 22C13.1 22 14 21.1 14 20H10C10 21.1 10.9 22 12 22Z" />
                            </svg>
                            <div class="ping" id="notificationPing">1</div>
                        </div>

                        <div class="notification-list" id="notificationList">
                            <ul>
                                <!-- Notifications will be generated here -->
                                <?php include "../bddConnexion/researchNotifications.php";?>
                            </ul>
                        </div>
                    </div>

                    <!-- Button to open mobile menu -->
                    <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">☰</button>
                </div>

                <!-- Hidden mobile menu by default -->
                <nav class="mobile-nav" id="mobileNav">
                    <a href="../view/AdminPanel.php">Panel</a>
                    <a href="../view/Leaderboard.php">Leaderboard</a>
                    <a href="../view/ask.php">Ask for a clan battle</a>
                    <a href="../view/documentation.html">Documentation</a>
                    <a href="../steamConnexion/logout.php">Logout</a>
                </nav>

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
                        <?php include "../bddConnexion/traitement_tournoiUpdate.php";?>
                    </div>
                </div>
                <div class="user-box second-box">
                    <div class="cards-wrapper" style="--delay: 1s;">
                        <div class="cards-header" style="padding: 10px 20px;">
                            <div class="cards-header-date" style="display:flex;">
                                <h3>Clan Asakai</h3> <span style="margin-left:8px;">->  
                                <?php echo $data["elo_rating"];?> elo</span>
                                <span style="margin-left:45vw;">Top 158</span>
                            </div>
                        </div>
                        <div class="cards card">
                            <div id="div-myChart">
                                <div id="table-container">
                                    <table id="clan-members">
                                        <thead>
                                            <tr>
                                                <th class="title-th" data-sort="name">Name</th>
                                                <th class="title-th" data-sort="rank">Rank</th>
                                                <th class="title-th" data-sort="xp">XP</th>
                                                <th class="title-th" data-sort="join_date">Join Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Members will be loaded here via AJAX -->
                                        </tbody>
                                    </table>
                                    <div id="pagination">
                                        <svg id="prev" onclick="loadMembers(currentPage - 1)" class="pagination-svg" aria-label="Previous page" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" viewBox="0 0 24 24" style="cursor: pointer;">
                                            <path d="M15 18l-6-6 6-6v12z"/>
                                        </svg>
                                        <span id="page-info">Page 1</span>
                                        <svg id="next" onclick="loadMembers(currentPage + 1)" class="pagination-svg" aria-label="Next page" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" viewBox="0 0 24 24" style="cursor: pointer;">
                                            <path d="M9 6l6 6-6 6V6z"/>
                                        </svg>
                                    </div>
                                </div>

                                <div style="max-width: 350px;">
                                    <canvas id="myChart" width="350" height="350"></canvas>
                                </div>
                            </div>      
                        </div>
                    </div>

                    <div class="card transection" style="--delay: 1.2s;">
                        <?php include "../view/info_tournois.php";?>          
                    </div>
                </div>
                <div class="card transection hey" style="display:none;">
                    <?php include "../view/info_tournois.php";?>
                </div>
            </div>
        </div>
    </body>
</html>




<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script src="../assets/script/script.js"></script>

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
    // Fonction pour faire la requête AJAX
   function checkReport() {
    $.ajax({
        url: '../bddConnexion/chronoVerification.php', // Fichier qui vérifie le temps écoulé
        type: 'POST',
        data: {
            id_tournoi: <?php echo $_SESSION['id_tournoi']; ?>,
            id_clan_demandeur: <?php echo $_SESSION['id_clan_demandeur']; ?>,
            id_clan_receveur: <?php echo $_SESSION['id_clan_receveur']; ?>
        },
        success: function(response) {
            const data = JSON.parse(response);

            if (data.match_verified) {
                console.log('Le match a déjà été vérifié.');
                return; 
            }

            if (data.status === 'redirect') {
                // Rediriger avec les données de formulaire
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = '../view/matchVerif.php';
                
                for (const key in data.formData) {
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = data.formData[key];
                    form.appendChild(input);
                }
                
                document.body.appendChild(form);
                form.submit();
            } else if (data.status === 'success') {
                window.location.href = data.redirect; // Redirection vers la page de traitement ELO
            } else if (data.status === 'waiting') {
                $('#response-container').html(data.message);
            } else if (data.status === 'no_report') {
                $('#response-container').html(data.message);
            }
        },
        error: function() {
            console.error("Erreur lors de la vérification du report.");
        }
    });
}

    // Fonction pour démarrer les requêtes AJAX toutes les secondes après le premier appel
    function updateTimerAndCheckReport() {
        // Lancer la première requête immédiatement
        checkReport();

        // Ensuite, exécuter la requête chaque seconde
        setInterval(function() {
            checkReport();
        }, 10000); // Toutes les secondes
    }

    // Démarrer la fonction
    updateTimerAndCheckReport();
</script>

<script>
        function toggleMobileMenu() {
            const nav = document.getElementById('mobileNav');
            nav.classList.toggle('active');
        }
    </script>