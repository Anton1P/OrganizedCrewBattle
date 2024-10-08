const ctx = document.getElementById('myChart').getContext('2d');

new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: [
      'Wins',
      'Loses'
    ],
    datasets: [{
      label: 'Career',
      data: [300, 50],
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
        display: true // Désactive la légende
      }
    }
  }
});

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

                tbody.append(`
                    <tr class="${rowClass}">
                        <td>${truncatedName}</td>
                        <td>${member.rank}</td>
                        <td>${member.xp}</td>
                        <td>${joinDate}</td>
                    </tr>
                `);
            });

            $('#page-info').text(`Page ${page} sur ${Math.ceil(totalMembers / membersPerPage)}`);
            currentPage = page;
            $('#prev').prop('disabled', page === 1);
            $('#next').prop('disabled', end >= totalMembers);
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

// Navigation précédente
$('#prev').on('click', function() {
    goToPage(currentPage - 1); // Page précédente
});

// Navigation suivante
$('#next').on('click', function() {
    goToPage(currentPage + 1); // Page suivante
});

$(document).ready(function() {
    loadMembers(currentPage, sortField, sortOrder); // Charge les membres à la première ouverture de la page
});
