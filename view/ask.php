<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande de Tournoi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.css">
    <link rel="stylesheet" href="../assets/styles/ask.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="container">
    <?php session_start();
      if (isset($_SESSION['from_treatment']) && isset($_SESSION['notification'])) {
            echo '<div class="notification">' . $_SESSION['notification'] . '</div>';
            unset($_SESSION['notification']); // Supprimer la notification après l'affichage
        }
    ?>
    
    <h2>Recherche de Clan</h2>

    <div id="recap" class="recap">
        <p id="recapText"></p> 
    </div>


    <form action="../bddConnexion/traitement_askForm.php" method="POST" id="tournamentForm">
     
        <div id="clanSearchComponent" class="search-container">
            <label for="clan_search">Rechercher un clan :</label>
            <input type="text" class="search-bar" id="clan_search" placeholder="Nom du clan" required>
            <div id="search_results" class="search-results"></div>
        </div>

        <!-- Choix du format (invisible par défaut) -->
        <div id="formatComponent" class="hidden">
            <h2>Choisir le format du tournoi</h2>
            <label for="format">Format :</label>
            <select id="format" name="format" required>
                <option disabled selected value="">--Sélectionnez un format--</option>
                <option value="1">CrewBattle Bo3</option>
                <option value="2">CrewBattle Bo5</option>
                <option value="3">French CrewBattle</option>
            </select>
            <button type="button" id="formatNext">Valider</button>
        </div>

        <!-- Choix de la date (invisible par défaut) -->
        <div id="dateComponent" class="hidden">
            <h2>Choisir la date et l'heure</h2>
            <label for="date_rencontre">Date et heure :</label>
            <input type="datetime-local" id="date_rencontre" name="date_rencontre" class="date-picker" required>
            <button type="submit" id="submitForm">Envoyer</button>
        </div>
        
        <!-- Champs cachés pour les clans sélectionnés -->
        <input type="hidden" name="clan_ids[]" id="clan_ids"> <!-- Pour stocker l'ID du clan sélectionné -->
        <input type="hidden" name="joueurs[]" id="joueurs_ids"> <!-- Pour stocker les joueurs sélectionnés -->
    </form>
</div>

<script>
    $(document).ready(function() {
        var recapClan = ""; // Variable pour stocker le nom du clan
        var recapFormat = ""; // Variable pour stocker le format
        var recapDate = ""; // Variable pour stocker la date

        function updateRecap() {
            var recapText = ""; // Variable pour stocker le texte à afficher

            if (recapClan) {
                recapText += recapClan;
            }
            if (recapFormat) {
                recapText += (recapText ? " > " : "") + recapFormat;
            }
            if (recapDate) {
                recapText += (recapText ? " > " : "") + recapDate;
            }

            // Si le texte de récapitulatif n'est pas vide, on l'affiche
            if (recapText) {
                $('#recap').show(); // Affiche la div si elle n'était pas affichée
                $('#recapText').text(recapText); // Met à jour le texte du récapitulatif
            } else {
                $('#recap').hide(); // Cache la div si aucun élément n'est sélectionné
            }
        }

        // Barre de recherche en temps réel
        $('#clan_search').on('keyup', function() {
            var searchQuery = $(this).val();
            if (searchQuery.length >= 2) {
                $.ajax({
                    url: '../bddConnexion/search_clans.php',
                    type: 'POST',
                    data: { search: searchQuery },
                    success: function(response) {
                        $('#search_results').html(response);
                    }
                });
            } else {
                $('#search_results').html('');
            }
        });

        // Lorsqu'un lien de clan est cliqué
        $(document).on('click', '.clan-link', function(e) {
            e.preventDefault(); // Empêcher la redirection

            // Récupérer les informations du clan depuis le lien
            var selectedClanId = $(this).data('id');
            var selectedClanName = $(this).data('nom');

            // Ajouter l'ID du clan sélectionné au formulaire
            $('#clan_ids').val(selectedClanId); // Met à jour le champ caché avec l'ID du clan

            // Mettre à jour la variable de récapitulatif pour le clan
            recapClan = selectedClanName;
            updateRecap(); // Mettre à jour l'affichage

            // Cacher la recherche et afficher le format de tournoi
            $('#clanSearchComponent').addClass('hidden');
            $('#formatComponent').removeClass('hidden');
        });

        // Lorsque le format est validé
        $('#formatNext').on('click', function() {
            var formatText = $('#format option:selected').text();

            // Mettre à jour la variable de récapitulatif pour le format
            recapFormat = formatText;
            updateRecap(); // Mettre à jour l'affichage

            // Cacher le format et afficher la sélection de date
            $('#formatComponent').addClass('hidden');
            $('#dateComponent').removeClass('hidden');
        });

        // Lorsque la date est sélectionnée
        $('#date_rencontre').on('change', function() {
            var selectedDate = $(this).val();

            // Mettre à jour la variable de récapitulatif pour la date
            recapDate = selectedDate;
            updateRecap(); // Mettre à jour l'affichage
        });

        // Initialiser le datepicker
        flatpickr(".date-picker", {
            enableTime: true,
            dateFormat: "Y-m-d H:i:S",
            time_24hr: true,
            minDate: "<?php echo (new DateTime())->format('Y-m-d\TH:i'); ?>"
        });
    });
</script>

</body>
</html>
