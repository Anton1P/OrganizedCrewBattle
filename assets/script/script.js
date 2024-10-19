let currentPage = 1;
const membersPerPage = 10;
let sortOrder = 'asc'; // Pour gérer l'ordre de tri
let sortField = 'join_date'; // Champ de tri par défaut
let totalMembers = 0; // Variable pour stocker le nombre total de membres

function loadMembers(page, sortField, order) {
    $.ajax({
        url: 'fetch_members.php',
        type: 'GET',
        data: { page: page, sort: sortField, order: order },
        success: function(response) {
            console.log("Réponse de l'API : ", response); // Ajoutez ceci pour voir la réponse
            if (typeof response === 'string') {
                try {
                    response = JSON.parse(response);
                } catch (e) {
                    console.error("Erreur lors du parsing JSON: ", e);
                    return;
                }
            }

            const members = response.data.clan || [];
            totalMembers = members.length; // Mettre à jour le nombre total de membres

            // Trier les membres par le champ spécifié
            members.sort((a, b) => {
                if (sortField === 'join_date') {
                    return (order === 'asc') ? a.join_date - b.join_date : b.join_date - a.join_date;
                } else if (sortField === 'xp') {
                    return (order === 'asc') ? a.xp - b.xp : b.xp - a.xp;
                } else {
                    const valA = a[sortField].toLowerCase();
                    const valB = b[sortField].toLowerCase();
                    return (order === 'asc') ? (valA < valB ? -1 : 1) : (valA > valB ? -1 : 1);
                }
            });

            const start = (page - 1) * membersPerPage;
            const end = start + membersPerPage;
            const paginatedMembers = members.slice(start, end);

            const tbody = $('#clan-members tbody');
            tbody.empty();

            paginatedMembers.forEach((member, index) => {
                const joinDate = new Date(member.join_date * 1000).toLocaleDateString();
                const rowClass = (index % 2 === 0) ? 'even-row' : 'odd-row'; // Classe pour les lignes alternées

                // Tronquer le pseudo si plus de 20 caractères
                const truncatedName = member.name.length > 20 ? member.name.substring(0, 20) + '...' : member.name;

                function calculateColor(xp, maxXP) {
                    const green = Math.min(255, Math.floor((xp / maxXP) * 255)); // Plus l'XP est élevée, plus il y a de vert
                    const red = 255 - green; // L'inverse pour rendre les faibles XP plus rouges
                    return `rgb(${red}, ${green}, 0)`; // Retourne une couleur entre rouge et vert
                }
                
                function calculateDateColor(joinDate, minDate, maxDate) {
                    const timeDifference = new Date(joinDate) - new Date(minDate);
                    const totalRange = new Date(maxDate) - new Date(minDate);
                    const green = Math.min(255, Math.floor((timeDifference / totalRange) * 255));
                    const red = 200 - green;
                    return `rgb(${green}, ${red}, 0)`; // Retourne une couleur du vert (dates anciennes) au rouge (dates récentes)
                }

                function getRankColor(rank) {
                    switch (rank) {
                        case 'Leader':
                            return 'gold'; // Couleur pour le Leader
                    }
                }

                // Calculer l'XP maximum pour ajuster la couleur
                const maxXP = Math.max(...members.map(member => member.xp));
                // Calculer les dates maximum pour ajuster la couleur
                const dates = members.map(member => new Date(member.join_date * 1000)); // Convertir les timestamps en Date
                const minDate = new Date(Math.min(...dates));
                const maxDate = new Date(Math.max(...dates));
                
                tbody.append(`
                    <tr class="${rowClass}">
                        <td><a target="_blank" href="https://corehalla.com/stats/player/${member.brawlhalla_id}">${truncatedName}</a></td>
                        <td style="color: ${getRankColor(member.rank)};">${member.rank}</td>
                        <td><span style="color: ${calculateColor(member.xp, maxXP)};">${member.xp}</span></td>
                        <td><span style="color: ${calculateDateColor(joinDate, minDate, maxDate)};">${joinDate}</span></td>
                    </tr>
                `);
            });

            $('#page-info').text(`Page ${page} sur ${Math.ceil(totalMembers / membersPerPage)}`);
            currentPage = page;
            $('#prev').prop('disabled', page === 1);
            $('#next').prop('disabled', end >= totalMembers);

            // Gestion de la navigation
            $('#prev').off('click').on('click', function() {
                if (currentPage === 1) {
                    loadMembers(Math.ceil(totalMembers / membersPerPage), sortField, sortOrder); // Aller à la dernière page
                } else {
                    loadMembers(currentPage - 1, sortField, sortOrder); // Page précédente
                }
            });

            $('#next').off('click').on('click', function() {
                if (end >= totalMembers) {
                    loadMembers(1, sortField, sortOrder); // Aller à la première page
                } else {
                    loadMembers(currentPage + 1, sortField, sortOrder); // Page suivante
                }
            });
        },
        error: function(xhr, status, error) {
            console.error(`Erreur : ${status} - ${error}`);
            alert('Erreur lors du chargement des membres.');
        }
    });
}

// Gestion des clics sur les en-têtes pour le tri
$('.title-th').on('click', function() {
    sortField = $(this).data('sort'); // Récupère le champ de tri
    sortOrder = (sortOrder === 'asc') ? 'desc' : 'asc'; // Inverse l'ordre de tri
    loadMembers(currentPage, sortField, sortOrder); // Recharge les membres avec le nouveau tri
});

// Fonctions pour la navigation de pages
function goToPage(page) {
    if (page < 1 || page > Math.ceil(totalMembers / membersPerPage)) {
        return; // Ne rien faire si la page est hors limites
    }
    loadMembers(page, sortField, sortOrder);
}

$(document).ready(function() {
    loadMembers(currentPage, sortField, sortOrder); // Charge les membres à la première ouverture de la page
});
