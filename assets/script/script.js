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