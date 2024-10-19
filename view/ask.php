<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranked CrewBattle - Tournament Request</title>
    <link rel="icon" href="../assets/img/mini-logo-2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.css">
    <link rel="stylesheet" href="../assets/styles/ask.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>
<body>

<div class="container">
    <?php 
        session_start();
        if (isset($_SESSION['from_treatment']) && isset($_SESSION['notification'])) {
            echo '<div class="notification">' . $_SESSION['notification'] . '</div>';
            unset($_SESSION['notification']); // Remove the notification after displaying it
        }
    ?>
    
    <h2>Clan Search</h2>

    <!-- Recap section -->
    <div id="recap" class="recap">
        <p id="recapText"></p> 
    </div>

    <!-- Search and request form -->
    <form action="../bddConnexion/traitement_askForm.php" method="POST" id="tournamentForm">
        
        <!-- Clan search component -->
        <div id="clanSearchComponent" class="search-container">
            <label for="clan_search">Search for a clan:</label>
            <div>
                <input type="text" class="search-bar" id="clan_search" placeholder="Clan name" required>
                <ul id="search_results" class="search-results"></ul>
            </div>

            <!-- Clan suggestion component -->
            <div id="clanSuggestionComponent">
                <h2>Clan Suggestions</h2>
                <div class="suggestion-container">
                    <div class="criteria">
                        <label for="suggestion_criteria">Suggestion criteria:</label>
                        <select id="suggestion_criteria" name="suggestion_criteria">
                            <option value="elo">Clans with similar Elo range</option>
                            <option value="elo_higher">Clans with ~100 more Elo</option>
                            <option value="elo_lower">Clans with ~100 less Elo</option>
                        </select>
                    </div>
                    
                    <div class="suggestions">
                        <label for="suggested_clan">Suggested clans:</label>
                        <ul id="suggested_clan_list" class="suggested-clan-list">
                            <!-- Suggestions will be dynamically added by AJAX -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tournament format selection (hidden by default) -->
        <div id="formatComponent" class="hidden">
            <h2>Select Tournament Format</h2>
            <div class="select-container">
                <ul id="sortable" class="sortable-list">
                    <li data-value="crew_battle" data-order="1">
                        <div class="handle"></div>
                        <span>Crew Battle</span>
                        <span class="bo-label">BO</span>
                        <input type="number" class="number-input" name="format_number[]" min="1" max="7" step="2" value="1">
                        <input type="hidden" name="format[]" value="crew_battle">
                        <span class="trash-icon" onclick="removeItem(this)">🗑️</span>
                    </li>
                    <li data-value="2v2" data-order="2">
                        <div class="handle"></div>
                        <span>2v2</span>
                        <span class="bo-label">BO</span>
                        <input type="number" class="number-input" name="format_number[]" min="1" max="7" step="2" value="1">
                        <input type="hidden" name="format[]" value="2v2">
                        <span class="trash-icon" onclick="removeItem(this)">🗑️</span>
                    </li>
                    <li data-value="1v1" data-order="3">
                        <div class="handle"></div>
                        <span>1v1</span>
                        <span class="bo-label">BO</span>
                        <input type="number" class="number-input" name="format_number[]" min="1" max="7" step="2" value="1">
                        <input type="hidden" name="format[]" value="1v1">
                        <span class="trash-icon" onclick="removeItem(this)">🗑️</span>
                    </li>
                </ul>
                <button type="button" id="formatNext">Confirm</button>
            </div>
        </div>

        <!-- Date selection (hidden by default) -->
        <div id="dateComponent" class="hidden">
            <h2>Select Date and Time</h2>
            <label for="date_rencontre">Date and Time:</label>
            <input type="datetime-local" id="date_rencontre" name="date_rencontre" class="date-picker" required>
            <button type="submit" id="submitForm">Submit</button>
        </div>
        
        <!-- Hidden fields for selected clans -->
        <input type="hidden" name="clan_ids[]" id="clan_ids"> <!-- To store the selected clan ID -->
        <input type="hidden" name="joueurs[]" id="joueurs_ids"> <!-- To store the selected players -->
    </form>
</div>

<script>
    $(document).ready(function() {
        var recapClan = ""; // Stores the name of the selected clan
        var recapFormat = ""; // Stores the tournament format
        var recapDate = ""; // Stores the selected date

        // Update the recap section
        function updateRecap() {
            var recapText = recapClan ? recapClan : '';
            recapText += recapFormat ? (recapText ? " > " : "") + recapFormat : '';
            recapText += recapDate ? (recapText ? " > " : "") + recapDate : '';

            if (recapText) {
                $('#recap').show();
                $('#recapText').text(recapText);
            } else {
                $('#recap').hide();
            }
        }

        // Real-time clan search
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

        // Clan selection from search results
        $(document).on('click', '.clan-link', function(e) {
            e.preventDefault();
            var selectedClanId = $(this).data('id');
            var selectedClanName = $(this).data('nom');

            $('#clan_ids').val(selectedClanId); // Store the clan ID
            recapClan = selectedClanName;
            updateRecap();

            $('#clanSearchComponent').addClass('hidden');
            $('#formatComponent').removeClass('hidden');
        });

        // Format confirmation
        $('#formatNext').on('click', function() {
            recapFormat = $('#sortable li').length ? 'Formats sélectionnés' : ''; // Met à jour le format sélectionné
            updateRecap();

            $('#formatComponent').addClass('hidden');
            $('#dateComponent').removeClass('hidden');
        });

        // Date selection
        $('#date_rencontre').on('change', function() {
            recapDate = $(this).val();
            updateRecap();
        });

        // Initialize date picker
        flatpickr(".date-picker", {
            enableTime: true,
            dateFormat: "Y-m-d H:i:S",
            time_24hr: true,
            minDate: "<?php echo (new DateTime())->format('Y-m-d\TH:i'); ?>",
        });

        // Load clan suggestions
        function loadClanSuggestions(criteria) {
            $.ajax({
                url: '../bddConnexion/suggest_clans.php',
                type: 'POST',
                data: { criteria: criteria },
                success: function(response) {
                    $('#suggested_clan_list').html(response);
                }
            });
        }

        // Load suggestions when the page loads
        loadClanSuggestions($('#suggestion_criteria').val());

        // Update suggestions when the criteria changes
        $('#suggestion_criteria').on('change', function() {
            loadClanSuggestions($(this).val());
        });

        // Fonction pour vérifier si la liste sortable a des éléments
        function checkSelection() {
            if ($('#sortable li').length === 0) {
                $('#formatNext').prop('disabled', true);
            } else {
                $('#formatNext').prop('disabled', false);
            }
        }

        // Suppression d'un format
        window.removeItem = function(element) {
            $(element).closest('li').remove(); // Remove the list item
            checkSelection(); // Vérifie la sélection après la suppression
        };

        // Make the list sortable
        $('#sortable').sortable({
            handle: '.handle',
            stop: checkSelection // Check selection after sorting
        });

        // Collect and send data with order on form submission
        $('#tournamentForm').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            var orderArray = [];
            $('#sortable li').each(function() {
                orderArray.push($(this).data('order')); // Collect data-order values
            });

            // Add the order array to the form data
            $('<input>').attr({
                type: 'hidden',
                name: 'format_order',
                value: JSON.stringify(orderArray) // Send as JSON string
            }).appendTo('#tournamentForm');

            this.submit(); // Submit the form
        });

        // Initial check for the sortable list
        checkSelection();
    });
</script>

</body>
</html>
